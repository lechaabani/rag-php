# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Core interfaces: `EmbedderInterface`, `VectorStoreInterface`, `LlmInterface`, `RetrieverInterface`
- Value objects: `Document`, `Vector`, `Response`, `Chunk`
- `RagPipeline` with builder pattern
- `SimilarityRetriever` for cosine similarity retrieval
- `InMemoryVectorStore` for testing and development
- `OpenAIEmbedder` supporting text-embedding-3-small, text-embedding-3-large, text-embedding-ada-002
- `OpenAILlm` supporting GPT-4o, GPT-4o-mini, GPT-3.5-turbo
- `PgVectorStore` with PostgreSQL + pgvector
- Event system: `QueryReceivedEvent`, `DocumentsRetrievedEvent`, `PromptBuiltEvent`, `ResponseGeneratedEvent`
- `PromptTemplate` for customizable prompt rendering
- CI/CD with GitHub Actions
- Docusaurus documentation site
