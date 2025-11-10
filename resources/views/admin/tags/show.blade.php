@extends('layouts.app')

@section('title', 'Tag: ' . $tag->name)

@section('content')
@php
    $userSettings = auth()->check() ? auth()->user()->settings : null;
    $isLight = $userSettings?->theme === 'light';
    $cardClass = $isLight ? 'bg-white border border-gray-200' : 'bg-gray-800';
    $textClass = $isLight ? 'text-gray-900' : 'text-white';
    $mutedClass = $isLight ? 'text-gray-600' : 'text-gray-400';
@endphp

<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('tags.index') }}" class="text-blue-400 hover:text-blue-300">← Back to Tags</a>
    </div>

    <div class="{{ $cardClass }} rounded-lg p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-semibold text-white" style="background-color: {{ $tag->color }}">
                    {{ $tag->name }}
                </span>
                <p class="{{ $mutedClass }} mt-2">{{ $tag->description ?? 'No description' }}</p>
                <p class="{{ $mutedClass }} text-sm mt-2">Slug: {{ $tag->slug }}</p>
            </div>
            <div class="text-right">
                <p class="{{ $textClass }} text-2xl font-bold">{{ $tag->pastes_count }}</p>
                <p class="{{ $mutedClass }} text-sm">Pastes</p>
            </div>
        </div>
    </div>

    <h2 class="text-2xl font-bold mb-4 {{ $textClass }}">Pastes with this tag</h2>

    @if($pastes->count() > 0)
        <div class="space-y-4">
            @foreach($pastes as $paste)
                <div class="{{ $cardClass }} rounded-lg p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <a href="{{ route('pastes.show', $paste->id) }}" class="text-blue-400 hover:text-blue-300 font-semibold text-lg">
                                {{ $paste->title }}
                            </a>
                            <p class="{{ $mutedClass }} text-sm mt-1">
                                by {{ $paste->user->username ?? 'Anonymous' }} • 
                                {{ $paste->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <div class="flex items-center gap-4 text-sm {{ $mutedClass }}">
                            <span>❤️ {{ $paste->likes_count }}</span>
                            <span>💬 {{ $paste->comments_count }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($pastes->hasPages())
            <div class="mt-6">
                {{ $pastes->links() }}
            </div>
        @endif
    @else
        <div class="{{ $cardClass }} rounded-lg p-8 text-center">
            <p class="{{ $mutedClass }}">No pastes found with this tag.</p>
        </div>
    @endif
</div>
@endsection
