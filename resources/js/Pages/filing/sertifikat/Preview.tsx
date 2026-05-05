import AuthenticatedLayout from '@/layouts/AuthenticatedLayout'
import { Head, Link } from '@inertiajs/react'
import { usePage } from '@inertiajs/react'
import '../../../../css/show-surat.css'

interface Sertifikat {
  id: number
  nama_sertifikat: string
  nomor_sertifikat: string
  jenis_sertifikat: string
  instansi: string
  file_url: string | null
  file_name: string | null
  file_mime: string | null
}

interface PageProps {
  sertifikat: Sertifikat
  [key: string]: unknown
}

export default function Preview() {
  const { sertifikat } = usePage<PageProps>().props
  const isPdf   = sertifikat.file_mime === 'application/pdf'
  const isImage = sertifikat.file_mime?.startsWith('image/')

  return (
    <AuthenticatedLayout>
      <Head title={`Preview — ${sertifikat.nama_sertifikat}`} />

      <div className="show-page">

        {/* Info Sertifikat */}
        <div className="section-card">
          <div className="section-header">
            <div className="section-icon icon-blue">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M3 2.5A1.5 1.5 0 0 1 4.5 1h5.086a1.5 1.5 0 0 1 1.06.44l2.415 2.414A1.5 1.5 0 0 1 13.5 4.914V13.5A1.5 1.5 0 0 1 12 15H4.5A1.5 1.5 0 0 1 3 13.5v-11Z" stroke="#185FA5" strokeWidth="1.2"/>
                <path d="M6 8h4M6 10.5h2.5" stroke="#185FA5" strokeWidth="1.2" strokeLinecap="round"/>
              </svg>
            </div>
            <span className="section-title">Informasi sertifikat</span>
          </div>

          <div className="nomor-surat">{sertifikat.nama_sertifikat}</div>

          <div className="info-grid">
            <div className="info-item">
              <span className="info-label">Nomor sertifikat</span>
              <span className="info-value">{sertifikat.nomor_sertifikat || '—'}</span>
            </div>
            <div className="info-item">
              <span className="info-label">Jenis sertifikat</span>
              <span className="info-value">{sertifikat.jenis_sertifikat || '—'}</span>
            </div>
            <div className="info-item">
              <span className="info-label">Instansi</span>
              <span className="info-value">{sertifikat.instansi || '—'}</span>
            </div>
            <div className="info-item">
              <span className="info-label">File</span>
              <span className="info-value">{sertifikat.file_name || '—'}</span>
            </div>
          </div>

          <div style={{ marginTop: '16px', paddingTop: '16px', borderTop: '0.5px solid rgba(0,0,0,0.07)', display: 'flex', gap: '8px' }}>
            <Link href={route('filing.arsip.index')} className="bg-red-700 px-4 py-2 rounded-full text-white"
              style={{ textDecoration: 'none' }}>
              ← Kembali
            </Link>
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
            <span className="section-title">File sertifikat</span>
          </div>

          {!sertifikat.file_url && (
            <div className="detail-empty">File belum diupload</div>
          )}

          {sertifikat.file_url && isPdf && (
            <iframe src={sertifikat.file_url}
              style={{ width: '100%', height: '75vh', borderRadius: '8px', border: '0.5px solid rgba(0,0,0,0.1)' }}
              title={sertifikat.file_name ?? 'Preview PDF'} />
          )}

          {sertifikat.file_url && isImage && (
            <img src={sertifikat.file_url} alt={sertifikat.file_name ?? 'Preview'}
              style={{ maxWidth: '100%', borderRadius: '8px', border: '0.5px solid rgba(0,0,0,0.1)' }} />
          )}

          {sertifikat.file_url && !isPdf && !isImage && (
            <div className="detail-empty">
              <p style={{ marginBottom: '12px' }}>Preview tidak tersedia untuk format ini.</p>
            </div>
          )}

            <div style={{ marginTop: '16px', paddingTop: '16px', borderTop: '0.5px solid rgba(0,0,0,0.07)', display: 'flex', gap: '8px' }}>
            {sertifikat.file_url && (
              <Link href={route('filing.sertifikat.download', sertifikat.id)} className="bg-blue-700 px-4 py-2 rounded-full text-white">
                + Download File
              </Link>
            )}
          </div>
        </div>

      </div>
    </AuthenticatedLayout>
  )
}