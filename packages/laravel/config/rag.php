<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Embedder Configuration
    |--------------------------------------------------------------------------
    |
    | Provider: openai, gemini, ollama, mistral
    |
    */
    'embedder' => [
        'provider' => env('RAG_EMBEDDER_PROVIDER', 'openai'),
        'model' => env('RAG_EMBEDDER_MODEL', 'text-embedding-3-small'),
        'api_key' => env('RAG_EMBEDDER_API_KEY', env('OPENAI_API_KEY')),
        'base_uri' => env('RAG_EMBEDDER_BASE_URI'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Vector Store Configuration
    |--------------------------------------------------------------------------
    |
    | Driver: pgvector, in_memory
    |
    */
    'vector_store' => [
        'driver' => env('RAG_STORE_DRIVER', 'pgvector'),
        'connection' => env('RAG_STORE_CONNECTION'),
        'table' => env('RAG_STORE_TABLE', 'rag_documents'),
        'dimensions' => (int) env('RAG_STORE_DIMENSIONS', 1536),
    ],

    /*
    |--------------------------------------------------------------------------
    | Retriever Configuration
    |--------------------------------------------------------------------------
    */
    'retriever' => [
        'strategy' => env('RAG_RETRIEVER_STRATEGY', 'similarity'),
        'top_k' => (int) env('RAG_RETRIEVER_TOP_K', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | LLM Configuration
    |--------------------------------------------------------------------------
    |
    | Provider: openai, gemini, ollama, anthropic, mistral
    |
    */
    'llm' => [
        'provider' => env('RAG_LLM_PROVIDER', 'openai'),
        'model' => env('RAG_LLM_MODEL', 'gpt-4o-mini'),
        'api_key' => env('RAG_LLM_API_KEY', env('OPENAI_API_KEY')),
        'base_uri' => env('RAG_LLM_BASE_URI'),
        'temperature' => (float) env('RAG_LLM_TEMPERATURE', 0.2),
        'max_tokens' => (int) env('RAG_LLM_MAX_TOKENS', 2048),
    ],

    /*
    |--------------------------------------------------------------------------
    | Prompt Template
    |--------------------------------------------------------------------------
    */
    'prompt' => [
        'template' => env('RAG_PROMPT_TEMPLATE'),
    ],
];
