<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Role::class);

        $roles = Role::with(['permissions', 'users'])
            ->get()
            ->map(fn($role) => [
                'id'          => $role->id,
                'name'        => $role->name,
                'permissions' => $role->permissions->map(fn($p) => ['id' => $p->id, 'name' => $p->name]),
                'users'       => $role->users->map(fn($u) => ['id' => $u->id, 'name' => $u->name]),
            ]);

        // ✅ Tambah data users dengan roles & permissions
        $users = User::with(['roles', 'permissions'])->get()->map(fn($user) => [
            'id'          => $user->id,
            'name'        => $user->name,
            'email'       => $user->email,
            'roles'       => $user->roles->pluck('name'),
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ]);

        return Inertia::render('Roles/Index', [
            'roles' => $roles,
            'users' => $users,
        ]);
    }

    public function create()
    {
        $this->authorize('create', Role::class);

        return Inertia::render('Roles/Create', [
            'permissions' => Permission::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Role::class);

        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions'    => ['array'],
            'permissions.*'  => ['exists:permissions,name'],
        ]);

        $role = Role::create(['name' => $validated['name']]);

        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()->route('roles.index')->with('success', 'Role berhasil dibuat.');
    }
}