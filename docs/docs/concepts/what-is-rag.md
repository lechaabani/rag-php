---
sidebar_position: 1
---

# What is RAG?

**Retrieval-Augmented Generation (RAG)** is a technique that enhances Large Language Model (LLM) responses by grounding them in relevant, retrieved information from your own data.

## The Problem

LLMs have two key limitations:

1. **Knowledge cutoff** — They only know what they were trained on.
2. **Hallucination** — They can generate plausible-sounding but incorrect information.

## The Solution

RAG solves both problems by:

1. **Retrieving** relevant documents from your knowledge base
2. **Augmenting** the LLM prompt with this context
3. **Generating** a response that's grounded in your actual data

## How It Works

```
User Question
     │
     ▼
┌─────────────┐     ┌──────────────┐     ┌─────────────┐
│  Embed the  │────▶│  Search for  │────▶│ Build prompt│
│   question  │     │  similar docs│     │ with context│
└─────────────┘     └──────────────┘     └──────┬──────┘
                                                │
                                                ▼
                                         ┌─────────────┐
                                         │  LLM generates│
                                         │   answer     │
                                         └─────────────┘
```

### Step by Step

1. **Embed the question** — Convert the user's question into a vector using an embedding model
2. **Search** — Find the most similar documents in your vector store
3. **Build prompt** — Construct a prompt that includes the retrieved context
4. **Generate** — Send the prompt to an LLM, which generates an answer grounded in the context

## Why RAG-PHP?

RAG-PHP provides all these components as clean, swappable PHP interfaces. You can use OpenAI, Ollama (free, local), or any other provider — and switch between them with a single line change.
