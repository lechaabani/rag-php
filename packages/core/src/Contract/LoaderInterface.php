<?php

declare(strict_types=1);

namespace RagPhp\Core\Contract;

use RagPhp\Core\ValueObject\Document;

interface LoaderInterface
{
    /**
     * Load documents from a source.
     *
     * @return iterable<Document>
     */
    public function load(): iterable;
}
