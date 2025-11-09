<?php
declare(strict_types=1);
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentOnPasteRequest;
use App\Models\{Paste, PasteComment};
use App\Services\PasteCommentService;
use Illuminate\Http\{JsonResponse, Response};
use Illuminate\Support\Facades\Auth;
use Spatie\RouteAttributes\Attributes\{Delete, Get, Middleware, Patch, Post, Prefix, Put};

#[Prefix('pastes')]
#[Middleware('auth:sanctum')]
class PasteCommentController extends Controller
{
    public function __construct(
        private readonly PasteCommentService $service,
    ) {
    }

    #[Get('{paste}/comments')]
    public function list(Paste $paste)
    {
        return $this->service->list($paste);
    }

    #[Post('{paste}/comments')]
    public function create(Paste $paste, CommentOnPasteRequest $request): JsonResponse
    {
        $data = $request->validated();

        $this->service->create($paste, Auth::user(), $data);

        return response()->json([
            'message' => 'Comment created successfully.'
        ], Response::HTTP_CREATED);
    }

    #[Put('comments/{comment}')]
    public function edit(PasteComment $comment, CommentOnPasteRequest $request): JsonResponse
    {
        $this->service->validateAuthenticatedUserOwnership($comment);

        $data = $request->validated();
        $this->service->edit($comment, $data);

        return response()->json([
            'message' => 'Comment updated successfully.'
        ], Response::HTTP_OK);
    }

    #[Delete('comments/{comment}')]
    public function delete(PasteComment $comment): JsonResponse
    {
        $this->service->validateAuthenticatedUserOwnership($comment);

        $this->service->delete($comment);

        return response()->json([
            'message' => 'Comment deleted successfully.'
        ], Response::HTTP_OK);
    }

    #[Patch('comments/{comment}/like')]
    public function toggleLike(PasteComment $comment): JsonResponse
    {
        $liked = $this->service->toggleLike($comment, Auth::user());

        return response()->json([
            'message' => $liked ? 'Comment liked.' : 'Comment unliked.'
        ], Response::HTTP_OK);
    }
}
