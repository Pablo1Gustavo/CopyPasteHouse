<?php
declare(strict_types=1);
namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidCredentials;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\UserService;
use Illuminate\Http\{JsonResponse, Response};
use Illuminate\Support\Facades\{Auth, Hash};
use OpenApi\Attributes as OA;
use Spatie\RouteAttributes\Attributes\Post;

class AuthController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
    ) {
    }

    #[Post('login', 'auth.login')]
    #[OA\Post(
        path: '/api/login',
        summary: 'User login',
        description: 'Authenticate a user and return an access token',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['login', 'password'],
                properties: [
                    new OA\Property(property: 'login', type: 'string', example: 'user@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password123')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Login successful',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'token', type: 'string', example: '1|abcdef123456...'),
                        new OA\Property(property: 'message', type: 'string', example: 'Login successful!')
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Invalid credentials'
            )
        ]
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = $this->userService->findByLogin($data['login']);

        $invalidCredentials = !$user || !Hash::check($data['password'], $user->password);

        if ($invalidCredentials)
        {
            throw new InvalidCredentials;
        }

        $token = $user->createToken('auth');

        return response()->json([
            'token'   => $token->plainTextToken,
            'message' => 'Login successful!',
        ], Response::HTTP_CREATED);
    }

    #[Post('logout', 'auth.logout', ['auth:sanctum'])]
    #[OA\Post(
        path: '/api/logout',
        summary: 'User logout',
        description: 'Logout the authenticated user and revoke all tokens',
        security: [['sanctum' => []]],
        tags: ['Authentication'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Logout successful',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Logout successful!')
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated'
            )
        ]
    )]
    public function logout(): JsonResponse
    {
        Auth::user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout successful!',
        ], Response::HTTP_OK);
    }
}
