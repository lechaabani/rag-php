<?php

declare(strict_types=1);

namespace RagPhp\Core\Event;

use RagPhp\Core\ValueObject\Response;

final class ResponseGeneratedEvent
{
    public function __construct(
        private readonly Response $response,
    ) {
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}
