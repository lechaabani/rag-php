<?php

declare(strict_types=1);

namespace RagPhp\Core\Tests\Unit\ValueObject;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RagPhp\Core\ValueObject\Vector;

#[CoversClass(Vector::class)]
final class VectorTest extends TestCase
{
    #[Test]
    public function it_stores_values_and_dimensions(): void
    {
        $vector = new Vector([1.0, 2.0, 3.0]);

        self::assertSame([1.0, 2.0, 3.0], $vector->values);
        self::assertSame(3, $vector->dimensions);
    }

    #[Test]
    public function cosine_similarity_of_identical_vectors_is_one(): void
    {
        $a = new Vector([1.0, 0.0, 0.0]);
        $b = new Vector([1.0, 0.0, 0.0]);

        self::assertEqualsWithDelta(1.0, $a->cosineSimilarity($b), 0.0001);
    }

    #[Test]
    public function cosine_similarity_of_orthogonal_vectors_is_zero(): void
    {
        $a = new Vector([1.0, 0.0]);
        $b = new Vector([0.0, 1.0]);

        self::assertEqualsWithDelta(0.0, $a->cosineSimilarity($b), 0.0001);
    }

    #[Test]
    public function cosine_similarity_of_opposite_vectors_is_negative_one(): void
    {
        $a = new Vector([1.0, 0.0]);
        $b = new Vector([-1.0, 0.0]);

        self::assertEqualsWithDelta(-1.0, $a->cosineSimilarity($b), 0.0001);
    }

    #[Test]
    public function cosine_similarity_throws_on_dimension_mismatch(): void
    {
        $a = new Vector([1.0, 2.0]);
        $b = new Vector([1.0, 2.0, 3.0]);

        $this->expectException(\InvalidArgumentException::class);
        $a->cosineSimilarity($b);
    }

    #[Test]
    public function cosine_similarity_of_zero_vectors_is_zero(): void
    {
        $a = new Vector([0.0, 0.0]);
        $b = new Vector([0.0, 0.0]);

        self::assertEqualsWithDelta(0.0, $a->cosineSimilarity($b), 0.0001);
    }
}
