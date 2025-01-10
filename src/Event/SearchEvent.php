<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class SearchEvent extends Event
{
    public function __construct(
        private string $query,
        private array $filters = []
    ) {
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }
}
