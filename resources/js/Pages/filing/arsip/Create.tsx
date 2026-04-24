import AuthenticatedLayout from '@/layouts/AuthenticatedLayout'
import { Head, useForm } from '@inertiajs/react'
import { FormEvent } from 'react'
import '../../../../css/create-surat.css'

export default function Create() {
  const { data, setData, post, processing, errors } = useForm({
    nomor_surat: '',
    judul: '',
    tujuan: '',
    jenis_surat: '',
    file: null as File | null,
  })

  const submit = (e: FormEvent) => {
    e.preventDefault()
    post(route('filing.arsip.store'), { forceFormData: true })
  }

  return (
    <AuthenticatedLayout>
      <Head title="Upload Arsip" />

      <form onSubmit={submit} className="create-form">

        <div className="create-form-heading">
          <h1 className="create-form-title">Upload arsip surat</h1>
          <p className="create-form-subtitle">Isi informasi arsip lalu unggah file dokumen</p>
        </div>

        {/* Informasi Arsip */}
        <div className="form-card">
          <div className="form-card-header">
            <div className="form-icon icon-blue">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <path d="M3 2.5A1.5 1.5 0 0 1 4.5 1h5.086a1.5 1.5 0 0 1 1.06.44l2.415 2.414A1.5 1.5 0 0 1 13.5 4.914V13.5A1.5 1.5 0 0 1 12 15H4.5A1.5 1.5 0 0 1 3 13.5v-11Z" stroke="#185FA5" strokeWidth="1.2"/>
                <path d="M6 8h4M6 10.5h2.5" stroke="#185FA5" strokeWidth="1.2" strokeLinecap="round"/>
              </svg>
            </div>
            <span className="form-card-title">Informasi arsip</span>
          </div>
          <div className="form-grid">
            <div className="field col-span-2">
              <label className="field-label">Judul arsip</label>
              <input className="field-input" placeholder="Judul dokumen arsip"
                value={data.judul} onChange={e => setData('judul', e.target.value)} />
              {errors.judul && <span className="field-error">{errors.judul}</span>}
            </div>
            <div className="field">
              <label className="field-label">Nomor surat</label>
              <input className="field-input" placeholder="Nomor surat"
                value={data.nomor_surat} onChange={e => setData('nomor_surat', e.target.value)} />
              {errors.nomor_surat && <span className="field-error">{errors.nomor_surat}</span>}
            </div>
            <div className="field">
              <label className="field-label">Tujuan</label>
              <input className="field-input" placeholder="Tujuan surat"
                value={data.tujuan} onChange={e => setData('tujuan', e.target.value)} />
              {errors.tujuan && <span className="field-error">{errors.tujuan}</span>}
            </div>
            <div className="field col-span-2">
              <label className="field-label">Jenis surat</label>
              <select className="field-input"
                value={data.jenis_surat} onChange={e => setData('jenis_surat', e.target.value)}>
                <option value="">Pilih jenis surat</option>
                <option value="Surat Pemberitahuan atau PHK">Surat Pemberitahuan atau PHK</option>
                <option value="Surat Pengajuan Garansi Material">Surat Pengajuan Garansi Material</option>
                <option value="Surat Pengembalian Dana">Surat Pengembalian Dana</option>
                <option value="Surat Garansi Pemasangan">Surat Garansi Pemasangan</option>
                <option value="Surat Pelepasan Hak">Surat Pelepasan Hak</option>
                <option value="Surat izin Kerja dan LK3">Surat Izin Kerja dan LK3</option>
                <option value="Surat Permohonan Keringanan Denda">Surat Permohonan Keringanan Denda</option>
                <option value="Surat Lainnya">Surat lainnya</option>
              </select>
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
            <span className="form-card-title">File dokumen</span>
          </div>
          <label className="file-upload-area">
            <input type="file" name="file" accept=".pdf,.doc,.docx"
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
                {data.file ? data.file.name : 'Klik untuk unggah file dokumen'}
              </div>
              <div className="file-upload-hint">PDF, DOC, DOCX hingga 10MB</div>
            </div>
          </label>
          {errors.file && <span className="field-error" style={{marginTop:'6px',display:'block'}}>{errors.file}</span>}
        </div>

        <button type="submit" className="btn-submit" disabled={processing}>
          {processing ? 'Mengupload…' : 'Upload arsip'}
        </button>

      </form>
    </AuthenticatedLayout>
  )
}