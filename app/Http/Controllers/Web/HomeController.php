<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PasteLike;
use App\Models\PasteAccessLog;

class HomeController extends Controller
{
    public function index()
    {
        // Use custom queries to show popular and trending content
        $popularPastes = PasteLike::getMostLikedPastes(5);
        $trendingPastes = PasteAccessLog::getMostViewedPastes(5);
        $recentActivity = PasteLike::getRecentLikes(10);

        // Get theme settings
        $userSettings = auth()->check() ? auth()->user()->settings : null;
        $isLight = $userSettings?->theme === 'light';

        return view('home', [
            'popularPastes' => $popularPastes,
            'trendingPastes' => $trendingPastes,
            'recentActivity' => $recentActivity,
            'isLight' => $isLight,
            'cardClass' => $isLight ? 'bg-white border border-gray-200' : 'bg-gray-800',
            'innerCardClass' => $isLight ? 'bg-gray-50 border border-gray-100' : 'bg-gray-900',
            'textClass' => $isLight ? 'text-gray-900' : 'text-white',
            'mutedClass' => $isLight ? 'text-gray-600' : 'text-gray-400',
            'linkClass' => $isLight ? 'text-blue-600 hover:text-blue-700' : 'text-blue-400 hover:text-blue-300',
            'hoverClass' => $isLight ? 'hover:bg-gray-100' : 'hover:bg-gray-700',
        ]);
    }
}
