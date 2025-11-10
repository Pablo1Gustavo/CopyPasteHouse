@extends('layouts.app')

@section('title', 'Statistics - CopyPasteHouse')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="bg-gray-800 rounded-lg p-6 mb-6">
        <h2 class="text-2xl font-bold mb-2">📊 Platform Statistics</h2>
        <p class="text-gray-400 text-sm">Analytics and insights about your pastebin platform</p>
    </div>

    <!-- Most Popular Pastes -->
    <div class="bg-gray-800 rounded-lg p-6 mb-6">
        <h3 class="text-xl font-bold mb-4">🔥 Most Liked Pastes</h3>
        @if($mostLikedPastes->count() > 0)
            <div class="space-y-3">
                @foreach($mostLikedPastes as $item)
                    <div class="flex items-center justify-between bg-gray-900 p-4 rounded">
                        <div class="flex-1">
                            <a href="{{ route('pastes.show', $item->paste->id) }}" class="text-blue-400 hover:text-blue-300 font-semibold">
                                {{ $item->paste->title }}
                            </a>
                            <p class="text-gray-400 text-sm mt-1">
                                by {{ $item->paste->user->username ?? 'Anonymous' }} • 
                                {{ $item->paste->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-pink-400">{{ $item->likes_count }}</div>
                            <div class="text-gray-400 text-xs">likes</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500">No pastes have been liked yet.</p>
        @endif
    </div>

    <!-- Most Viewed Pastes -->
    <div class="bg-gray-800 rounded-lg p-6 mb-6">
        <h3 class="text-xl font-bold mb-4">👁️ Most Viewed Pastes</h3>
        @if($mostViewedPastes->count() > 0)
            <div class="space-y-3">
                @foreach($mostViewedPastes as $item)
                    <div class="flex items-center justify-between bg-gray-900 p-4 rounded">
                        <div class="flex-1">
                            <a href="{{ route('pastes.show', $item->paste->id) }}" class="text-blue-400 hover:text-blue-300 font-semibold">
                                {{ $item->paste->title }}
                            </a>
                            <p class="text-gray-400 text-sm mt-1">
                                by {{ $item->paste->user->username ?? 'Anonymous' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-blue-400">{{ $item->views_count }}</div>
                            <div class="text-gray-400 text-xs">views</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500">No paste views recorded yet.</p>
        @endif
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-2 gap-6 mb-6">
        <!-- Recent Paste Likes -->
        <div class="bg-gray-800 rounded-lg p-6">
            <h3 class="text-xl font-bold mb-4">❤️ Recent Paste Likes</h3>
            @if($recentPasteLikes->count() > 0)
                <div class="space-y-2">
                    @foreach($recentPasteLikes as $like)
                        <div class="text-sm bg-gray-900 p-3 rounded">
                            <span class="text-blue-400">{{ $like->user->username }}</span>
                            <span class="text-gray-400">liked</span>
                            <a href="{{ route('pastes.show', $like->paste->id) }}" class="text-green-400 hover:text-green-300">
                                {{ Str::limit($like->paste->title, 30) }}
                            </a>
                            <div class="text-gray-500 text-xs mt-1">{{ $like->liked_at->diffForHumans() }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">No recent likes</p>
            @endif
        </div>

        <!-- Recent Comment Likes -->
        <div class="bg-gray-800 rounded-lg p-6">
            <h3 class="text-xl font-bold mb-4">💬 Recent Comment Likes</h3>
            @if($recentCommentLikes->count() > 0)
                <div class="space-y-2">
                    @foreach($recentCommentLikes as $like)
                        <div class="text-sm bg-gray-900 p-3 rounded">
                            <span class="text-blue-400">{{ $like->user->username }}</span>
                            <span class="text-gray-400">liked a comment on</span>
                            <a href="{{ route('pastes.show', $like->comment->paste_id) }}" class="text-green-400 hover:text-green-300">
                                {{ Str::limit($like->comment->paste->title, 25) }}
                            </a>
                            <div class="text-gray-500 text-xs mt-1">{{ $like->liked_at->diffForHumans() }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">No recent comment likes</p>
            @endif
        </div>
    </div>

    <!-- Platform Totals -->
    <div class="grid grid-cols-4 gap-4">
        <div class="bg-gray-800 rounded-lg p-6 text-center">
            <div class="text-3xl font-bold text-green-400">{{ $totalPastes }}</div>
            <div class="text-gray-400 text-sm mt-2">Total Pastes</div>
        </div>
        <div class="bg-gray-800 rounded-lg p-6 text-center">
            <div class="text-3xl font-bold text-blue-400">{{ $totalUsers }}</div>
            <div class="text-gray-400 text-sm mt-2">Total Users</div>
        </div>
        <div class="bg-gray-800 rounded-lg p-6 text-center">
            <div class="text-3xl font-bold text-pink-400">{{ $totalPasteLikes }}</div>
            <div class="text-gray-400 text-sm mt-2">Paste Likes</div>
        </div>
        <div class="bg-gray-800 rounded-lg p-6 text-center">
            <div class="text-3xl font-bold text-purple-400">{{ $totalCommentLikes }}</div>
            <div class="text-gray-400 text-sm mt-2">Comment Likes</div>
        </div>
    </div>
</div>
@endsection
