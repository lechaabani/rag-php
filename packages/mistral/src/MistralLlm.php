<?php

declare(strict_types=1);

namespace RagPhp\Mistral;

use RagPhp\Core\Contract\LlmInterface;
use RagPhp\Core\Exception\RagException;
use RagPhp\Core\ValueObject\Document;
use RagPhp\Core\ValueObject\Response;

final class MistralLlm implements LlmInterface
{
    private readonly string $apiKey;
    private readonly string $baseUri;

    public function __construct(
        string|MistralConfiguration $config,
        private readonly string $model = 'mistral-large-latest',
        private readonly float $temperature = 0.2,
        private readonly int $maxTokens = 2048,
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
     * @param list<Document> $context
     */
    public function complete(string $prompt, array $context): Response
    {
        $result = $this->request('/chat/completions', [
            'model' => $this->model,
            'temperature' => $this->temperature,
            'max_tokens' => $this->maxTokens,
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        $answer = $result['choices'][0]['message']['content'] ?? '';
        $tokensUsed = $result['usage']['total_tokens'] ?? 0;

        return new Response($answer, $context, $tokensUsed);
    }

    /**
     * @param list<Document> $context
     * @return \Generator<int, string, mixed, void>
     */
    public function stream(string $prompt, array $context): \Generator
    {
        $url = rtrim($this->baseUri, '/') . '/chat/completions';

        $payload = json_encode([
            'model' => $this->model,
            'temperature' => $this->temperature,
            'max_tokens' => $this->maxTokens,
            'stream' => true,
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ], JSON_THROW_ON_ERROR);

        $ch = curl_init($url);

        if ($ch === false) {
            throw new RagException('Failed to initialize cURL');
        }

        $buffer = '';

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
            ],
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_WRITEFUNCTION => static function ($ch, string $data) use (&$buffer): int {
                $buffer .= $data;

                return strlen($data);
            },
        ]);

        curl_exec($ch);
        curl_close($ch);

        foreach (explode("\n", $buffer) as $line) {
            $line = trim($line);

            if (!str_starts_with($line, 'data: ')) {
                continue;
            }

            $json = substr($line, 6);

            if ($json === '[DONE]') {
                break;
            }

            $data = json_decode($json, true);

            if (!is_array($data)) {
                continue;
            }

            $text = $data['choices'][0]['delta']['content'] ?? '';

            if ($text !== '') {
                yield $text;
            }
        }
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
            throw new RagException('Failed to initialize cURL');
        }

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_THROW_ON_ERROR),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!is_string($response) || $httpCode !== 200) {
            throw new RagException(
                sprintf('Mistral API error (HTTP %d): %s', $httpCode, is_string($response) ? $response : 'No response'),
            );
        }

        /** @var array<string, mixed> */
        return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    }
}
