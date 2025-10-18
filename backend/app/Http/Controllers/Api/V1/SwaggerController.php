<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use OpenApi\Generator;

class SwaggerController extends Controller
{
    public function docs(): JsonResponse
    {
        $openapi = Generator::scan([
            app_path('Http/Controllers/Api/V1'),
            app_path('Models'),
        ]);
        return response()->json($openapi);
    }
}
