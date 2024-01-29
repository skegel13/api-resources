<?php

namespace App\Support;

use App\Exceptions\ApiException;
use App\Exceptions\Contracts\ApiExceptionInterface;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * The ApiClient class is an abstract base class for making HTTP requests to an
 * API.
 * It provides a method for sending an ApiRequest and methods for getting and
 * authorizing a base request.
 * Subclasses must implement the baseUrl method to specify the base URL for the
 * API.
 */
abstract class ApiClient
{
    protected string $exceptionClass = ApiException::class;

    /**
     * Send an ApiRequest to the API and return the response.
     * @throws ApiException
     * @throws Exception
     */
    public function send(ApiRequest $request): Response
    {
        try {
            return $this->getBaseRequest()
                ->withHeaders($request->getHeaders())
                ->{$request->getMethod()->value}(
                    $request->getUri(),
                    $request->getMethod() === HttpMethod::GET
                        ? $request->getQuery()
                        : $request->getBody()
                );
        } catch (Exception $exception) {
            if (! is_subclass_of($this->exceptionClass, ApiExceptionInterface::class)) {
                // If the exceptionClass does not implement the ApiExceptionInterface,
                // let's just throw the caught exception since we don't know how to instantiate
                // the exceptionClass.
                throw $exception;
            }

            // Create our new exception and throw it.
            throw new $this->exceptionClass(
                request: $request,
                response: $exception?->response,
                previous: $exception,
            );
        }
    }

    /**
     * Get a base request for the API.
     * This method has some helpful defaults for API requests.
     * The base request is a PendingRequest with JSON acceptance, a content type
     * of 'application/json', and the base URL for the API.
     * It also throws exceptions for non-successful responses.
     */
    protected function getBaseRequest(): PendingRequest
    {
        $request = Http::acceptJson()
            ->contentType('application/json')
            ->throw()
            ->baseUrl($this->baseUrl());

        return $this->authorize($request);
    }

    /**
     * Authorize a request for the API.
     * This method is intended to be overridden by subclasses to provide
     * API-specific authorization.
     * By default, it simply returns the given request.
     */
    protected function authorize(PendingRequest $request): PendingRequest
    {
        return $request;
    }

    /**
     * Get the base URL for the API.
     * This method must be implemented by subclasses to provide the base URL for
     * the API.
     */
    abstract protected function baseUrl(): string;
}
