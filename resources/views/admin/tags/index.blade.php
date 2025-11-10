@extends('layouts.app')

@section('title', 'Manage Tags')

@section('content')
@php
    $userSettings = auth()->check() ? auth()->user()->settings : null;
    $isLight = $userSettings?->theme === 'light';
    $cardClass = $isLight ? 'bg-white border border-gray-200' : 'bg-gray-800';
    $textClass = $isLight ? 'text-gray-900' : 'text-white';
    $mutedClass = $isLight ? 'text-gray-600' : 'text-gray-400';
    $inputClass = $isLight ? 'bg-white border-gray-300 text-gray-900' : 'bg-gray-900 border-gray-700 text-white';
    $buttonClass = $isLight ? 'bg-blue-600 hover:bg-blue-700' : 'bg-blue-600 hover:bg-blue-700';
@endphp

<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold {{ $textClass }}">📌 Manage Tags</h1>
        <a href="{{ route('tags.create') }}" class="{{ $buttonClass }} text-white px-4 py-2 rounded-lg font-semibold transition">
            + Create New Tag
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-600 text-white p-4 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-600 text-white p-4 rounded-lg mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="{{ $cardClass }} rounded-lg overflow-hidden">
        <table class="w-full">
            <thead class="{{ $isLight ? 'bg-gray-50' : 'bg-gray-900' }}">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium {{ $mutedClass }} uppercase tracking-wider">Tag</th>
                    <th class="px-6 py-3 text-left text-xs font-medium {{ $mutedClass }} uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium {{ $mutedClass }} uppercase tracking-wider">Creator</th>
                    <th class="px-6 py-3 text-left text-xs font-medium {{ $mutedClass }} uppercase tracking-wider">Pastes</th>
                    <th class="px-6 py-3 text-left text-xs font-medium {{ $mutedClass }} uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium {{ $mutedClass }} uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y {{ $isLight ? 'divide-gray-200' : 'divide-gray-700' }}">
                @forelse($tags as $tag)
                    <tr class="{{ $isLight ? 'hover:bg-gray-50' : 'hover:bg-gray-700' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold text-white" style="background-color: {{ $tag->color }}">
                                {{ $tag->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 {{ $textClass }}">
                            {{ Str::limit($tag->description ?? 'No description', 50) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap {{ $mutedClass }} text-sm">
                            {{ $tag->user ? $tag->user->username : 'System' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap {{ $textClass }}">
                            {{ $tag->pastes_count }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap {{ $mutedClass }} text-sm">
                            {{ $tag->created_at->format('Y-m-d') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex gap-2">
                                <a href="{{ route('tags.show', $tag->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded">View</a>
                                @if(auth()->user()->is_admin || auth()->id() === $tag->user_id)
                                    <a href="{{ route('tags.edit', $tag->id) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded">Edit</a>
                                    <form action="{{ route('tags.destroy', $tag->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded" onclick="return confirm('Are you sure you want to delete this tag?')">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center {{ $mutedClass }}">
                            No tags found. Create your first tag!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tags->hasPages())
        <div class="mt-6">
            {{ $tags->links() }}
        </div>
    @endif
</div>
@endsection
