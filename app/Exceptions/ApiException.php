<?php

namespace App\Exceptions;

use App\Exceptions\Contracts\ApiExceptionInterface;
use App\Support\ApiRequest;
use Exception;
use Illuminate\Http\Client\Response;
use Throwable;

class ApiException extends Exception implements ApiExceptionInterface
{
    public function __construct(
        public readonly ?ApiRequest $request = null,
        public readonly ?Response $response = null,
        Throwable $previous = null,
    ) {
        // Typically, we will just pass in the message from the previous exception, but provide a default if for some reason we threw this exception without a previous one.
        $message = $previous?->getMessage() ?: 'An error occurred making an API request.';

        parent::__construct(
            message: $message,
            code: $previous?->getCode(),
            previous: $previous,
        );
    }

    public function context(): array
    {
        return [
            'uri' => $this->request?->getUri(),
            'method' => $this->request?->getMethod(),
        ];
    }
}
