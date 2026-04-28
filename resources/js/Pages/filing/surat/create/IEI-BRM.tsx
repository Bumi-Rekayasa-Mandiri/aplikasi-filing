import AuthenticatedLayout from "@/layouts/AuthenticatedLayout"
import { Head, useForm } from "@inertiajs/react"
import '../../../../../css/create-surat.css'

type TtdItem = {
  nama_penandatangan: string
  jabatan: string
  urutan: number
  label: string
  file: File | null
}

type JenisPekerjaanItem = { deskripsi: string }

type Props = {
  surat: { id: number; nomor_surat: string; judul: string; tujuan: string; status: string

   }
}

export default function CreateIEI({ surat }: Props) {
  const { data, setData, post, processing, errors } = useForm({
    judul: '', perihal: '', tujuan: '', tanggal_surat: '',
    project: '', lokasi_kerja: '', isi_surat: '', lampiran: '',
    jenis_pekerjaan: [{ deskripsi: '' }] as JenisPekerjaanItem[],
    cap: null as File | null,
    ttds: [
      { nama_penandatangan: '', jabatan: '', urutan: 1, label: 'Pihak Pertama', file: null },
      { nama_penandatangan: '', jabatan: '', urutan: 2, label: 'Pihak Kedua', file: null },
    ] as TtdItem[],

    dokumen: [] as string[],
  })

  const submit = (e: React.FormEvent) => {
    e.preventDefault()
    if (!surat?.id) return
    post(route('filing.surat.generateIEI-pdf', { surat: surat.id }), {
      preserveScroll: true, forceFormData: true,
    })
  }

  const addPekerjaan = () => {
    if (data.jenis_pekerjaan.length >= 6) return
    setData('jenis_pekerjaan', [...data.jenis_pekerjaan, { deskripsi: '' }])
  }
  const removePekerjaan = (i: number) =>
    setData('jenis_pekerjaan', data.jenis_pekerjaan.filter((_, idx) => idx !== i))
  const updatePekerjaan = (i: number, value: string) => {
    const updated = [...data.jenis_pekerjaan]
    updated[i] = { deskripsi: value }
    setData('jenis_pekerjaan', updated)
  }

  const addTtd = () => setData('ttds', [
    ...data.ttds,
    { nama_penandatangan: '', jabatan: '', urutan: data.ttds.length + 1,
      label: `Pihak ${data.ttds.length + 1}`, file: null }
  ])
  const removeTtd = (i: number) => setData('ttds', data.ttds.filter((_, idx) => idx !== i))
  const updateTtd = (i: number, field: keyof TtdItem, value: string | File | null) => {
    const updated = [...data.ttds]
    updated[i] = { ...updated[i], [field]: value }
    setData('ttds', updated)
  }

  if (!surat?.id) return (
    <AuthenticatedLayout><p className="text-gray-500">Menyiapkan draft surat…</p></AuthenticatedLayout>
  )

  return (
    <AuthenticatedLayout>
      <Head title="Buat Surat Garansi Pekerjaan" />
      <form onSubmit={submit} className="create-form">

        <div className="create-form-heading">
          <h1 className="create-form-title">Form data surat garansi pekerjaan</h1>
          <p className="create-form-subtitle">Isi semua field yang diperlukan lalu klik Generate PDF</p>
        </div>

        {Object.keys(errors).length > 0 && (
          <div className="form-error-summary">
            {Object.entries(errors).map(([key, msg]) => (
              <p key={key}><strong>{key}:</strong> {msg}</p>
            ))}
          </div>
        )}

        {/* Informasi Umum */}
        <div className="form-card">
          <div className="form-card-header">
            <div className="form-icon icon-blue">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <path d="M3 2.5A1.5 1.5 0 0 1 4.5 1h5.086a1.5 1.5 0 0 1 1.06.44l2.415 2.414A1.5 1.5 0 0 1 13.5 4.914V13.5A1.5 1.5 0 0 1 12 15H4.5A1.5 1.5 0 0 1 3 13.5v-11Z" stroke="#185FA5" strokeWidth="1.2"/>
                <path d="M6 8h4M6 10.5h2.5" stroke="#185FA5" strokeWidth="1.2" strokeLinecap="round"/>
              </svg>
            </div>
            <span className="form-card-title">Informasi umum</span>
          </div>
          <div className="form-grid">
            <div className="field col-span-2">
              <label className="field-label">Judul</label>
              <input className="field-input" placeholder="Contoh: Surat Garansi Pekerjaan Pagar BRC"
                onChange={e => setData('judul', e.target.value)} />
              {errors.judul && <span className="field-error">{errors.judul}</span>}
            </div>
            <div className="field">
              <label className="field-label">Perihal</label>
              <input className="field-input" placeholder="Perihal surat"
                onChange={e => setData('perihal', e.target.value)} />
              {errors.perihal && <span className="field-error">{errors.perihal}</span>}
            </div>
            <div className="field">
              <label className="field-label">Tujuan</label>
              <input className="field-input" placeholder="Tujuan surat"
                onChange={e => setData('tujuan', e.target.value)} />
              {errors.tujuan && <span className="field-error">{errors.tujuan}</span>}
            </div>
            <div className="field">
              <label className="field-label">Tanggal surat</label>
              <input type="date" className="field-input" onChange={e => setData('tanggal_surat', e.target.value)} />
            </div>
          </div>
        </div>

        {/* Detail Pekerjaan */}
        <div className="form-card">
          <div className="form-card-header">
            <div className="form-icon icon-purple">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <rect x="2" y="2" width="12" height="12" rx="2" stroke="#534AB7" strokeWidth="1.2"/>
                <path d="M5 5.5h6M5 8h6M5 10.5h4" stroke="#534AB7" strokeWidth="1.2" strokeLinecap="round"/>
              </svg>
            </div>
            <span className="form-card-title">Detail pekerjaan</span>
          </div>
          <div className="form-grid">
            <div className="field">
              <label className="field-label">Project</label>
              <input className="field-input" placeholder="Nama project"
                onChange={e => setData('project', e.target.value)} />
            </div>
            <div className="field">
              <label className="field-label">Lokasi kerja</label>
              <input className="field-input" placeholder="Lokasi pekerjaan"
                onChange={e => setData('lokasi_kerja', e.target.value)} />
            </div>
            <div className="field col-span-2">
              <label className="field-label">Masa garansi</label>
              <input className="field-input" placeholder="Contoh: 01/03/2026 s/d 01/03/2027"
                onChange={e => setData('isi_surat', e.target.value)} />
            </div>
            <div className="field col-span-2">
              <label className="field-label">Daftar pekerjaan (maks. 6)</label>
              <div className="pekerjaan-input-list">
                {data.jenis_pekerjaan.map((item, i) => (
                  <div key={i} className="pekerjaan-input-row">
                    <span className="pekerjaan-input-num">{i + 1}</span>
                    <input className="field-input" placeholder={`Pekerjaan ${i + 1}`}
                      value={item.deskripsi} onChange={e => updatePekerjaan(i, e.target.value)} />
                    {data.jenis_pekerjaan.length > 1 && (
                      <button type="button" className="pekerjaan-remove" onClick={() => removePekerjaan(i)}>
                        <svg width="12" height="12" viewBox="0 0 16 16" fill="none">
                          <path d="M3 3l10 10M13 3L3 13" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/>
                        </svg>
                      </button>
                    )}
                  </div>
                ))}
                {data.jenis_pekerjaan.length < 6 && (
                  <button type="button" className="btn-add" style={{marginTop: '6px'}} onClick={addPekerjaan}>
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                      <path d="M8 3v10M3 8h10" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/>
                    </svg>
                    Tambah pekerjaan
                  </button>
                )}
              </div>
            </div>
          </div>
        </div>

        {/* Cap Perusahaan */}
        <div className="form-card">
          <div className="form-card-header">
            <div className="form-icon icon-teal">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <circle cx="8" cy="8" r="6" stroke="#0F6E56" strokeWidth="1.2"/>
                <circle cx="8" cy="8" r="3" stroke="#0F6E56" strokeWidth="1.2"/>
                <path d="M8 2v2M8 12v2M2 8h2M12 8h2" stroke="#0F6E56" strokeWidth="1.2" strokeLinecap="round"/>
              </svg>
            </div>
            <span className="form-card-title">Cap perusahaan</span>
          </div>
          <label className="file-upload-area">
            <input type="file" accept="image/*" className="sr-only"
              onChange={e => setData('cap', e.target.files?.[0] ?? null)} />
            <div className="file-upload-icon">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <path d="M8 11V5M5.5 7.5L8 5l2.5 2.5" stroke="#185FA5" strokeWidth="1.3" strokeLinecap="round" strokeLinejoin="round"/>
                <path d="M3 13h10" stroke="#185FA5" strokeWidth="1.3" strokeLinecap="round"/>
              </svg>
            </div>
            <div>
              <div className="file-upload-text">Klik untuk unggah cap perusahaan</div>
              <div className="file-upload-hint">PNG, JPG hingga 2MB</div>
            </div>
          </label>
        </div>

        {/* Tanda Tangan */}
        <div className="form-card">
          <div className="form-card-header">
            <div className="form-icon icon-amber">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <path d="M2 12c1-2 2-3 3.5-3S8 11 9.5 11s2-1 3-3" stroke="#854F0B" strokeWidth="1.2" strokeLinecap="round"/>
                <path d="M11 4l1 1-5 5-1.5.5.5-1.5L11 4Z" stroke="#854F0B" strokeWidth="1.2" strokeLinejoin="round"/>
              </svg>
            </div>
            <span className="form-card-title">Tanda tangan</span>
          </div>
          <div className="ttd-list">
            {data.ttds.map((ttd, i) => (
              <div key={i} className="ttd-item">
                <div className="ttd-item-header">
                  <div className="ttd-item-title">
                    <span className="ttd-num">{i + 1}</span>
                    Penandatangan {i + 1}
                  </div>
                  {data.ttds.length > 1 && (
                    <button type="button" className="ttd-remove" onClick={() => removeTtd(i)}>Hapus</button>
                  )}
                </div>
                <div className="ttd-fields">
                  <div className="field">
                    <label className="field-label">Label</label>
                    <input className="field-input" value={ttd.label} placeholder="Contoh: Hormat Kami"
                      onChange={e => updateTtd(i, 'label', e.target.value)} />
                  </div>
                  <div className="field">
                    <label className="field-label">Nama penandatangan</label>
                    <input className="field-input" value={ttd.nama_penandatangan} placeholder="Nama lengkap"
                      onChange={e => updateTtd(i, 'nama_penandatangan', e.target.value)} />
                  </div>
                  <div className="field">
                    <label className="field-label">Jabatan</label>
                    <input className="field-input" value={ttd.jabatan} placeholder="Jabatan"
                      onChange={e => updateTtd(i, 'jabatan', e.target.value)} />
                  </div>
                  <label className="file-upload-area">
                    <input type="file" accept="image/*" className="sr-only"
                      onChange={e => updateTtd(i, 'file', e.target.files?.[0] ?? null)} />
                    <div className="file-upload-icon">
                      <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                        <path d="M8 11V5M5.5 7.5L8 5l2.5 2.5" stroke="#185FA5" strokeWidth="1.3" strokeLinecap="round" strokeLinejoin="round"/>
                        <path d="M3 13h10" stroke="#185FA5" strokeWidth="1.3" strokeLinecap="round"/>
                      </svg>
                    </div>
                    <div>
                      <div className="file-upload-text">Unggah gambar tanda tangan</div>
                      <div className="file-upload-hint">PNG transparan lebih baik</div>
                    </div>
                  </label>
                </div>
              </div>
            ))}
            <button type="button" className="btn-add" onClick={addTtd}>
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <path d="M8 3v10M3 8h10" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/>
              </svg>
              Tambah tanda tangan
            </button>
          </div>
        </div>

        <div className="form-card">
          <div className="form-card-header">
            <div className="form-icon icon-purple">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <rect x="2" y="2" width="12" height="12" rx="2" stroke="#534AB7" strokeWidth="1.2"/>
                <path d="M5 5.5h6M5 8h6M5 10.5h4" stroke="#534AB7" strokeWidth="1.2" strokeLinecap="round"/>
              </svg>
            </div>
            <span className="form-card-title">Daftar dokumen lampiran</span>
          </div>
          <div className="ttd-list">
            {(data.dokumen ?? []).map((dok: string, i: number) => (
              <div key={i} className="ttd-item">
                <div className="ttd-item-header">
                  <div className="ttd-item-title">
                    <span className="ttd-num">{i + 1}</span>
                    Dokumen {i + 1}
                  </div>
                  <button type="button" className="ttd-remove"
                    onClick={() => setData('dokumen', (data.dokumen ?? []).filter((_: string, idx: number) => idx !== i))}>
                    Hapus
                  </button>
                </div>
                <div className="field">
                  <input className="field-input" placeholder="Contoh: Guarantee Letter"
                    value={dok}
                    onChange={e => {
                      const updated = [...(data.dokumen ?? [])]
                      updated[i] = e.target.value
                      setData('dokumen', updated)
                    }} />
                </div>
              </div>
            ))}
            <button type="button" className="btn-add"
              onClick={() => setData('dokumen', [...(data.dokumen ?? []), ''])}>
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <path d="M8 3v10M3 8h10" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/>
              </svg>
              Tambah dokumen
            </button>
          </div>
        </div>

        <button type="submit" className="btn-submit" disabled={processing}>
          {processing ? 'Memproses…' : 'Generate PDF'}
        </button>

      </form>
    </AuthenticatedLayout>
  )
}