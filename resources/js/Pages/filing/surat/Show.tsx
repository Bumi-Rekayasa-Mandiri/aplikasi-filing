import AuthenticatedLayout from '@/layouts/AuthenticatedLayout'
import { Head } from '@inertiajs/react'
import DetailRouter from '@/Pages/filing/surat/detail/DetailRouter'
import '../../../../css/show-surat.css'
import StatusToggle from '@/components/filing/surat/StatusToggle'

type SuratDetail = {
  id: number
  jenis: string
  nomor_surat: string
  judul: string
  perihal: string
  tujuan: string
  tanggal_surat: string
  status: string
  cap_url?: string
  ttds?: {
    id: number
    nama: string
    jabatan: string
    label: string
    url: string
  }[]
}

type Props = {
  surat: SuratDetail
  can: { approve: boolean; revertDraft: boolean }
}

const statusClass: Record<string, string> = {
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

function getInitials(nama: string) {
  return nama.split(' ').slice(0, 2).map(n => n[0]).join('').toUpperCase()
}

// ✅ destructure 'can' dari parameter
export default function Show({ surat, can }: Props) {
  return (
    <AuthenticatedLayout>
      <Head title="Detail Surat" />

      <div className="show-page">

        {/* INFO UMUM */}
        <div className="section-card">
          <div className="section-header">
            <div className="section-icon icon-blue">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M3 2.5A1.5 1.5 0 0 1 4.5 1h5.086a1.5 1.5 0 0 1 1.06.44l2.415 2.414A1.5 1.5 0 0 1 13.5 4.914V13.5A1.5 1.5 0 0 1 12 15H4.5A1.5 1.5 0 0 1 3 13.5v-11Z" stroke="#185FA5" strokeWidth="1.2"/>
                <path d="M6 8h4M6 10.5h2.5" stroke="#185FA5" strokeWidth="1.2" strokeLinecap="round"/>
              </svg>
            </div>
            <span className="section-title">Informasi surat</span>
          </div>

          <div className="nomor-surat">{surat.nomor_surat}</div>

          <div className="info-grid">
            <div className="info-item">
              <span className="info-label">Jenis</span>
              <span className="info-value">{surat.jenis}</span>
            </div>
            <div className="info-item">
              <span className="info-label">Tanggal</span>
              <span className="info-value">{surat.tanggal_surat}</span>
            </div>
            <div className="info-item">
              <span className="info-label">Judul</span>
              <span className="info-value">{surat.judul}</span>
            </div>
            <div className="info-item">
              <span className="info-label">Status</span>
              <span className={statusClass[surat.status] ?? 'badge badge-draft'}>
                {statusLabel[surat.status] ?? surat.status}
              </span>
            </div>
            <div className="info-item">
              <span className="info-label">Perihal</span>
              <span className="info-value">{surat.perihal}</span>
            </div>
            <div className="info-item">
              <span className="info-label">Tujuan</span>
              <span className="info-value">{surat.tujuan}</span>
            </div>
          </div>
                  {(can?.approve || can?.revertDraft) && (
                   <div className="status-toggle-section">
                      <StatusToggle
                        suratId={surat.id}
                        status={surat.status as 'draft' | 'approved'}
                        canApprove={can?.approve || can?.revertDraft}
                      />
                    </div>
                  )}
        </div>{/* tutup section-card */}

        {/* DETAIL SPESIFIK */}
        <div className="section-card">
          <div className="section-header">
            <div className="section-icon icon-purple">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <rect x="2" y="2" width="12" height="12" rx="2" stroke="#534AB7" strokeWidth="1.2"/>
                <path d="M5 5.5h6M5 8h6M5 10.5h4" stroke="#534AB7" strokeWidth="1.2" strokeLinecap="round"/>
              </svg>
            </div>
            <span className="section-title">Detail surat</span>
          </div>
          <DetailRouter surat={surat} />
        </div>

        {/* CAP PERUSAHAAN */}
        {surat.cap_url && (
          <div className="section-card">
            <div className="section-header">
              <div className="section-icon icon-teal">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                  <circle cx="8" cy="8" r="6" stroke="#0F6E56" strokeWidth="1.2"/>
                  <circle cx="8" cy="8" r="3" stroke="#0F6E56" strokeWidth="1.2"/>
                  <path d="M8 2v2M8 12v2M2 8h2M12 8h2" stroke="#0F6E56" strokeWidth="1.2" strokeLinecap="round"/>
                </svg>
              </div>
              <span className="section-title">Cap perusahaan</span>
            </div>
            <img src={surat.cap_url} className="cap-img" alt="Cap perusahaan" />
          </div>
        )}

        {/* TANDA TANGAN */}
        {surat.ttds && surat.ttds.length > 0 && (
          <div className="section-card">
            <div className="section-header">
              <div className="section-icon icon-amber">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                  <path d="M2 12c1-2 2-3 3.5-3S8 11 9.5 11s2-1 3-3" stroke="#854F0B" strokeWidth="1.2" strokeLinecap="round"/>
                  <path d="M11 4l1 1-5 5-1.5.5.5-1.5L11 4Z" stroke="#854F0B" strokeWidth="1.2" strokeLinejoin="round"/>
                </svg>
              </div>
              <span className="section-title">Tanda tangan</span>
            </div>
            <div className="ttd-grid">
              {surat.ttds.map(ttd => (
                <div key={ttd.id} className="ttd-card">
                  {ttd.url ? (
                    <div className="ttd-img-wrap">
                      <img src={ttd.url} alt={ttd.nama} />
                    </div>
                  ) : (
                    <div className="ttd-initials">{getInitials(ttd.nama)}</div>
                  )}
                  <p className="ttd-nama">{ttd.nama}</p>
                  {ttd.jabatan && <p className="ttd-jabatan">{ttd.jabatan}</p>}
                  {ttd.label && <p className="ttd-label">{ttd.label}</p>}
                </div>
              ))}
            </div>
          </div>
        )}

      </div>
    </AuthenticatedLayout>
  )
}