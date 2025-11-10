@extends('layouts.app')

@section('title', 'Password Required - CopyPasteHouse')

@section('content')
<div class="max-w-md mx-auto px-4">
        <div class="bg-gray-800 rounded-lg p-6">
            <h2 class="text-xl font-bold mb-4">🔒 Password Required</h2>
            <p class="text-gray-400 text-sm mb-6">This paste is password protected. Enter the password to view it.</p>
            
            @if($errors->any())
                <div class="bg-red-900 border border-red-700 text-red-200 px-4 py-3 mb-4 rounded">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="GET" action="{{ route('pastes.show', $paste->id) }}" class="space-y-4">
                <div>
                    <label for="password" class="block text-sm font-medium mb-2">Password:</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required
                        autofocus
                        class="w-full bg-gray-900 border border-gray-600 text-white px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                    >
                </div>

                <div class="pt-4">
                    <button 
                        type="submit" 
                        class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded font-medium uppercase"
                    >
                        Unlock Paste
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
