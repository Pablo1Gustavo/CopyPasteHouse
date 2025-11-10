@extends('layouts.app')

@section('title', 'User Management - CopyPasteHouse')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">
    @if(session('success'))
        <div class="bg-green-900 border border-green-700 text-green-200 px-4 py-3 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-900 border border-red-700 text-red-200 px-4 py-3 mb-4 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-gray-800 rounded-lg p-6 mb-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold">👥 User Management</h2>
            <a href="{{ route('pastes.create') }}" class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded text-sm">
                ← Back
            </a>
        </div>
    </div>

    <div class="bg-gray-800 rounded-lg overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Username</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Pastes</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Comments</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Joined</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @foreach($users as $user)
                    <tr class="hover:bg-gray-750">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('admin.users.show', $user->id) }}" class="text-blue-400 hover:text-blue-300">
                                {{ $user->username }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-300">
                            {{ $user->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-300">
                            {{ $user->pastes_count }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-300">
                            {{ $user->comments_count }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-300">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                                    View
                                </a>
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $users->links() }}
    </div>
</div>
@endsection
