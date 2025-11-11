<?php
declare(strict_types=1);
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\{CreateSyntaxHighlightRequest, UpdateSyntaxHighlightRequest};
use App\Models\SyntaxHighlight;
use App\Services\SyntaxHighlightService;
use Illuminate\Http\{JsonResponse, Response};
use Spatie\RouteAttributes\Attributes\{Delete, Get, Middleware, Post, Prefix, Put};

#[Prefix('syntax-highlights')]
#[Middleware('auth:sanctum')]
class SyntaxHighlightController extends Controller
{
    public function __construct(
        private readonly SyntaxHighlightService $service,
    ) {
    }

    #[Get('', 'api.syntax-highlights.list')]
    public function list(): JsonResponse
    {
        $syntaxHighlights = $this->service->list();

        return response()->json($syntaxHighlights, Response::HTTP_OK);
    }

    #[Post('', 'api.syntax-highlights.create')]
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
    public function delete(SyntaxHighlight $syntaxHighlight): JsonResponse
    {
        $this->service->delete($syntaxHighlight);

        return response()->json([
            'message' => 'Syntax highlight deleted successfully!',
        ], Response::HTTP_OK);
    }
}
