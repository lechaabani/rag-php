<?php

declare(strict_types=1);

namespace RagPhp\Core\ValueObject;

final readonly class Vector
{
    public int $dimensions;

    /**
     * @param list<float> $values
     */
    public function __construct(
        public array $values,
    ) {
        $this->dimensions = count($values);
    }

    /**
     * Compute cosine similarity between this vector and another.
     */
    public function cosineSimilarity(self $other): float
    {
        if ($this->dimensions !== $other->dimensions) {
            throw new \InvalidArgumentException(
                sprintf('Vector dimensions mismatch: %d vs %d', $this->dimensions, $other->dimensions)
            );
        }

        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        for ($i = 0; $i < $this->dimensions; $i++) {
            $dotProduct += $this->values[$i] * $other->values[$i];
            $normA += $this->values[$i] ** 2;
            $normB += $other->values[$i] ** 2;
        }

        $denominator = sqrt($normA) * sqrt($normB);

        if ($denominator === 0.0) {
            return 0.0;
        }

        return $dotProduct / $denominator;
    }
}
