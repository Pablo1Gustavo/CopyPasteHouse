<?php
declare(strict_types=1);
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentOnPasteRequest;
use App\Models\{Paste, PasteComment};
use App\Services\PasteCommentService;
use Illuminate\Http\{JsonResponse, Response};
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;
use Spatie\RouteAttributes\Attributes\{Delete, Get, Middleware, Patch, Post, Prefix, Put};

#[Prefix('pastes')]
#[Middleware('auth:sanctum')]
#[OA\Tag(name: 'Paste Comments', description: 'Comment management for pastes')]
class PasteCommentController extends Controller
{
    public function __construct(
        private readonly PasteCommentService $service,
    ) {
    }

    #[Get('{paste}/comments', 'api.pastes.comments.list')]
    #[OA\Get(
        path: '/pastes/{paste}/comments',
        summary: 'List comments on a paste',
        description: 'Retrieve all comments for a specific paste',
        security: [['sanctum' => []]],
        tags: ['Paste Comments'],
        parameters: [
            new OA\Parameter(name: 'paste', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of comments',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer'),
                            new OA\Property(property: 'comment', type: 'string'),
                            new OA\Property(property: 'user_id', type: 'integer'),
                            new OA\Property(property: 'created_at', type: 'string', format: 'date-time')
                        ]
                    )
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Paste not found')
        ]
    )]
    public function list(Paste $paste)
    {
        return $this->service->list($paste);
    }

    #[Post('{paste}/comments', 'api.pastes.comments.create')]
    #[OA\Post(
        path: '/pastes/{paste}/comments',
        summary: 'Create a comment',
        description: 'Add a new comment to a paste',
        security: [['sanctum' => []]],
        tags: ['Paste Comments'],
        parameters: [
            new OA\Parameter(name: 'paste', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['comment'],
                properties: [
                    new OA\Property(property: 'comment', type: 'string', example: 'Great paste!')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Comment created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Comment created successfully.')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error')
        ]
    )]
    public function create(Paste $paste, CommentOnPasteRequest $request): JsonResponse
    {
        $data = $request->validated();

        $this->service->create($paste, Auth::user(), $data);

        return response()->json([
            'message' => 'Comment created successfully.'
        ], Response::HTTP_CREATED);
    }

    #[Put('comments/{comment}', 'api.pastes.comments.edit')]
    #[OA\Put(
        path: '/pastes/comments/{comment}',
        summary: 'Update a comment',
        description: 'Update an existing comment (user must be comment owner)',
        security: [['sanctum' => []]],
        tags: ['Paste Comments'],
        parameters: [
            new OA\Parameter(name: 'comment', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['comment'],
                properties: [
                    new OA\Property(property: 'comment', type: 'string', example: 'Updated comment text')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Comment updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Comment updated successfully.')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden - Not comment owner'),
            new OA\Response(response: 404, description: 'Comment not found')
        ]
    )]
    public function edit(PasteComment $comment, CommentOnPasteRequest $request): JsonResponse
    {
        $this->service->validateAuthenticatedUserOwnership($comment);

        $data = $request->validated();
        $this->service->edit($comment, $data);

        return response()->json([
            'message' => 'Comment updated successfully.'
        ], Response::HTTP_OK);
    }

    #[Delete('comments/{comment}', 'api.pastes.comments.delete')]
    #[OA\Delete(
        path: '/pastes/comments/{comment}',
        summary: 'Delete a comment',
        description: 'Delete an existing comment (user must be comment owner)',
        security: [['sanctum' => []]],
        tags: ['Paste Comments'],
        parameters: [
            new OA\Parameter(name: 'comment', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Comment deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Comment deleted successfully.')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden - Not comment owner'),
            new OA\Response(response: 404, description: 'Comment not found')
        ]
    )]
    public function delete(PasteComment $comment): JsonResponse
    {
        $this->service->validateAuthenticatedUserOwnership($comment);

        $this->service->delete($comment);

        return response()->json([
            'message' => 'Comment deleted successfully.'
        ], Response::HTTP_OK);
    }

    #[Patch('comments/{comment}/like', 'api.pastes.comments.like')]
    #[OA\Patch(
        path: '/pastes/comments/{comment}/like',
        summary: 'Toggle like on comment',
        description: 'Like or unlike a comment',
        security: [['sanctum' => []]],
        tags: ['Paste Comments'],
        parameters: [
            new OA\Parameter(name: 'comment', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Like toggled successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Comment liked.')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Comment not found')
        ]
    )]
    public function toggleLike(PasteComment $comment): JsonResponse
    {
        $liked = $this->service->toggleLike($comment, Auth::user());

        return response()->json([
            'message' => $liked ? 'Comment liked.' : 'Comment unliked.'
        ], Response::HTTP_OK);
    }
}
