import AuthenticatedLayout from '@/layouts/AuthenticatedLayout'
import { Head, usePage } from '@inertiajs/react'
import { AppPageProps } from '@/types/filing/inertia'
import { getPreviewRoute } from '@/types/filing/suratRoutes'
import '../../css/dashboard.css'

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

const statusBadgeClass: Record<string, string> = {
    approved:  'badge badge-approved',
    pending:   'badge badge-pending',
    submitted: 'badge badge-submitted',
    rejected:  'badge badge-rejected',
    draft:     'badge badge-draft',
}

const statusLabel: Record<string, string> = {
    approved:  'Approved',
    pending:   'Pending',
    submitted: 'Submitted',
    rejected:  'Rejected',
    draft:     'Draft',
}

export default function Dashboard() {
    const { recentSurat } = usePage<DashboardProps>().props

    return (
        <AuthenticatedLayout>
            <Head title="Dashboard" />

            <div className="dashboard-card">
                <div className="card-header">
                    <div className="card-header-icon">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M3 2.5A1.5 1.5 0 0 1 4.5 1h5.086a1.5 1.5 0 0 1 1.06.44l2.415 2.414A1.5 1.5 0 0 1 13.5 4.914V13.5A1.5 1.5 0 0 1 12 15H4.5A1.5 1.5 0 0 1 3 13.5v-11Z" stroke="#185FA5" strokeWidth="1.2"/>
                            <path d="M6 8h4M6 10.5h2.5" stroke="#185FA5" strokeWidth="1.2" strokeLinecap="round"/>
                        </svg>
                    </div>
                    <div>
                        <div className="card-title">Surat terakhir diakses</div>
                        <div className="card-subtitle">{recentSurat.length} dokumen terbaru</div>
                    </div>
                </div>

                {recentSurat.length === 0 ? (
                    <div className="empty-state">
                        <svg viewBox="0 0 16 16" fill="none">
                            <path d="M3 2.5A1.5 1.5 0 0 1 4.5 1h5.086a1.5 1.5 0 0 1 1.06.44l2.415 2.414A1.5 1.5 0 0 1 13.5 4.914V13.5A1.5 1.5 0 0 1 12 15H4.5A1.5 1.5 0 0 1 3 13.5v-11Z" stroke="currentColor" strokeWidth="1.2"/>
                        </svg>
                        Belum ada surat yang diakses
                    </div>
                ) : (
                    <ul className="surat-list">
                        {recentSurat.map((s) => (
                            <li key={s.id}>
                                <a href={getPreviewRoute(s.jenis, s.id)} className="surat-item">
                                    <div className="surat-icon">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                            <path d="M3 2.5A1.5 1.5 0 0 1 4.5 1h5.086a1.5 1.5 0 0 1 1.06.44l2.415 2.414A1.5 1.5 0 0 1 13.5 4.914V13.5A1.5 1.5 0 0 1 12 15H4.5A1.5 1.5 0 0 1 3 13.5v-11Z" stroke="currentColor" strokeWidth="1.2"/>
                                            <path d="M6 8h4M6 10.5h2.5" stroke="currentColor" strokeWidth="1.2" strokeLinecap="round"/>
                                        </svg>
                                    </div>
                                    <div className="surat-content">
                                        <div className="surat-judul">{s.judul}</div>
                                        <div className="surat-meta">
                                            <span className="jenis-badge">{s.jenis}</span>
                                            <span className="surat-dot">·</span>
                                            <span className="surat-nomor">{s.nomor_surat}</span>
                                            <span className={statusBadgeClass[s.status] ?? 'badge badge-draft'}>
                                                {statusLabel[s.status] ?? s.status}
                                            </span>
                                        </div>
                                    </div>
                                    <span className="surat-time">{s.last_viewed_at}</span>
                                </a>
                            </li>
                        ))}
                    </ul>
                )}
            </div>
        </AuthenticatedLayout>
    )
}