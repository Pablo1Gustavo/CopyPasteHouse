<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $paste->title }} - CopyPasteHouse</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
</head>
<body class="bg-gray-900 text-white">
    <!-- Header -->
    <div class="bg-gray-800 py-4 px-4 mb-8">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <h1 class="text-2xl font-bold">
                <a href="{{ route('dashboard') }}" class="text-white hover:text-gray-300">CopyPasteHouse</a>
            </h1>
            <div class="flex items-center gap-4 text-sm">
                @auth
                    <a href="{{ route('dashboard') }}" class="border border-green-500 text-green-500 px-4 py-2 hover:bg-green-500 hover:text-white transition uppercase">
                        + New Paste
                    </a>
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
        @if(session('success'))
            <div class="bg-green-900 border border-green-700 text-green-200 px-4 py-3 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-gray-800 rounded-lg p-6">
            @if(isset($paste->destroyed) && $paste->destroyed)
                <div class="bg-yellow-900 border border-yellow-700 text-yellow-200 px-4 py-3 mb-4 rounded">
                    ⚠️ This paste was set to "destroy on open" and has been permanently deleted after this view.
                </div>
            @endif

            <h2 class="text-xl font-bold mb-4">{{ $paste->title }}</h2>

            <div class="flex flex-wrap gap-2 mb-4">
                @if($paste->syntaxHighlight)
                    <span class="border border-gray-600 px-3 py-1 text-xs rounded">
                        📄 {{ $paste->syntaxHighlight->label }}
                    </span>
                @endif
                <span class="border border-gray-600 px-3 py-1 text-xs rounded">
                    📅 {{ $paste->created_at->format('M d, Y H:i') }}
                </span>
                @if($paste->expiration)
                    <span class="border border-gray-600 px-3 py-1 text-xs rounded">
                        ⏰ {{ $paste->expiration->format('M d, Y H:i') }}
                    </span>
                @else
                    <span class="border border-gray-600 px-3 py-1 text-xs rounded">
                        ⏰ Never
                    </span>
                @endif
            </div>

            @if($paste->tags && is_array($paste->tags) && count($paste->tags) > 0)
                <div class="mb-4 flex flex-wrap gap-2">
                    <span class="text-gray-400 text-sm">Tags:</span>
                    @foreach($paste->tags as $index => $tag)
                        @php
                            $colors = ['red', 'green', 'blue', 'orange', 'purple'];
                            $colorIndex = $index % count($colors);
                            $bgColors = [
                                'red' => 'bg-red-600',
                                'green' => 'bg-green-600',
                                'blue' => 'bg-blue-600',
                                'orange' => 'bg-orange-600',
                                'purple' => 'bg-purple-600'
                            ];
                            $bgColor = $bgColors[$colors[$colorIndex]];
                        @endphp
                        <span class="inline-flex items-center gap-1 bg-gray-700 text-white text-xs px-2 py-1 rounded">
                            <span class="{{ $bgColor }} w-5 h-5 rounded-full flex items-center justify-center text-white text-xs uppercase">
                                {{ substr($tag, 0, 1) }}
                            </span>
                            {{ $tag }}
                        </span>
                    @endforeach
                </div>
            @endif

            <div class="bg-gray-900 rounded-lg overflow-auto min-h-[200px]">
                <pre class="p-4"><code class="language-{{ $paste->syntaxHighlight->value ?? 'plaintext' }}">{{ $paste->content }}</code></pre>
            </div>

            <div class="mt-4 flex items-center justify-between">
                <div class="text-sm text-gray-400">
                    {{ $paste->likes_count ?? 0 }} likes • {{ $paste->access_count ?? 0 }} views
                </div>
                
                <div class="flex gap-2">
                    <button 
                        onclick="copyToClipboard()"
                        class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 text-sm rounded"
                    >
                        📋 Copy
                    </button>
                    
                    @auth
                        @if(!isset($paste->destroyed) && $paste->user_id === auth()->id())
                            <a 
                                href="{{ route('pastes.edit', $paste->id) }}"
                                class="bg-blue-700 hover:bg-blue-600 text-white px-4 py-2 text-sm rounded"
                            >
                                Edit
                            </a>
                            <form method="POST" action="{{ route('pastes.destroy', $paste->id) }}" class="inline" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button 
                                    type="submit"
                                    class="bg-red-700 hover:bg-red-600 text-white px-4 py-2 text-sm rounded"
                                >
                                    Delete
                                </button>
                            </form>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize syntax highlighting
        hljs.highlightAll();

        function copyToClipboard() {
            const code = @json($paste->content);
            navigator.clipboard.writeText(code).then(() => {
                alert('Copied to clipboard!');
            });
        }
    </script>
</body>
</html>
