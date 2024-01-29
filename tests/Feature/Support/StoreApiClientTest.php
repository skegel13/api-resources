<?php

use App\Exceptions\StoreApiException;
use App\Support\ApiRequest;
use App\Support\StoreApiClient;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Http::fake();
    config([
        'services.store_api.url' => 'https://example.com',
    ]);
});

it('sets the base url', function () {
    $request = ApiRequest::get('products');

    app(StoreApiClient::class)->send($request);

    Http::assertSent(static function (Request $request) {
        expect($request)->url()->toStartWith('https://example.com/products');

        return true;
    });
});

it('throws a StoreApiException', function () {
    // Arrange
    Http::resetStubs();
    Http::fakeSequence()->pushStatus(404);
    $request = ApiRequest::get('products');

    // Act
    app(StoreApiClient::class)->send($request);
})->throws(StoreApiException::class, exceptionCode: 404);
