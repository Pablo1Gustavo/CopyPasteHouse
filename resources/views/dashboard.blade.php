@extends('layouts.app')

@section('title', 'Create Paste - CopyPasteHouse')

@section('content')
@php
    $isLight = auth()->check() && auth()->user()->settings && auth()->user()->settings->theme === 'light';
    $cardClass = $isLight ? 'bg-white border border-gray-300' : 'bg-gray-800';
    $inputClass = $isLight ? 'bg-gray-50 border-gray-300 text-gray-900' : 'bg-gray-900 border-gray-600 text-white';
    $textClass = $isLight ? 'text-gray-900' : 'text-white';
    $mutedClass = $isLight ? 'text-gray-600' : 'text-gray-400';
    // Special class for code/paste content - needs high contrast
    $codeInputClass = $isLight ? 'bg-white border-gray-400 text-gray-900' : 'bg-gray-900 border-gray-600 text-white';
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

        <div class="{{ $cardClass }} rounded-lg p-6">
            <h2 class="text-xl font-bold mb-6 {{ $textClass }}">Create a new paste</h2>

        <form method="POST" action="{{ route('pastes.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="content" class="block text-sm font-medium mb-2 {{ $textClass }}">Paste Content:</label>
                <textarea 
                    id="content" 
                    name="content"
                    rows="15"
                    required
                    spellcheck="false"
                    placeholder="Enter your text here..."
                    class="w-full {{ $codeInputClass }} border px-4 py-3 text-sm font-mono focus:outline-none focus:border-gray-400 rounded"
                >{{ old('content') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="title" class="block text-sm font-medium mb-2 {{ $textClass }}">Title:</label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title"
                        value="{{ old('title') }}"
                        placeholder="Untitled"
                        required
                        maxlength="50"
                        class="w-full {{ $inputClass }} px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                    >
                    <div class="text-xs {{ $mutedClass }} mt-1">Max 50 characters</div>
                </div>

                <div>
                    <label for="syntax_highlight_id" class="block text-sm font-medium mb-2 {{ $textClass }}">Syntax Highlighting:</label>
                    <select 
                        id="syntax_highlight_id" 
                        name="syntax_highlight_id"
                        required
                        class="w-full {{ $inputClass }} px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                    >
                        @foreach($syntaxHighlights as $highlight)
                            <option value="{{ $highlight->id }}" {{ old('syntax_highlight_id') == $highlight->id ? 'selected' : '' }}>
                                {{ $highlight->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label for="tag_search" class="block text-sm font-medium mb-2 {{ $textClass }}">Tags:</label>
                    <div class="relative" x-data="{ myTagsOnly: false }">
                        <input 
                            type="text" 
                            id="tag_search" 
                            placeholder="Search and select tags..."
                            class="w-full {{ $inputClass }} px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                            autocomplete="off"
                        >
                        <div id="tag_dropdown" class="hidden absolute z-10 w-full mt-1 {{ $isLight ? 'bg-white border border-gray-300' : 'bg-gray-800 border border-gray-600' }} rounded-lg shadow-lg max-h-48 overflow-y-auto">
                            <div class="px-4 py-2 border-b {{ $isLight ? 'border-gray-300 bg-gray-50' : 'border-gray-600 bg-gray-700' }}">
                                <label class="flex items-center cursor-pointer text-sm">
                                    <input 
                                        type="checkbox" 
                                        x-model="myTagsOnly"
                                        class="mr-2 w-3 h-3"
                                    >
                                    <span class="{{ $textClass }}">My Tags Only</span>
                                </label>
                            </div>
                            @foreach($tags as $tag)
                                <div class="tag-option px-4 py-2 cursor-pointer {{ $isLight ? 'hover:bg-gray-100' : 'hover:bg-gray-700' }} flex flex-col gap-1" 
                                     data-tag-id="{{ $tag->id }}" 
                                     data-tag-name="{{ $tag->name }}"
                                     data-tag-color="{{ $tag->color }}"
                                     data-user-id="{{ $tag->user_id }}"
                                     x-show="!myTagsOnly || '{{ $tag->user_id }}' == '{{ auth()->id() }}'">
                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full" style="background-color: {{ $tag->color }}"></span>
                                        <span class="{{ $textClass }}">{{ $tag->name }}</span>
                                    </div>
                                    @if($tag->description)
                                        <div class="{{ $mutedClass }} text-xs ml-5">{{ $tag->description }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div id="selected_tags" class="mt-2 flex flex-wrap gap-2"></div>
                    <div class="text-xs {{ $mutedClass }} mt-1">
                        Don't see the tag you need? 
                        <a href="{{ route('tags.create') }}" class="text-blue-400 hover:text-blue-300" target="_blank">Create a new tag</a>
                    </div>
                </div>

                <div class="md:col-span-1 grid grid-cols-2 gap-4">
                    <div>
                        <label for="expiration" class="block text-sm font-medium mb-2 {{ $textClass }}">Expiration:</label>
                        <select 
                            id="expiration" 
                            name="expiration"
                            class="w-full {{ $inputClass }} px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                        >
                            <option value="">Never</option>
                            @foreach($expirationTimes as $expTime)
                                <option value="{{ $expTime->minutes }}" {{ old('expiration') == $expTime->minutes ? 'selected' : '' }}>
                                    {{ $expTime->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 {{ $textClass }}">Exposure:</label>
                        <div class="flex items-center gap-4 mt-3">
                            <label class="flex items-center cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    name="listable" 
                                    value="1"
                                    {{ old('listable', true) ? 'checked' : '' }}
                                    class="mr-2 w-4 h-4"
                                >
                                <span class="text-sm {{ $textClass }}">Listable</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium mb-2 {{ $textClass }}">Password (optional):</label>
                <div class="flex gap-2">
                    <input 
                        type="password" 
                        id="password" 
                        name="password"
                        placeholder="Leave blank for no password"
                        minlength="8"
                        class="flex-1 {{ $inputClass }} px-4 py-2 text-sm focus:outline-none focus:border-gray-400 rounded"
                    >
                    <button 
                        type="button" 
                        onclick="generatePassword()"
                        class="{{ $isLight ? 'bg-gray-300 hover:bg-gray-400' : 'bg-gray-700 hover:bg-gray-600' }} px-4 py-2 rounded text-sm"
                        title="Generate random password"
                    >
                        🔄
                    </button>
                </div>
                <div class="text-xs {{ $mutedClass }} mt-1">Min 8 characters</div>
            </div>

            <div class="flex items-center gap-2">
                <input 
                    type="checkbox" 
                    id="destroy_on_open" 
                    name="destroy_on_open" 
                    value="1"
                    {{ old('destroy_on_open') ? 'checked' : '' }}
                    class="w-4 h-4"
                >
                <label for="destroy_on_open" class="text-sm {{ $textClass }}">🔥 Destroy on open (Burn After Reading)</label>
            </div>

            <div class="pt-4">
                <button 
                    type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded font-medium uppercase"
                >
                    Create the paste
                </button>
            </div>
        </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function generatePassword() {
            const password = Math.random().toString(36).slice(2) + Math.random().toString(36).slice(2);
            document.getElementById('password').type = 'text';
            document.getElementById('password').value = password;
        }

        // Tag search and multi-select functionality
        const tagSearch = document.getElementById('tag_search');
        const tagDropdown = document.getElementById('tag_dropdown');
        const selectedTagsContainer = document.getElementById('selected_tags');
        const selectedTags = new Map(); // Store selected tags: id -> {name, color}

        // Show dropdown on focus
        tagSearch.addEventListener('focus', () => {
            tagDropdown.classList.remove('hidden');
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#tag_search') && !e.target.closest('#tag_dropdown')) {
                tagDropdown.classList.add('hidden');
            }
        });

        // Filter tags based on search
        tagSearch.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const options = document.querySelectorAll('.tag-option');
            
            options.forEach(option => {
                const tagName = option.dataset.tagName.toLowerCase();
                if (tagName.includes(searchTerm)) {
                    option.style.display = 'flex';
                } else {
                    option.style.display = 'none';
                }
            });
        });

        // Handle tag selection
        document.querySelectorAll('.tag-option').forEach(option => {
            option.addEventListener('click', () => {
                const tagId = option.dataset.tagId;
                const tagName = option.dataset.tagName;
                const tagColor = option.querySelector('span').style.backgroundColor;

                if (!selectedTags.has(tagId)) {
                    selectedTags.set(tagId, { name: tagName, color: tagColor });
                    renderSelectedTags();
                    
                    // Close dropdown for tactile feedback
                    tagDropdown.classList.add('hidden');
                }

                tagSearch.value = '';
                
                // Reset filter
                document.querySelectorAll('.tag-option').forEach(opt => {
                    opt.style.display = 'flex';
                });
            });
        });

        function renderSelectedTags() {
            selectedTagsContainer.innerHTML = '';
            
            selectedTags.forEach((tag, id) => {
                const tagBadge = document.createElement('div');
                tagBadge.className = 'inline-flex items-center gap-2 px-3 py-1 rounded-full text-white text-sm';
                tagBadge.style.backgroundColor = tag.color;
                tagBadge.innerHTML = `
                    <span>${tag.name}</span>
                    <button type="button" onclick="removeTag('${id}')" class="text-white hover:text-gray-200">×</button>
                    <input type="hidden" name="tag_ids[]" value="${id}">
                `;
                selectedTagsContainer.appendChild(tagBadge);
            });
        }

        function removeTag(tagId) {
            selectedTags.delete(tagId);
            renderSelectedTags();
        }

        // Make removeTag available globally
        window.removeTag = removeTag;
    </script>
    @endpush
@endsection
