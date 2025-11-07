<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CopyPasteHouse</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white">
    <!-- Header -->
    <div class="bg-gray-800 text-white py-3 px-4">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <h1 class="text-xl font-bold">CopyPasteHouse</h1>
            <div class="flex items-center gap-4 text-sm">
                <span class="text-gray-300">{{ auth()->user()->name }}</span>
                <a href="#" class="text-gray-300 hover:text-white">My Pastes</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-gray-300 hover:text-white">Logout</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-6xl mx-auto px-4 py-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold mb-2">New Paste</h2>
            <p class="text-sm text-gray-600">Create and share text snippets</p>
        </div>

        <form class="space-y-4">
            <div>
                <label for="title" class="block text-sm font-medium mb-1">Paste Name / Title: (optional)</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title"
                    placeholder="Untitled"
                    class="w-full border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-gray-500"
                >
            </div>

            <div>
                <label for="syntax" class="block text-sm font-medium mb-1">Syntax Highlighting:</label>
                <select 
                    id="syntax" 
                    name="syntax"
                    class="border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-gray-500"
                >
                    <option>None</option>
                    <option>PHP</option>
                    <option>JavaScript</option>
                    <option>Python</option>
                    <option>HTML</option>
                    <option>CSS</option>
                </select>
            </div>

            <div>
                <label for="content" class="block text-sm font-medium mb-1">Paste Content:</label>
                <textarea 
                    id="content" 
                    name="content"
                    rows="20"
                    placeholder="Enter your text here..."
                    class="w-full border border-gray-300 px-3 py-2 text-sm font-mono focus:outline-none focus:border-gray-500"
                ></textarea>
            </div>

            <div class="flex items-center gap-4">
                <div>
                    <label for="expiration" class="block text-sm font-medium mb-1">Paste Expiration:</label>
                    <select 
                        id="expiration" 
                        name="expiration"
                        class="border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-gray-500"
                    >
                        <option>Never</option>
                        <option>10 Minutes</option>
                        <option>1 Hour</option>
                        <option>1 Day</option>
                        <option>1 Week</option>
                        <option>1 Month</option>
                    </select>
                </div>

                <div>
                    <label for="visibility" class="block text-sm font-medium mb-1">Paste Exposure:</label>
                    <select 
                        id="visibility" 
                        name="visibility"
                        class="border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-gray-500"
                    >
                        <option>Public</option>
                        <option>Unlisted</option>
                        <option>Private</option>
                    </select>
                </div>
            </div>

            <div>
                <button 
                    type="submit" 
                    class="bg-gray-700 hover:bg-gray-800 text-white px-6 py-2 text-sm font-medium"
                >
                    Create New Paste
                </button>
            </div>
        </form>

        <!-- Recent Pastes -->
        <div class="mt-8 pt-8 border-t border-gray-200">
            <h3 class="text-lg font-bold mb-4">Your Recent Pastes</h3>
            <div class="text-sm text-gray-500">
                No pastes yet. Create your first paste above!
            </div>
        </div>
    </div>
</body>
</html>
