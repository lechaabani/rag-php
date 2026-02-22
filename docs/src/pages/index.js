import React from 'react';
import clsx from 'clsx';
import Link from '@docusaurus/Link';
import useDocusaurusContext from '@docusaurus/useDocusaurusContext';
import Layout from '@theme/Layout';
import CodeBlock from '@theme/CodeBlock';

const features = [
  {
    title: 'Modular Architecture',
    description: 'Clean interfaces with swappable components. Use OpenAI, Ollama, Mistral, or bring your own provider.',
    icon: '🏗️',
  },
  {
    title: 'Framework Agnostic',
    description: 'Works standalone or with official bridges for Symfony and Laravel. No vendor lock-in.',
    icon: '🔌',
  },
  {
    title: 'Production Ready',
    description: 'PHPStan level 9, 95%+ test coverage, PSR-12 compliant. Built for real-world PHP applications.',
    icon: '✅',
  },
  {
    title: 'Multiple Vector Stores',
    description: 'PostgreSQL + pgvector, Qdrant, Pinecone, or in-memory for testing. Choose what fits your stack.',
    icon: '🗄️',
  },
  {
    title: 'Smart Retrieval',
    description: 'Similarity search, MMR, HyDE, BM25, and hybrid strategies to find the most relevant documents.',
    icon: '🔍',
  },
  {
    title: 'Event-Driven Pipeline',
    description: 'Hook into every stage of the RAG pipeline with events. Extend without modifying the core.',
    icon: '⚡',
  },
];

const quickStart = `composer require rag-php/core rag-php/openai rag-php/pgvector`;

const example = `<?php

use RagPhp\\Core\\Pipeline\\RagPipeline;
use RagPhp\\Core\\Retriever\\SimilarityRetriever;
use RagPhp\\OpenAI\\OpenAIEmbedder;
use RagPhp\\OpenAI\\OpenAILlm;
use RagPhp\\PgVector\\PgVectorStore;

$rag = RagPipeline::create()
    ->withEmbedder(new OpenAIEmbedder(\$apiKey))
    ->withVectorStore(new PgVectorStore(\$connection))
    ->withRetriever(new SimilarityRetriever(topK: 5))
    ->withLlm(new OpenAILlm(\$apiKey, model: 'gpt-4o'))
    ->build();

$response = $rag->query('How do I return a product?');
echo $response->getAnswer();`;

function Feature({ title, description, icon }) {
  return (
    <div className={clsx('col col--4')}>
      <div className="feature-card">
        <div style={{ fontSize: '3rem' }}>{icon}</div>
        <h3>{title}</h3>
        <p>{description}</p>
      </div>
    </div>
  );
}

export default function Home() {
  const { siteConfig } = useDocusaurusContext();
  return (
    <Layout title="Home" description={siteConfig.tagline}>
      <header className={clsx('hero hero--primary')}>
        <div className="container">
          <h1 className="hero__title">{siteConfig.title}</h1>
          <p className="hero__subtitle">{siteConfig.tagline}</p>
          <div style={{ marginTop: '2rem' }}>
            <Link
              className="button button--secondary button--lg"
              to="/docs/getting-started"
              style={{ marginRight: '1rem' }}
            >
              Get Started
            </Link>
            <Link
              className="button button--outline button--lg"
              to="https://github.com/rag-php/rag-php"
              style={{ color: 'white', borderColor: 'white' }}
            >
              GitHub
            </Link>
          </div>
        </div>
      </header>

      <main>
        <section className="features-section">
          <div className="container">
            <div className="row">
              {features.map((props, idx) => (
                <Feature key={idx} {...props} />
              ))}
            </div>
          </div>
        </section>

        <section style={{ padding: '4rem 0', background: 'var(--ifm-background-surface-color)' }}>
          <div className="container">
            <h2 style={{ textAlign: 'center', marginBottom: '2rem' }}>Quick Start</h2>
            <CodeBlock language="bash">{quickStart}</CodeBlock>
            <div style={{ marginTop: '2rem' }}>
              <CodeBlock language="php" title="Your first RAG query">{example}</CodeBlock>
            </div>
          </div>
        </section>
      </main>
    </Layout>
  );
}
