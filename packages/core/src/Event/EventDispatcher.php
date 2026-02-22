<?php

declare(strict_types=1);

namespace RagPhp\Core\Event;

final class EventDispatcher
{
    /** @var array<class-string, list<callable>> */
    private array $listeners = [];

    /**
     * @template T of object
     * @param class-string<T> $eventClass
     * @param callable(T): void $listener
     */
    public function listen(string $eventClass, callable $listener): void
    {
        $this->listeners[$eventClass][] = $listener;
    }

    /**
     * @template T of object
     * @param T $event
     * @return T
     */
    public function dispatch(object $event): object
    {
        $eventClass = $event::class;

        foreach ($this->listeners[$eventClass] ?? [] as $listener) {
            $listener($event);
        }

        return $event;
    }
}
