<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PasteLike;
use App\Models\CommentLike;
use App\Models\PasteAccessLog;
use App\Models\Paste;
use App\Models\User;

class StatisticsController extends Controller
{
    public function index()
    {
        // Use PasteLike custom queries
        $mostLikedPastes = PasteLike::getMostLikedPastes(10);
        $recentPasteLikes = PasteLike::getRecentLikes(10);
        $totalPasteLikes = PasteLike::count(); // Total count of all paste likes

        // Use CommentLike custom queries
        $recentCommentLikes = CommentLike::getRecentLikes(10);
        $totalCommentLikes = CommentLike::count(); // Total count of all comment likes

        // Use PasteAccessLog custom queries
        $mostViewedPastes = PasteAccessLog::getMostViewedPastes(10);

        // Basic counts
        $totalPastes = Paste::count();
        $totalUsers = User::count();

        return view('admin.statistics', [
            'mostLikedPastes' => $mostLikedPastes,
            'recentPasteLikes' => $recentPasteLikes,
            'totalPasteLikes' => $totalPasteLikes,
            'recentCommentLikes' => $recentCommentLikes,
            'totalCommentLikes' => $totalCommentLikes,
            'mostViewedPastes' => $mostViewedPastes,
            'totalPastes' => $totalPastes,
            'totalUsers' => $totalUsers,
        ]);
    }
}
