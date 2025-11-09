<?php
declare(strict_types=1);
namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidCredentials;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\UserService;
use Illuminate\Http\{JsonResponse, Response};
use Illuminate\Support\Facades\{Auth, Hash};
use Spatie\RouteAttributes\Attributes\Post;

class AuthController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
    ) {
    }

    #[Post('login', 'auth.login')]
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
    public function logout(): JsonResponse
    {
        Auth::user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout successful!',
        ], Response::HTTP_OK);
    }
}
