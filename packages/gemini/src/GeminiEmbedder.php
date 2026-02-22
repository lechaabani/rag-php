<?php

declare(strict_types=1);

namespace RagPhp\Gemini;

use RagPhp\Core\Contract\EmbedderInterface;
use RagPhp\Core\Exception\EmbeddingException;

final class GeminiEmbedder implements EmbedderInterface
{
    private const array MODEL_DIMENSIONS = [
        'text-embedding-004' => 768,
        'embedding-001' => 768,
    ];

    private readonly string $apiKey;
    private readonly string $baseUri;

    public function __construct(
        string|GeminiConfiguration $config,
        private readonly string $model = 'text-embedding-004',
    ) {
        if ($config instanceof GeminiConfiguration) {
            $this->apiKey = $config->apiKey;
            $this->baseUri = $config->baseUri;
        } else {
            $this->apiKey = $config;
            $this->baseUri = 'https://generativelanguage.googleapis.com/v1beta';
        }
    }

    /**
     * @return list<float>
     */
    public function embed(string $text): array
    {
        $result = $this->request('embedContent', [
            'model' => "models/{$this->model}",
            'content' => ['parts' => [['text' => $text]]],
        ]);

        /** @var list<float> */
        return $result['embedding']['values']
            ?? throw new EmbeddingException('Invalid Gemini embedding response');
    }

    /**
     * @param list<string> $texts
     * @return list<list<float>>
     */
    public function embedBatch(array $texts): array
    {
        if ($texts === []) {
            return [];
        }

        $requests = array_map(fn (string $text): array => [
            'model' => "models/{$this->model}",
            'content' => ['parts' => [['text' => $text]]],
        ], $texts);

        $result = $this->request('batchEmbedContents', [
            'requests' => $requests,
        ]);

        return array_map(
            static fn (array $embedding): array => $embedding['values'],
            $result['embeddings'] ?? [],
        );
    }

    public function getDimensions(): int
    {
        return self::MODEL_DIMENSIONS[$this->model]
            ?? throw new EmbeddingException(sprintf('Unknown model dimensions for: %s', $this->model));
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function request(string $endpoint, array $payload): array
    {
        $url = sprintf(
            '%s/models/%s:%s?key=%s',
            $this->baseUri,
            $this->model,
            $endpoint,
            $this->apiKey,
        );

        $ch = curl_init($url);

        if ($ch === false) {
            throw new EmbeddingException('Failed to initialize cURL');
        }

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_THROW_ON_ERROR),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!is_string($response) || $httpCode !== 200) {
            throw new EmbeddingException(
                sprintf('Gemini API error (HTTP %d): %s', $httpCode, is_string($response) ? $response : 'No response'),
            );
        }

        /** @var array<string, mixed> */
        return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    }
}
