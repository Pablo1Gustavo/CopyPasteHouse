<?php declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\{AccessPasteRequest, CreatePasteRequest, ListPastesRequest, UpdatePasteRequest};
use App\Models\Paste;
use App\Services\PasteService;
use Illuminate\Http\{JsonResponse, Response};
use Illuminate\Support\Facades\{Auth};
use Spatie\RouteAttributes\Attributes\{Delete, Get, Middleware, Post, Prefix, Put};

#[Prefix('pastes')]
class PasteController extends Controller
{
    public function __construct(
        private readonly PasteService $service,
    ) {
    }

    #[Get('', 'pastes.list')]
    public function list(ListPastesRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $pastes = $this->service->list($filters);

        return response()->json($pastes, Response::HTTP_OK);
    }

    #[Get('my-pastes', 'pastes.my-pastes', 'auth:sanctum')]
    public function myPastes(ListPastesRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $filters['user_id'] = Auth::id();

        $pastes = $this->service->list($filters);

        return response()->json($pastes, Response::HTTP_OK);
    }

    #[Get('{paste}', 'pastes.show')]
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

    #[Post('', 'pastes.create')]
    public function create(CreatePasteRequest $request): JsonResponse
    {
        $data = $request->validated();

        $paste = $this->service->create($data, Auth::user());

        return response()->json([
            'paste' => $paste,
            'message' => 'Paste created successfully!',
        ], Response::HTTP_CREATED);
    }

    #[Put('{paste}', 'pastes.edit', 'auth:sanctum')]
    public function edit(Paste $paste, UpdatePasteRequest $request): JsonResponse
    {
        $editedPaste = $this->service->edit($paste, $request->validated());

        return response()->json([
            'paste' => $editedPaste,
            'message' => 'Paste updated successfully!',
        ], Response::HTTP_OK);
    }

    #[Delete('{paste}', 'pastes.delete', 'auth:sanctum')]
    public function delete(Paste $paste): JsonResponse
    {
        $this->service->delete($paste);

        return response()->json([
            'message' => 'Paste deleted successfully!',
        ], Response::HTTP_OK);
    }

    #[Middleware('auth:sanctum')]
    #[Post('{paste}/like', 'pastes.toggle-like', 'auth:sanctum')]
    public function toggleLike(Paste $paste): JsonResponse
    {
        $user = Auth::user();
        $isLiked = $this->service->toggleLike($paste, $user);

        return response()->json([
            'message' => $isLiked ? 'Paste liked!' : 'Paste unliked!',
        ], Response::HTTP_OK);
    }
}
