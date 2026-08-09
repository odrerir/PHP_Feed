<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "Instagram Clone API",
    version: "1.0.0",
    description: "API REST do projeto final"
)]
#[OA\SecurityScheme(
    securityScheme: "sanctum",
    type: "http",
    scheme: "bearer"
)]
class Definitions
{
    //
}