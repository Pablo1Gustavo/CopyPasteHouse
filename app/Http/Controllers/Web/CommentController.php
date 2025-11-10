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
     * Show the form for editing the specified comment
     */
    public function edit(string $id)
    {
        $comment = PasteComment::with(['paste', 'user'])->find($id);

        if (!$comment) {
            return redirect()->route('comments.index')
                ->with('error', 'Comment not found');
        }

        // Only allow admin or comment owner to edit
        if (!auth()->user()->is_admin && auth()->id() !== $comment->user_id) {
            return redirect()->back()
                ->with('error', 'You do not have permission to edit this comment');
        }

        return view('admin.comments.edit', compact('comment'));
    }

    /**
     * Update the specified comment
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'content' => 'required|string|min:1|max:10000',
        ]);

        $comment = PasteComment::find($id);

        if (!$comment) {
            return redirect()->route('comments.index')
                ->with('error', 'Comment not found');
        }

        // Only allow admin or comment owner to update
        if (!auth()->user()->is_admin && auth()->id() !== $comment->user_id) {
            return redirect()->back()
                ->with('error', 'You do not have permission to edit this comment');
        }

        $result = $this->commentService->update($id, $validated);

        if (!$result) {
            return redirect()->back()
                ->with('error', 'Failed to update comment');
        }

        return redirect()->route('pastes.show', $comment->paste_id)
            ->with('success', 'Comment updated successfully');
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
