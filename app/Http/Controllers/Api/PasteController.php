<?php declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\{AccessPasteRequest, CreatePasteRequest, ListPastesRequest, UpdatePasteRequest};
use App\Models\Paste;
use App\Services\PasteService;
use Illuminate\Http\{JsonResponse, Response};
use Illuminate\Support\Facades\{Auth};
use OpenApi\Attributes as OA;
use Spatie\RouteAttributes\Attributes\{Delete, Get, Middleware, Post, Prefix, Put};

#[Prefix('pastes')]
#[OA\Tag(name: 'Pastes', description: 'Paste management operations')]
class PasteController extends Controller
{
    public function __construct(
        private readonly PasteService $service,
    ) {
    }

    #[Get('', 'api.pastes.list')]
    #[OA\Get(
        path: '/pastes',
        summary: 'List all pastes',
        description: 'Retrieve a paginated list of pastes with optional filters',
        tags: ['Pastes'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of pastes',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(
                            properties: [
                                new OA\Property(property: 'id', type: 'integer'),
                                new OA\Property(property: 'title', type: 'string'),
                                new OA\Property(property: 'content', type: 'string'),
                                new OA\Property(property: 'created_at', type: 'string', format: 'date-time')
                            ]
                        ))
                    ]
                )
            )
        ]
    )]
    public function list(ListPastesRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $pastes = $this->service->list($filters);

        return response()->json($pastes, Response::HTTP_OK);
    }

    #[Get('my-pastes', 'api.pastes.my-pastes', 'auth:sanctum')]
    #[OA\Get(
        path: '/pastes/my-pastes',
        summary: 'List authenticated user pastes',
        description: 'Retrieve a list of pastes created by the authenticated user',
        security: [['sanctum' => []]],
        tags: ['Pastes'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of user pastes'),
            new OA\Response(response: 401, description: 'Unauthenticated')
        ]
    )]
    public function myPastes(ListPastesRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $filters['user_id'] = Auth::id();

        $pastes = $this->service->list($filters);

        return response()->json($pastes, Response::HTTP_OK);
    }

    #[Get('{paste}', 'api.pastes.show')]
    #[OA\Get(
        path: '/pastes/{paste}',
        summary: 'Get a specific paste',
        description: 'Retrieve details of a specific paste by ID',
        tags: ['Pastes'],
        parameters: [
            new OA\Parameter(name: 'paste', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'password', in: 'query', required: false, schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paste details',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'title', type: 'string'),
                        new OA\Property(property: 'content', type: 'string'),
                        new OA\Property(property: 'created_at', type: 'string', format: 'date-time')
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Paste not found'),
            new OA\Response(response: 403, description: 'Password required')
        ]
    )]
    public function show(Paste $paste, AccessPasteRequest $request): JsonResponse
    {
        $password = $request->validated("password") ?? null;

        $accessedPaste = $this->service->access(
            paste    : $paste,
            password : $password,
            user     : Auth::user(),
            ipAddress: $request->ip(),
            userAgent: $request->userAgent()
        );

        return response()->json($accessedPaste, Response::HTTP_OK);
    }

    #[Post('', 'api.pastes.create')]
    #[OA\Post(
        path: '/pastes',
        summary: 'Create a new paste',
        description: 'Create a new paste with title and content',
        tags: ['Pastes'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title', 'content'],
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: 'My Code Snippet'),
                    new OA\Property(property: 'content', type: 'string', example: 'console.log("Hello World")'),
                    new OA\Property(property: 'syntax_highlight_id', type: 'integer', nullable: true),
                    new OA\Property(property: 'expiration_time_id', type: 'integer', nullable: true),
                    new OA\Property(property: 'password', type: 'string', nullable: true)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Paste created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'paste', type: 'object'),
                        new OA\Property(property: 'message', type: 'string', example: 'Paste created successfully!')
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error')
        ]
    )]
    public function create(CreatePasteRequest $request): JsonResponse
    {
        $data = $request->validated();

        $paste = $this->service->create($data, Auth::user());

        return response()->json([
            'paste' => $paste,
            'message' => 'Paste created successfully!',
        ], Response::HTTP_CREATED);
    }

    #[Put('{paste}', 'api.pastes.edit', 'auth:sanctum')]
    #[OA\Put(
        path: '/pastes/{paste}',
        summary: 'Update a paste',
        description: 'Update an existing paste (requires authentication)',
        security: [['sanctum' => []]],
        tags: ['Pastes'],
        parameters: [
            new OA\Parameter(name: 'paste', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title', type: 'string'),
                    new OA\Property(property: 'content', type: 'string'),
                    new OA\Property(property: 'syntax_highlight_id', type: 'integer', nullable: true)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paste updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'paste', type: 'object'),
                        new OA\Property(property: 'message', type: 'string', example: 'Paste updated successfully!')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Paste not found')
        ]
    )]
    public function edit(Paste $paste, UpdatePasteRequest $request): JsonResponse
    {
        $editedPaste = $this->service->edit($paste, $request->validated());

        return response()->json([
            'paste' => $editedPaste,
            'message' => 'Paste updated successfully!',
        ], Response::HTTP_OK);
    }

    #[Delete('{paste}', 'api.pastes.delete', 'auth:sanctum')]
    #[OA\Delete(
        path: '/pastes/{paste}',
        summary: 'Delete a paste',
        description: 'Delete an existing paste (requires authentication)',
        security: [['sanctum' => []]],
        tags: ['Pastes'],
        parameters: [
            new OA\Parameter(name: 'paste', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paste deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Paste deleted successfully!')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Paste not found')
        ]
    )]
    public function delete(Paste $paste): JsonResponse
    {
        $this->service->delete($paste);

        return response()->json([
            'message' => 'Paste deleted successfully!',
        ], Response::HTTP_OK);
    }

    #[Post('{paste}/like', 'api.pastes.toggle-like', 'auth:sanctum')]
    #[OA\Post(
        path: '/pastes/{paste}/like',
        summary: 'Toggle like on paste',
        description: 'Like or unlike a paste (requires authentication)',
        security: [['sanctum' => []]],
        tags: ['Pastes'],
        parameters: [
            new OA\Parameter(name: 'paste', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Like toggled successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Paste liked!')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Paste not found')
        ]
    )]
    public function toggleLike(Paste $paste): JsonResponse
    {
        $user = Auth::user();
        $isLiked = $this->service->toggleLike($paste, $user);

        return response()->json([
            'message' => $isLiked ? 'Paste liked!' : 'Paste unliked!',
        ], Response::HTTP_OK);
    }
}
