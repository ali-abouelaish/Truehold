<?php

namespace App\Http\Controllers;

use App\Models\AdminPermission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminPermissionController extends Controller
{
    /**
     * Display a listing of admin permissions.
     */
    public function index(): View
    {
        $users = User::with('adminPermissions')->get();
        $sections = AdminPermission::getAvailableSections();
        
        return view('admin.permissions.index', compact('users', 'sections'));
    }

    /**
     * Show the form for editing permissions for a specific user.
     */
    public function edit(User $user): View
    {
        $sections = AdminPermission::getAvailableSections();
        $permissions = $user->adminPermissions()->get()->keyBy('section');
        
        return view('admin.permissions.edit', compact('user', 'sections', 'permissions'));
    }

    /**
     * Update permissions for a specific user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*.section' => 'required|string',
            'permissions.*.can_view' => 'boolean',
            'permissions.*.can_create' => 'boolean',
            'permissions.*.can_edit' => 'boolean',
            'permissions.*.can_delete' => 'boolean',
        ]);

        // Delete existing permissions for this user
        $user->adminPermissions()->delete();


        // Create new permissions - only for sections that are checked (can_view = true)
        foreach ($request->permissions as $permissionData) {
            if (isset($permissionData['section']) && !empty($permissionData['section'])) {
                // Only create permission if can_view is checked (checkbox was checked)
                // Check if can_view exists and is truthy (not just present)
                if (isset($permissionData['can_view']) && $permissionData['can_view'] == '1') {
                    AdminPermission::create([
                        'user_id' => $user->id,
                        'section' => $permissionData['section'],
                        'can_view' => true,
                        'can_create' => $permissionData['can_create'] == '1',
                        'can_edit' => $permissionData['can_edit'] == '1',
                        'can_delete' => $permissionData['can_delete'] == '1',
                    ]);
                }
            }
        }

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permissions updated successfully.');
    }

    /**
     * Reset permissions for a user (remove all permissions).
     */
    public function reset(User $user): RedirectResponse
    {
        $user->adminPermissions()->delete();

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permissions reset successfully.');
    }
}
