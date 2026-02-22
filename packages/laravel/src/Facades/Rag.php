<?php

declare(strict_types=1);

namespace RagPhp\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use RagPhp\Core\Pipeline\RagPipeline;
use RagPhp\Core\ValueObject\Document;
use RagPhp\Core\ValueObject\Response;

/**
 * @method static void index(array $documents)
 * @method static Response query(string $query, int $topK = 5)
 *
 * @see RagPipeline
 */
final class Rag extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return RagPipeline::class;
    }
}
