@extends('layouts.app')

@section('title', 'Welcome - CopyPasteHouse')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg p-8 mb-8 text-center">
        <h1 class="text-4xl font-bold mb-4">📋 CopyPasteHouse</h1>
        <p class="text-xl mb-6 text-gray-100">Share code snippets, text, and more with the world</p>
        <a href="{{ route('pastes.create') }}" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-bold text-lg hover:bg-gray-100 transition inline-block">
            Create New Paste
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Most Popular Section -->
        <div class="{{ $cardClass }} rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-4 {{ $textClass }}">🔥 Most Liked Pastes</h2>
            @if($popularPastes->count() > 0)
                <div class="space-y-3">
                    @foreach($popularPastes as $item)
                        <div class="{{ $innerCardClass }} p-4 rounded {{ $hoverClass }} transition">
                            <a href="{{ route('pastes.show', $item->paste->id) }}" class="{{ $linkClass }} font-semibold text-lg">
                                {{ Str::limit($item->paste->title, 50) }}
                            </a>
                            <div class="flex items-center justify-between mt-2">
                                <span class="{{ $mutedClass }} text-sm">
                                    by {{ $item->paste->user->username ?? 'Anonymous' }}
                                </span>
                                <div class="flex items-center gap-3 text-sm">
                                    <span class="text-pink-400">❤️ {{ $item->likes_count }} likes</span>
                                    <span class="{{ $mutedClass }}">{{ $item->paste->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="{{ $mutedClass }}">No pastes available yet. Be the first to create one!</p>
            @endif
        </div>

        <!-- Most Viewed Section -->
        <div class="{{ $cardClass }} rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-4 {{ $textClass }}">👁️ Trending Pastes</h2>
            @if($trendingPastes->count() > 0)
                <div class="space-y-3">
                    @foreach($trendingPastes as $item)
                        <div class="{{ $innerCardClass }} p-4 rounded {{ $hoverClass }} transition">
                            <a href="{{ route('pastes.show', $item->paste->id) }}" class="{{ $linkClass }} font-semibold text-lg">
                                {{ Str::limit($item->paste->title, 50) }}
                            </a>
                            <div class="flex items-center justify-between mt-2">
                                <span class="{{ $mutedClass }} text-sm">
                                    by {{ $item->paste->user->username ?? 'Anonymous' }}
                                </span>
                                <div class="flex items-center gap-3 text-sm">
                                    <span class="text-blue-400">👁️ {{ $item->views_count }} views</span>
                                    <span class="{{ $mutedClass }}">{{ $item->paste->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="{{ $mutedClass }}">No trending pastes yet.</p>
            @endif
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="{{ $cardClass }} rounded-lg p-6 mt-6">
        <h2 class="text-2xl font-bold mb-4 {{ $textClass }}">⚡ Recent Activity</h2>
        @if($recentActivity->count() > 0)
            <div class="space-y-2">
                @foreach($recentActivity as $like)
                    <div class="flex items-center justify-between {{ $innerCardClass }} p-3 rounded text-sm">
                        <div>
                            <span class="{{ $linkClass }} font-semibold">{{ $like->user->username }}</span>
                            <span class="{{ $mutedClass }}">liked</span>
                            <a href="{{ route('pastes.show', $like->paste->id) }}" class="text-green-400 hover:text-green-300 font-semibold">
                                {{ Str::limit($like->paste->title, 40) }}
                            </a>
                        </div>
                        <span class="{{ $mutedClass }}">{{ $like->liked_at->diffForHumans() }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="{{ $mutedClass }}">No recent activity.</p>
        @endif
    </div>

    <!-- Call to Action -->
    <div class="{{ $cardClass }} rounded-lg p-8 mt-6 text-center">
        <h3 class="text-2xl font-bold mb-3 {{ $textClass }}">Ready to share your code?</h3>
        <p class="{{ $mutedClass }} mb-6">Create an account to manage your pastes, track views, and engage with the community</p>
        <div class="flex justify-center gap-4">
            @guest
                <a href="{{ route('register') }}" class="bg-orange-500 text-white px-6 py-3 rounded-lg font-bold hover:bg-orange-600 transition">
                    Sign Up Now
                </a>
                <a href="{{ route('login') }}" class="{{ $isLight ? 'border border-gray-300 text-gray-800 hover:bg-gray-100' : 'border border-gray-600 text-gray-300 hover:bg-gray-700' }} px-6 py-3 rounded-lg font-bold transition">
                    Login
                </a>
            @else
                <a href="{{ route('pastes.index') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700 transition">
                    View My Pastes
                </a>
            @endguest
        </div>
    </div>
</div>
@endsection
