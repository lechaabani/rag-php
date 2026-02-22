<?php

declare(strict_types=1);

namespace RagPhp\Core\Tests\Unit\Store;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RagPhp\Core\Store\InMemoryVectorStore;
use RagPhp\Core\ValueObject\Document;

#[CoversClass(InMemoryVectorStore::class)]
final class InMemoryVectorStoreTest extends TestCase
{
    #[Test]
    public function it_stores_and_retrieves_documents(): void
    {
        $store = new InMemoryVectorStore();

        $doc = new Document('Hello world');
        $id = $store->store($doc, [1.0, 0.0, 0.0]);

        self::assertSame('1', $id);
        self::assertSame(1, $store->count());
    }

    #[Test]
    public function it_uses_document_id_if_provided(): void
    {
        $store = new InMemoryVectorStore();

        $doc = new Document('content', 'custom-id');
        $id = $store->store($doc, [1.0, 0.0]);

        self::assertSame('custom-id', $id);
    }

    #[Test]
    public function it_searches_by_similarity(): void
    {
        $store = new InMemoryVectorStore();

        $store->store(new Document('doc about cats'), [1.0, 0.0, 0.0]);
        $store->store(new Document('doc about dogs'), [0.9, 0.1, 0.0]);
        $store->store(new Document('doc about cars'), [0.0, 0.0, 1.0]);

        $results = $store->search([1.0, 0.0, 0.0], topK: 2);

        self::assertCount(2, $results);
        self::assertSame('doc about cats', $results[0]->content);
        self::assertSame('doc about dogs', $results[1]->content);
    }

    #[Test]
    public function it_filters_by_metadata(): void
    {
        $store = new InMemoryVectorStore();

        $store->store(new Document('doc A'), [1.0, 0.0], ['category' => 'tech']);
        $store->store(new Document('doc B'), [0.9, 0.1], ['category' => 'sports']);

        $results = $store->search([1.0, 0.0], topK: 10, filters: ['category' => 'sports']);

        self::assertCount(1, $results);
        self::assertSame('doc B', $results[0]->content);
    }

    #[Test]
    public function it_deletes_a_document(): void
    {
        $store = new InMemoryVectorStore();

        $id = $store->store(new Document('doc'), [1.0]);
        self::assertSame(1, $store->count());

        $store->delete($id);
        self::assertSame(0, $store->count());
    }

    #[Test]
    public function it_clears_all_documents(): void
    {
        $store = new InMemoryVectorStore();

        $store->store(new Document('a'), [1.0]);
        $store->store(new Document('b'), [0.5]);
        self::assertSame(2, $store->count());

        $store->clear();
        self::assertSame(0, $store->count());
    }
}
