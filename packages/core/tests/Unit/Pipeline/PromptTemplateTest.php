<?php

declare(strict_types=1);

namespace RagPhp\Core\Tests\Unit\Pipeline;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RagPhp\Core\Pipeline\PromptTemplate;
use RagPhp\Core\ValueObject\Document;

#[CoversClass(PromptTemplate::class)]
final class PromptTemplateTest extends TestCase
{
    #[Test]
    public function it_renders_default_template(): void
    {
        $template = new PromptTemplate();
        $docs = [
            new Document('Context paragraph 1'),
            new Document('Context paragraph 2'),
        ];

        $result = $template->render('What is RAG?', $docs);

        self::assertStringContainsString('What is RAG?', $result);
        self::assertStringContainsString('Context paragraph 1', $result);
        self::assertStringContainsString('Context paragraph 2', $result);
    }

    #[Test]
    public function it_renders_custom_template(): void
    {
        $template = new PromptTemplate('Q: {question} | C: {context}');
        $docs = [new Document('the context')];

        $result = $template->render('the question', $docs);

        self::assertSame('Q: the question | C: the context', $result);
    }
}
