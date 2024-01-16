<?php

namespace App\Data;

use App\Enums\SortDirection;
use Spatie\LaravelData\Data;

class ListProductsData extends Data
{
    public function __construct(
        public readonly ?int $limit = null,
        public readonly ?SortDirection $sort = null,
    ) {}

    public function toArray(): array
    {
        return collect(parent::toArray())
            ->filter()
            ->toArray();
    }
}
