<?php

declare(strict_types=1);

namespace RagPhp\Core\Contract;

interface EmbedderInterface
{
    /**
     * Embed a single text into a vector of floats.
     *
     * @return list<float>
     */
    public function embed(string $text): array;

    /**
     * Embed multiple texts in a single batch call for efficiency.
     *
     * @param list<string> $texts
     * @return list<list<float>>
     */
    public function embedBatch(array $texts): array;

    /**
     * Return the dimensionality of the embedding model.
     */
    public function getDimensions(): int;
}
