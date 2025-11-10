@extends('layouts.app')

@section('title', 'Edit Tag')

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

<div class="max-w-3xl mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ auth()->user()->is_admin ? route('tags.index') : route('tags.my') }}" class="text-blue-400 hover:text-blue-300">← Back to Tags</a>
    </div>

    <h1 class="text-3xl font-bold mb-6 {{ $textClass }}">✏️ Edit Tag: {{ $tag->name }}</h1>

    @if($errors->any())
        <div class="bg-red-600 text-white p-4 rounded-lg mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('tags.update', $tag->id) }}" method="POST" class="{{ $cardClass }} rounded-lg p-6">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="name" class="block text-sm font-semibold mb-2 {{ $labelClass }}">Tag Name *</label>
            <input type="text" 
                   id="name" 
                   name="name" 
                   value="{{ old('name', $tag->name) }}"
                   class="w-full px-4 py-2 border rounded-lg {{ $inputClass }}" 
                   required 
                   maxlength="50">
            <p class="{{ $mutedClass }} text-sm mt-1">Enter a unique tag name (e.g., "Python", "Tutorial", "API")</p>
        </div>

        <div class="mb-4">
            <label for="description" class="block text-sm font-semibold mb-2 {{ $labelClass }}">Description</label>
            <textarea id="description" 
                      name="description" 
                      rows="3" 
                      class="w-full px-4 py-2 border rounded-lg {{ $inputClass }}"
                      maxlength="500">{{ old('description', $tag->description) }}</textarea>
            <p class="{{ $mutedClass }} text-sm mt-1">Optional description for this tag</p>
        </div>

        <div class="mb-6">
            <label for="color" class="block text-sm font-semibold mb-2 {{ $labelClass }}">Color *</label>
            <div class="flex items-center gap-4">
                <input type="color" 
                       id="color" 
                       name="color" 
                       value="{{ old('color', $tag->color) }}"
                       class="h-12 w-24 cursor-pointer rounded border {{ $isLight ? 'border-gray-300' : 'border-gray-700' }}" 
                       required>
                <div class="flex-1">
                    <input type="text" 
                           id="color_hex" 
                           value="{{ old('color', $tag->color) }}"
                           class="w-full px-4 py-2 border rounded-lg {{ $inputClass }}" 
                           pattern="^#[0-9A-Fa-f]{6}$"
                           readonly>
                    <p class="{{ $mutedClass }} text-sm mt-1">Choose a color for this tag badge</p>
                </div>
            </div>
        </div>

        @if(auth()->user()->is_admin)
        <div class="mb-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" 
                       name="is_public" 
                       value="1"
                       {{ old('is_public', $tag->is_public) ? 'checked' : '' }}
                       class="w-4 h-4">
                <span class="{{ $labelClass }} text-sm font-semibold">Public Tag</span>
            </label>
            <p class="{{ $mutedClass }} text-sm mt-1">If unchecked, only you will be able to see and use this tag</p>
        </div>
        @else
        <div class="mb-6 p-4 {{ $isLight ? 'bg-blue-50 border-blue-200' : 'bg-blue-900/20 border-blue-800' }} border rounded-lg">
            <p class="{{ $textClass }} text-sm">
                🔒 <strong>Note:</strong> Your tags are private and only visible to you.
            </p>
        </div>
        @endif

        <div class="flex gap-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                Update Tag
            </button>
            <a href="{{ auth()->user()->is_admin ? route('tags.index') : route('tags.my') }}" class="{{ $isLight ? 'bg-gray-200 text-gray-800 hover:bg-gray-300' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }} px-6 py-2 rounded-lg font-semibold transition">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
    // Sync color picker with hex input
    const colorPicker = document.getElementById('color');
    const colorHex = document.getElementById('color_hex');
    
    colorPicker.addEventListener('input', (e) => {
        colorHex.value = e.target.value.toUpperCase();
    });
    
    colorHex.addEventListener('input', (e) => {
        if (/^#[0-9A-Fa-f]{6}$/.test(e.target.value)) {
            colorPicker.value = e.target.value;
        }
    });
</script>
@endsection
