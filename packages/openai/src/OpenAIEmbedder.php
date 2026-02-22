<?php

declare(strict_types=1);

namespace RagPhp\OpenAI;

use OpenAI;
use OpenAI\Client;
use RagPhp\Core\Contract\EmbedderInterface;
use RagPhp\Core\Exception\EmbeddingException;

final class OpenAIEmbedder implements EmbedderInterface
{
    private const array MODEL_DIMENSIONS = [
        'text-embedding-3-small' => 1536,
        'text-embedding-3-large' => 3072,
        'text-embedding-ada-002' => 1536,
    ];

    private readonly Client $client;

    public function __construct(
        string|OpenAIConfiguration $config,
        private readonly string $model = 'text-embedding-3-small',
    ) {
        $apiKey = $config instanceof OpenAIConfiguration ? $config->apiKey : $config;
        $this->client = OpenAI::client($apiKey);
    }

    /**
     * @return list<float>
     */
    public function embed(string $text): array
    {
        try {
            $response = $this->client->embeddings()->create([
                'model' => $this->model,
                'input' => $text,
            ]);

            /** @var list<float> */
            return $response->embeddings[0]->embedding;
        } catch (\Throwable $e) {
            throw new EmbeddingException(
                sprintf('OpenAI embedding failed: %s', $e->getMessage()),
                previous: $e,
            );
        }
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

        try {
            $response = $this->client->embeddings()->create([
                'model' => $this->model,
                'input' => $texts,
            ]);

            return array_map(
                static fn ($embedding): array => $embedding->embedding,
                $response->embeddings,
            );
        } catch (\Throwable $e) {
            throw new EmbeddingException(
                sprintf('OpenAI batch embedding failed: %s', $e->getMessage()),
                previous: $e,
            );
        }
    }

    public function getDimensions(): int
    {
        return self::MODEL_DIMENSIONS[$this->model]
            ?? throw new EmbeddingException(sprintf('Unknown model dimensions for: %s', $this->model));
    }
}
