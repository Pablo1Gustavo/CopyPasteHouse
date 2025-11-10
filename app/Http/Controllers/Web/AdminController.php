<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {}

    /**
     * Display a listing of users
     */
    public function users()
    {
        // In a real app, you'd check if user is admin
        // For now, just return all users
        $users = \App\Models\User::with(['settings', 'pastes', 'comments'])
            ->withCount(['pastes', 'comments'])
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Display the specified user
     */
    public function showUser(string $id)
    {
        $user = $this->userService->show($id);

        if (!$user) {
            return redirect()->route('admin.users')->with('error', 'User not found');
        }

        return view('admin.users.show', compact('user'));
    }

    /**
     * Remove the specified user
     */
    public function destroyUser(string $id)
    {
        $user = \App\Models\User::find($id);

        if (!$user) {
            return redirect()->route('admin.users')->with('error', 'User not found');
        }

        // Don't allow deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')->with('error', 'Cannot delete your own account');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully');
    }
}
