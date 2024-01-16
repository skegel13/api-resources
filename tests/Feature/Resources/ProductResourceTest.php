<?php

use App\ApiResources\ProductResource;
use App\Data\ListProductsData;
use App\Data\ProductData;
use App\Data\SaveProductData;
use App\Enums\SortDirection;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Request;
use Spatie\LaravelData\DataCollection;
use Tests\Helpers\StoreApiTestHelper;

uses(StoreApiTestHelper::class);

it('shows a list of products', function () {
    // Fake the response from the API.
    Http::fake([
        '*/products' => Http::response([
            $this->getFakeProduct(['id' => 1]),
            $this->getFakeProduct(['id' => 2]),
            $this->getFakeProduct(['id' => 3]),
            $this->getFakeProduct(['id' => 4]),
            $this->getFakeProduct(['id' => 5]),
        ]),
    ]);

    $resource = resolve(ProductResource::class);

    $response = $resource->list();

    // Assert that the response is a collection of product data objects.
    expect($response)
        ->toBeInstanceOf(DataCollection::class)
        ->count()->toBe(5)
        ->getIterator()->each->toBeInstanceOf(ProductData::class);

    // Assert that a GET request was sent to the correct endpoint.
    Http::assertSent(function (Request $request) {
        expect($request)
            ->url()->toEndWith('/products')
            ->method()->toBe('GET');

        return true;
    });
});

it('limits and sorts products', function () {
    // Fake the response from the API.
    Http::fake([
        '*/products?*' => Http::response([
            $this->getFakeProduct(['id' => 3]),
            $this->getFakeProduct(['id' => 2]),
            $this->getFakeProduct(['id' => 1]),
        ]),
    ]);

    $resource = resolve(ProductResource::class);

    // Create a request data object with a three-item limit and descending direction.
    $requestData = new ListProductsData(3, SortDirection::DESC);

    $response = $resource->list($requestData);

    // Assert that the response is a collection of product data objects.
    expect($response)
        ->toBeInstanceOf(DataCollection::class)
        ->count()->toBe(3)
        ->getIterator()->each->toBeInstanceOf(ProductData::class);

    // Assert that a GET request was sent to the correct endpoint with the correct query data.
    Http::assertSent(function (Request $request) {
        parse_str(parse_url($request->url(), PHP_URL_QUERY), $queryParams);
        $path = (parse_url($request->url(), PHP_URL_PATH));

        expect($queryParams)->toMatchArray(['limit' => 3, 'sort' => 'desc'])
            ->and($path)->toEndWith('/products')
            ->and($request)->method()->toBe('GET');

        return true;
    });
});

it('fetches a product', function () {
    // Create a fake product
    $fakeProduct = $this->getFakeProduct();

    // Fake the response from the API.
    Http::fake(["*/products/{$fakeProduct['id']}" => Http::response($fakeProduct)]);

    $resource = resolve(ProductResource::class);

    // Request a product
    $response = $resource->show($fakeProduct['id']);
    expect($response)
        ->toBeInstanceOf(ProductData::class)
        ->id->toBe($fakeProduct['id']);

    // Assert that a GET request was sent to the correct endpoint with the correct method.
    Http::assertSent(function (Request $request) use ($fakeProduct) {
        expect($request)
            ->url()->toEndWith("/products/{$fakeProduct['id']}")
            ->method()->toBe('GET');

        return true;
    });
});

it('deletes a product', function () {
    // Create a fake product
    $fakeProduct = $this->getFakeProduct();

    // Fake the response from the API.
    Http::fake(["*/products/{$fakeProduct['id']}" => Http::response($fakeProduct)]);

    $resource = resolve(ProductResource::class);

    // Request a product
    $response = $resource->delete($fakeProduct['id']);
    expect($response)
        ->toBeInstanceOf(ProductData::class)
        ->id->toBe($fakeProduct['id']);

    // Assert that a DELETE request was sent to the correct endpoint.
    Http::assertSent(function (Request $request) use ($fakeProduct) {
        expect($request)
            ->url()->toEndWith("/products/{$fakeProduct['id']}")
            ->method()->toBe('DELETE');

        return true;
    });
});

it('creates a product', function () {
    // Create a fake product
    $fakeProduct = $this->getFakeProduct();

    // Fake the response from the API.
    Http::fake(["*/products" => Http::response($fakeProduct)]);

    $resource = resolve(ProductResource::class);

    // Data for creating a product.
    $data = new SaveProductData(
        title: $fakeProduct['title'],
        price: $fakeProduct['price'],
        description: $fakeProduct['description'],
        category: $fakeProduct['category'],
        image: $fakeProduct['image'],
    );

    // Request a product
    $response = $resource->create($data);
    expect($response)
        ->toBeInstanceOf(ProductData::class)
        ->id->toBe($fakeProduct['id']);

    // Assert that a POST request was sent to the correct endpoint.
    Http::assertSent(function (Request $request) {
        expect($request)
            ->url()->toEndWith('/products')
            ->method()->toBe('POST');

        return true;
    });
});

it('updates a product', function () {
    // Create a fake product
    $fakeProduct = $this->getFakeProduct();

    // Fake the response from the API.
    Http::fake(["*/products/{$fakeProduct['id']}" => Http::response($fakeProduct)]);

    $resource = resolve(ProductResource::class);

    $data = new SaveProductData(
        title: $fakeProduct['title'],
        price: $fakeProduct['price'],
        description: $fakeProduct['description'],
        category: $fakeProduct['category'],
        image: $fakeProduct['image'],
    );

    // Request a product
    $response = $resource->update($fakeProduct['id'], $data);
    expect($response)
        ->toBeInstanceOf(ProductData::class)
        ->id->toBe($fakeProduct['id']);

    // Assert that a PUT request was sent to the correct endpoint.
    Http::assertSent(function (Request $request) use ($fakeProduct) {
        expect($request)
            ->url()->toEndWith("/products/{$fakeProduct['id']}")
            ->method()->toBe('PUT');

        return true;
    });
});
