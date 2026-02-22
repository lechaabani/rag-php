---
sidebar_position: 1
---

# Symfony Integration

The `rag-php/symfony` bundle provides full DI integration, YAML configuration, and auto-wiring.

## Installation

```bash
composer require rag-php/symfony rag-php/openai rag-php/pgvector
```

## Configuration

```yaml
# config/packages/rag.yaml
rag:
    embedder:
        provider: openai
        model: text-embedding-3-small
        api_key: '%env(OPENAI_API_KEY)%'

    vector_store:
        driver: pgvector
        table: rag_documents
        dimensions: 1536

    retriever:
        strategy: similarity
        top_k: 5

    llm:
        provider: openai
        model: gpt-4o-mini
        api_key: '%env(OPENAI_API_KEY)%'
        temperature: 0.2
```

## Usage in Controllers

```php
use RagPhp\Core\Pipeline\RagPipeline;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class RagController extends AbstractController
{
    #[Route('/api/query', methods: ['POST'])]
    public function query(Request $request, RagPipeline $pipeline): JsonResponse
    {
        $question = $request->getPayload()->getString('question');
        $response = $pipeline->query($question);

        return $this->json([
            'answer' => $response->getAnswer(),
            'sources' => count($response->getSources()),
            'tokens' => $response->getTokensUsed(),
        ]);
    }
}
```

## Available Services

All components are auto-wired:

| Service | Interface |
|---------|-----------|
| `rag.embedder` | `EmbedderInterface` |
| `rag.vector_store` | `VectorStoreInterface` |
| `rag.retriever` | `RetrieverInterface` |
| `rag.llm` | `LlmInterface` |
| `rag.pipeline` | `RagPipeline` |

## Switching Providers

Just change the YAML config — no code changes needed:

```yaml
# Switch to Gemini
rag:
    embedder:
        provider: gemini
        api_key: '%env(GEMINI_API_KEY)%'
    llm:
        provider: gemini
        model: gemini-2.0-flash
        api_key: '%env(GEMINI_API_KEY)%'
```
