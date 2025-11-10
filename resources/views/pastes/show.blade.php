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

            @if(!isset($paste->destroyed) && isset($paste->destroy_on_open) && $paste->destroy_on_open && request()->query('created') === '1')
                @auth
                    @if(auth()->id() === $paste->user_id)
                        <div class="bg-blue-900 border border-blue-700 text-blue-200 px-4 py-3 mb-4 rounded">
                            🔒 <strong>Burn After Reading Enabled:</strong> As the creator, you can view this paste multiple times. However, when <strong>anyone else</strong> views it, they will see a confirmation page and the paste will be permanently deleted after they confirm.
                        </div>
                    @else
                        <div class="bg-orange-900 border border-orange-700 text-orange-200 px-4 py-3 mb-4 rounded">
                            🔥 <strong>Burn After Reading Enabled:</strong> This paste will show a confirmation page and be permanently deleted when viewed. Share the link carefully!
                        </div>
                    @endif
                @else
                    <div class="bg-orange-900 border border-orange-700 text-orange-200 px-4 py-3 mb-4 rounded">
                        🔥 <strong>Burn After Reading Enabled:</strong> This paste will show a confirmation page and be permanently deleted when viewed. Share the link carefully!
                    </div>
                @endauth
            @endif

            <h2 class="text-xl font-bold mb-4">{{ $paste->title }}</h2>

            <div class="mb-3">
                <span class="text-gray-400 text-sm">
                    By <span class="text-blue-400 font-semibold">{{ $paste->user ? $paste->user->username : 'Anonymous' }}</span>
                    <span class="mx-2">•</span>
                    {{ $paste->created_at->diffForHumans() }}
                </span>
            </div>

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
                <div class="flex items-center gap-4">
                    @auth
                        <button 
                            onclick="toggleLike()"
                            id="likeButton"
                            class="flex items-center gap-2 {{ $userHasLiked ? 'text-red-400' : 'text-gray-400' }} hover:text-red-400 transition"
                        >
                            <span id="likeIcon">{{ $userHasLiked ? '❤️' : '🤍' }}</span>
                            <span id="likeCount">{{ $paste->likes_count ?? 0 }}</span>
                        </button>
                    @else
                        <span class="text-gray-400">🤍 {{ $paste->likes_count ?? 0 }}</span>
                    @endauth
                    <span class="text-gray-400">👁️ {{ $paste->access_count ?? 0 }} views</span>
                    <span class="text-gray-400">💬 {{ $comments->count() }} comments</span>
                </div>
                
                <div class="flex gap-2">
                    <button 
                        onclick="copyToClipboard()"
                        class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 text-sm rounded"
                    >
                        📋 Copy
                    </button>
                    
                    @if(!isset($paste->destroyed))
                        <a 
                            href="{{ route('pastes.raw', $paste->id) }}"
                            class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 text-sm rounded inline-flex items-center"
                            target="_blank"
                        >
                            📄 Raw
                        </a>
                    @endif
                    
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

        <!-- Comments Section -->
        <div class="bg-gray-800 shadow-md rounded-lg p-6 mt-6">
            <h2 class="text-xl font-bold mb-4">Comments ({{ $comments->count() }})</h2>

            <!-- Comments List -->
            @forelse($comments as $comment)
                <div class="border-b border-gray-700 pb-4 mb-4 last:border-0">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <span class="font-semibold text-blue-400">{{ $comment->user->username }}</span>
                            <span class="text-gray-500 text-sm ml-2">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            @auth
                                <button 
                                    onclick="toggleCommentLike('{{ $comment->id }}')"
                                    id="commentLikeButton-{{ $comment->id }}"
                                    class="text-red-500 hover:text-red-400 text-sm"
                                >
                                    <span id="commentLikeIcon-{{ $comment->id }}">
                                        {{ in_array($comment->id, $likedCommentIds) ? '❤️' : '🤍' }}
                                    </span>
                                    <span id="commentLikeCount-{{ $comment->id }}">{{ $comment->likes_count }}</span>
                                </button>
                            @else
                                <span class="text-gray-500 text-sm">🤍 {{ $comment->likes_count }}</span>
                            @endauth
                        </div>
                    </div>

                    @if($comment->syntax_highlight_id)
                        <pre><code class="language-{{ $comment->syntaxHighlight->code ?? 'plaintext' }} rounded">{{ $comment->content }}</code></pre>
                    @else
                        <p class="text-gray-300">{{ $comment->content }}</p>
                    @endif
                </div>
            @empty
                <p class="text-gray-500 text-center py-4">No comments yet. Be the first to comment!</p>
            @endforelse

            <!-- Comment Form -->
            @auth
                <div class="mt-6">
                    <h3 class="text-lg font-semibold mb-3">Add a Comment</h3>
                    <form action="{{ route('pastes.comments.store', $paste->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <textarea 
                                name="content" 
                                rows="4" 
                                class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white"
                                placeholder="Write your comment..."
                                required
                            >{{ old('content') }}</textarea>
                            @error('content')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Syntax Highlight (optional)</label>
                            <select 
                                name="syntax_highlight_id" 
                                class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2"
                            >
                                <option value="">None</option>
                                @foreach($syntaxHighlights as $highlight)
                                    <option value="{{ $highlight->id }}" {{ old('syntax_highlight_id') == $highlight->id ? 'selected' : '' }}>
                                        {{ $highlight->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button 
                            type="submit"
                            class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2 rounded"
                        >
                            Post Comment
                        </button>
                    </form>
                </div>
            @else
                <div class="mt-6 text-center">
                    <p class="text-gray-500 mb-3">You must be logged in to comment.</p>
                    <a href="{{ route('login') }}" class="text-blue-400 hover:underline">Login</a>
                    or
                    <a href="{{ route('register') }}" class="text-blue-400 hover:underline">Register</a>
                </div>
            @endauth
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

        // Toggle paste like
        function toggleLike() {
            fetch('{{ route('pastes.like', $paste->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                const icon = document.getElementById('likeIcon');
                const count = document.getElementById('likeCount');
                
                icon.textContent = data.liked ? '❤️' : '🤍';
                count.textContent = data.count;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to toggle like. Please try again.');
            });
        }

        // Toggle comment like
        function toggleCommentLike(commentId) {
            fetch(`/comments/${commentId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                const icon = document.getElementById(`commentLikeIcon-${commentId}`);
                const count = document.getElementById(`commentLikeCount-${commentId}`);
                
                icon.textContent = data.liked ? '❤️' : '🤍';
                count.textContent = data.count;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to toggle like. Please try again.');
            });
        }
    </script>
</body>
</html>
