<?php

declare(strict_types=1);

namespace RagPhp\Ollama;

use RagPhp\Core\Contract\LlmInterface;
use RagPhp\Core\Exception\RagException;
use RagPhp\Core\ValueObject\Document;
use RagPhp\Core\ValueObject\Response;

final class OllamaLlm implements LlmInterface
{
    private readonly string $baseUri;

    public function __construct(
        string|OllamaConfiguration|null $config = null,
        private readonly string $model = 'llama3',
        private readonly float $temperature = 0.2,
    ) {
        $this->baseUri = match (true) {
            $config instanceof OllamaConfiguration => $config->baseUri,
            is_string($config) => $config,
            default => 'http://localhost:11434',
        };
    }

    /**
     * @param list<Document> $context
     */
    public function complete(string $prompt, array $context): Response
    {
        $result = $this->request('/api/chat', [
            'model' => $this->model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'stream' => false,
            'options' => [
                'temperature' => $this->temperature,
            ],
        ]);

        $answer = $result['message']['content'] ?? '';
        $tokensUsed = ($result['eval_count'] ?? 0) + ($result['prompt_eval_count'] ?? 0);

        return new Response($answer, $context, $tokensUsed);
    }

    /**
     * @param list<Document> $context
     * @return \Generator<int, string, mixed, void>
     */
    public function stream(string $prompt, array $context): \Generator
    {
        $url = rtrim($this->baseUri, '/') . '/api/chat';

        $payload = json_encode([
            'model' => $this->model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'stream' => true,
            'options' => [
                'temperature' => $this->temperature,
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
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_WRITEFUNCTION => static function ($ch, string $data) use (&$buffer): int {
                $buffer .= $data;

                return strlen($data);
            },
        ]);

        curl_exec($ch);
        curl_close($ch);

        foreach (explode("\n", $buffer) as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            $data = json_decode($line, true);

            if (!is_array($data)) {
                continue;
            }

            if ($data['done'] ?? false) {
                break;
            }

            $text = $data['message']['content'] ?? '';

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
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 300,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!is_string($response) || $httpCode !== 200) {
            throw new RagException(
                sprintf('Ollama API error (HTTP %d): %s', $httpCode, is_string($response) ? $response : 'No response'),
            );
        }

        /** @var array<string, mixed> */
        return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    }
}
