<?php

declare(strict_types=1);

namespace RagPhp\Mistral;

use RagPhp\Core\Contract\EmbedderInterface;
use RagPhp\Core\Exception\EmbeddingException;

final class MistralEmbedder implements EmbedderInterface
{
    private const array MODEL_DIMENSIONS = [
        'mistral-embed' => 1024,
    ];

    private readonly string $apiKey;
    private readonly string $baseUri;

    public function __construct(
        string|MistralConfiguration $config,
        private readonly string $model = 'mistral-embed',
    ) {
        if ($config instanceof MistralConfiguration) {
            $this->apiKey = $config->apiKey;
            $this->baseUri = $config->baseUri;
        } else {
            $this->apiKey = $config;
            $this->baseUri = 'https://api.mistral.ai/v1';
        }
    }

    /**
     * @return list<float>
     */
    public function embed(string $text): array
    {
        $result = $this->request('/embeddings', [
            'model' => $this->model,
            'input' => [$text],
        ]);

        /** @var list<float> */
        return $result['data'][0]['embedding']
            ?? throw new EmbeddingException('Invalid Mistral embedding response');
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

        $result = $this->request('/embeddings', [
            'model' => $this->model,
            'input' => $texts,
        ]);

        return array_map(
            static fn (array $item): array => $item['embedding'],
            $result['data'] ?? [],
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
        $url = rtrim($this->baseUri, '/') . $endpoint;
        $ch = curl_init($url);

        if ($ch === false) {
            throw new EmbeddingException('Failed to initialize cURL');
        }

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_THROW_ON_ERROR),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!is_string($response) || $httpCode !== 200) {
            throw new EmbeddingException(
                sprintf('Mistral API error (HTTP %d): %s', $httpCode, is_string($response) ? $response : 'No response'),
            );
        }

        /** @var array<string, mixed> */
        return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    }
}
