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
