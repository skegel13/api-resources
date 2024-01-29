<?php

use App\Exceptions\ApiException;
use App\Support\ApiRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;

it('sets default message and code', function () {
    // Act
    $apiException = new ApiException();

    // Assert
    expect($apiException)
        ->getMessage()->toBe('An error occurred making an API request.')
        ->getCode()->toBe(0);
});

it('sets context based on request', function () {
    // Arrange
    $request = ApiRequest::get(fake()->url);

    // Act
    $apiException = new ApiException($request);

    // Assert
    expect($apiException)->context()->toBe([
        'uri' => $request->getUri(),
        'method' => $request->getMethod(),
    ]);
});

it('gets response from RequestException', function () {
    // Arrange
    $requestException = new RequestException(
        new Response(
            new GuzzleHttp\Psr7\Response(
                422,
                [],
                json_encode(['message' => 'Something went wrong.']),
            ),
        )
    );

    // Act
    $apiException = new ApiException(response: $requestException->response, previous: $requestException);

    // Assert
    expect($apiException->getCode())->toBe(422)
        ->and($apiException->response)->toBeInstanceOf(Response::class)
        ->and($apiException->response->json('message'))->toBe('Something went wrong.');
});
