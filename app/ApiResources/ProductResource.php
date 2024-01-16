<?php

namespace App\ApiResources;

use App\Data\ListProductsData;
use App\Data\ProductData;
use App\Data\SaveProductData;
use App\Support\ApiRequest;
use App\Support\StoreApiClient;
use Spatie\LaravelData\DataCollection;

/**
 * ApiResource for products.
 */
class ProductResource
{
    /**
     * Use dependency injection to get the StoreApiClient.
     */
    public function __construct(private readonly StoreApiClient $client)
    {
    }

    /**
     * List all products.
     */
    public function list(?ListProductsData $data = null): DataCollection
    {
        $request = ApiRequest::get('/products');

        if ($data) {
            $request->setQuery($data->toArray());
        }

        $response = $this->client->send($request);

        return ProductData::collection($response->json());
    }

    /**
     * Show a single product.
     */
    public function show(int $id): ProductData
    {
        $request = ApiRequest::get("/products/$id");
        $response = $this->client->send($request);

        return ProductData::from($response->json());
    }

    /**
     * Create a new product.
     */
    public function create(SaveProductData $data): ProductData
    {
        $request = ApiRequest::post('/products')->setBody($data->toArray());
        $response = $this->client->send($request);

        return ProductData::from($response->json());
    }

    /**
     * Update a product.
     */
    public function update(int $id, SaveProductData $data): ProductData
    {
        $request = ApiRequest::put("/products/$id")->setBody($data->toArray());
        $response = $this->client->send($request);

        return ProductData::from($response->json());
    }

    /**
     * Delete a product.
     */
    public function delete(int $id): ProductData
    {
        $request = ApiRequest::delete("/products/$id");
        $response = $this->client->send($request);

        return ProductData::from($response->json());
    }
}
