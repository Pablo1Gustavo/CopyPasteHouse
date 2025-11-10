<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Paste - CopyPasteHouse</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 text-white">
    <!-- Header -->
    <div class="bg-gray-800 py-4 px-4 mb-8">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <a href="{{ route('pastes.create') }}" class="text-2xl font-bold text-white hover:text-gray-300">CopyPasteHouse</a>
            <div class="flex items-center gap-4 text-sm">
                <a href="{{ route('pastes.archive') }}" class="border border-blue-500 text-blue-500 px-4 py-2 hover:bg-blue-500 hover:text-white transition uppercase">
                    📚 Public Pastes
                </a>
                <a href="{{ route('pastes.create') }}" class="border border-green-500 text-green-500 px-4 py-2 hover:bg-green-500 hover:text-white transition uppercase">
                    + New Paste
                </a>
                @auth
                    <a href="{{ route('pastes.index') }}" class="border border-gray-400 px-4 py-2 hover:bg-gray-700 transition uppercase">
                        My Pastes
                    </a>
                    <span class="text-gray-300">{{ auth()->user()->username }}</span>
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

    <!-- Main Content -->
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

        <div class="bg-gray-800 rounded-lg p-6">
            <h2 class="text-xl font-bold mb-6">Create a new paste</h2>

        <form method="POST" action="{{ route('pastes.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="content" class="block text-sm font-medium mb-2">Paste Content:</label>
                <textarea 
                    id="content" 
                    name="content"
                    rows="15"
                    required
                    spellcheck="false"
                    placeholder="Enter your text here..."
                    class="w-full bg-gray-900 border border-gray-600 text-white px-4 py-3 text-sm font-mono focus:outline-none focus:border-gray-400 rounded"
                >{{ old('content') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="title" class="block text-sm font-medium mb-2">Title:</label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title"
                        value="{{ old('title') }}"
                        placeholder="Untitled"
                        required
                        maxlength="50"
                        class="w-full bg-gray-900 border border-gray-600 text-white px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                    >
                    <div class="text-xs text-gray-400 mt-1">Max 50 characters</div>
                </div>

                <div>
                    <label for="syntax_highlight_id" class="block text-sm font-medium mb-2">Syntax Highlighting:</label>
                    <select 
                        id="syntax_highlight_id" 
                        name="syntax_highlight_id"
                        required
                        class="w-full bg-gray-900 border border-gray-600 text-white px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
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
                <label for="tags" class="block text-sm font-medium mb-2">Tags:</label>
                <input 
                    type="text" 
                    id="tags" 
                    name="tags"
                    value="{{ old('tags') }}"
                    placeholder="tag1, tag2, tag3"
                    maxlength="255"
                    class="w-full bg-gray-900 border border-gray-600 text-white px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                >
                <div class="text-xs text-gray-400 mt-1">Comma separated (max 10 tags)</div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="expiration" class="block text-sm font-medium mb-2">Expiration:</label>
                    <select 
                        id="expiration" 
                        name="expiration"
                        class="w-full bg-gray-900 border border-gray-600 text-white px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
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
                    <label class="block text-sm font-medium mb-2">Exposure:</label>
                    <div class="flex items-center gap-4 mt-3">
                        <label class="flex items-center cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="listable" 
                                value="1"
                                {{ old('listable', true) ? 'checked' : '' }}
                                class="mr-2 w-4 h-4"
                            >
                            <span class="text-sm">Listable</span>
                        </label>
                    </div>
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium mb-2">Password (optional):</label>
                <div class="flex gap-2">
                    <input 
                        type="password" 
                        id="password" 
                        name="password"
                        placeholder="Leave blank for no password"
                        minlength="8"
                        class="flex-1 bg-gray-900 border border-gray-600 text-white px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                    >
                    <button 
                        type="button" 
                        onclick="generatePassword()"
                        class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded text-sm"
                        title="Generate random password"
                    >
                        🔄
                    </button>
                </div>
                <div class="text-xs text-gray-400 mt-1">Min 8 characters</div>
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
                <label for="destroy_on_open" class="text-sm text-red-400">Destroy on open</label>
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

    <script>
        function generatePassword() {
            const password = Math.random().toString(36).slice(2) + Math.random().toString(36).slice(2);
            document.getElementById('password').type = 'text';
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>
