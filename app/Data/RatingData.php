<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class RatingData extends Data
{
    public function __construct(
        public float $rate,
        public int $count,
    ) {}
}
