<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Menu;
use App\Models\User;
use App\Models\UserRole;
use App\Models\RoleMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function __construct()
    {
        // Role management requires super admin access
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access to role management.');
        }

        $query = Role::with(['creator', 'updater']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('display_name', 'ILIKE', "%{$search}%")
                  ->orWhere('description', 'ILIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->get('status') === 'active');
        }

        if ($request->filled('system')) {
            $query->where('is_system', $request->get('system') === 'system');
        }

        $roles = $query->orderBy('name')->paginate(15);

        return view('settings.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized to create roles.');
        }

        return view('settings.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized to create roles.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $validated['id'] = Str::uuid();
        $validated['is_system'] = false;

        $role = Role::create($validated);

        return redirect()->route('settings.roles.index')
                        ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized to view roles.');
        }

        $role->load(['creator', 'updater', 'userRoles.user', 'userRoles.assignedBy', 'roleMenus.menu.module']);
        
        return view('settings.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized to edit roles.');
        }

        return view('settings.roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized to update roles.');
        }

        // Prevent editing system roles unless super admin
        if ($role->is_system && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Cannot edit system roles.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $role->update($validated);

        return redirect()->route('settings.roles.index')
                        ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized to delete roles.');
        }

        // Prevent deleting system roles
        if ($role->is_system) {
            abort(403, 'Cannot delete system roles.');
        }

        // Check if role has users
        if ($role->userRoles()->exists()) {
            return redirect()->route('settings.roles.index')
                            ->with('error', 'Cannot delete role that has users assigned. Please remove user assignments first.');
        }

        $role->delete();

        return redirect()->route('settings.roles.index')
                        ->with('success', 'Role deleted successfully.');
    }

    /**
     * Toggle role status
     */
    public function toggleStatus(Role $role)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized to update roles.');
        }

        $role->update(['is_active' => !$role->is_active]);

        $status = $role->is_active ? 'activated' : 'deactivated';
        
        return response()->json([
            'success' => true,
            'message' => "Role {$status} successfully.",
            'is_active' => $role->is_active
        ]);
    }

    /**
     * Show role permissions management
     */
    public function permissions(Role $role)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized to manage role permissions.');
        }

        $role->load(['roleMenus.menu.module']);
        $menus = Menu::with('module')->active()->orderBy('sort_order')->get();
        $assignedMenus = $role->roleMenus->pluck('menu_id')->toArray();

        return view('settings.roles.permissions', compact('role', 'menus', 'assignedMenus'));
    }

    /**
     * Update role permissions
     */
    public function updatePermissions(Request $request, Role $role)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized to manage role permissions.');
        }

        $validated = $request->validate([
            'menus' => 'nullable|array',
            'menus.*' => 'exists:menus,id',
            'permissions' => 'nullable|array',
        ]);

        // Remove existing role menu assignments
        $role->roleMenus()->delete();

        // Add new assignments
        if (!empty($validated['menus'])) {
            foreach ($validated['menus'] as $menuId) {
                $permissions = $validated['permissions'][$menuId] ?? ['read'];
                
                RoleMenu::create([
                    'id' => Str::uuid(),
                    'role_id' => $role->id,
                    'menu_id' => $menuId,
                    'permissions' => $permissions,
                    'is_active' => true,
                ]);
            }
        }

        return redirect()->route('settings.roles.show', $role)
                        ->with('success', 'Role permissions updated successfully.');
    }

    /**
     * Show users management
     */
    public function users(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized to manage users.');
        }

        $query = User::with(['userRoles.role']);

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

        $users = $query->paginate(15);
        $roles = Role::active()->orderBy('display_name')->get();

        return view('settings.users.index', compact('users', 'roles'));
    }

    /**
     * Show user details
     */
    public function showUser(User $user)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized to view user details.');
        }

        $user->load(['userRoles.role']);
        $availableRoles = Role::active()->orderBy('display_name')->get();

        return view('settings.users.show', compact('user', 'availableRoles'));
    }

    /**
     * Update user roles
     */
    public function updateUserRoles(Request $request, User $user)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized to manage user roles.');
        }

        $validated = $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        // Remove existing role assignments
        $user->userRoles()->delete();

        // Add new role assignments
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
                        ->with('success', 'User roles updated successfully.');
    }
}
