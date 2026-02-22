---
sidebar_position: 3
---

# LlmInterface

The `LlmInterface` handles text generation using Large Language Models.

## Interface

```php
namespace RagPhp\Core\Contract;

interface LlmInterface
{
    public function complete(string $prompt, array $context): Response;
    public function stream(string $prompt, array $context): \Generator;
}
```

## Methods

### `complete()`

Generates a full response from a prompt with context documents.

```php
$response = $llm->complete($prompt, $contextDocuments);

echo $response->getAnswer();      // The generated text
echo $response->getTokensUsed();  // Token usage
$response->getSources();           // Source documents
```

### `stream()`

Streams the response as a PHP Generator, yielding text chunks as they arrive.

```php
foreach ($llm->stream($prompt, $context) as $chunk) {
    echo $chunk; // Print each chunk as it arrives
    flush();
}
```

## Available Implementations

| Implementation | Package | Models |
|---------------|---------|--------|
| `OpenAILlm` | `rag-php/openai` | GPT-4o, GPT-4o-mini, GPT-3.5-turbo |
| `OllamaLlm` | `rag-php/ollama` | Llama 3, Mistral, Gemma (coming soon) |
