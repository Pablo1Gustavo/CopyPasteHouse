@extends('layouts.app')

@section('title', 'Create Paste - CopyPasteHouse')

@section('content')
@php
    $isLight = auth()->check() && auth()->user()->settings && auth()->user()->settings->theme === 'light';
    $cardClass = $isLight ? 'bg-white border border-gray-300' : 'bg-gray-800';
    $inputClass = $isLight ? 'bg-gray-50 border-gray-300 text-gray-900' : 'bg-gray-900 border-gray-600 text-white';
    $textClass = $isLight ? 'text-gray-900' : 'text-white';
    $mutedClass = $isLight ? 'text-gray-600' : 'text-gray-400';
    // Special class for code/paste content - needs high contrast
    $codeInputClass = $isLight ? 'bg-white border-gray-400 text-gray-900' : 'bg-gray-900 border-gray-600 text-white';
@endphp
<div class="max-w-4xl mx-auto px-4 py-6">
        @if(session('success'))
            <div class="bg-green-900 border border-green-700 text-green-200 px-4 py-3 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-900 border border-red-700 text-red-200 px-4 py-3 mb-4 rounded">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="{{ $cardClass }} rounded-lg p-6">
            <h2 class="text-xl font-bold mb-6 {{ $textClass }}">Create a new paste</h2>

        <form method="POST" action="{{ route('pastes.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="content" class="block text-sm font-medium mb-2 {{ $textClass }}">Paste Content:</label>
                <textarea 
                    id="content" 
                    name="content"
                    rows="15"
                    required
                    spellcheck="false"
                    placeholder="Enter your text here..."
                    class="w-full {{ $codeInputClass }} border px-4 py-3 text-sm font-mono focus:outline-none focus:border-gray-400 rounded"
                >{{ old('content') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="title" class="block text-sm font-medium mb-2 {{ $textClass }}">Title:</label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title"
                        value="{{ old('title') }}"
                        placeholder="Untitled"
                        required
                        maxlength="50"
                        class="w-full {{ $inputClass }} px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                    >
                    <div class="text-xs {{ $mutedClass }} mt-1">Max 50 characters</div>
                </div>

                <div>
                    <label for="syntax_highlight_id" class="block text-sm font-medium mb-2 {{ $textClass }}">Syntax Highlighting:</label>
                    <select 
                        id="syntax_highlight_id" 
                        name="syntax_highlight_id"
                        required
                        class="w-full {{ $inputClass }} px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                    >
                        @foreach($syntaxHighlights as $highlight)
                            <option value="{{ $highlight->id }}" {{ old('syntax_highlight_id') == $highlight->id ? 'selected' : '' }}>
                                {{ $highlight->label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label for="tags" class="block text-sm font-medium mb-2 {{ $textClass }}">Tags:</label>
                <input 
                    type="text" 
                    id="tags" 
                    name="tags"
                    value="{{ old('tags') }}"
                    placeholder="tag1, tag2, tag3"
                    maxlength="255"
                    class="w-full {{ $inputClass }} px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                >
                <div class="text-xs {{ $mutedClass }} mt-1">Comma separated (max 10 tags)</div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="expiration" class="block text-sm font-medium mb-2 {{ $textClass }}">Expiration:</label>
                    <select 
                        id="expiration" 
                        name="expiration"
                        class="w-full {{ $inputClass }} px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                    >
                        <option value="">Never</option>
                        @foreach($expirationTimes as $expTime)
                            <option value="{{ $expTime->minutes }}" {{ old('expiration') == $expTime->minutes ? 'selected' : '' }}>
                                {{ $expTime->label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 {{ $textClass }}">Exposure:</label>
                    <div class="flex items-center gap-4 mt-3">
                        <label class="flex items-center cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="listable" 
                                value="1"
                                {{ old('listable', true) ? 'checked' : '' }}
                                class="mr-2 w-4 h-4"
                            >
                            <span class="text-sm {{ $textClass }}">Listable</span>
                        </label>
                    </div>
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium mb-2 {{ $textClass }}">Password (optional):</label>
                <div class="flex gap-2">
                    <input 
                        type="password" 
                        id="password" 
                        name="password"
                        placeholder="Leave blank for no password"
                        minlength="8"
                        class="flex-1 {{ $inputClass }} px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                    >
                    <button 
                        type="button" 
                        onclick="generatePassword()"
                        class="{{ $isLight ? 'bg-gray-300 hover:bg-gray-400' : 'bg-gray-700 hover:bg-gray-600' }} px-4 py-2 rounded text-sm"
                        title="Generate random password"
                    >
                        🔄
                    </button>
                </div>
                <div class="text-xs {{ $mutedClass }} mt-1">Min 8 characters</div>
            </div>

            <div class="flex items-center gap-2">
                <input 
                    type="checkbox" 
                    id="destroy_on_open" 
                    name="destroy_on_open" 
                    value="1"
                    {{ old('destroy_on_open') ? 'checked' : '' }}
                    class="w-4 h-4"
                >
                <label for="destroy_on_open" class="text-sm {{ $textClass }}">🔥 Destroy on open (Burn After Reading)</label>
            </div>

            <div class="pt-4">
                <button 
                    type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded font-medium uppercase"
                >
                    Create the paste
                </button>
            </div>
        </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function generatePassword() {
            const password = Math.random().toString(36).slice(2) + Math.random().toString(36).slice(2);
            document.getElementById('password').type = 'text';
            document.getElementById('password').value = password;
        }
    </script>
    @endpush
@endsection
