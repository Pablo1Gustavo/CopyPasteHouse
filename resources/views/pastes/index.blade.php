@extends('layouts.app')

@section('title', 'My Pastes - CopyPasteHouse')

@section('content')
@php
    $isLight = auth()->check() && auth()->user()->settings && auth()->user()->settings->theme === 'light';
    $cardClass = $isLight ? 'bg-white border border-gray-300' : 'bg-gray-800';
    $textClass = $isLight ? 'text-gray-900' : 'text-white';
    $mutedClass = $isLight ? 'text-gray-600' : 'text-gray-400';
    $borderClass = $isLight ? 'border-gray-300' : 'border-gray-600';
@endphp
<div class="max-w-6xl mx-auto px-4 py-6">
        @if(session('success'))
            <div class="bg-green-900 border border-green-700 text-green-200 px-4 py-3 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="{{ $cardClass }} rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold {{ $textClass }}">My Pastes</h2>
        </div>

        @if($pastes->isEmpty())
            <div class="{{ $cardClass }} rounded-lg p-12 text-center {{ $mutedClass }}">
                <p class="mb-4">You haven't created any pastes yet.</p>
                <a href="{{ route('pastes.create') }}" class="inline-block bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded font-medium uppercase">
                    + Create Your First Paste
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($pastes as $paste)
                    <div class="{{ $cardClass }} rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-lg font-bold mb-2">
                                    <a href="{{ route('pastes.show', $paste->id) }}" class="text-blue-400 hover:text-blue-300">
                                        {{ $paste->title }}
                                    </a>
                                </h3>
                                
                                <div class="flex flex-wrap gap-2 mb-3">
                                    @if($paste->syntaxHighlight)
                                        <span class="border {{ $borderClass }} {{ $textClass }} px-3 py-1 text-xs rounded">
                                            📄 {{ $paste->syntaxHighlight->name }}
                                        </span>
                                    @endif
                                    <span class="border {{ $borderClass }} {{ $textClass }} px-3 py-1 text-xs rounded">
                                        📅 {{ $paste->created_at->format('M d, Y') }}
                                    </span>
                                    @if($paste->expiration)
                                        <span class="border {{ $borderClass }} {{ $textClass }} px-3 py-1 text-xs rounded">
                                            ⏰ {{ $paste->expiration->diffForHumans() }}
                                        </span>
                                    @else
                                        <span class="border {{ $borderClass }} {{ $textClass }} px-3 py-1 text-xs rounded">
                                            ⏰ Never
                                        </span>
                                    @endif
                                </div>

                                @if($paste->tags && is_array($paste->tags))
                                    <div class="mb-3 flex flex-wrap gap-2">
                                        @foreach($paste->tags as $index => $tag)
                                            @php
                                                $colors = ['red', 'green', 'blue', 'orange', 'purple'];
                                                $colorIndex = $index % count($colors);
                                                $bgColors = [
                                                    'red' => 'bg-red-600',
                                                    'green' => 'bg-green-600',
                                                    'blue' => 'bg-blue-600',
                                                    'orange' => 'bg-orange-600',
                                                    'purple' => 'bg-purple-600'
                                                ];
                                                $bgColor = $bgColors[$colors[$colorIndex]];
                                            @endphp
                                            <span class="inline-flex items-center gap-1 {{ $isLight ? 'bg-gray-200 text-gray-900' : 'bg-gray-700 text-white' }} text-xs px-2 py-1 rounded">
                                                <span class="{{ $bgColor }} w-5 h-5 rounded-full flex items-center justify-center text-white text-xs uppercase">
                                                    {{ substr($tag, 0, 1) }}
                                                </span>
                                                {{ $tag }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="text-sm {{ $mutedClass }}">
                                    {{ $paste->likes_count ?? 0 }} likes • 
                                    {{ $paste->comments_count ?? 0 }} comments •
                                    {{ $paste->listable ? 'Public' : 'Unlisted' }}
                                </div>
                            </div>
                            
                            <div class="flex gap-2 ml-4">
                                <a 
                                    href="{{ route('pastes.edit', $paste->id) }}"
                                    class="{{ $isLight ? 'bg-gray-300 hover:bg-gray-400 text-gray-900' : 'bg-gray-700 hover:bg-gray-600 text-white' }} px-4 py-2 text-sm rounded"
                                >
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('pastes.destroy', $paste->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this paste?');">
                                    @csrf
                                    @method('DELETE')
                                    <button 
                                        type="submit"
                                        class="bg-red-700 hover:bg-red-600 text-white px-4 py-2 text-sm rounded"
                                    >
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 text-gray-300">
                {{ $pastes->links() }}
            </div>
        @endif
    </div>
@endsection
