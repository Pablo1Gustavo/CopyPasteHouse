@extends('layouts.app')

@section('title', 'Edit Comment')

@section('content')
@php
    $userSettings = auth()->check() ? auth()->user()->settings : null;
    $isLight = $userSettings?->theme === 'light';
    $cardClass = $isLight ? 'bg-white border border-gray-200' : 'bg-gray-800';
    $textClass = $isLight ? 'text-gray-900' : 'text-white';
    $mutedClass = $isLight ? 'text-gray-600' : 'text-gray-400';
    $inputClass = $isLight ? 'bg-white border-gray-300 text-gray-900' : 'bg-gray-900 border-gray-700 text-white';
    $labelClass = $isLight ? 'text-gray-700' : 'text-gray-300';
@endphp

<div class="max-w-4xl mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('pastes.show', $comment->paste_id) }}" class="text-blue-400 hover:text-blue-300">← Back to Paste</a>
    </div>

    <h1 class="text-3xl font-bold mb-6 {{ $textClass }}">✏️ Edit Comment</h1>

    @if($errors->any())
        <div class="bg-red-600 text-white p-4 rounded-lg mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('comments.update', $comment->id) }}" method="POST" class="{{ $cardClass }} rounded-lg p-6">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="content" class="block text-sm font-semibold mb-2 {{ $labelClass }}">Comment Content *</label>
            <textarea id="content" 
                      name="content" 
                      rows="8" 
                      class="w-full px-4 py-2 border rounded-lg font-mono {{ $inputClass }}"
                      required
                      maxlength="10000">{{ old('content', $comment->content) }}</textarea>
            <p class="{{ $mutedClass }} text-sm mt-1">Edit your comment (max 10,000 characters)</p>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                Update Comment
            </button>
            <a href="{{ route('pastes.show', $comment->paste_id) }}" class="{{ $isLight ? 'bg-gray-200 text-gray-800 hover:bg-gray-300' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }} px-6 py-2 rounded-lg font-semibold transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
