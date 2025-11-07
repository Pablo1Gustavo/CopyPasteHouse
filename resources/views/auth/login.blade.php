<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CopyPasteHouse</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white">
    <!-- Header -->
    <div class="bg-gray-800 text-white py-3 px-4 mb-6">
        <div class="max-w-4xl mx-auto flex items-center justify-between">
            <h1 class="text-xl font-bold">CopyPasteHouse</h1>
            <div class="text-sm">
                <a href="{{ route('register') }}" class="text-gray-300 hover:text-white">Sign Up</a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-md mx-auto px-4">
        <h2 class="text-2xl font-bold mb-4">Login</h2>
        
        @if($errors->any())
            <div class="bg-red-50 border border-red-300 text-red-800 px-3 py-2 mb-4 text-sm">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-50 border border-green-300 text-green-800 px-3 py-2 mb-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf
            
            <div>
                <label for="login" class="block text-sm font-medium mb-1">Username or Email:</label>
                <input 
                    type="text" 
                    name="login" 
                    id="login" 
                    value="{{ old('login') }}"
                    required
                    class="w-full border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-gray-500"
                >
                @error('login')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium mb-1">Password:</label>
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    required
                    class="w-full border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-gray-500"
                >
            </div>

            <div>
                <label class="flex items-center text-sm">
                    <input type="checkbox" name="remember" class="mr-2">
                    Remember me
                </label>
            </div>

            <div>
                <button 
                    type="submit" 
                    class="bg-gray-700 hover:bg-gray-800 text-white px-6 py-2 text-sm font-medium"
                >
                    Login
                </button>
            </div>
        </form>

        <p class="text-sm text-gray-600 mt-4">
            Don't have an account? 
            <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Sign up here</a>
        </p>
    </div>
</body>
</html>
