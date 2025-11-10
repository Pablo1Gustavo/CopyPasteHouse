@extends('layouts.app')

@section('title', 'My Profile - CopyPasteHouse')

@section('content')
@php
    $isLight = auth()->check() && auth()->user()->settings && auth()->user()->settings->theme === 'light';
    $cardClass = $isLight ? 'bg-white border border-gray-300' : 'bg-gray-800';
    $inputClass = $isLight ? 'bg-gray-50 border-gray-300 text-gray-900' : 'bg-gray-900 border-gray-600 text-white';
    $textClass = $isLight ? 'text-gray-900' : 'text-white';
    $mutedClass = $isLight ? 'text-gray-600' : 'text-gray-400';
@endphp
<div class="max-w-4xl mx-auto px-4 py-6">

        @if(session('success'))
            <div class="bg-green-900 border border-green-700 text-green-200 px-4 py-3 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-900 border border-red-700 text-red-200 px-4 py-3 mb-4 rounded">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="{{ $cardClass }} rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-6 {{ $textClass }}">{{ __('app.edit_profile') }}</h2>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('PUT') 
                <div>
                    <label for="username" class="block text-sm font-medium mb-2 {{ $textClass }}">{{ __('app.username') }}:</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username"
                        value="{{ old('username', auth()->user()->username) }}"
                        required
                        maxlength="50"
                        class="w-full {{ $inputClass }} px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                    >
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium mb-2 {{ $textClass }}">{{ __('app.email') }}:</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email"
                        value="{{ old('email', auth()->user()->email) }}"
                        required
                        class="w-full {{ $inputClass }} px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                    >
                </div>
                
                <div class="flex pt-4">
                    <button 
                        type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded font-medium"
                    >
                        {{ __('app.update_profile') }}
                    </button>
                </div>
            </form>
        </div>

        <div class="{{ $cardClass }} rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-6 {{ $textClass }}">{{ __('app.change_password') }}</h2>

            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-sm font-medium mb-2 {{ $textClass }}">{{ __('app.current_password') }}:</label>
                    <input 
                        type="password" 
                        id="current_password" 
                        name="current_password"
                        required
                        class="w-full {{ $inputClass }} px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium mb-2 {{ $textClass }}">{{ __('app.new_password') }}:</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password"
                        required
                        minlength="8"
                        class="w-full {{ $inputClass }} px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                    >
                    <div class="text-xs {{ $mutedClass }} mt-1">{{ __('app.min_8_chars') }}</div>
                </div>

                 <div>
                    <label for="password_confirmation" class="block text-sm font-medium mb-2 {{ $textClass }}">{{ __('app.confirm_password') }}:</label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation"
                        required
                        class="w-full {{ $inputClass }} px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                    >
                </div>
                
                <div class="flex pt-4">
                    <button 
                        type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded font-medium"
                    >
                        {{ __('app.update_password') }}
                    </button>
                </div>
                        </form>
        </div>

        <div class="{{ $cardClass }} rounded-lg p-6 mt-6">
            <h2 class="text-xl font-bold mb-6 {{ $textClass }}">⚙️ {{ __('app.preferences') }}</h2>

            <form method="POST" action="{{ route('settings.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="timezone" class="block text-sm font-medium mb-2 {{ $textClass }}">{{ __('app.timezone') }}:</label>
                    <select 
                        id="timezone" 
                        name="timezone"
                        class="w-full {{ $inputClass }} px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                    >
                        @php
                            $currentTimezone = auth()->user()->settings->timezone ?? 'UTC';
                            $timezones = [
                                'UTC' => 'UTC',
                                'America/New_York' => 'Eastern Time (US)',
                                'America/Chicago' => 'Central Time (US)',
                                'America/Denver' => 'Mountain Time (US)',
                                'America/Los_Angeles' => 'Pacific Time (US)',
                                'Europe/London' => 'London',
                                'Europe/Paris' => 'Paris',
                                'Europe/Berlin' => 'Berlin',
                                'Europe/Lisbon' => 'Lisbon',
                                'Asia/Tokyo' => 'Tokyo',
                                'Asia/Shanghai' => 'Shanghai',
                                'Australia/Sydney' => 'Sydney',
                            ];
                        @endphp
                        @foreach($timezones as $value => $label)
                            <option value="{{ $value }}" {{ $currentTimezone === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="language" class="block text-sm font-medium mb-2 {{ $textClass }}">{{ __('app.language') }}:</label>
                    <select 
                        id="language" 
                        name="language"
                        class="w-full {{ $inputClass }} px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                    >
                        @php
                            $currentLanguage = auth()->user()->settings->language ?? 'en';
                            $languages = [
                                'en' => 'English',
                                'pt' => 'Português',
                            ];
                        @endphp
                        @foreach($languages as $value => $label)
                            <option value="{{ $value }}" {{ $currentLanguage === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="theme" class="block text-sm font-medium mb-2 {{ $textClass }}">{{ __('app.theme') }}:</label>
                    <select 
                        id="theme" 
                        name="theme"
                        class="w-full {{ $inputClass }} px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                    >
                        @php
                            $currentTheme = auth()->user()->settings->theme ?? 'system';
                        @endphp
                        <option value="system" {{ $currentTheme === 'system' ? 'selected' : '' }}>{{ __('app.theme_system') }}</option>
                        <option value="light" {{ $currentTheme === 'light' ? 'selected' : '' }}>{{ __('app.theme_light') }}</option>
                        <option value="dark" {{ $currentTheme === 'dark' ? 'selected' : '' }}>{{ __('app.theme_dark') }}</option>
                    </select>
                </div>
                
                <div class="flex pt-4">
                    <button 
                        type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded font-medium"
                    >
                        {{ __('app.update_preferences') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
