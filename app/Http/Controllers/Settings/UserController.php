<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        // User management requires super admin access
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access to user management.');
        }

        $query = User::with(['userRoles.role']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('userRoles', function($q) use ($request) {
                $q->where('role_id', $request->get('role'));
            });
        }

        $users = $query->orderBy('name')->paginate(15);
        $roles = Role::active()->orderBy('display_name')->get();

        return view('settings.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized to create users.');
        }

        $roles = Role::active()->orderBy('display_name')->get();
        return view('settings.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized to create users.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        // Create user
        $user = User::create([
            'id' => Str::uuid(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        // Assign roles
        if (!empty($validated['roles'])) {
            foreach ($validated['roles'] as $roleId) {
                UserRole::create([
                    'id' => Str::uuid(),
                    'user_id' => $user->id,
                    'role_id' => $roleId,
                    'is_active' => true,
                    'assigned_at' => now(),
                    'assigned_by' => auth()->id(),
                ]);
            }
        }

        return redirect()->route('settings.users.index')
                        ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized to view user details.');
        }

        $user->load(['userRoles.role', 'userRoles.assignedBy']);
        $availableRoles = Role::active()->orderBy('display_name')->get();

        return view('settings.users.show', compact('user', 'availableRoles'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized to edit users.');
        }

        $user->load(['userRoles.role']);
        $availableRoles = Role::active()->orderBy('display_name')->get();

        return view('settings.users.edit', compact('user', 'availableRoles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized to update users.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        // Update user basic info
        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        // Update roles
        $user->userRoles()->delete();

        if (!empty($validated['roles'])) {
            foreach ($validated['roles'] as $roleId) {
                UserRole::create([
                    'id' => Str::uuid(),
                    'user_id' => $user->id,
                    'role_id' => $roleId,
                    'is_active' => true,
                    'assigned_at' => now(),
                    'assigned_by' => auth()->id(),
                ]);
            }
        }

        return redirect()->route('settings.users.show', $user)
                        ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized to delete users.');
        }

        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return redirect()->route('settings.users.index')
                            ->with('error', 'Cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('settings.users.index')
                        ->with('success', 'User deleted successfully.');
    }
}

