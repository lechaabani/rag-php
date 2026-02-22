<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__ . '/packages/core/src',
        __DIR__ . '/packages/core/tests',
        __DIR__ . '/packages/openai/src',
        __DIR__ . '/packages/openai/tests',
        __DIR__ . '/packages/pgvector/src',
        __DIR__ . '/packages/pgvector/tests',
        __DIR__ . '/packages/loaders/src',
        __DIR__ . '/packages/loaders/tests',
    ]);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        'strict_param' => true,
        'declare_strict_types' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'single_quote' => true,
        'trailing_comma_in_multiline' => true,
        'no_whitespace_in_blank_line' => true,
        'blank_line_before_statement' => ['statements' => ['return', 'throw']],
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true);
