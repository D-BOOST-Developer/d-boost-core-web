<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    public function __construct()
    {
        // Menu management requires super admin or admin with settings access
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->canAccessMenu('menus_management', 'read')) {
            abort(403, 'Unauthorized access to menu management.');
        }

        $query = Menu::with(['module', 'parent', 'creator', 'updater']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('display_name', 'ILIKE', "%{$search}%")
                  ->orWhere('description', 'ILIKE', "%{$search}%")
                  ->orWhere('route', 'ILIKE', "%{$search}%");
            });
        }

        if ($request->filled('module')) {
            $query->where('module_id', $request->get('module'));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->get('status') === 'active');
        }

        if ($request->filled('system')) {
            $query->where('is_system', $request->get('system') === 'system');
        }

        $menus = $query->orderBy('sort_order')->paginate(15);
        $modules = Module::active()->orderBy('display_name')->get();

        return view('settings.menus.index', compact('menus', 'modules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->canAccessMenu('menus_management', 'create')) {
            abort(403, 'Unauthorized to create menus.');
        }

        $modules = Module::active()->orderBy('display_name')->get();
        $parentMenus = Menu::whereNull('parent_id')->orderBy('display_name')->get();
        
        $selectedModule = null;
        if ($request->filled('module')) {
            $selectedModule = Module::find($request->get('module'));
        }

        return view('settings.menus.create', compact('modules', 'parentMenus', 'selectedModule'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->canAccessMenu('menus_management', 'create')) {
            abort(403, 'Unauthorized to create menus.');
        }

        $validated = $request->validate([
            'module_id' => 'required|exists:modules,id',
            'parent_id' => 'nullable|exists:menus,id',
            'name' => 'required|string|max:255|unique:menus,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'route' => 'nullable|string|max:255',
            'icon' => 'nullable|string',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'permissions' => 'nullable|array',
        ]);

        $validated['id'] = Str::uuid();
        $validated['is_system'] = false;

        $menu = Menu::create($validated);

        return redirect()->route('settings.menus.index')
                        ->with('success', 'Menu created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu)
    {
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->canAccessMenu('menus_management', 'read')) {
            abort(403, 'Unauthorized to view menus.');
        }

        $menu->load(['module', 'parent', 'children', 'creator', 'updater', 'roleMenus.role']);
        
        return view('settings.menus.show', compact('menu'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Menu $menu)
    {
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->canAccessMenu('menus_management', 'update')) {
            abort(403, 'Unauthorized to edit menus.');
        }

        $modules = Module::active()->orderBy('display_name')->get();
        $parentMenus = Menu::where('id', '!=', $menu->id)
                          ->whereNull('parent_id')
                          ->orderBy('display_name')
                          ->get();

        return view('settings.menus.edit', compact('menu', 'modules', 'parentMenus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->canAccessMenu('menus_management', 'update')) {
            abort(403, 'Unauthorized to update menus.');
        }

        // Prevent editing system menus unless super admin
        if ($menu->is_system && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Cannot edit system menus.');
        }

        $validated = $request->validate([
            'module_id' => 'required|exists:modules,id',
            'parent_id' => 'nullable|exists:menus,id',
            'name' => ['required', 'string', 'max:255', Rule::unique('menus')->ignore($menu->id)],
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'route' => 'nullable|string|max:255',
            'icon' => 'nullable|string',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'permissions' => 'nullable|array',
        ]);

        // Prevent circular parent relationship
        if ($validated['parent_id'] === $menu->id) {
            return back()->withErrors(['parent_id' => 'A menu cannot be its own parent.']);
        }

        $menu->update($validated);

        return redirect()->route('settings.menus.index')
                        ->with('success', 'Menu updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->canAccessMenu('menus_management', 'delete')) {
            abort(403, 'Unauthorized to delete menus.');
        }

        // Prevent deleting system menus unless super admin
        if ($menu->is_system && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Cannot delete system menus.');
        }

        // Check if menu has children
        if ($menu->children()->exists()) {
            return redirect()->route('settings.menus.index')
                            ->with('error', 'Cannot delete menu that has child menus. Please delete child menus first.');
        }

        // Check if menu is assigned to roles
        if ($menu->roleMenus()->exists()) {
            return redirect()->route('settings.menus.index')
                            ->with('error', 'Cannot delete menu that is assigned to roles. Please remove role assignments first.');
        }

        $menu->delete();

        return redirect()->route('settings.menus.index')
                        ->with('success', 'Menu deleted successfully.');
    }

    /**
     * Toggle menu status
     */
    public function toggleStatus(Menu $menu)
    {
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->canAccessMenu('menus_management', 'update')) {
            abort(403, 'Unauthorized to update menus.');
        }

        $menu->update(['is_active' => !$menu->is_active]);

        $status = $menu->is_active ? 'activated' : 'deactivated';
        
        return response()->json([
            'success' => true,
            'message' => "Menu {$status} successfully.",
            'is_active' => $menu->is_active
        ]);
    }
}
