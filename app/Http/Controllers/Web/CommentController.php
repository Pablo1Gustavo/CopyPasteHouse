<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\PasteCommentService;
use App\Models\PasteComment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct(
        private PasteCommentService $commentService
    ) {}

    /**
     * Display a listing of comments (admin view)
     */
    public function index()
    {
        $comments = PasteComment::with(['paste', 'user', 'syntaxHighlight'])
            ->withCount('likes')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.comments.index', compact('comments'));
    }

    /**
     * Display the specified comment
     */
    public function show(string $id)
    {
        $comment = PasteComment::with(['paste', 'user', 'syntaxHighlight', 'likes'])
            ->withCount('likes')
            ->find($id);

        if (!$comment) {
            return redirect()->route('comments.index')
                ->with('error', 'Comment not found');
        }

        return view('admin.comments.show', compact('comment'));
    }

    /**
     * Remove the specified comment
     */
    public function destroy(string $id)
    {
        $result = $this->commentService->delete($id);

        if (!$result) {
            return redirect()->route('comments.index')
                ->with('error', 'Comment not found');
        }

        return redirect()->route('comments.index')
            ->with('success', 'Comment deleted successfully');
    }
}
