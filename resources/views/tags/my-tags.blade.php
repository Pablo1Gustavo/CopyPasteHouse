@extends('layouts.app')

@section('title', 'My Tags - CopyPasteHouse')

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
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-2xl font-bold {{ $textClass }}">🏷️ My Tags</h2>
                <p class="{{ $mutedClass }} text-sm mt-1">Manage tags you've created</p>
            </div>
            <a href="{{ route('tags.create') }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-medium">
                + Create Tag
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-900 border border-green-700 text-green-200 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if($tags->count() > 0)
        <div class="{{ $cardClass }} rounded-lg overflow-hidden">
            <table class="w-full">
                <thead class="{{ $isLight ? 'bg-gray-100' : 'bg-gray-900' }}">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium {{ $textClass }} uppercase tracking-wider">Tag</th>
                        <th class="px-6 py-3 text-left text-xs font-medium {{ $textClass }} uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium {{ $textClass }} uppercase tracking-wider">Pastes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium {{ $textClass }} uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium {{ $textClass }} uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y {{ $isLight ? 'divide-gray-200' : 'divide-gray-700' }}">
                    @foreach($tags as $tag)
                        <tr class="{{ $isLight ? 'hover:bg-gray-50' : 'hover:bg-gray-700' }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center gap-2 text-white text-sm px-3 py-1 rounded-full" 
                                          style="background-color: {{ $tag->color }}">
                                        {{ $tag->name }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 {{ $mutedClass }} text-sm">
                                {{ Str::limit($tag->description ?? 'No description', 50) }}
                            </td>
                            <td class="px-6 py-4 {{ $textClass }} text-sm">
                                {{ $tag->pastes_count }}
                            </td>
                            <td class="px-6 py-4 {{ $mutedClass }} text-sm">
                                {{ $tag->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm space-x-2">
                                <a href="{{ route('tags.public.show', $tag->slug) }}" 
                                   class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded">
                                    View
                                </a>
                                <a href="{{ route('tags.edit', $tag) }}" 
                                   class="inline-block bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded">
                                    Edit
                                </a>
                                <form action="{{ route('tags.destroy', $tag) }}" 
                                      method="POST" 
                                      class="inline-block"
                                      onsubmit="return confirm('Are you sure you want to delete this tag? This will remove it from all pastes.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $tags->links() }}
        </div>
    @else
        <div class="{{ $cardClass }} rounded-lg p-12 text-center">
            <p class="{{ $mutedClass }} text-lg mb-4">You haven't created any tags yet.</p>
            <a href="{{ route('tags.create') }}" 
               class="inline-block bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded font-medium">
                Create Your First Tag
            </a>
        </div>
    @endif
</div>
@endsection
