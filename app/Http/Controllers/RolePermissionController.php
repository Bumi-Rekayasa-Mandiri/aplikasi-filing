<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    //
    public function index()
    {
        $roles = Role::with(['permissions', 'users'])
            ->get()
            ->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions' => $role->permissions->map(fn ($p) => [
                        'id' => $p->id,
                        'name' => $p->name,
                    ]),
                    'users' => $role->users->map(fn ($u) => [
                        'id' => $u->id,
                        'name' => $u->name,
                    ]),
                ];
            });

        return inertia('Roles/Index', [
            'roles' => $roles,
        ]);
    }

    public function create ()
    {
        return Inertia::render('Roles/Create', [
            'permissions' => Permission::orderBy('name')->get(),
        ]);
    }

    public function store (Request $request)
    {
        $this->authorize('create', Role::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Role::unique('roles', 'name')],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role = Role::create(['name' => $validated['name'],
        ]);

        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()
            ->route('Roles.Index')
            ->with('success', 'Role created successfully.');
    }

    // public function __construct()
    // {
    //     $this->middleware('permission:view roles')->only('index');
    //     $this->middleware('permission:create roles')->only(['create', 'store']);
    //     $this->middleware('permission:manage roles')->only(['edit', 'update', 'destroy']);
    // }
}