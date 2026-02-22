<?php

declare(strict_types=1);

namespace RagPhp\Core\Tests\Unit\ValueObject;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RagPhp\Core\ValueObject\Document;
use RagPhp\Core\ValueObject\Response;

#[CoversClass(Response::class)]
final class ResponseTest extends TestCase
{
    #[Test]
    public function it_stores_answer_sources_and_tokens(): void
    {
        $sources = [new Document('source doc')];
        $response = new Response('The answer is 42', $sources, 150);

        self::assertSame('The answer is 42', $response->getAnswer());
        self::assertSame($sources, $response->getSources());
        self::assertSame(150, $response->getTokensUsed());
    }

    #[Test]
    public function it_defaults_to_empty_sources_and_zero_tokens(): void
    {
        $response = new Response('answer');

        self::assertSame([], $response->getSources());
        self::assertSame(0, $response->getTokensUsed());
    }
}
