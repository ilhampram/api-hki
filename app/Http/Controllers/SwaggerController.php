<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

class SwaggerController extends Controller
{
    #[OA\Get(
        path: '/api/test',
        summary: 'Cek API',
        description: 'Endpoint untuk mengecek status API',
        tags: ['Test'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Berhasil',
                content: new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'message', type: 'string')
                        ]
                    )
                )
            )
        ]
    )]
    public function test()
    {
        return response()->json([
            'message' => 'API HKI Berhasil'
        ]);
    }
}
