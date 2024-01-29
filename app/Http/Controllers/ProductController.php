<?php

namespace App\Http\Controllers;

use App\ApiResources\ProductResource;
use App\Data\SaveProductData;
use App\Exceptions\ApiException;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(public readonly ProductResource $productResource)
    {

    }

    public function create(Request $request)
    {
        try {
            return $this->productResource->create(SaveProductData::from($request->all());
        } catch (ApiException $exception) {
            if ($exception->getCode() === 422) {
                // 422 is typically the HTTP status code used for validation errors.
                // Let's assume that the API returns an 'errors' property similar to Laravel.
                $errors = $exception->response?->json('errors');
            }

            return response()->json([
                'message' => $exception->getMessage(),
                'errors' => $errors ?? null,
            ], $exception->getCode());
        }
    }
}
