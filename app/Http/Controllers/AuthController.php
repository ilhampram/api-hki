<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
    path: '/api/register',
    summary: 'Register User',
    tags: ['Authentication'],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['name','email','password'],
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Ilham'),
                new OA\Property(property: 'email', type: 'string', example: 'ilham@gmail.com'),
                new OA\Property(property: 'password', type: 'string', example: '123456')
            ]
        )
    ),
    responses: [
        new OA\Response(
            response: 200,
            description: 'Register berhasil'
            )
        ]
    )]
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        $$user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'user'
    ]);

        return response()->json([
            'success' => true,
            'message' => 'Register berhasil',
            'data' => $user
        ]);
    }

    
    #[OA\Post(
    path: '/api/login',
    summary: 'Login User',
    tags: ['Authentication'],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['email','password'],
            properties: [
                new OA\Property(
                    property: 'email',
                    type: 'string',
                    example: 'ilham@gmail.com'
                ),
                new OA\Property(
                    property: 'password',
                    type: 'string',
                    example: '123456'
                )
            ]
        )
    ),
    responses: [
        new OA\Response(
            response: 200,
            description: 'Login berhasil'
            )
        ]
    )]
     
    public function login(Request $request)
    {
        $user = User::where(
            'email',
            $request->email
        )->first();

        if (!$user || !Hash::check(
            $request->password,
            $user->password
        )) {

            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah'
            ], 401);
        }

        $token = $user->createToken(
            'api-token'
        )->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token
        ]);
    }

    #[OA\Post(
    path: '/api/logout',
    summary: 'Logout User',
    tags: ['Authentication'],
    security: [['bearerAuth' => []]],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Logout berhasil'
            )
        ]
    )]
    public function logout(Request $request)
    {
        $request->user()
            ->currentAccessToken()
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    
}

