<?php

declare(strict_types=1);

namespace RagPhp\Core\Pipeline;

use RagPhp\Core\ValueObject\Document;

final readonly class PromptTemplate
{
    private const string DEFAULT_TEMPLATE = <<<'PROMPT'
        Use the following context to answer the question. If you cannot find the answer in the context, say so clearly.

        Context:
        {context}

        Question: {question}

        Answer:
        PROMPT;

    public function __construct(
        private string $template = self::DEFAULT_TEMPLATE,
    ) {
    }

    /**
     * Render the prompt by injecting the query and context documents.
     *
     * @param list<Document> $documents
     */
    public function render(string $query, array $documents): string
    {
        $context = implode("\n\n---\n\n", array_map(
            static fn (Document $doc): string => $doc->content,
            $documents,
        ));

        return str_replace(
            ['{question}', '{context}'],
            [$query, $context],
            $this->template,
        );
    }
}
