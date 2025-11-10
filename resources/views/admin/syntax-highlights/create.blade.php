@extends('layouts.app')

@section('title', 'Add Syntax Highlight - CopyPasteHouse')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6">
    <div class="bg-gray-800 rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Add Syntax Highlight</h2>
            <a href="{{ route('syntax-highlights.index') }}" class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded text-sm">
                ← Back
            </a>
        </div>

        @if($errors->any())
            <div class="bg-red-900 border border-red-700 text-red-200 px-4 py-3 mb-4 rounded">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('syntax-highlights.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium mb-2">Name:</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="e.g., JavaScript"
                    required
                    maxlength="50"
                    class="w-full bg-gray-900 border border-gray-600 text-white px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                >
                <div class="text-xs text-gray-400 mt-1">The display name (e.g., "JavaScript", "Python")</div>
            </div>

            <div>
                <label for="extension" class="block text-sm font-medium mb-2">Extension:</label>
                <input 
                    type="text" 
                    id="extension" 
                    name="extension"
                    value="{{ old('extension') }}"
                    placeholder="e.g., javascript"
                    required
                    maxlength="50"
                    class="w-full bg-gray-900 border border-gray-600 text-white px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded font-mono"
                >
                <div class="text-xs text-gray-400 mt-1">The technical value used by highlight.js (e.g., "javascript", "python")</div>
            </div>

            <div class="flex gap-2 pt-4">
                <button 
                    type="submit"
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded font-medium"
                >
                    Create Syntax Highlight
                </button>
                <a 
                    href="{{ route('syntax-highlights.index') }}"
                    class="flex-1 bg-gray-700 hover:bg-gray-600 text-white px-6 py-3 rounded font-medium text-center"
                >
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
