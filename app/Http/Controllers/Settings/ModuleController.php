<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ModuleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check if user has permission to access modules management
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->canAccessMenu('modules_management', 'read')) {
            abort(403, 'Unauthorized access to modules management.');
        }

        $query = Module::with(['creator', 'updater']);

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

        $modules = $query->orderBy('sort_order')->paginate(15);

        return view('settings.modules.index', compact('modules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->canAccessMenu('modules_management', 'create')) {
            abort(403, 'Unauthorized to create modules.');
        }

        return view('settings.modules.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->canAccessMenu('modules_management', 'create')) {
            abort(403, 'Unauthorized to create modules.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:modules,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'config' => 'nullable|array',
        ]);

        $validated['id'] = Str::uuid();
        $validated['is_system'] = false; // User-created modules are not system modules

        $module = Module::create($validated);

        return redirect()->route('settings.modules.index')
                        ->with('success', 'Module created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Module $module)
    {
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->canAccessMenu('modules_management', 'read')) {
            abort(403, 'Unauthorized to view modules.');
        }

        $module->load(['menus', 'creator', 'updater']);
        
        return view('settings.modules.show', compact('module'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Module $module)
    {
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->canAccessMenu('modules_management', 'update')) {
            abort(403, 'Unauthorized to edit modules.');
        }

        return view('settings.modules.edit', compact('module'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Module $module)
    {
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->canAccessMenu('modules_management', 'update')) {
            abort(403, 'Unauthorized to update modules.');
        }

        // Prevent editing system modules unless super admin
        if ($module->is_system && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Cannot edit system modules.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('modules')->ignore($module->id)],
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'config' => 'nullable|array',
        ]);

        $module->update($validated);

        return redirect()->route('settings.modules.index')
                        ->with('success', 'Module updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Module $module)
    {
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->canAccessMenu('modules_management', 'delete')) {
            abort(403, 'Unauthorized to delete modules.');
        }

        // Prevent deleting system modules unless super admin
        if ($module->is_system && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Cannot delete system modules.');
        }

        // Check if module has menus
        if ($module->menus()->exists()) {
            return redirect()->route('settings.modules.index')
                            ->with('error', 'Cannot delete module that has menus. Please delete all menus first.');
        }

        $module->delete();

        return redirect()->route('settings.modules.index')
                        ->with('success', 'Module deleted successfully.');
    }

    /**
     * Toggle module status
     */
    public function toggleStatus(Module $module)
    {
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->canAccessMenu('modules_management', 'update')) {
            abort(403, 'Unauthorized to update modules.');
        }

        $module->update(['is_active' => !$module->is_active]);

        $status = $module->is_active ? 'activated' : 'deactivated';
        
        return response()->json([
            'success' => true,
            'message' => "Module {$status} successfully.",
            'is_active' => $module->is_active
        ]);
    }
}
