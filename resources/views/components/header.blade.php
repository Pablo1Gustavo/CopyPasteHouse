@php
    $isLight = false;
    if (auth()->check() && auth()->user()->settings && auth()->user()->settings->theme === 'light') {
        $isLight = true;
    }
@endphp
<div class="{{ $isLight ? 'bg-white border-b border-gray-300' : 'bg-gray-800' }} py-4 px-4 mb-8">
    <div class="max-w-6xl mx-auto flex items-center justify-between">
        <h1 class="text-2xl font-bold">
            <a href="{{ route('home') }}" class="{{ $isLight ? 'text-gray-900 hover:text-gray-700' : 'text-white hover:text-gray-300' }}">{{ __('app.app_name') }}</a>
        </h1>
        <div class="flex items-center gap-4 text-sm">
            <a href="{{ route('pastes.archive') }}" class="border border-blue-500 text-blue-500 px-4 py-2 hover:bg-blue-500 hover:text-white transition uppercase">
                📚 {{ __('app.public_pastes') }}
            </a>
            <a href="{{ route('pastes.create') }}" class="border border-green-500 text-green-500 px-4 py-2 hover:bg-green-500 hover:text-white transition uppercase">
                + {{ __('app.new_paste') }}
            </a>
            @auth
                <a href="{{ route('pastes.index') }}" class="{{ $isLight ? 'border border-gray-400 text-gray-700 hover:bg-gray-200' : 'border border-gray-400 hover:bg-gray-700' }} px-4 py-2 transition uppercase">
                    {{ __('app.my_pastes') }}
                </a>
                <a href="{{ route('tags.my') }}" class="{{ $isLight ? 'border border-gray-400 text-gray-700 hover:bg-gray-200' : 'border border-gray-400 hover:bg-gray-700' }} px-4 py-2 transition uppercase">
                    🏷️ My Tags
                </a>
                @if(auth()->user()->is_admin)
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="border border-purple-500 text-purple-500 px-4 py-2 hover:bg-purple-500 hover:text-white transition uppercase">
                            ⚙️ Admin
                        </button>
                                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-56 rounded-md shadow-lg {{ $isLight ? 'bg-white' : 'bg-gray-800' }} ring-1 ring-black ring-opacity-5">
                            <a href="{{ route('admin.statistics') }}" class="block px-4 py-2 {{ $isLight ? 'text-gray-900 hover:bg-gray-100' : 'text-white hover:bg-gray-700' }}">📊 Statistics</a>
                            <a href="{{ route('admin.users') }}" class="block px-4 py-2 {{ $isLight ? 'text-gray-900 hover:bg-gray-100' : 'text-white hover:bg-gray-700' }}">👥 Users</a>
                            <a href="{{ route('admin.comments') }}" class="block px-4 py-2 {{ $isLight ? 'text-gray-900 hover:bg-gray-100' : 'text-white hover:bg-gray-700' }}">💬 Comments</a>
                            <a href="{{ route('syntax-highlights.index') }}" class="block px-4 py-2 {{ $isLight ? 'text-gray-900 hover:bg-gray-100' : 'text-white hover:bg-gray-700' }}">📄 Syntax Highlights</a>
                            <a href="{{ route('expiration-times.index') }}" class="block px-4 py-2 {{ $isLight ? 'text-gray-900 hover:bg-gray-100' : 'text-white hover:bg-gray-700' }}">⏰ Expiration Times</a>
                            <a href="{{ route('tags.index') }}" class="block px-4 py-2 {{ $isLight ? 'text-gray-900 hover:bg-gray-100' : 'text-white hover:bg-gray-700' }}">📌 Tags</a>
                        </div>
                    </div>
                @endif
                <a href="{{ route('profile.edit') }}" class="{{ $isLight ? 'bg-gray-200 hover:bg-gray-300 text-gray-900' : 'bg-gray-700 hover:bg-gray-600 text-white' }} px-4 py-2 rounded transition font-medium">
                    👤 {{ auth()->user()->username }}
                </a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="{{ $isLight ? 'border border-gray-400 text-gray-700 hover:bg-gray-200' : 'border border-gray-600 text-gray-300 hover:bg-gray-700' }} px-4 py-2 rounded transition uppercase">{{ __('app.logout') }}</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="{{ $isLight ? 'border border-gray-400 text-gray-700 hover:bg-gray-200' : 'border border-gray-600 text-gray-300 hover:bg-gray-700' }} px-4 py-2 rounded transition uppercase">{{ __('app.login') }}</a>
                <a href="{{ route('register') }}" class="border border-orange-500 text-orange-500 px-4 py-2 hover:bg-orange-500 hover:text-white rounded transition uppercase font-medium">{{ __('app.signup') }}</a>
            @endauth
        </div>
    </div>
</div>
