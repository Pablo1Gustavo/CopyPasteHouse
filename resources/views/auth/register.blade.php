@extends('layouts.app')

@section('title', 'Register - CopyPasteHouse')

@section('content')
<div class="max-w-md mx-auto px-4">
        <div class="bg-gray-800 rounded-lg p-6">
            <h2 class="text-xl font-bold mb-6">Sign Up</h2>
            
            @if($errors->any())
                <div class="bg-red-900 border border-red-700 text-red-200 px-4 py-3 mb-4 rounded">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
                @csrf
                
                <div>
                    <label for="username" class="block text-sm font-medium mb-2">Username:</label>
                    <input 
                        type="text" 
                        name="username" 
                        id="username" 
                        value="{{ old('username') }}"
                        required
                        class="w-full bg-gray-900 border border-gray-600 text-white px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                    >
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium mb-2">Email:</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email') }}"
                        required
                        class="w-full bg-gray-900 border border-gray-600 text-white px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium mb-2">Password:</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required
                        class="w-full bg-gray-900 border border-gray-600 text-white px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                    >
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium mb-2">Confirm Password:</label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        id="password_confirmation" 
                        required
                        class="w-full bg-gray-900 border border-gray-600 text-white px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                    >
                </div>

                <div class="pt-4">
                    <button 
                        type="submit" 
                        class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded font-medium uppercase"
                    >
                        Sign Up
                    </button>
                </div>
            </form>

            <p class="text-sm text-gray-400 mt-6 text-center">
                Already have an account? 
                <a href="{{ route('login') }}" class="text-blue-400 hover:text-blue-300">Login here</a>
            </p>
        </div>
    </div>
@endsection
