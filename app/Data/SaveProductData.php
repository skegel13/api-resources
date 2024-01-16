<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class SaveProductData extends Data
{
    public function __construct(
        public string $title,
        public float $price,
        public string $description,
        public string $category,
        public string $image,
    ) {}
}
