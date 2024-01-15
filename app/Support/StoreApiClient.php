<?php

namespace App\Support;

class StoreApiClient extends ApiClient
{

    protected function baseUrl(): string
    {
        return config('services.store_api.url');
    }
}
