<?php

use App\Exceptions\ApiException;
use App\Support\ApiClient;
use App\Support\ApiRequest;
use App\Support\HttpMethod;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Http::fake();

    $this->client = new class extends ApiClient {
        protected function baseUrl(): string
        {
            return 'https://example.com';
        }
    };
});

it('sends a get request', function () {
    $request = ApiRequest::get('foo')
        ->setHeaders(['X-Foo' => 'Bar'])
        ->setQuery(['baz' => 'qux']);

    $this->client->send($request);

    Http::assertSent(static function (Request $request) {
        expect($request)
            ->url()->toBe('https://example.com/foo?baz=qux')
            ->method()->toBe(HttpMethod::GET->name)
            ->header('X-Foo')->toBe(['Bar']);

        return true;
    });
});

it('sends a post request', function () {
    $request = ApiRequest::post('foo')
        ->setBody(['foo' => 'bar'])
        ->setHeaders(['X-Foo' => 'Bar'])
        ->setQuery(['baz' => 'qux']);

    $this->client->send($request);

    Http::assertSent(static function (Request $request) {
        expect($request)
            ->url()->toBe('https://example.com/foo?baz=qux')
            ->method()->toBe(HttpMethod::POST->name)
            ->data()->toBe(['foo' => 'bar'])
            ->header('X-Foo')->toBe(['Bar']);

        return true;
    });
});

it('sends a put request', function () {
    $request = ApiRequest::put('foo')
        ->setBody(['foo' => 'bar'])
        ->setHeaders(['X-Foo' => 'Bar'])
        ->setQuery(['baz' => 'qux']);

    $this->client->send($request);

    Http::assertSent(static function (Request $request) {
        expect($request)
            ->url()->toBe('https://example.com/foo?baz=qux')
            ->method()->toBe(HttpMethod::PUT->name)
            ->data()->toBe(['foo' => 'bar'])
            ->header('X-Foo')->toBe(['Bar']);

        return true;
    });
});

it('sends a delete request', function () {
    $request = ApiRequest::delete('foo')
        ->setBody(['foo' => 'bar'])
        ->setHeaders(['X-Foo' => 'Bar'])
        ->setQuery(['baz' => 'qux']);

    $this->client->send($request);

    Http::assertSent(static function (Request $request) {
        expect($request)
            ->url()->toBe('https://example.com/foo?baz=qux')
            ->method()->toBe(HttpMethod::DELETE->name)
            ->data()->toBe(['foo' => 'bar'])
            ->header('X-Foo')->toBe(['Bar']);

        return true;
    });
});

it('handles authorization', function () {
    $client = new class extends ApiClient {
        protected function baseUrl(): string
        {
            return 'https://example.com';
        }

        protected function authorize(PendingRequest $request): PendingRequest
        {
            return $request->withHeaders(['Authorization' => 'Bearer foo']);
        }
    };

    $request = ApiRequest::get('foo');

    $client->send($request);

    Http::assertSent(static function (Request $request) {
        expect($request)->header('Authorization')->toBe(['Bearer foo']);

        return true;
    });
});

it('throws an api exception', function () {
    // Arrange
    Http::resetStubs();
    Http::fakeSequence()->pushStatus(500);
    $request = ApiRequest::get('foo');

    // Act
    $this->client->send($request);
})->throws(ApiException::class, exceptionCode: 500);

it('throws request exception if client exception does not implement ApiExceptionInterface', function () {
    // Arrange
    Http::resetStubs();
    Http::fakeSequence()->pushStatus(500);
    $client = new class extends ApiClient {
        protected string $exceptionClass = Exception::class;

        protected function baseUrl(): string
        {
            return 'https://example.com';
        }
    };
    $request = ApiRequest::get('foo');

    // Act
    $client->send($request);
})->throws(RequestException::class, exceptionCode: 500);
