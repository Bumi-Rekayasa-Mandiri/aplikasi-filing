import AuthenticatedLayout from '@/layouts/AuthenticatedLayout'
import { Head, usePage, Link } from '@inertiajs/react'
import { useState } from 'react'
import type { AppPageProps } from '@/types/filing/inertia'

interface Permission { id: number; name: string }
interface Role {
    id: number; name: string
    permissions: Permission[]
    users: { id: number; name: string }[]
}
interface UserRow {
    id: number; name: string; email: string
    roles: string[]; permissions: string[]
}

type Props = AppPageProps & { roles: Role[]; users: UserRow[] }

export default function Index() {
    const { roles, users } = usePage<Props>().props
    const [activeTab, setActiveTab] = useState<'roles' | 'users'>('roles')

    return (
        <AuthenticatedLayout>
            <Head title="Roles & Permissions" />

            <div className="space-y-6">
                {/* HEADER */}
                <div className="flex items-center justify-between">
                    <h1 className="text-2xl font-semibold text-gray-800">
                        Roles & Permissions
                    </h1>
                    <Link
                        href={route('roles.create')}
                        className="btn btn-sm bg-green-700 px-4 py-2 rounded-full text-white font-semibold"
                    >
                        + Create Role
                    </Link>
                </div>

                {/* TABS */}
                <div className="flex gap-2 border-b border-gray-200">
                    <button
                        className={`px-4 py-2 text-sm font-semibold border-b-2 transition ${
                            activeTab === 'roles'
                                ? 'border-green-700 text-green-700'
                                : 'border-transparent text-gray-500'
                        }`}
                        onClick={() => setActiveTab('roles')}
                    >
                        Roles
                    </button>
                    <button
                        className={`px-4 py-2 text-sm font-semibold border-b-2 transition ${
                            activeTab === 'users'
                                ? 'border-green-700 text-green-700'
                                : 'border-transparent text-gray-500'
                        }`}
                        onClick={() => setActiveTab('users')}
                    >
                        Users
                    </button>
                </div>

                {/* TAB: ROLES */}
                {activeTab === 'roles' && (
                    <table className="table w-full border">
                        <thead className="bg-green-700 text-white">
                            <tr>
                                <th className="px-4 py-3 text-center">Nama User</th>
                                <th className="px-4 py-3 text-center">Role</th>
                                <th className="px-4 py-3 text-center">Permissions</th>
                            </tr>
                        </thead>
                        <tbody className="text-center">
                            {roles.length === 0 && (
                                <tr>
                                    <td colSpan={3} className="py-6 text-gray-400">
                                        No roles found.
                                    </td>
                                </tr>
                            )}
                            {roles.map(role => (
                                <tr key={role.id} className="border-b">
                                    <td className="px-4 py-3">
                                        {role.users.length === 0 ? (
                                            <span className="text-gray-400 text-sm">No users</span>
                                        ) : (
                                            role.users.map(u => <div key={u.id}>{u.name}</div>)
                                        )}
                                    </td>
                                    <td className="font-medium">{role.name}</td>
                                    <td className="px-4 py-3">
                                        <div className="flex flex-wrap gap-2 justify-center">
                                            {role.permissions.length === 0 ? (
                                                <span className="text-sm text-gray-400">No permissions</span>
                                            ) : (
                                                role.permissions.map(p => (
                                                    <span key={p.id} className="badge badge-outline text-xs">
                                                        {p.name}
                                                    </span>
                                                ))
                                            )}
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                )}

                {/* TAB: USERS */}
                {activeTab === 'users' && (
                    <table className="table w-full border">
                        <thead className="bg-green-700 text-white">
                            <tr>
                                <th className="px-4 py-3 text-center">Nama</th>
                                <th className="px-4 py-3 text-center">Email</th>
                                <th className="px-4 py-3 text-center">Roles</th>
                                <th className="px-4 py-3 text-center">Permissions</th>
                            </tr>
                        </thead>
                        <tbody className="text-center">
                            {users.length === 0 && (
                                <tr>
                                    <td colSpan={4} className="py-6 text-gray-400">
                                        No users found.
                                    </td>
                                </tr>
                            )}
                            {users.map(user => (
                                <tr key={user.id} className="border-b">
                                    <td className="px-4 py-3 font-medium">{user.name}</td>
                                    <td className="px-4 py-3 text-sm text-gray-600">{user.email}</td>
                                    <td className="px-4 py-3">
                                        <div className="flex flex-wrap gap-1 justify-center">
                                            {user.roles.length === 0 ? (
                                                <span className="text-gray-400 text-sm">No roles</span>
                                            ) : (
                                                user.roles.map(r => (
                                                    <span key={r} className="badge bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                                                        {r}
                                                    </span>
                                                ))
                                            )}
                                        </div>
                                    </td>
                                    <td className="px-4 py-3">
                                        <div className="flex flex-wrap gap-1 justify-center">
                                            {user.permissions.length === 0 ? (
                                                <span className="text-gray-400 text-sm">No permissions</span>
                                            ) : (
                                                user.permissions.map(p => (
                                                    <span key={p} className="badge badge-outline text-xs">
                                                        {p}
                                                    </span>
                                                ))
                                            )}
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                )}
            </div>
        </AuthenticatedLayout>
    )
}