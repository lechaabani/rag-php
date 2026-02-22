<?php

declare(strict_types=1);

namespace RagPhp\SymfonyBundle;

use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class RagPhpBundle extends AbstractBundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
