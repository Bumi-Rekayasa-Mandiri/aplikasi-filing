import AuthenticatedLayout from "@/layouts/AuthenticatedLayout"
import AppLayout from "@/layouts/AppLayout"
import { Head, useForm } from "@inertiajs/react"
import '../../../../../css/create-surat.css'

type TtdItem = {
  nama_penandatangan: string
  urutan: number
  label: string
  jabatan: string
  file: File | null
}

type Props = {
  surat: {
    id: number
    nomor_surat: string
    judul: string
    tujuan: string
    status: string
  }
}

type JsaItem = {
  urutan_kerja: string
  potensi_bahaya: string
  upaya_pengendalian: string
}

type PekerjaItem = {
  nama: string
  role: string
  ktp: File | null
}

export default function CreateBRM1({ surat }: Props) {
  const { data, setData, post, processing, errors } = useForm({
    judul: '',
    perihal: '',
    tujuan: '',
    tanggal_surat: '',
    nama: '',
    lokasi_kerja: '',
    jenis_pekerjaan: '',
    waktu: '',
    jam_kerja: '',
    jumlah_pekerja: '',
    departemen: '',
    apd: '',
    periode: '',
    no_pekerja: '',
    cap: null as File | null,
    ttds: [
      { nama_penandatangan: '', urutan: 1, label: 'Pihak Pertama', jabatan: '', file: null },
      { nama_penandatangan: '', urutan: 2, label: 'Pihak Kedua',   jabatan: '', file: null },
    ] as TtdItem[],
    jsa: [] as JsaItem[],
    pekerja: [] as PekerjaItem[],
  })

  const submit = (e: React.FormEvent) => {
    e.preventDefault()
    if (!surat?.id) return
    post(route('filing.surat.generateBRM1-pdf', { surat: surat.id }), {
      preserveScroll: true,
      forceFormData: true,
    })
  }

  const addTtd = () => setData('ttds', [
    ...data.ttds,
    { nama_penandatangan: '', urutan: data.ttds.length + 1, label: `Pihak ${data.ttds.length + 1}`, jabatan: '', file: null }
  ])

  const removeTtd = (i: number) => setData('ttds', data.ttds.filter((_, idx) => idx !== i))

  const updateTtd = (i: number, field: keyof TtdItem, value: string | File | null) => {
    const updated = [...data.ttds]
    updated[i] = { ...updated[i], [field]: value }
    setData('ttds', updated)
  }

  if (!surat?.id) {
    return <AppLayout title="Loading"><p className="text-gray-500">Menyiapkan draft surat…</p></AppLayout>
  }

  const addJsa = () => setData('jsa', [
    ...data.jsa,
    { urutan_kerja: '', potensi_bahaya: '', upaya_pengendalian: '' }
  ])

  const removeJsa = (i: number) => setData('jsa', data.jsa.filter((_, idx) => idx !== i))

  const updateJsa = (i: number, field: keyof JsaItem, value: string) => {
    const updated = [...data.jsa]
    updated[i] = { ...updated[i], [field]: value }
    setData('jsa', updated)
  }

  const addPekerja = () => setData('pekerja', [
    ...data.pekerja,
    { nama: '', role: 'Pekerja', ktp: null }
  ])

  const removePekerja = (i: number) => setData('pekerja', data.pekerja.filter((_, idx) => idx !== i))

  const updatePekerja = (i: number, field: keyof PekerjaItem, value: string | File | null) => {
    const updated = [...data.pekerja]
    updated[i] = { ...updated[i], [field]: value }
    setData('pekerja', updated)
  }

  return (
    <AuthenticatedLayout>
      <Head title="Buat Surat Izin Kerja dan LK3" />

      <form onSubmit={submit} className="create-form">

        <div className="create-form-heading">
          <h1 className="create-form-title">Form data surat izin kerja dan LK3</h1>
          <p className="create-form-subtitle">Isi semua field yang diperlukan lalu klik Generate PDF</p>
        </div>

        {/* Error summary */}
        {Object.keys(errors).length > 0 && (
          <div className="form-error-summary">
            {Object.entries(errors).map(([key, msg]) => (
              <p key={key}><strong>{key}:</strong> {msg}</p>
            ))}
          </div>
        )}

        {/* ── Informasi Umum ─────────────────────── */}
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
              <input className="field-input" placeholder="Contoh: Surat Izin Kerja Perbaikan Atap" onChange={e => setData('judul', e.target.value)} />
              {errors.judul && <span className="field-error">{errors.judul}</span>}
            </div>
            <div className="field">
              <label className="field-label">Perihal</label>
              <input className="field-input" placeholder="Perihal surat" onChange={e => setData('perihal', e.target.value)} />
              {errors.perihal && <span className="field-error">{errors.perihal}</span>}
            </div>
            <div className="field">
              <label className="field-label">Tujuan</label>
              <input className="field-input" placeholder="Tujuan surat" onChange={e => setData('tujuan', e.target.value)} />
              {errors.tujuan && <span className="field-error">{errors.tujuan}</span>}
            </div>
            <div className="field">
              <label className="field-label">Tanggal surat</label>
              <input type="date" className="field-input" onChange={e => setData('tanggal_surat', e.target.value)} />
            </div>
            <div className="field">
              <label className="field-label">Nama tujuan</label>
              <input className="field-input" placeholder="Nama penerima" onChange={e => setData('nama', e.target.value)} />
            </div>
          </div>
        </div>

        {/* ── Detail Pekerjaan ───────────────────── */}
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
              <label className="field-label">Departemen</label>
              <input className="field-input" placeholder="Nama departemen" onChange={e => setData('departemen', e.target.value)} />
            </div>
            <div className="field">
              <label className="field-label">Lokasi kerja</label>
              <input className="field-input" placeholder="Lokasi pekerjaan" onChange={e => setData('lokasi_kerja', e.target.value)} />
            </div>
            <div className="field col-span-2">
              <label className="field-label">Jenis pekerjaan</label>
              <textarea className="field-input" placeholder={"Tulis setiap jenis pekerjaan dipisah baris baru\nContoh:\n1. Perbaikan atap\n2. Pemasangan plafon"} onChange={e => setData('jenis_pekerjaan', e.target.value)} />
            </div>
            <div className="field">
              <label className="field-label">Waktu</label>
              <input className="field-input" placeholder="Contoh: 3 hari" onChange={e => setData('waktu', e.target.value)} />
            </div>
            <div className="field">
              <label className="field-label">Jam kerja</label>
              <input className="field-input" placeholder="Contoh: 08.00 – 17.00" onChange={e => setData('jam_kerja', e.target.value)} />
            </div>
            <div className="field">
              <label className="field-label">Jumlah pekerja</label>
              <input className="field-input" placeholder="Contoh: 5 orang" onChange={e => setData('jumlah_pekerja', e.target.value)} />
            </div>
          </div>
        </div>

        {/* ── Cap Perusahaan ─────────────────────── */}
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
            <input type="file" accept="image/*" className="sr-only" onChange={e => setData('cap', e.target.files?.[0] ?? null)} />
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

        {/* ── Tanda Tangan ───────────────────────── */}
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
                    <input className="field-input" value={ttd.label} placeholder="Contoh: Hormat Kami" onChange={e => updateTtd(i, 'label', e.target.value)} />
                  </div>
                  <div className="field">
                    <label className="field-label">Nama penandatangan</label>
                    <input className="field-input" value={ttd.nama_penandatangan} placeholder="Nama lengkap" onChange={e => updateTtd(i, 'nama_penandatangan', e.target.value)} />
                  </div>
                  <div className="field">
                    <label className="field-label">Jabatan</label>
                    <input className="field-input" value={ttd.jabatan} placeholder="Jabatan" onChange={e => updateTtd(i, 'jabatan', e.target.value)} />
                  </div>
                  <label className="file-upload-area">
                    <input type="file" accept="image/*" className="sr-only" onChange={e => updateTtd(i, 'file', e.target.files?.[0] ?? null)} />
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
            <span className="form-card-title">
              Lampiran A — JSA
              {data.jsa.length > 0 && (
                <span style={{ marginLeft: '8px', fontSize: '11px', background: '#EEEDFE', color: '#3C3489', padding: '2px 8px', borderRadius: '999px' }}>
                  {data.jsa.length} item
                </span>
              )}
            </span>
          </div>

          {/* ── Lampiran ───────────────────────────── */}
          <div className="form-card">
            <div className="form-card-header">
              <div className="form-icon icon-teal">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                  <path d="M4 8l2.5 2.5L12 5" stroke="#0F6E56" strokeWidth="1.3" strokeLinecap="round" strokeLinejoin="round"/>
                  <rect x="1.5" y="1.5" width="13" height="13" rx="3" stroke="#0F6E56" strokeWidth="1.2"/>
                </svg>
              </div>
              <span className="form-card-title">Lampiran</span>
            </div>
            <div className="form-grid">
              <div className="field">
                <label className="field-label">APD</label>
                <input className="field-input" placeholder="Alat pelindung diri" onChange={e => setData('apd', e.target.value)} />
              </div>
              <div className="field">
                <label className="field-label">Periode</label>
                <input className="field-input" placeholder="Contoh: Maret 2026" onChange={e => setData('periode', e.target.value)} />
              </div>
              <div className="field">
                <label className="field-label">No pekerja</label>
                <input className="field-input" placeholder="Nomor ID pekerja" onChange={e => setData('no_pekerja', e.target.value)} />
              </div>
            </div>
          </div>

          <div className="ttd-list">
            {data.jsa.map((item, i) => (
              <div key={i} className="ttd-item">
                <div className="ttd-item-header">
                  <div className="ttd-item-title">
                    <span className="ttd-num">{i + 1}</span>
                    Item JSA {i + 1}
                  </div>
                  <button type="button" className="ttd-remove" onClick={() => removeJsa(i)}>Hapus</button>
                </div>
                <div className="ttd-fields">
                  <div className="field col-span-2">
                    <label className="field-label">Urutan kerja</label>
                    <input className="field-input" placeholder="Contoh: Penggalian pondasi"
                      value={item.urutan_kerja}
                      onChange={e => updateJsa(i, 'urutan_kerja', e.target.value)} />
                  </div>
                  <div className="field">
                    <label className="field-label">Potensi bahaya</label>
                    <textarea className="field-input"
                      placeholder={"Tulis tiap potensi dipisah baris baru\nContoh:\nSalah gali\nTools kurang"}
                      value={item.potensi_bahaya}
                      rows={4}
                      onChange={e => updateJsa(i, 'potensi_bahaya', e.target.value)} />
                  </div>
                  <div className="field">
                    <label className="field-label">Upaya pengendalian</label>
                    <textarea className="field-input"
                      placeholder={"Tulis tiap upaya dipisah baris baru\nContoh:\nAPD mandatori\nSupervisi pengawas"}
                      value={item.upaya_pengendalian}
                      rows={4}
                      onChange={e => updateJsa(i, 'upaya_pengendalian', e.target.value)} />
                  </div>
                </div>
              </div>
            ))}
            <button type="button" className="btn-add" onClick={addJsa}>
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <path d="M8 3v10M3 8h10" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/>
              </svg>
              Tambah item JSA
            </button>
          </div>
        </div>

        <div className="form-card">
          <div className="form-card-header">
            <div className="form-icon icon-teal">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <circle cx="8" cy="5" r="3" stroke="#0F6E56" strokeWidth="1.2"/>
                <path d="M2 14c0-3 2.5-5 6-5s6 2 6 5" stroke="#0F6E56" strokeWidth="1.2" strokeLinecap="round"/>
              </svg>
            </div>
            <span className="form-card-title">
              Lampiran B — Daftar pekerja
              {data.pekerja.length > 0 && (
                <span style={{ marginLeft: '8px', fontSize: '11px', background: '#EAF3DE', color: '#27500A', padding: '2px 8px', borderRadius: '999px' }}>
                  {data.pekerja.length} pekerja
                </span>
              )}
            </span>
          </div>

          <div className="ttd-list">
            {data.pekerja.map((p, i) => (
              <div key={i} className="ttd-item">
                <div className="ttd-item-header">
                  <div className="ttd-item-title">
                    <span className="ttd-num">{i + 1}</span>
                    Pekerja {i + 1}
                  </div>
                  <button type="button" className="ttd-remove" onClick={() => removePekerja(i)}>Hapus</button>
                </div>
                <div className="ttd-fields">
                  <div className="field">
                    <label className="field-label">Nama pekerja</label>
                    <input className="field-input" placeholder="Nama lengkap"
                      value={p.nama}
                      onChange={e => updatePekerja(i, 'nama', e.target.value)} />
                  </div>
                  <div className="field">
                    <label className="field-label">Role</label>
                    <select className="field-input"
                      value={p.role}
                      onChange={e => updatePekerja(i, 'role', e.target.value)}>
                      <option value="Pengawas">Pengawas</option>
                      <option value="Pekerja">Pekerja</option>
                      <option value="Driver">Driver</option>
                      <option value="Teknisi">Teknisi</option>
                    </select>
                  </div>
                  <label className="file-upload-area col-span-2">
                    <input type="file" accept="image/*" className="sr-only"
                      onChange={e => updatePekerja(i, 'ktp', e.target.files?.[0] ?? null)} />
                    <div className="file-upload-icon">
                      <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                        <path d="M8 11V5M5.5 7.5L8 5l2.5 2.5" stroke="#185FA5" strokeWidth="1.3" strokeLinecap="round" strokeLinejoin="round"/>
                        <path d="M3 13h10" stroke="#185FA5" strokeWidth="1.3" strokeLinecap="round"/>
                      </svg>
                    </div>
                    <div>
                      <div className="file-upload-text">
                        {p.ktp ? p.ktp.name : 'Unggah foto KTP'}
                      </div>
                      <div className="file-upload-hint">JPG, PNG hingga 2MB</div>
                    </div>
                  </label>
                </div>
              </div>
            ))}
            <button type="button" className="btn-add" onClick={addPekerja}>
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <path d="M8 3v10M3 8h10" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/>
              </svg>
              Tambah pekerja
            </button>
          </div>
        </div>

        {/* ── Submit ─────────────────────────────── */}
        <button type="submit" className="btn-submit" disabled={processing}>
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
            <path d="M3 2.5A1.5 1.5 0 0 1 4.5 1h5.086a1.5 1.5 0 0 1 1.06.44l2.415 2.414A1.5 1.5 0 0 1 13.5 4.914V13.5A1.5 1.5 0 0 1 12 15H4.5A1.5 1.5 0 0 1 3 13.5v-11Z" stroke="white" strokeWidth="1.2"/>
            <path d="M6 8h4M6 10.5h2.5" stroke="white" strokeWidth="1.2" strokeLinecap="round"/>
          </svg>
          {processing ? 'Memproses…' : 'Generate PDF'}
        </button>

      </form>
    </AuthenticatedLayout>
  )
}