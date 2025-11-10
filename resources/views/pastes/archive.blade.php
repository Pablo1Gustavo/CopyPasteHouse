@extends('layouts.app')

@section('title', 'Public Pastes - CopyPasteHouse')

@section('content')
@php
    $isLight = auth()->check() && auth()->user()->settings && auth()->user()->settings->theme === 'light';
    $cardClass = $isLight ? 'bg-white border border-gray-300' : 'bg-gray-800';
    $textClass = $isLight ? 'text-gray-900' : 'text-white';
    $mutedClass = $isLight ? 'text-gray-600' : 'text-gray-400';
    $borderClass = $isLight ? 'border-gray-300' : 'border-gray-600';
@endphp
<div class="max-w-6xl mx-auto px-4 py-6">
    <div class="{{ $cardClass }} rounded-lg p-6 mb-6">
        <h2 class="text-2xl font-bold mb-2 {{ $textClass }}">📚 Public Pastes Archive</h2>
        <p class="{{ $mutedClass }} text-sm">Browse recent public pastes shared by the community</p>
    </div>

    @if($pastes->count() > 0)
        <div class="space-y-4">
            @foreach($pastes as $paste)
                <div class="{{ $cardClass }} rounded-lg p-5 {{ $isLight ? 'hover:bg-gray-50' : 'hover:bg-gray-750' }} transition">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <a href="{{ route('pastes.show', $paste->id) }}" class="text-xl font-semibold text-blue-400 hover:text-blue-300">
                                {{ $paste->title }}
                            </a>
                            <div class="flex flex-wrap gap-2 mt-2 text-xs">
                                @if($paste->syntaxHighlight)
                                    <span class="border {{ $borderClass }} {{ $textClass }} px-2 py-1 rounded">
                                        📄 {{ $paste->syntaxHighlight->label }}
                                    </span>
                                @endif
                                <span class="border {{ $borderClass }} {{ $textClass }} px-2 py-1 rounded">
                                    📅 {{ $paste->created_at->diffForHumans() }}
                                </span>
                                @if($paste->user)
                                    <span class="border {{ $borderClass }} {{ $textClass }} px-2 py-1 rounded">
                                        👤 {{ $paste->user->username }}
                                    </span>
                                @endif
                                @if($paste->expiration)
                                    <span class="border border-orange-600 text-orange-400 px-2 py-1 rounded">
                                        ⏰ Expires {{ $paste->expiration->diffForHumans() }}
                                    </span>
                                @endif
                                <span class="border {{ $borderClass }} {{ $textClass }} px-2 py-1 rounded">
                                    👁️ {{ $paste->access_count ?? 0 }} views
                                </span>
                                @if($paste->likes_count > 0)
                                    <span class="border {{ $borderClass }} {{ $textClass }} px-2 py-1 rounded">
                                        ❤️ {{ $paste->likes_count }} likes
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    @if($paste->tags && is_array($paste->tags) && count($paste->tags) > 0)
                        <div class="flex flex-wrap gap-2 mt-3">
                            @foreach($paste->tags as $tag)
                                <span class="{{ $isLight ? 'bg-gray-200 text-gray-700' : 'bg-gray-700 text-gray-300' }} px-2 py-1 rounded text-xs">
                                    🏷️ {{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-3 {{ $mutedClass }} text-sm">
                        {{ Str::limit($paste->content, 150) }}
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $pastes->links() }}
        </div>
    @else
        <div class="{{ $cardClass }} rounded-lg p-12 text-center">
            <p class="{{ $mutedClass }} text-lg">📭 No public pastes yet</p>
            <p class="{{ $mutedClass }} text-sm mt-2">Be the first to create a public paste!</p>
        </div>
    @endif
</div>
@endsection
