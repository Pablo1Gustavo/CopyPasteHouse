<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Required - CopyPasteHouse</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 text-white">
    <div class="bg-gray-800 py-4 px-4 mb-8">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <h1 class="text-2xl font-bold">
                <a href="{{ route('pastes.create') }}" class="text-white hover:text-gray-300">CopyPasteHouse</a>
            </h1>
            <div class="flex items-center gap-4 text-sm">
                <a href="{{ route('pastes.create') }}" class="border border-green-500 text-green-500 px-4 py-2 hover:bg-green-500 hover:text-white transition uppercase">
                    + New Paste
                </a>
                @auth
                    <a href="{{ route('pastes.index') }}" class="border border-gray-400 px-4 py-2 hover:bg-gray-700 transition uppercase">
                        My Pastes
                    </a>
                    <a href="{{ route('profile.edit') }}" class="text-gray-300 hover:text-white">
                        {{ auth()->user()->username }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-300 hover:text-white">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-300 hover:text-white">Login</a>
                    <a href="{{ route('register') }}" class="text-gray-300 hover:text-white">Sign up</a>
                @endauth
            </div>
        </div>
    </div>

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
</body>
</html>
