@extends('layouts.app')

@section('title', 'Login - CopyPasteHouse')

@section('content')
<div class="max-w-md mx-auto px-4">
        <div class="bg-gray-800 rounded-lg p-6">
            <h2 class="text-xl font-bold mb-6">Login</h2>
            
            @if($errors->any())
                <div class="bg-red-900 border border-red-700 text-red-200 px-4 py-3 mb-4 rounded">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-900 border border-green-700 text-green-200 px-4 py-3 mb-4 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf
                
                <div>
                    <label for="login" class="block text-sm font-medium mb-2">Username or Email:</label>
                    <input 
                        type="text" 
                        name="login" 
                        id="login" 
                        value="{{ old('login') }}"
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
                    <label class="flex items-center text-sm cursor-pointer">
                        <input type="checkbox" name="remember" class="mr-2 w-4 h-4">
                        Remember me
                    </label>
                </div>

                <div class="pt-4">
                    <button 
                        type="submit" 
                        class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded font-medium uppercase"
                    >
                        Login
                    </button>
                </div>
            </form>

            <p class="text-sm text-gray-400 mt-6 text-center">
                Don't have an account? 
                <a href="{{ route('register') }}" class="text-blue-400 hover:text-blue-300">Sign up here</a>
            </p>
        </div>
    </div>
@endsection
