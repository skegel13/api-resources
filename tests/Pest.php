<?php

use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Http;
use Tests\CreatesApplication;

uses(
    TestCase::class,
    CreatesApplication::class,
)
    ->beforeEach(function () {
        Http::preventStrayRequests();
    })
    ->in('Feature');
