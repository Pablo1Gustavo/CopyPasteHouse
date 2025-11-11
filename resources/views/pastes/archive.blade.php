@extends('layouts.app')

@section('title', 'Public Pastes - CopyPasteHouse')

@section('content')
@php
    $isLight = auth()->check() && auth()->user()->settings && auth()->user()->settings->theme === 'light';
    $cardClass = $isLight ? 'bg-white border border-gray-300' : 'bg-gray-800';
    $textClass = $isLight ? 'text-gray-900' : 'text-white';
    $mutedClass = $isLight ? 'text-gray-600' : 'text-gray-400';
    $borderClass = $isLight ? 'border-gray-300' : 'border-gray-600';
    $inputClass = $isLight ? 'bg-white text-gray-900 border-gray-300' : 'bg-gray-700 text-white border-gray-600';
    $buttonClass = $isLight ? 'bg-gray-200 hover:bg-gray-300 text-gray-900' : 'bg-gray-700 hover:bg-gray-600 text-white';
@endphp
<div class="max-w-6xl mx-auto px-4 py-6">
    <div class="{{ $cardClass }} rounded-lg p-6 mb-6">
        <h2 class="text-2xl font-bold mb-2 {{ $textClass }}">📚 Public Pastes Archive</h2>
        <p class="{{ $mutedClass }} text-sm">Browse recent public pastes shared by the community</p>
        
        <!-- Search and Filters -->
        <form method="GET" action="{{ route('pastes.archive') }}" class="mt-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search Bar -->
                <div class="md:col-span-3">
                    <div class="relative">
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}"
                            placeholder="🔍 Search by title or content..."
                            class="w-full {{ $inputClass }} border px-4 py-2 rounded focus:outline-none focus:border-blue-500"
                        >
                        @if(request('search'))
                            <a href="{{ route('pastes.archive') }}" 
                               class="absolute right-3 top-1/2 -translate-y-1/2 {{ $mutedClass }} hover:text-red-500"
                               title="Clear search">
                                ✕
                            </a>
                        @endif
                    </div>
                </div>
                
                <!-- Tag Filter -->
                <div>
                    <select 
                        name="tag" 
                        class="w-full {{ $inputClass }} border px-4 py-2 rounded focus:outline-none focus:border-blue-500"
                        onchange="this.form.submit()"
                    >
                        <option value="">🏷️ All Tags</option>
                        @foreach($tags as $tag)
                            <option value="{{ $tag->slug }}" {{ request('tag') === $tag->slug ? 'selected' : '' }}>
                                {{ $tag->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Syntax Filter -->
                <div>
                    <select 
                        name="syntax" 
                        class="w-full {{ $inputClass }} border px-4 py-2 rounded focus:outline-none focus:border-blue-500"
                        onchange="this.form.submit()"
                    >
                        <option value="">📄 All Languages</option>
                        @foreach($syntaxHighlights as $syntax)
                            <option value="{{ $syntax->id }}" {{ request('syntax') == $syntax->id ? 'selected' : '' }}>
                                {{ $syntax->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Submit/Clear Buttons -->
                <div class="flex gap-2">
                    <button 
                        type="submit"
                        class="flex-1 {{ $buttonClass }} px-4 py-2 rounded font-medium"
                    >
                        Search
                    </button>
                    @if(request()->hasAny(['search', 'tag', 'syntax']))
                        <a 
                            href="{{ route('pastes.archive') }}"
                            class="{{ $buttonClass }} px-4 py-2 rounded font-medium text-center"
                        >
                            Clear
                        </a>
                    @endif
                </div>
            </div>
        </form>
        
        <!-- Active Filters Display -->
        @if(request()->hasAny(['search', 'tag', 'syntax']))
            <div class="mt-4 flex flex-wrap gap-2 text-sm">
                <span class="{{ $mutedClass }}">Active filters:</span>
                @if(request('search'))
                    <span class="{{ $isLight ? 'bg-blue-100 text-blue-800' : 'bg-blue-900 text-blue-200' }} px-2 py-1 rounded">
                        Search: "{{ request('search') }}"
                    </span>
                @endif
                @if(request('tag'))
                    @php
                        $activeTag = $tags->firstWhere('slug', request('tag'));
                    @endphp
                    @if($activeTag)
                        <span class="text-white px-2 py-1 rounded" style="background-color: {{ $activeTag->color }}">
                            Tag: {{ $activeTag->name }}
                        </span>
                    @endif
                @endif
                @if(request('syntax'))
                    @php
                        $activeSyntax = $syntaxHighlights->firstWhere('id', request('syntax'));
                    @endphp
                    @if($activeSyntax)
                        <span class="{{ $isLight ? 'bg-gray-200 text-gray-800' : 'bg-gray-700 text-gray-200' }} px-2 py-1 rounded">
                            Language: {{ $activeSyntax->name }}
                        </span>
                    @endif
                @endif
            </div>
        @endif
    </div>

    @if($pastes->count() > 0)
        <!-- Results Count -->
        <div class="mb-4 {{ $mutedClass }} text-sm">
            Showing {{ $pastes->firstItem() }} to {{ $pastes->lastItem() }} of {{ $pastes->total() }} pastes
        </div>
        
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
                    
                    @if($paste->tags && $paste->tags->count() > 0)
                        <div class="flex flex-wrap gap-2 mt-3">
                            @foreach($paste->tags as $tag)
                                <a 
                                    href="{{ route('pastes.archive', ['tag' => $tag->slug]) }}" 
                                    class="inline-flex items-center gap-1 text-white text-xs px-3 py-1 rounded-full hover:opacity-80 transition" 
                                    style="background-color: {{ $tag->color }}"
                                    title="{{ $tag->description ?: $tag->name }}">
                                    {{ $tag->name }}
                                </a>
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
