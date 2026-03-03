import AuthenticatedLayout from '@/layouts/AuthenticatedLayout'
import { Head, usePage, Link } from '@inertiajs/react'
import { AppPageProps } from '@/types/filing/inertia'
import { getPreviewRoute } from '@/types/filing/suratRoutes'

interface DashboardProps extends AppPageProps {
        recentSurat: {
            id: number
            judul: string
            nomor_surat: string
            jenis: string
            status: string
            last_viewed_at: string
        }[]
    }

export default function Dashboard() {
    const { recentSurat } = usePage<DashboardProps>().props

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Dashboard
                </h2>
            }
        >
            <Head title="Dashboard" />

            <div className="card bg-white shadow border border-black">
                <div className="card-body">
                    <h2 className="font-semibold text-lg mb-2">
                        Surat Terakhir Diakses
                    </h2>

                    {recentSurat.length === 0 && (
                        <p className="text-sm text-gray-500">
                            Belum ada surat yang diakses
                        </p>
                    )}

                    {/* ✅ Satu loop saja, route ditentukan dari jenis */}
                    <ul className="space-y-2">
                        {recentSurat.map((s) => (
                            <li key={s.id}>
                                
                                <a href={getPreviewRoute(s.jenis, s.id)}
                                    className="block p-2 border border-black rounded hover:bg-gray-100"
                                >
                                    <div className="font-semibold">{s.judul}</div>
                                    <div className="text-xs text-gray-500">
                                        {s.jenis} • {s.nomor_surat}
                                    </div>
                                    <div className="text-xs text-gray-400">
                                        {s.last_viewed_at}
                                    </div>
                                </a>
                            </li>
                        ))}
                    </ul>
                </div>
            </div>
        </AuthenticatedLayout>
    )
}