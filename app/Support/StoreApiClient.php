<?php

namespace App\Support;

use App\Exceptions\StoreApiException;

class StoreApiClient extends ApiClient
{
    protected string $exceptionClass = StoreApiException::class;

    protected function baseUrl(): string
    {
        return config('services.store_api.url');
    }
}
