<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserPermissionController extends Controller
{
    /**
     * Display a listing of user permissions.
     */
    public function index(): View
    {
        $users = User::with('userPermissions')->get();
        $sections = UserPermission::getAvailableSections();
        
        return view('admin.user-permissions.index', compact('users', 'sections'));
    }

    /**
     * Show the form for editing permissions for a specific user.
     */
    public function edit(User $user): View
    {
        $sections = UserPermission::getAvailableSections();
        $permissions = $user->userPermissions()->get()->keyBy('section');
        
        return view('admin.user-permissions.edit', compact('user', 'sections', 'permissions'));
    }

    /**
     * Update permissions for a specific user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'boolean'
        ]);

        // Delete existing permissions for this user
        $user->userPermissions()->delete();

        // Create new permissions for checked sections only
        foreach ($request->permissions as $section => $hasAccess) {
            if ($hasAccess) {
                UserPermission::create([
                    'user_id' => $user->id,
                    'section' => $section,
                    'can_access' => true
                ]);
            }
        }

        return redirect()->route('admin.user-permissions.index')
            ->with('success', 'Permissions updated successfully.');
    }

    /**
     * Reset permissions for a user (remove all permissions).
     */
    public function reset(User $user): RedirectResponse
    {
        $user->userPermissions()->delete();

        return redirect()->route('admin.user-permissions.index')
            ->with('success', 'Permissions reset successfully.');
    }
}