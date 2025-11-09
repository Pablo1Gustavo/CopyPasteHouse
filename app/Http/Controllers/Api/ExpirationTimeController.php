<?php
declare(strict_types=1);
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\{CreateExpirationTimeRequest};
use App\Models\ExpirationTime;
use App\Services\{ExpirationTimeService};
use Illuminate\Http\{JsonResponse, Response};
use Spatie\RouteAttributes\Attributes\{Delete, Get, Middleware, Post, Prefix};

#[Prefix('expiration-times')]
#[Middleware('auth:sanctum')]
class ExpirationTimeController extends Controller
{
    public function __construct(
        private readonly ExpirationTimeService $service,
    ) {
    }

    #[Get('','expiration-times.list')]
    public function list(): JsonResponse
    {
        $expirationTimes = $this->service->list();

        return response()->json($expirationTimes, Response::HTTP_OK);
    }

    #[Post('', 'expiration-times.create')]
    public function create(CreateExpirationTimeRequest $request): JsonResponse
    {
        $data = $request->validated();

        $expirationTime = $this->service->create($data);

        return response()->json([
            'expiration_time' => $expirationTime,
            'message'         => 'Expiration time created successfully!',
        ], Response::HTTP_CREATED);
    }

    #[Delete('{expirationTime}', 'expiration-times.delete')]
    public function delete(ExpirationTime $expirationTime): JsonResponse
    {
        $this->service->delete($expirationTime);

        return response()->json([
            'message' => 'Expiration time deleted successfully!',
        ], Response::HTTP_OK);
    }
}
