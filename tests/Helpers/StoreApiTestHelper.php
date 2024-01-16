<?php

namespace Tests\Helpers;

trait StoreApiTestHelper
{
    private function getFakeProduct(array $data = []): array {
        return [
            'id' => data_get($data, 'id', fake()->numberBetween(1, 1000)),
            'title' => data_get($data, 'title', fake()->text()),
            'price' => data_get($data, 'price', fake()->randomFloat(2, 0, 100)),
            'description' => data_get($data, 'description', fake()->paragraph()),
            'category' => data_get($data, 'category', fake()->text()),
            'image' => data_get($data, 'image', fake()->url()),
            'rating' => data_get($data, 'rating', [
                'rate' => fake()->randomFloat(2, 0, 5),
                'count' => fake()->numberBetween(1, 1000),
            ]),
        ];
    }
}
