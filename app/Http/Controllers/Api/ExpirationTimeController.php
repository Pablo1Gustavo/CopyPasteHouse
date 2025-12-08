<?php
declare(strict_types=1);
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\{CreateExpirationTimeRequest};
use App\Models\ExpirationTime;
use App\Services\{ExpirationTimeService};
use Illuminate\Http\{JsonResponse, Response};
use OpenApi\Attributes as OA;
use Spatie\RouteAttributes\Attributes\{Delete, Get, Middleware, Post, Prefix};

#[Prefix('expiration-times')]
#[Middleware('auth:sanctum')]
#[OA\Tag(name: 'Expiration Times', description: 'Manage paste expiration time options')]
class ExpirationTimeController extends Controller
{
    public function __construct(
        private readonly ExpirationTimeService $service,
    ) {
    }

    #[Get('','api.expiration-times.list')]
    #[OA\Get(
        path: '/expiration-times',
        summary: 'List all expiration times',
        description: 'Retrieve a list of available expiration time options for pastes',
        security: [['sanctum' => []]],
        tags: ['Expiration Times'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of expiration times',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer'),
                            new OA\Property(property: 'name', type: 'string'),
                            new OA\Property(property: 'duration', type: 'integer')
                        ]
                    )
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated')
        ]
    )]
    public function list(): JsonResponse
    {
        $expirationTimes = $this->service->list();

        return response()->json($expirationTimes, Response::HTTP_OK);
    }

    #[Post('', 'api.expiration-times.create')]
    #[OA\Post(
        path: '/expiration-times',
        summary: 'Create an expiration time',
        description: 'Create a new expiration time option',
        security: [['sanctum' => []]],
        tags: ['Expiration Times'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'duration'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: '1 hour'),
                    new OA\Property(property: 'duration', type: 'integer', example: 3600, description: 'Duration in seconds')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Expiration time created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'expiration_time', type: 'object'),
                        new OA\Property(property: 'message', type: 'string', example: 'Expiration time created successfully!')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error')
        ]
    )]
    public function create(CreateExpirationTimeRequest $request): JsonResponse
    {
        $data = $request->validated();

        $expirationTime = $this->service->create($data);

        return response()->json([
            'expiration_time' => $expirationTime,
            'message'         => 'Expiration time created successfully!',
        ], Response::HTTP_CREATED);
    }

    #[Delete('{expirationTime}', 'api.expiration-times.delete')]
    #[OA\Delete(
        path: '/expiration-times/{expirationTime}',
        summary: 'Delete an expiration time',
        description: 'Delete an existing expiration time option',
        security: [['sanctum' => []]],
        tags: ['Expiration Times'],
        parameters: [
            new OA\Parameter(name: 'expirationTime', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Expiration time deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Expiration time deleted successfully!')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Expiration time not found')
        ]
    )]
    public function delete(ExpirationTime $expirationTime): JsonResponse
    {
        $this->service->delete($expirationTime);

        return response()->json([
            'message' => 'Expiration time deleted successfully!',
        ], Response::HTTP_OK);
    }
}
