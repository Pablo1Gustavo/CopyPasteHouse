<?php
declare(strict_types=1);
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\{CreateSyntaxHighlightRequest, UpdateSyntaxHighlightRequest};
use App\Models\SyntaxHighlight;
use App\Services\SyntaxHighlightService;
use Illuminate\Http\{JsonResponse, Response};
use OpenApi\Attributes as OA;
use Spatie\RouteAttributes\Attributes\{Delete, Get, Middleware, Post, Prefix, Put};

#[Prefix('syntax-highlights')]
#[Middleware('auth:sanctum')]
#[OA\Tag(name: 'Syntax Highlights', description: 'Manage syntax highlighting options')]
class SyntaxHighlightController extends Controller
{
    public function __construct(
        private readonly SyntaxHighlightService $service,
    ) {
    }

    #[Get('', 'api.syntax-highlights.list')]
    #[OA\Get(
        path: '/api/syntax-highlights',
        summary: 'List all syntax highlights',
        description: 'Retrieve a list of available syntax highlighting options',
        security: [['sanctum' => []]],
        tags: ['Syntax Highlights'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of syntax highlights',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer'),
                            new OA\Property(property: 'name', type: 'string'),
                            new OA\Property(property: 'code', type: 'string')
                        ]
                    )
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated')
        ]
    )]
    public function list(): JsonResponse
    {
        $syntaxHighlights = $this->service->list();

        return response()->json($syntaxHighlights, Response::HTTP_OK);
    }

    #[Post('', 'api.syntax-highlights.create')]
    #[OA\Post(
        path: '/api/syntax-highlights',
        summary: 'Create a syntax highlight',
        description: 'Create a new syntax highlighting option',
        security: [['sanctum' => []]],
        tags: ['Syntax Highlights'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'code'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'JavaScript'),
                    new OA\Property(property: 'code', type: 'string', example: 'javascript')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Syntax highlight created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'syntax_highlight', type: 'object'),
                        new OA\Property(property: 'message', type: 'string', example: 'Syntax highlight created successfully!')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error')
        ]
    )]
    public function create(CreateSyntaxHighlightRequest $request): JsonResponse
    {
        $data = $request->validated();

        $syntaxHighlight = $this->service->create($data);

        return response()->json([
            'syntax_highlight' => $syntaxHighlight,
            'message'          => 'Syntax highlight created successfully!',
        ], Response::HTTP_CREATED);
    }

    #[Put('{syntaxHighlight}', 'api.syntax-highlights.edit')]
    #[OA\Put(
        path: '/api/syntax-highlights/{syntaxHighlight}',
        summary: 'Update a syntax highlight',
        description: 'Update an existing syntax highlighting option',
        security: [['sanctum' => []]],
        tags: ['Syntax Highlights'],
        parameters: [
            new OA\Parameter(name: 'syntaxHighlight', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Python'),
                    new OA\Property(property: 'code', type: 'string', example: 'python')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Syntax highlight updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'syntax_highlight', type: 'object'),
                        new OA\Property(property: 'message', type: 'string', example: 'Syntax highlight updated successfully!')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Syntax highlight not found')
        ]
    )]
    public function edit(SyntaxHighlight $syntaxHighlight, UpdateSyntaxHighlightRequest $request): JsonResponse
    {
        $data = $request->validated();
        $updatedSyntaxHighlight = $this->service->edit($syntaxHighlight, $data);

        return response()->json([
            'syntax_highlight' => $updatedSyntaxHighlight,
            'message'          => 'Syntax highlight updated successfully!',
        ], Response::HTTP_OK);
    }

    #[Delete('{syntaxHighlight}', 'api.syntax-highlights.delete')]
    #[OA\Delete(
        path: '/api/syntax-highlights/{syntaxHighlight}',
        summary: 'Delete a syntax highlight',
        description: 'Delete an existing syntax highlighting option',
        security: [['sanctum' => []]],
        tags: ['Syntax Highlights'],
        parameters: [
            new OA\Parameter(name: 'syntaxHighlight', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Syntax highlight deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Syntax highlight deleted successfully!')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Syntax highlight not found')
        ]
    )]
    public function delete(SyntaxHighlight $syntaxHighlight): JsonResponse
    {
        $this->service->delete($syntaxHighlight);

        return response()->json([
            'message' => 'Syntax highlight deleted successfully!',
        ], Response::HTTP_OK);
    }
}
