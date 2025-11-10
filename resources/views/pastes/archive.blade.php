<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Public Pastes - CopyPasteHouse</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 text-white">
    <!-- Header -->
    <div class="bg-gray-800 py-4 px-4 mb-8">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <h1 class="text-2xl font-bold">
                <a href="{{ route('pastes.create') }}" class="text-white hover:text-gray-300">CopyPasteHouse</a>
            </h1>
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
    <div class="max-w-6xl mx-auto px-4 py-6">
        <div class="bg-gray-800 rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-bold mb-2">📚 Public Pastes Archive</h2>
            <p class="text-gray-400 text-sm">Browse recent public pastes shared by the community</p>
        </div>

        @if($pastes->count() > 0)
            <div class="space-y-4">
                @foreach($pastes as $paste)
                    <div class="bg-gray-800 rounded-lg p-5 hover:bg-gray-750 transition">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <a href="{{ route('pastes.show', $paste->id) }}" class="text-xl font-semibold text-blue-400 hover:text-blue-300">
                                    {{ $paste->title }}
                                </a>
                                <div class="flex flex-wrap gap-2 mt-2 text-xs">
                                    @if($paste->syntaxHighlight)
                                        <span class="border border-gray-600 px-2 py-1 rounded">
                                            📄 {{ $paste->syntaxHighlight->label }}
                                        </span>
                                    @endif
                                    <span class="border border-gray-600 px-2 py-1 rounded">
                                        📅 {{ $paste->created_at->diffForHumans() }}
                                    </span>
                                    @if($paste->user)
                                        <span class="border border-gray-600 px-2 py-1 rounded">
                                            👤 {{ $paste->user->username }}
                                        </span>
                                    @endif
                                    @if($paste->expiration)
                                        <span class="border border-orange-600 text-orange-400 px-2 py-1 rounded">
                                            ⏰ Expires {{ $paste->expiration->diffForHumans() }}
                                        </span>
                                    @endif
                                    <span class="border border-gray-600 px-2 py-1 rounded">
                                        👁️ {{ $paste->access_count ?? 0 }} views
                                    </span>
                                    @if($paste->likes_count > 0)
                                        <span class="border border-gray-600 px-2 py-1 rounded">
                                            ❤️ {{ $paste->likes_count }} likes
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        @if($paste->tags && is_array($paste->tags) && count($paste->tags) > 0)
                            <div class="flex flex-wrap gap-2 mt-3">
                                @foreach($paste->tags as $tag)
                                    <span class="bg-gray-700 text-gray-300 px-2 py-1 rounded text-xs">
                                        🏷️ {{ $tag }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-3 text-gray-400 text-sm">
                            {{ Str::limit($paste->content, 150) }}
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $pastes->links() }}
            </div>
        @else
            <div class="bg-gray-800 rounded-lg p-12 text-center">
                <p class="text-gray-400 text-lg">📭 No public pastes yet</p>
                <p class="text-gray-500 text-sm mt-2">Be the first to create a public paste!</p>
            </div>
        @endif
    </div>
</body>
</html>
