---
sidebar_position: 2
---

# Laravel Integration

The `rag-php/laravel` package provides a ServiceProvider, Facade, config file, and Artisan commands.

## Installation

```bash
composer require rag-php/laravel rag-php/openai rag-php/pgvector

# Publish config
php artisan vendor:publish --tag=rag-config
```

## Configuration

```php
// config/rag.php — or use .env variables
RAG_LLM_PROVIDER=openai
RAG_LLM_MODEL=gpt-4o-mini
OPENAI_API_KEY=sk-...
RAG_STORE_DRIVER=pgvector
```

## Usage with Facade

```php
use RagPhp\Laravel\Facades\Rag;
use RagPhp\Core\ValueObject\Document;

// Index
Rag::index([
    new Document('Returns accepted within 30 days.'),
]);

// Query
$response = Rag::query('How do I return a product?');
echo $response->getAnswer();
```

## Dependency Injection

```php
use RagPhp\Core\Pipeline\RagPipeline;

class SearchController extends Controller
{
    public function query(Request $request, RagPipeline $pipeline)
    {
        $response = $pipeline->query($request->input('question'));

        return response()->json([
            'answer' => $response->getAnswer(),
            'tokens' => $response->getTokensUsed(),
        ]);
    }
}
```

## Artisan Commands

```bash
# Index documents
php artisan rag:index ./storage/docs --format=md

# Interactive query
php artisan rag:query "What is your return policy?"

# Pipeline stats
php artisan rag:stats
```

## Switching Providers

Just update `.env`:

```env
# Switch to Ollama (free, local)
RAG_EMBEDDER_PROVIDER=ollama
RAG_EMBEDDER_MODEL=nomic-embed-text
RAG_LLM_PROVIDER=ollama
RAG_LLM_MODEL=llama3
```
