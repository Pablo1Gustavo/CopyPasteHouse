@php
    // Determine the current theme
    $theme = 'dark'; // default
    if (auth()->check() && auth()->user()->settings) {
        $userTheme = auth()->user()->settings->theme;
        if ($userTheme === 'light') {
            $theme = 'light';
        } elseif ($userTheme === 'system') {
            // For now, default to dark. Could enhance with JavaScript detection
            $theme = 'dark';
        }
    }
    
    // Define theme-aware CSS classes
    $classes = [
        'card' => $theme === 'light' ? 'bg-white border border-gray-300' : 'bg-gray-800',
        'input' => $theme === 'light' ? 'bg-gray-50 border-gray-300 text-gray-900' : 'bg-gray-900 border-gray-600 text-white',
        'text' => $theme === 'light' ? 'text-gray-900' : 'text-white',
        'text-muted' => $theme === 'light' ? 'text-gray-600' : 'text-gray-400',
        'border' => $theme === 'light' ? 'border-gray-300' : 'border-gray-700',
        'hover-bg' => $theme === 'light' ? 'hover:bg-gray-100' : 'hover:bg-gray-700',
        'table-row' => $theme === 'light' ? 'border-b border-gray-200' : 'border-b border-gray-700',
    ];
@endphp
