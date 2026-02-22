<?php

declare(strict_types=1);

namespace RagPhp\Ollama;

use RagPhp\Core\Contract\EmbedderInterface;
use RagPhp\Core\Exception\EmbeddingException;

final class OllamaEmbedder implements EmbedderInterface
{
    private const array MODEL_DIMENSIONS = [
        'nomic-embed-text' => 768,
        'mxbai-embed-large' => 1024,
        'all-minilm' => 384,
        'snowflake-arctic-embed' => 1024,
    ];

    private readonly string $baseUri;

    public function __construct(
        string|OllamaConfiguration|null $config = null,
        private readonly string $model = 'nomic-embed-text',
    ) {
        $this->baseUri = match (true) {
            $config instanceof OllamaConfiguration => $config->baseUri,
            is_string($config) => $config,
            default => 'http://localhost:11434',
        };
    }

    /**
     * @return list<float>
     */
    public function embed(string $text): array
    {
        $result = $this->request('/api/embed', [
            'model' => $this->model,
            'input' => $text,
        ]);

        /** @var list<float> */
        return $result['embeddings'][0]
            ?? throw new EmbeddingException('Invalid Ollama embedding response');
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

        $result = $this->request('/api/embed', [
            'model' => $this->model,
            'input' => $texts,
        ]);

        /** @var list<list<float>> */
        return $result['embeddings']
            ?? throw new EmbeddingException('Invalid Ollama batch embedding response');
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
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!is_string($response) || $httpCode !== 200) {
            throw new EmbeddingException(
                sprintf('Ollama API error (HTTP %d): %s', $httpCode, is_string($response) ? $response : 'No response'),
            );
        }

        /** @var array<string, mixed> */
        return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    }
}
