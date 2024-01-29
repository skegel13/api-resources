<?php

namespace App\Exceptions\Contracts;

use App\Support\ApiRequest;
use Illuminate\Http\Client\Response;
use Throwable;

interface ApiExceptionInterface
{
    public function __construct(
        ?ApiRequest $request = null,
        ?Response $response = null,
        Throwable $previous = null,
    );
}
