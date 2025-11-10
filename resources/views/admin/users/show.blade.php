@extends('layouts.app')

@section('title', 'User Details - ' . $user->username)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
    <div class="bg-gray-800 rounded-lg p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">👤 User Details</h2>
            <a href="{{ route('admin.users') }}" class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded text-sm">
                ← Back to Users
            </a>
        </div>

        <div class="space-y-4">
            <div>
                <label class="text-gray-400 text-sm">Username:</label>
                <p class="text-white text-lg">{{ $user->username }}</p>
            </div>

            <div>
                <label class="text-gray-400 text-sm">Email:</label>
                <p class="text-white">{{ $user->email }}</p>
            </div>

            <div>
                <label class="text-gray-400 text-sm">Joined:</label>
                <p class="text-white">{{ $user->created_at->format('M d, Y H:i') }}</p>
            </div>

            @if($user->settings)
                <div class="border-t border-gray-700 pt-4">
                    <h3 class="text-lg font-bold mb-2">Settings</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="text-gray-400 text-sm">Timezone:</label>
                            <p class="text-white">{{ $user->settings->timezone ?? 'Not set' }}</p>
                        </div>
                        <div>
                            <label class="text-gray-400 text-sm">Language:</label>
                            <p class="text-white">{{ $user->settings->language ?? 'Not set' }}</p>
                        </div>
                        <div>
                            <label class="text-gray-400 text-sm">Theme:</label>
                            <p class="text-white">{{ $user->settings->theme ?? 'Not set' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="border-t border-gray-700 pt-4">
                <h3 class="text-lg font-bold mb-2">Statistics</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-gray-400 text-sm">Total Pastes:</label>
                        <p class="text-white text-2xl">{{ $user->pastes->count() }}</p>
                    </div>
                    <div>
                        <label class="text-gray-400 text-sm">Total Comments:</label>
                        <p class="text-white text-2xl">{{ $user->comments->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($user->pastes->count() > 0)
        <div class="bg-gray-800 rounded-lg p-6">
            <h3 class="text-xl font-bold mb-4">Recent Pastes</h3>
            <div class="space-y-2">
                @foreach($user->pastes->take(5) as $paste)
                    <div class="border-b border-gray-700 pb-2">
                        <a href="{{ route('pastes.show', $paste->id) }}" class="text-blue-400 hover:text-blue-300">
                            {{ $paste->title }}
                        </a>
                        <span class="text-gray-500 text-sm ml-2">
                            {{ $paste->created_at->diffForHumans() }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
