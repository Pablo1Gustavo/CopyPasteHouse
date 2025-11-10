@extends('layouts.app')

@section('title', 'Comment Details - CopyPasteHouse')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
    <div class="bg-gray-800 rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">💬 Comment Details</h2>
            <a href="{{ route('admin.comments') }}" class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded text-sm">
                ← Back to Comments
            </a>
        </div>

        <div class="space-y-4">
            <div>
                <label class="text-gray-400 text-sm">Author:</label>
                <p class="text-white text-lg">{{ $comment->user->username }}</p>
            </div>

            <div>
                <label class="text-gray-400 text-sm">Paste:</label>
                <a href="{{ route('pastes.show', $comment->paste_id) }}" class="text-blue-400 hover:text-blue-300 text-lg">
                    {{ $comment->paste->title }}
                </a>
            </div>

            <div>
                <label class="text-gray-400 text-sm">Created:</label>
                <p class="text-white">{{ $comment->created_at->format('M d, Y H:i') }}</p>
            </div>

            <div>
                <label class="text-gray-400 text-sm">Likes:</label>
                <p class="text-white">{{ $comment->likes_count }}</p>
            </div>

            @if($comment->syntax_highlight_id)
                <div>
                    <label class="text-gray-400 text-sm">Syntax Highlighting:</label>
                    <p class="text-white">{{ $comment->syntaxHighlight->label }}</p>
                </div>
            @endif

            <div>
                <label class="text-gray-400 text-sm mb-2 block">Content:</label>
                @if($comment->syntax_highlight_id)
                    <div class="bg-gray-900 rounded-lg p-4">
                        <pre><code class="language-{{ $comment->syntaxHighlight->value }}">{{ $comment->content }}</code></pre>
                    </div>
                @else
                    <div class="bg-gray-900 rounded-lg p-4 text-gray-300">
                        {{ $comment->content }}
                    </div>
                @endif
            </div>

            <div class="pt-4">
                <form method="POST" action="{{ route('admin.comments.destroy', $comment->id) }}" onsubmit="return confirm('Are you sure you want to delete this comment?');">
                    @csrf
                    @method('DELETE')
                    <button 
                        type="submit" 
                        class="bg-red-700 hover:bg-red-600 text-white px-6 py-3 rounded font-medium"
                    >
                        Delete Comment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script>
    hljs.highlightAll();
</script>
@endpush
@endsection
