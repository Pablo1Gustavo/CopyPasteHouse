@extends('layouts.app')

@section('title', 'Burn After Reading - ' . $paste->title)

@section('content')
<!-- Warning Content -->
<div class="max-w-3xl mx-auto px-4 py-12">
        <div class="bg-gray-800 rounded-lg p-8 text-center">
            <div class="mb-6">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-red-900 rounded-full mb-4">
                    <svg class="w-10 h-10 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-red-400 mb-4">🔥 Burn After Reading</h2>
            </div>

            <div class="bg-red-900/20 border border-red-700 rounded-lg p-6 mb-6 text-left">
                <p class="text-red-200 mb-4 text-lg font-semibold">
                    ⚠️ Warning: Once you view this paste, it will be permanently destroyed.
                </p>
                <ul class="text-red-300 space-y-2 text-sm">
                    <li>• You will <strong>NOT</strong> be able to view this paste again after clicking "View & Burn"</li>
                    <li>• The paste will be <strong>permanently deleted</strong> from our servers</li>
                    <li>• If you need this information later, <strong>copy it to a secure location</strong> immediately</li>
                    <li>• There is <strong>no undo</strong> for this action</li>
                </ul>
            </div>

            <div class="bg-gray-700 rounded-lg p-4 mb-8">
                <p class="text-gray-300 text-sm mb-2">You're about to burn this paste:</p>
                <p class="text-white font-bold text-xl">{{ $paste->title }}</p>
                @if($paste->user)
                    <p class="text-gray-400 text-sm mt-2">Created by {{ $paste->user->username }}</p>
                @endif
                <p class="text-gray-500 text-xs mt-1">{{ $paste->created_at->diffForHumans() }}</p>
            </div>

            <div class="flex gap-4 justify-center">
                <a 
                    href="{{ url()->previous() !== url()->current() ? url()->previous() : route('pastes.create') }}" 
                    class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-semibold transition"
                >
                    ← Go Back
                </a>
                <a 
                    href="{{ route('pastes.show', ['id' => $paste->id, 'confirm_burn' => '1']) }}" 
                    class="px-6 py-3 bg-red-700 hover:bg-red-600 text-white rounded-lg font-semibold transition"
                >
                    🔥 View & Burn This Paste
                </a>
            </div>

            <p class="text-gray-500 text-xs mt-6">
                By clicking "View & Burn This Paste", you acknowledge that this action is irreversible.
            </p>
        </div>
    </div>
@endsection
