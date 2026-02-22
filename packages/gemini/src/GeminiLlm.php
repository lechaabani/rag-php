<?php

declare(strict_types=1);

namespace RagPhp\Gemini;

use RagPhp\Core\Contract\LlmInterface;
use RagPhp\Core\Exception\RagException;
use RagPhp\Core\ValueObject\Document;
use RagPhp\Core\ValueObject\Response;

final class GeminiLlm implements LlmInterface
{
    private readonly string $apiKey;
    private readonly string $baseUri;

    public function __construct(
        string|GeminiConfiguration $config,
        private readonly string $model = 'gemini-2.0-flash',
        private readonly float $temperature = 0.2,
        private readonly int $maxTokens = 2048,
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
     * @param list<Document> $context
     */
    public function complete(string $prompt, array $context): Response
    {
        $url = sprintf(
            '%s/models/%s:generateContent?key=%s',
            $this->baseUri,
            $this->model,
            $this->apiKey,
        );

        $payload = [
            'contents' => [
                ['parts' => [['text' => $prompt]]],
            ],
            'generationConfig' => [
                'temperature' => $this->temperature,
                'maxOutputTokens' => $this->maxTokens,
            ],
        ];

        $result = $this->request($url, $payload);

        $answer = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
        $tokensUsed = ($result['usageMetadata']['totalTokenCount'] ?? 0);

        return new Response($answer, $context, $tokensUsed);
    }

    /**
     * @param list<Document> $context
     * @return \Generator<int, string, mixed, void>
     */
    public function stream(string $prompt, array $context): \Generator
    {
        $url = sprintf(
            '%s/models/%s:streamGenerateContent?key=%s&alt=sse',
            $this->baseUri,
            $this->model,
            $this->apiKey,
        );

        $payload = [
            'contents' => [
                ['parts' => [['text' => $prompt]]],
            ],
            'generationConfig' => [
                'temperature' => $this->temperature,
                'maxOutputTokens' => $this->maxTokens,
            ],
        ];

        $ch = curl_init($url);

        if ($ch === false) {
            throw new RagException('Failed to initialize cURL');
        }

        $buffer = '';

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_THROW_ON_ERROR),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
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
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

            if ($text !== '') {
                yield $text;
            }
        }
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function request(string $url, array $payload): array
    {
        $ch = curl_init($url);

        if ($ch === false) {
            throw new RagException('Failed to initialize cURL');
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
            throw new RagException(
                sprintf('Gemini API error (HTTP %d): %s', $httpCode, is_string($response) ? $response : 'No response'),
            );
        }

        /** @var array<string, mixed> */
        return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    }
}
