/** @type {import('@docusaurus/plugin-content-docs').SidebarsConfig} */
const sidebars = {
  docs: [
    'introduction',
    'getting-started',
    {
      type: 'category',
      label: 'Concepts',
      items: ['concepts/what-is-rag', 'concepts/architecture'],
    },
    {
      type: 'category',
      label: 'API Reference',
      items: [
        'api/embedder',
        'api/vector-store',
        'api/llm',
        'api/retriever',
      ],
    },
    {
      type: 'category',
      label: 'Providers',
      items: ['providers/openai'],
    },
    {
      type: 'category',
      label: 'Vector Stores',
      items: ['vector-stores/pgvector', 'vector-stores/in-memory'],
    },
    'pipeline',
    'events',
    {
      type: 'category',
      label: 'Frameworks',
      items: ['frameworks/symfony', 'frameworks/laravel'],
    },
    {
      type: 'category',
      label: 'Cookbook',
      items: [
        'cookbook/chatbot',
        'cookbook/search-engine',
        'cookbook/local-ollama',
        'cookbook/multi-provider',
      ],
    },
    'testing',
    'contributing',
  ],
};

module.exports = sidebars;
