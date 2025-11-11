@extends('layouts.app')

@section('title', 'Syntax Highlights Management - CopyPasteHouse')

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
            <h2 class="text-2xl font-bold">📄 Syntax Highlights</h2>
            <div class="flex gap-2">
                <a href="{{ route('syntax-highlights.create') }}" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded text-sm">
                    + Add New
                </a>
                <a href="{{ route('pastes.create') }}" class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded text-sm">
                    ← Back
                </a>
            </div>
        </div>
    </div>

    <div class="bg-gray-800 rounded-lg overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Extension</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @foreach($highlights as $highlight)
                    <tr class="hover:bg-gray-750">
                        <td class="px-6 py-4 whitespace-nowrap text-white">
                            {{ $highlight->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-300 font-mono text-sm">
                            {{ $highlight->extension }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex gap-2">
                                <a href="{{ route('syntax-highlights.edit', $highlight->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('syntax-highlights.destroy', $highlight->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this syntax highlight?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
