@extends('layouts.app')

@section('title', $tag->name . ' - CopyPasteHouse')

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
        <div class="flex items-center gap-4 mb-4">
            <span class="inline-flex items-center text-white text-lg px-4 py-2 rounded-full" 
                  style="background-color: {{ $tag->color }}">
                {{ $tag->name }}
            </span>
            <div class="flex-1">
                @if($tag->description)
                    <p class="{{ $textClass }}">{{ $tag->description }}</p>
                @endif
                <p class="{{ $mutedClass }} text-sm">{{ $tag->pastes_count }} {{ Str::plural('paste', $tag->pastes_count) }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('pastes.archive') }}" class="text-blue-400 hover:text-blue-300">← Back to Archive</a>
        </div>
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
                                        📄 {{ $paste->syntaxHighlight->name }}
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
                                @if($paste->likes_count > 0)
                                    <span class="border {{ $borderClass }} {{ $textClass }} px-2 py-1 rounded">
                                        ❤️ {{ $paste->likes_count }} likes
                                    </span>
                                @endif
                                @if($paste->comments_count > 0)
                                    <span class="border {{ $borderClass }} {{ $textClass }} px-2 py-1 rounded">
                                        💬 {{ $paste->comments_count }} comments
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

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
            <p class="{{ $mutedClass }} text-lg">No public pastes with this tag yet.</p>
        </div>
    @endif
</div>
@endsection
