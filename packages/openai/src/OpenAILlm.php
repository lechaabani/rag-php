<?php

declare(strict_types=1);

namespace RagPhp\OpenAI;

use OpenAI;
use OpenAI\Client;
use RagPhp\Core\Contract\LlmInterface;
use RagPhp\Core\Exception\RagException;
use RagPhp\Core\ValueObject\Document;
use RagPhp\Core\ValueObject\Response;

final class OpenAILlm implements LlmInterface
{
    private readonly Client $client;

    public function __construct(
        string|OpenAIConfiguration $config,
        private readonly string $model = 'gpt-4o',
        private readonly float $temperature = 0.2,
        private readonly int $maxTokens = 2048,
    ) {
        $apiKey = $config instanceof OpenAIConfiguration ? $config->apiKey : $config;
        $this->client = OpenAI::client($apiKey);
    }

    /**
     * @param list<Document> $context
     */
    public function complete(string $prompt, array $context): Response
    {
        try {
            $result = $this->client->chat()->create([
                'model' => $this->model,
                'temperature' => $this->temperature,
                'max_tokens' => $this->maxTokens,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            $answer = $result->choices[0]->message->content ?? '';
            $tokensUsed = $result->usage->totalTokens ?? 0;

            return new Response($answer, $context, $tokensUsed);
        } catch (\Throwable $e) {
            throw new RagException(
                sprintf('OpenAI completion failed: %s', $e->getMessage()),
                previous: $e,
            );
        }
    }

    /**
     * @param list<Document> $context
     * @return \Generator<int, string, mixed, void>
     */
    public function stream(string $prompt, array $context): \Generator
    {
        try {
            $stream = $this->client->chat()->createStreamed([
                'model' => $this->model,
                'temperature' => $this->temperature,
                'max_tokens' => $this->maxTokens,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            foreach ($stream as $response) {
                $delta = $response->choices[0]->delta->content ?? '';

                if ($delta !== '') {
                    yield $delta;
                }
            }
        } catch (\Throwable $e) {
            throw new RagException(
                sprintf('OpenAI streaming failed: %s', $e->getMessage()),
                previous: $e,
            );
        }
    }
}
