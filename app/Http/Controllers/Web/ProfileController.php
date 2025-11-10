<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Services\UserSettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function __construct(
        private UserService $userService,
        private UserSettingsService $userSettingsService
    ) {}

    public function edit()
    {
        // Ensure settings are loaded, create default if not exists
        $user = Auth::user()->load('settings');
        
        if (!$user->settings) {
            $this->userSettingsService->createOrUpdate($user->id, [
                'timezone' => 'UTC',
                'language' => 'en',
                'theme' => 'system',
            ]);
            $user->load('settings');
        }
        
        return view('profile.edit');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore(Auth::id())],
            'email' => ['required', 'email', Rule::unique('users')->ignore(Auth::id())],
        ]);

        $this->userService->edit(Auth::user(), $validated);

        return redirect()->route('profile.edit')
            ->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $success = $this->userService->changePassword(
            Auth::user(),
            $validated['current_password'],
            $validated['new_password']
        );

        if (!$success) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        return redirect()->route('profile.edit')
            ->with('success', 'Password changed successfully!');
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'timezone' => 'required|string|max:40',
            'language' => 'required|string|max:10',
            'theme' => 'required|in:light,dark,system',
        ]);

        $this->userSettingsService->createOrUpdate(Auth::id(), $validated);

        return redirect()->route('profile.edit')
            ->with('success', 'Settings updated successfully!');
    }
}
