@extends('layouts.app')

@section('title', 'Browse Tags - CopyPasteHouse')

@section('content')
@php
    $isLight = auth()->check() && auth()->user()->settings && auth()->user()->settings->theme === 'light';
    $cardClass = $isLight ? 'bg-white border border-gray-300' : 'bg-gray-800';
    $textClass = $isLight ? 'text-gray-900' : 'text-white';
    $mutedClass = $isLight ? 'text-gray-600' : 'text-gray-400';
@endphp

<div class="max-w-6xl mx-auto px-4 py-6">
    <div class="{{ $cardClass }} rounded-lg p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold {{ $textClass }}">🏷️ Browse Tags</h2>
                <p class="{{ $mutedClass }} text-sm mt-1">Explore popular tags and find pastes</p>
            </div>
        </div>
    </div>

    @if($tags->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($tags as $tag)
                <a href="{{ route('tags.public.show', $tag->slug) }}" 
                   class="{{ $cardClass }} rounded-lg p-6 hover:shadow-lg transition group">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-block w-3 h-3 rounded-full" style="background-color: {{ $tag->color }}"></span>
                                <h3 class="text-lg font-bold {{ $textClass }} group-hover:text-blue-500 transition">
                                    {{ $tag->name }}
                                </h3>
                            </div>
                            @if($tag->description)
                                <p class="{{ $mutedClass }} text-sm mb-3">{{ Str::limit($tag->description, 80) }}</p>
                            @endif
                            <div class="flex items-center gap-4 text-xs {{ $mutedClass }}">
                                <span>📝 {{ $tag->pastes_count }} {{ Str::plural('paste', $tag->pastes_count) }}</span>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="{{ $cardClass }} rounded-lg p-12 text-center">
            <p class="{{ $textClass }} text-lg mb-2">No tags with pastes found</p>
            <p class="{{ $mutedClass }}">Tags will appear here once pastes are tagged</p>
        </div>
    @endif
</div>
@endsection
