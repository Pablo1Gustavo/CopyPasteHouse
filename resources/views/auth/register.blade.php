<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CopyPasteHouse</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white">
    <!-- Header -->
    <div class="bg-gray-800 text-white py-3 px-4 mb-6">
        <div class="max-w-4xl mx-auto flex items-center justify-between">
            <h1 class="text-xl font-bold">CopyPasteHouse</h1>
            <div class="text-sm">
                <a href="{{ route('login') }}" class="text-gray-300 hover:text-white">Login</a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-md mx-auto px-4">
        <h2 class="text-2xl font-bold mb-4">Sign Up</h2>
        
        @if($errors->any())
            <div class="bg-red-50 border border-red-300 text-red-800 px-3 py-2 mb-4 text-sm">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
            @csrf
            
            <div>
                <label for="username" class="block text-sm font-medium mb-1">Username:</label>
                <input 
                    type="text" 
                    name="username" 
                    id="username" 
                    value="{{ old('username') }}"
                    required
                    class="w-full border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-gray-500"
                >
            </div>

            <div>
                <label for="email" class="block text-sm font-medium mb-1">Email:</label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    value="{{ old('email') }}"
                    required
                    class="w-full border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-gray-500"
                >
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
                <label for="password_confirmation" class="block text-sm font-medium mb-1">Confirm Password:</label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    id="password_confirmation" 
                    required
                    class="w-full border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-gray-500"
                >
            </div>

            <div>
                <button 
                    type="submit" 
                    class="bg-gray-700 hover:bg-gray-800 text-white px-6 py-2 text-sm font-medium"
                >
                    Sign Up
                </button>
            </div>
        </form>

        <p class="text-sm text-gray-600 mt-4">
            Already have an account? 
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Login here</a>
        </p>
    </div>
</body>
</html>
