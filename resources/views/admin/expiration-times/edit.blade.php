@extends('layouts.app')

@section('title', 'Edit Expiration Time - CopyPasteHouse')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6">
    <div class="bg-gray-800 rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Edit Expiration Time</h2>
            <a href="{{ route('expiration-times.index') }}" class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded text-sm">
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

        <form method="POST" action="{{ route('expiration-times.update', $expirationTime) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="label" class="block text-sm font-medium mb-2">Label:</label>
                <input 
                    type="text" 
                    id="label" 
                    name="label"
                    value="{{ old('label', $expirationTime->label) }}"
                    required
                    maxlength="50"
                    class="w-full bg-gray-900 border border-gray-600 text-white px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                >
                <div class="text-xs text-gray-400 mt-1">The display name</div>
            </div>

            <div>
                <label for="minutes" class="block text-sm font-medium mb-2">Minutes:</label>
                <input 
                    type="number" 
                    id="minutes" 
                    name="minutes"
                    value="{{ old('minutes', $expirationTime->minutes) }}"
                    required
                    min="1"
                    class="w-full bg-gray-900 border border-gray-600 text-white px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                >
                <div class="text-xs text-gray-400 mt-1">Number of minutes</div>
            </div>

            <div class="flex gap-2 pt-4">
                <button 
                    type="submit"
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded font-medium"
                >
                    Update Expiration Time
                </button>
                <a 
                    href="{{ route('expiration-times.index') }}"
                    class="flex-1 bg-gray-700 hover:bg-gray-600 text-white px-6 py-3 rounded font-medium text-center"
                >
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
