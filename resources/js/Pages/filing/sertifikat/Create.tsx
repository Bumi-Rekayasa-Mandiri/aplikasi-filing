import AuthenticatedLayout from '@/layouts/AuthenticatedLayout'
import { Head, useForm } from '@inertiajs/react'
import { FormEvent } from 'react'
import '../../../../css/create-surat.css'

export default function Create() {
  const { data, setData, post, processing, errors } = useForm({
    nama_sertifikat: '',
    nomor_sertifikat: '',
    instansi: '',
    jenis_sertifikat: '',
    file: null as File | null,
  })

  const submit = (e: FormEvent) => {
    e.preventDefault()
    post(route('filing.sertifikat.store'), { forceFormData: true })
  }

  return (
    <AuthenticatedLayout>
      <Head title="Upload Sertifikat" />

      <form onSubmit={submit} className="create-form">

        <div className="create-form-heading">
          <h1 className="create-form-title">Upload sertifikat</h1>
          <p className="create-form-subtitle">Isi informasi sertifikat lalu unggah file dokumen</p>
        </div>

        {/* Informasi Sertifikat */}
        <div className="form-card">
          <div className="form-card-header">
            <div className="form-icon icon-blue">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <path d="M3 2.5A1.5 1.5 0 0 1 4.5 1h5.086a1.5 1.5 0 0 1 1.06.44l2.415 2.414A1.5 1.5 0 0 1 13.5 4.914V13.5A1.5 1.5 0 0 1 12 15H4.5A1.5 1.5 0 0 1 3 13.5v-11Z" stroke="#185FA5" strokeWidth="1.2"/>
                <path d="M6 8h4M6 10.5h2.5" stroke="#185FA5" strokeWidth="1.2" strokeLinecap="round"/>
              </svg>
            </div>
            <span className="form-card-title">Informasi sertifikat</span>
          </div>
          <div className="form-grid">
            <div className="field col-span-2">
              <label className="field-label">Nama sertifikat</label>
              <input className="field-input" placeholder="Nama lengkap sertifikat"
                value={data.nama_sertifikat}
                onChange={e => setData('nama_sertifikat', e.target.value)} />
              {errors.nama_sertifikat && <span className="field-error">{errors.nama_sertifikat}</span>}
            </div>
            <div className="field">
              <label className="field-label">Nomor sertifikat</label>
              <input className="field-input" placeholder="Nomor sertifikat"
                value={data.nomor_sertifikat}
                onChange={e => setData('nomor_sertifikat', e.target.value)} />
              {errors.nomor_sertifikat && <span className="field-error">{errors.nomor_sertifikat}</span>}
            </div>
            <div className="field">
              <label className="field-label">Instansi</label>
              <input className="field-input" placeholder="Nama instansi penerbit"
                value={data.instansi}
                onChange={e => setData('instansi', e.target.value)} />
              {errors.instansi && <span className="field-error">{errors.instansi}</span>}
            </div>
            <div className="field col-span-2">
              <label className="field-label">Jenis sertifikat</label>
              <input className="field-input" placeholder="Contoh: ISO, K3, Kompetensi"
                value={data.jenis_sertifikat}
                onChange={e => setData('jenis_sertifikat', e.target.value)} />
              {errors.jenis_sertifikat && <span className="field-error">{errors.jenis_sertifikat}</span>}
            </div>
          </div>
        </div>

        {/* Upload File */}
        <div className="form-card">
          <div className="form-card-header">
            <div className="form-icon icon-teal">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <path d="M8 11V5M5.5 7.5L8 5l2.5 2.5" stroke="#0F6E56" strokeWidth="1.3" strokeLinecap="round" strokeLinejoin="round"/>
                <path d="M3 13h10" stroke="#0F6E56" strokeWidth="1.3" strokeLinecap="round"/>
              </svg>
            </div>
            <span className="form-card-title">File sertifikat</span>
          </div>
          <label className="file-upload-area">
            <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png"
              className="sr-only"
              onChange={e => setData('file', e.target.files?.[0] ?? null)} />
            <div className="file-upload-icon">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <path d="M8 11V5M5.5 7.5L8 5l2.5 2.5" stroke="#185FA5" strokeWidth="1.3" strokeLinecap="round" strokeLinejoin="round"/>
                <path d="M3 13h10" stroke="#185FA5" strokeWidth="1.3" strokeLinecap="round"/>
              </svg>
            </div>
            <div>
              <div className="file-upload-text">
                {data.file ? data.file.name : 'Klik untuk unggah file sertifikat'}
              </div>
              <div className="file-upload-hint">PDF, JPG, PNG hingga 10MB</div>
            </div>
          </label>
          {errors.file && <span className="field-error" style={{ marginTop: '6px', display: 'block' }}>{errors.file}</span>}
        </div>

        <button type="submit" className="btn-submit" disabled={processing}>
          {processing ? 'Mengupload…' : 'Upload sertifikat'}
        </button>

      </form>
    </AuthenticatedLayout>
  )
}