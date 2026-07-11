<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "API HKI",
    description: "Dokumentasi API HKI"
)]

#[OA\Server(
    url: "http://127.0.0.1:8000",
    description: "Local Server"
)]

#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer"
)]

final class OpenApiSpec
{
}