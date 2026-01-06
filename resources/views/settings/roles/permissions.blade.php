<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('settings.roles.show', $role) }}" 
                   class="inline-flex items-center text-gray-600 hover:text-gray-800 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Role
                </a>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                    Manage Permissions: {{ $role->display_name }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('settings.roles.update-permissions', $role) }}" method="POST" x-data="permissionForm()">
                @csrf
                @method('PATCH')
                
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Configure Menu Access</h3>
                        <p class="text-sm text-gray-600 mt-1">Select which menus and modules this role can access and their permissions.</p>
                    </div>

                    <div class="p-6">
                        @php
                            $menusByModule = $menus->groupBy(function($menu) {
                                return $menu->module->display_name ?? 'Other';
                            });
                            $assignedMenusData = [];
                            foreach($role->roleMenus->where('is_active', true) as $roleMenu) {
                                // Ensure permissions is always an array
                                $permissions = $roleMenu->permissions;
                                if (is_string($permissions)) {
                                    $permissions = json_decode($permissions, true) ?? ['read'];
                                }
                                $assignedMenusData[$roleMenu->menu_id] = is_array($permissions) ? $permissions : ['read'];
                            }
                        @endphp

                        <div class="space-y-8">
                            @foreach($menusByModule as $moduleName => $moduleMenus)
                                <div>
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="text-md font-semibold text-gray-900 flex items-center space-x-2">
                                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                            </svg>
                                            <span>{{ $moduleName }}</span>
                                        </h4>
                                        <label class="flex items-center space-x-2 cursor-pointer">
                                            <input type="checkbox" 
                                                   @change="toggleModuleMenus($event, '{{ $moduleName }}', $event.target.checked)"
                                                   class="w-4 h-4 text-[#006BFF] bg-white border-gray-300 rounded focus:ring-[#006BFF] focus:ring-2">
                                            <span class="text-sm text-gray-600">Select All</span>
                                        </label>
                                    </div>
                                    
                                    <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                        @foreach($moduleMenus as $menu)
                                            <div class="flex items-start space-x-4 p-4 bg-white rounded-lg border border-gray-200" 
                                                 x-data="{ menuId: 'menu_{{ $menu->id }}', isChecked: {{ in_array($menu->id, $assignedMenus) ? 'true' : 'false' }} }"
                                                 x-init="if(isChecked) form.selectedMenus.push(menuId)">
                                                <div class="flex items-center space-x-3 flex-1">
                                                    <input type="checkbox" 
                                                           :id="menuId"
                                                           name="menus[]" 
                                                           value="{{ $menu->id }}"
                                                           x-model="isChecked"
                                                           @change="toggleMenu(menuId, $event.target.checked)"
                                                           {{ in_array($menu->id, $assignedMenus) ? 'checked' : '' }}
                                                           class="w-4 h-4 text-[#006BFF] bg-white border-gray-300 rounded focus:ring-[#006BFF] focus:ring-2">
                                                    <label :for="menuId" class="flex-1 cursor-pointer">
                                                        <div class="font-medium text-gray-900">{{ $menu->display_name }}</div>
                                                        <div class="text-xs text-gray-600">{{ $menu->description ?? $menu->route }}</div>
                                                    </label>
                                                </div>
                                                
                                                <div class="flex items-center space-x-3" x-show="isChecked">
                                                    <div class="flex items-center space-x-2">
                                                        <label class="flex items-center space-x-1">
                                                            <input type="checkbox" 
                                                                   name="permissions[{{ $menu->id }}][]" 
                                                                   value="read"
                                                                   {{ isset($assignedMenusData[$menu->id]) && in_array('read', $assignedMenusData[$menu->id]) ? 'checked' : '' }}
                                                                   class="w-3 h-3 text-[#006BFF] bg-white border-gray-300 rounded">
                                                            <span class="text-xs text-gray-600">Read</span>
                                                        </label>
                                                        <label class="flex items-center space-x-1">
                                                            <input type="checkbox" 
                                                                   name="permissions[{{ $menu->id }}][]" 
                                                                   value="create"
                                                                   {{ isset($assignedMenusData[$menu->id]) && in_array('create', $assignedMenusData[$menu->id]) ? 'checked' : '' }}
                                                                   class="w-3 h-3 text-[#006BFF] bg-white border-gray-300 rounded">
                                                            <span class="text-xs text-gray-600">Create</span>
                                                        </label>
                                                        <label class="flex items-center space-x-1">
                                                            <input type="checkbox" 
                                                                   name="permissions[{{ $menu->id }}][]" 
                                                                   value="update"
                                                                   {{ isset($assignedMenusData[$menu->id]) && in_array('update', $assignedMenusData[$menu->id]) ? 'checked' : '' }}
                                                                   class="w-3 h-3 text-[#006BFF] bg-white border-gray-300 rounded">
                                                            <span class="text-xs text-gray-600">Update</span>
                                                        </label>
                                                        <label class="flex items-center space-x-1">
                                                            <input type="checkbox" 
                                                                   name="permissions[{{ $menu->id }}][]" 
                                                                   value="delete"
                                                                   {{ isset($assignedMenusData[$menu->id]) && in_array('delete', $assignedMenusData[$menu->id]) ? 'checked' : '' }}
                                                                   class="w-3 h-3 text-[#006BFF] bg-white border-gray-300 rounded">
                                                            <span class="text-xs text-gray-600">Delete</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="px-6 py-4 bg-white border-t border-gray-200 flex items-center justify-end space-x-3">
                        <a href="{{ route('settings.roles.show', $role) }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:text-gray-900 hover:border-gray-400 transition-colors duration-200">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-2 bg-[#006BFF] hover:bg-[#006BFF]/80 text-white font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Save Permissions
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function permissionForm() {
            return {
                form: {
                    selectedMenus: []
                },

                toggleMenu(menuId, isChecked) {
                    const checkbox = document.getElementById(menuId);
                    if (isChecked && !this.form.selectedMenus.includes(menuId)) {
                        this.form.selectedMenus.push(menuId);
                    } else if (!isChecked) {
                        this.form.selectedMenus = this.form.selectedMenus.filter(id => id !== menuId);
                    }
                },

                toggleModuleMenus(evt, moduleName, isChecked) {
                    // Find all menus in this module
                    const moduleContainer = evt.target.closest('.space-y-8 > div');
                    const checkboxes = moduleContainer.querySelectorAll('input[type="checkbox"][name="menus[]"]');
                    const permissionCheckboxes = moduleContainer.querySelectorAll('input[name^="permissions"]');
                    
                    checkboxes.forEach(cb => {
                        cb.checked = isChecked;
                        const menuId = 'menu_' + cb.value;
                        if (isChecked && !this.form.selectedMenus.includes(menuId)) {
                            this.form.selectedMenus.push(menuId);
                        } else if (!isChecked) {
                            this.form.selectedMenus = this.form.selectedMenus.filter(id => id !== menuId);
                        }
                    });

                    // Also check read permission by default when selecting menu
                    if (isChecked) {
                        permissionCheckboxes.forEach(cb => {
                            if (cb.value === 'read') {
                                cb.checked = true;
                            }
                        });
                    }
                }
            }
        }
    </script>
</x-app-layout>

