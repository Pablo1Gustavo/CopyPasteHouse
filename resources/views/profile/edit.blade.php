<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - CopyPasteHouse</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 text-white">

    <div class="bg-gray-800 py-4 px-4 mb-8">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <a href="{{ route('pastes.create') }}" class="text-xl font-bold text-white hover:text-gray-300">CopyPasteHouse</a>
            <div class="flex items-center gap-4 text-sm">
                
                <a href="{{ route('pastes.create') }}" class="border border-green-500 text-green-500 px-4 py-2 hover:bg-green-500 hover:text-white transition">
                    + NEW PASTE
                </a>
                <a href="{{ route('pastes.index') }}" class="border border-gray-400 px-4 py-2 hover:bg-gray-700 transition">
                    MY PASTES
                </a>

                <a href="{{ route('profile.edit') }}" class="border border-blue-400 text-blue-400 px-4 py-2 hover:bg-blue-400 hover:text-white transition">
                    MY PROFILE
                </a>

                <span class="text-gray-300">{{ auth()->user()->username }}</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-gray-300 hover:text-white">Logout</button>
                </form>
            </div>
        </div>
    </div>

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

        <div class="bg-gray-800 rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-6">Edit Profile</h2>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('PUT') <div>
                    <label for="username" class="block text-sm font-medium mb-2">Username:</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username"
                        value="{{ old('username', auth()->user()->username) }}"
                        required
                        maxlength="50"
                        class="w-full bg-gray-900 border border-gray-600 text-white px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                    >
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium mb-2">Email:</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email"
                        value="{{ old('email', auth()->user()->email) }}"
                        required
                        class="w-full bg-gray-900 border border-gray-600 text-white px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                    >
                </div>
                
                <div class="flex pt-4">
                    <button 
                        type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded font-medium"
                    >
                        Update Profile
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-gray-800 rounded-lg p-6">
            <h2 class="text-xl font-bold mb-6">Change Password</h2>

            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-sm font-medium mb-2">Current Password:</label>
                    <input 
                        type="password" 
                        id="current_password" 
                        name="current_password"
                        required
                        class="w-full bg-gray-900 border border-gray-600 text-white px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium mb-2">New Password:</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password"
                        required
                        minlength="8"
                        class="w-full bg-gray-900 border border-gray-600 text-white px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                    >
                    <div class="text-xs text-gray-400 mt-1">Min 8 characters</div>
                </div>

                 <div>
                    <label for="password_confirmation" class="block text-sm font-medium mb-2">Confirm New Password:</label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation"
                        required
                        class="w-full bg-gray-900 border border-gray-600 text-white px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                    >
                </div>
                
                <div class="flex pt-4">
                    <button 
                        type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded font-medium"
                    >
                        Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>