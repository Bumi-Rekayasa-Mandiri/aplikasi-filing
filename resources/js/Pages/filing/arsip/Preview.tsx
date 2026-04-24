import AuthenticatedLayout from '@/layouts/AuthenticatedLayout'
import { Head, Link } from '@inertiajs/react'
import { usePage } from '@inertiajs/react'
import '../../../../css/show-surat.css'

interface Arsip {
  id: number
  nomor_surat: string
  judul: string
  tujuan: string
  jenis_surat: string
  file_url: string | null
  file_name: string | null
  file_mime: string | null
}

interface PageProps {
  arsip: Arsip
  [key: string]: unknown
}

export default function Preview() {
  const { arsip } = usePage<PageProps>().props
  const isPdf   = arsip.file_mime === 'application/pdf'
  const isImage = arsip.file_mime?.startsWith('image/')

  return (
    <AuthenticatedLayout>
      <Head title={`Preview — ${arsip.judul}`} />

      <div className="show-page">

        {/* Info Arsip */}
        <div className="section-card">
          <div className="section-header">
            <div className="section-icon icon-blue">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M3 2.5A1.5 1.5 0 0 1 4.5 1h5.086a1.5 1.5 0 0 1 1.06.44l2.415 2.414A1.5 1.5 0 0 1 13.5 4.914V13.5A1.5 1.5 0 0 1 12 15H4.5A1.5 1.5 0 0 1 3 13.5v-11Z" stroke="#185FA5" strokeWidth="1.2"/>
                <path d="M6 8h4M6 10.5h2.5" stroke="#185FA5" strokeWidth="1.2" strokeLinecap="round"/>
              </svg>
            </div>
            <span className="section-title">Informasi arsip</span>
          </div>

          <div className="nomor-surat">{arsip.nomor_surat || '—'}</div>

          <div className="info-grid">
            <div className="info-item">
              <span className="info-label">Judul</span>
              <span className="info-value">{arsip.judul || '—'}</span>
            </div>
            <div className="info-item">
              <span className="info-label">Jenis surat</span>
              <span className="info-value">{arsip.jenis_surat || '—'}</span>
            </div>
            <div className="info-item">
              <span className="info-label">Tujuan</span>
              <span className="info-value">{arsip.tujuan || '—'}</span>
            </div>
            <div className="info-item">
              <span className="info-label">File</span>
              <span className="info-value">{arsip.file_name || '—'}</span>
            </div>
          </div>

          <div style={{ marginTop: '16px', paddingTop: '16px', borderTop: '0.5px solid rgba(0,0,0,0.07)', display: 'flex', gap: '8px' }}>
            <Link href={route('filing.arsip.index')} className="act-btn act-detail"
              style={{ textDecoration: 'none' }}>
              ← Kembali
            </Link>
            {arsip.file_url && (
              <a href={route('filing.arsip.download', arsip.id)}
                className="act-btn act-preview" target="_blank" rel="noopener noreferrer">
                Download file
              </a>
            )}
          </div>
        </div>

        {/* File Preview */}
        <div className="section-card">
          <div className="section-header">
            <div className="section-icon icon-teal">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M8 11V5M5.5 7.5L8 5l2.5 2.5" stroke="#0F6E56" strokeWidth="1.3" strokeLinecap="round" strokeLinejoin="round"/>
                <path d="M3 13h10" stroke="#0F6E56" strokeWidth="1.3" strokeLinecap="round"/>
              </svg>
            </div>
            <span className="section-title">File arsip</span>
          </div>

          {!arsip.file_url && (
            <div className="detail-empty">File belum diupload</div>
          )}

          {arsip.file_url && isPdf && (
            <iframe src={arsip.file_url} style={{ width: '100%', height: '75vh', borderRadius: '8px', border: '0.5px solid rgba(0,0,0,0.1)' }}
              title={arsip.file_name ?? 'Preview PDF'} />
          )}

          {arsip.file_url && isImage && (
            <img src={arsip.file_url} alt={arsip.file_name ?? 'Preview'}
              style={{ maxWidth: '100%', borderRadius: '8px', border: '0.5px solid rgba(0,0,0,0.1)' }} />
          )}

          {arsip.file_url && !isPdf && !isImage && (
            <div className="detail-empty">
              <p style={{ marginBottom: '12px' }}>Preview tidak tersedia untuk format ini.</p>
              <a href={route('filing.arsip.download', arsip.id)}
                className="act-btn act-preview">
                Download file
              </a>
            </div>
          )}
        </div>

      </div>
    </AuthenticatedLayout>
  )
}