@extends('layouts.app')

@section('title', 'Comment Management - CopyPasteHouse')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">
    @if(session('success'))
        <div class="bg-green-900 border border-green-700 text-green-200 px-4 py-3 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-900 border border-red-700 text-red-200 px-4 py-3 mb-4 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-gray-800 rounded-lg p-6 mb-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold">💬 Comment Management</h2>
            <a href="{{ route('pastes.create') }}" class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded text-sm">
                ← Back
            </a>
        </div>
    </div>

    <div class="space-y-4">
        @foreach($comments as $comment)
            <div class="bg-gray-800 rounded-lg p-5">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-blue-400 font-semibold">
                                {{ $comment->user->username }}
                            </span>
                            <span class="text-gray-500 text-sm">
                                on
                            </span>
                            <a href="{{ route('pastes.show', $comment->paste_id) }}" class="text-blue-400 hover:text-blue-300">
                                {{ $comment->paste->title }}
                            </a>
                        </div>
                        <div class="text-gray-400 text-sm mb-2">
                            {{ $comment->created_at->diffForHumans() }} • 
                            {{ $comment->likes_count }} likes
                        </div>
                        <div class="text-gray-300">
                            {{ Str::limit($comment->content, 200) }}
                        </div>
                    </div>
                    <div class="flex gap-2 ml-4">
                        <a href="{{ route('admin.comments.show', $comment->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                            View
                        </a>
                        <form method="POST" action="{{ route('admin.comments.destroy', $comment->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this comment?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $comments->links() }}
    </div>
</div>
@endsection
