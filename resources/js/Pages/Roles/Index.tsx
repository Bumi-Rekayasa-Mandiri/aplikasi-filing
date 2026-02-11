import AuthenticatedLayout from '@/layouts/AuthenticatedLayout';
import { Head, usePage, Link } from '@inertiajs/react';
import type { AppPageProps } from '@/types/filing/inertia';

interface Permission {
    id: number;
    name: string;
}

interface Role {
    id: number;
    name: string;
    permissions: Permission[];
}

type Props = AppPageProps & {
    roles: Role[];
};

export default function Index() {
    const { roles } = usePage<Props>().props;

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

                {/* TABLE */}
                    <table className="table table-zebra w-full border text sm mt-4">
                        <thead className="bg-green-700 text-white">
                            <tr>
                                <th className="px-4 py-3 text-center w-[180px] font-semibold">No</th>
                                <th className="px-4 py-3 text-center font-semibold">Role</th>
                                <th className="px-4 py-3 text-center font-semibold">Permissions</th>
                            </tr>
                        </thead>
                        <tbody className="text-center px-4 py-3 font-semibold">
                            <tr>
                                <td>


                                </td>
                            </tr>

                            {roles.length === 0 && (
                                <tr>
                                    <td
                                        colSpan={2}
                                        className="text-center px-4 py-3 text-gray-500 py-6"
                                    >
                                        No roles found.
                                    </td>
                                </tr>
                            )}

                            {roles.map((role) => (
                                <tr key={role.id}>
                                    <td className="font-medium">
                                        {role.name}
                                    </td>

                                    <td className='text-center px-4 py-3'>
                                        <div className="flex flex-wrap gap-2">
                                            {role.permissions.length === 0 ? (
                                                <span className="text-sm text-gray-400">
                                                    No permissions
                                                </span>
                                            ) : (
                                                role.permissions.map((permission) => (
                                                    <span
                                                        key={permission.id}
                                                        className="badge badge-outline"
                                                    >
                                                        {permission.name}
                                                    </span>
                                                ))
                                            )}
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
                
        </AuthenticatedLayout>
    );
}