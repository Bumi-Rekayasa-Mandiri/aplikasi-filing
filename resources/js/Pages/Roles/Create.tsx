import { useForm, usePage, Link } from '@inertiajs/react';
import type { AppPageProps } from '@/types/filing/inertia';

interface Permission {
    id: number;
    name: string;
}

type Props = AppPageProps & {
    permissions: Permission[];
};

export default function Create() {
    const { permissions } = usePage<Props>().props;

    const form = useForm({
        name: '',
        permissions: [] as string[],
    });

    const togglePermission = (name: string) => {
        form.setData(
            'permissions',
            form.data.permissions.includes(name)
                ? form.data.permissions.filter(p => p !== name)
                : [...form.data.permissions, name]
        );
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post(route('roles.store'));
    };

    return (
        <div className="max-w-4xl mx-auto p-6">
            <h1 className="text-xl font-semibold mb-4">Buat Role Baru</h1>

            <form onSubmit={submit} className="space-y-6">
                {/* Role Name */}
                <div>
                    <label className="block text-sm font-medium mb-1">
                        Nama Role
                    </label>
                    <input
                        className="input input-bordered w-full"
                        value={form.data.name}
                        onChange={e => form.setData('name', e.target.value)}
                    />
                    {form.errors.name && (
                        <p className="text-sm text-red-500 mt-1">
                            {form.errors.name}
                        </p>
                    )}
                </div>

                {/* Permissions */}
                <div>
                    <h2 className="font-medium mb-2">Permissions</h2>

                    <div className="grid grid-cols-2 md:grid-cols-3 gap-3">
                        {permissions.map(permission => (
                            <label
                                key={permission.id}
                                className="flex items-center gap-2"
                            >
                                <input
                                    type="checkbox"
                                    className="checkbox checkbox-sm"
                                    checked={form.data.permissions.includes(permission.name)}
                                    onChange={() => togglePermission(permission.name)}
                                />
                                <span className="text-sm">
                                    {permission.name}
                                </span>
                            </label>
                        ))}
                    </div>
                </div>

                {/* Actions */}
                <div className="flex gap-3">
                    <button
                        type="submit"
                        className="btn btn-primary"
                        disabled={form.processing}
                    >
                        Simpan
                    </button>

                    <Link href={route('roles.index')} className="btn btn-ghost">
                        Batal
                    </Link>
                </div>
            </form>
        </div>
    );
}