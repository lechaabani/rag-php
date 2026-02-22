<?php

declare(strict_types=1);

namespace RagPhp\Anthropic;

use RagPhp\Core\Contract\LlmInterface;
use RagPhp\Core\Exception\RagException;
use RagPhp\Core\ValueObject\Document;
use RagPhp\Core\ValueObject\Response;

final class AnthropicLlm implements LlmInterface
{
    private const string API_VERSION = '2023-06-01';

    private readonly string $apiKey;
    private readonly string $baseUri;

    public function __construct(
        string|AnthropicConfiguration $config,
        private readonly string $model = 'claude-sonnet-4-5-20250929',
        private readonly float $temperature = 0.2,
        private readonly int $maxTokens = 2048,
    ) {
        if ($config instanceof AnthropicConfiguration) {
            $this->apiKey = $config->apiKey;
            $this->baseUri = $config->baseUri;
        } else {
            $this->apiKey = $config;
            $this->baseUri = 'https://api.anthropic.com/v1';
        }
    }

    /**
     * @param list<Document> $context
     */
    public function complete(string $prompt, array $context): Response
    {
        $result = $this->request('/messages', [
            'model' => $this->model,
            'max_tokens' => $this->maxTokens,
            'temperature' => $this->temperature,
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        $answer = '';
        foreach ($result['content'] ?? [] as $block) {
            if (($block['type'] ?? '') === 'text') {
                $answer .= $block['text'];
            }
        }

        $inputTokens = $result['usage']['input_tokens'] ?? 0;
        $outputTokens = $result['usage']['output_tokens'] ?? 0;

        return new Response($answer, $context, $inputTokens + $outputTokens);
    }

    /**
     * @param list<Document> $context
     * @return \Generator<int, string, mixed, void>
     */
    public function stream(string $prompt, array $context): \Generator
    {
        $url = rtrim($this->baseUri, '/') . '/messages';

        $payload = json_encode([
            'model' => $this->model,
            'max_tokens' => $this->maxTokens,
            'temperature' => $this->temperature,
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
                'x-api-key: ' . $this->apiKey,
                'anthropic-version: ' . self::API_VERSION,
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
            $data = json_decode($json, true);

            if (!is_array($data)) {
                continue;
            }

            if (($data['type'] ?? '') === 'content_block_delta') {
                $text = $data['delta']['text'] ?? '';

                if ($text !== '') {
                    yield $text;
                }
            }

            if (($data['type'] ?? '') === 'message_stop') {
                break;
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
                'x-api-key: ' . $this->apiKey,
                'anthropic-version: ' . self::API_VERSION,
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!is_string($response) || $httpCode !== 200) {
            throw new RagException(
                sprintf('Anthropic API error (HTTP %d): %s', $httpCode, is_string($response) ? $response : 'No response'),
            );
        }

        /** @var array<string, mixed> */
        return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    }
}
