import { useForm, Head, router } from '@inertiajs/react'
import { useState, useEffect } from 'react'
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout'
import '../../../../css/create-surat.css'
import StatusToggle from '@/components/filing/surat/StatusToggle'
import '../../../../css/status-toggle.css'

import { FormFieldProvider, Field, TextArea } from '@/components/filing/surat/editors/FormFieldContext'
import CapEditor from '@/components/filing/surat/editors/CapEditor'
import TtdEditor, { TtdItem } from '@/components/filing/surat/editors/TtdEditor'
import JsaEditor, { JsaItem } from '@/components/filing/surat/editors/JsaEditor'
import PekerjaEditor, { PekerjaItem } from '@/components/filing/surat/editors/PekerjaEditor'
import DokumenEditor from '@/components/filing/surat/editors/DokumenEditor'
import RegeneratedBanner from '@/components/filing/surat/RegeneratedBanner'
import ApprovedErrorModal from '@/components/filing/surat/ApprovedErrorModal'
import ApprovedReadOnlyNotice from '@/components/filing/surat/ApprovedReadOnlyNotice'
 
type SuratData = {
    id: number
    jenis: string
    judul: string
    perihal?: string
    tujuan?: string
    isi_surat?: string
    tanggal_surat: string
    nama?: string
    jabatan_terakhir?: string
    departemen?: string
    lokasi_kerja?: string
    jenis_pekerjaan?: string
    waktu?: string
    jam_kerja?: string
    jumlah_pekerja?: string
    apd?: string
    periode?: string
    no_pekerja?: string
    merk?: string
    warna?: string
    rangka?: string
    project?: string
    material?: string
    alamat?: string
    masa_garansi?: string
    hasil_denda?: string
    keringanan_denda?: string
    lampiran?: string
    item_pembelian?: string
    nominal?: string
    nominal_bagihasil?: string
    no_ktp?: string
    status?: string
    last_regenerated_at?: string | null
    meta?: {
        jsa?: JsaItem[]
        pekerja?: Array<{ nama: string; role: string; ktp_url?: string | null }>
        dokumen?: string[]
    }
    cap_url?: string | null
    ttds?: Array<{
        id: number
        nama_penandatangan: string
        jabatan: string
        label: string
        urutan: number
        ttd_url?: string | null
    }>
    ktp_urls?: string[]   // urutan sama dengan meta.pekerja
}
 
type Props = {
    surat: SuratData
    jenis: string
    can: { approve: boolean; revertDraft: boolean }
}
 
const judulJenis: Record<string, string> = {
    'SKP-BRM': 'Surat Pemberitahuan PHK',
    'GRS-BRM': 'Surat Pengajuan Garansi Material',
    'SPD-BRM': 'Surat Pengembalian Dana',
    'SK-BRM':  'Surat Permohonan Keringanan Denda',
    'IEI-BRM': 'Surat Garansi Pekerjaan',
    'SPI-BRM': 'Surat Permohonan Investasi',
    'BRM-1':   'Surat Izin Kerja dan LK3',
    'BRM-2':   'Surat Pelepasan Hak',
}
 
const IconDoc = (color = '#185FA5') => (
    <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
        <path d="M3 2.5A1.5 1.5 0 0 1 4.5 1h5.086a1.5 1.5 0 0 1 1.06.44l2.415 2.414A1.5 1.5 0 0 1 13.5 4.914V13.5A1.5 1.5 0 0 1 12 15H4.5A1.5 1.5 0 0 1 3 13.5v-11Z"
              stroke={color} strokeWidth="1.2"/>
        <path d="M6 8h4M6 10.5h2.5" stroke={color} strokeWidth="1.2" strokeLinecap="round"/>
    </svg>
)
 
const IconDetail = (color = '#534AB7') => (
    <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
        <rect x="2" y="2" width="12" height="12" rx="2" stroke={color} strokeWidth="1.2"/>
        <path d="M5 5.5h6M5 8h6M5 10.5h4" stroke={color} strokeWidth="1.2" strokeLinecap="round"/>
    </svg>
)
 
export default function Edit({
    surat,
    jenis,
    can = { approve: false, revertDraft: false },
}: Props) {
    // Initial state — hydrate dari surat (termasuk ttds, meta, urls)
    const initialTtds: TtdItem[] = (surat.ttds ?? []).map(t => ({
        id:                  t.id,
        nama_penandatangan:  t.nama_penandatangan,
        jabatan:             t.jabatan,
        label:               t.label,
        urutan:              t.urutan,
        file:                null,
        existing_ttd_url:    t.ttd_url ?? null,
    }))
 
    const initialJsa: JsaItem[] = surat.meta?.jsa ?? []
 
    const initialPekerja: PekerjaItem[] = (surat.meta?.pekerja ?? []).map((p, i) => ({
        nama:             p.nama,
        role:             p.role,
        ktp:              null,
        existing_ktp_url: surat.ktp_urls?.[i] ?? null,
    }))
 
    const initialDokumen: string[] = surat.meta?.dokumen ?? []
 
    const { data, setData, processing, errors } = useForm<{
        // Field surat
        judul:           string
        perihal:         string
        tujuan:          string
        isi_surat:       string
        tanggal_surat:   string
        nama:            string
        jabatan_terakhir:string
        departemen:      string
        lokasi_kerja:    string
        jenis_pekerjaan: string
        waktu:           string
        jam_kerja:       string
        jumlah_pekerja:  string
        apd:             string
        periode:         string
        no_pekerja:      string
        merk:            string
        warna:           string
        rangka:          string
        project:         string
        material:        string
        alamat:          string
        masa_garansi:    string
        hasil_denda:     string
        keringanan_denda:string
        lampiran:        string
        item_pembelian:  string
        nominal:         string
        nominal_bagihasil:string
        no_ktp:          string
        // Files & relasi
        cap:             File | null
        ttds:            TtdItem[]
        jsa:             JsaItem[]
        pekerja:         PekerjaItem[]
        dokumen:         string[]
    }>({
        judul:            surat.judul              ?? '',
        perihal:          surat.perihal            ?? '',
        tujuan:           surat.tujuan             ?? '',
        isi_surat:        surat.isi_surat          ?? '',
        tanggal_surat:    surat.tanggal_surat      ?? '',
        nama:             surat.nama               ?? '',
        jabatan_terakhir: surat.jabatan_terakhir   ?? '',
        departemen:       surat.departemen         ?? '',
        lokasi_kerja:     surat.lokasi_kerja       ?? '',
        jenis_pekerjaan:  surat.jenis_pekerjaan    ?? '',
        waktu:            surat.waktu              ?? '',
        jam_kerja:        surat.jam_kerja          ?? '',
        jumlah_pekerja:   surat.jumlah_pekerja     ?? '',
        apd:              surat.apd                ?? '',
        periode:          surat.periode            ?? '',
        no_pekerja:       surat.no_pekerja         ?? '',
        merk:             surat.merk               ?? '',
        warna:            surat.warna              ?? '',
        rangka:           surat.rangka             ?? '',
        project:          surat.project            ?? '',
        material:         surat.material           ?? '',
        alamat:           surat.alamat             ?? '',
        masa_garansi:     surat.masa_garansi       ?? '',
        hasil_denda:      surat.hasil_denda        ?? '',
        keringanan_denda: surat.keringanan_denda   ?? '',
        lampiran:         surat.lampiran           ?? '',
        item_pembelian:   surat.item_pembelian     ?? '',
        nominal:          surat.nominal            ?? '',
        nominal_bagihasil:surat.nominal_bagihasil  ?? '',
        no_ktp:           surat.no_ktp             ?? '',
        cap:              null,
        ttds:             initialTtds,
        jsa:              initialJsa,
        pekerja:          initialPekerja,
        dokumen:          initialDokumen,
    })

    const isReadOnly = surat.status === 'approved'

    const [statusError, setStatusError] = useState<string | null>(null)

    useEffect(() => {
        const statusErr = (errors as Record<string, string | undefined>).status
        if (statusErr) setStatusError(statusErr)
    }, [errors]) 
 
    const submit = (e: React.FormEvent) => {
        e.preventDefault()
        if (isReadOnly) return
        // Pakai POST + _method=PUT karena perlu kirim files
        router.post(
            route('filing.surat.update', { surat: surat.id, jenis }),
            { ...data, _method: 'PUT' } as any,
            { forceFormData: true, preserveScroll: true }
        )
    }
 
    return (
        <AuthenticatedLayout>
            <Head title={`Edit ${judulJenis[jenis] ?? 'Surat'}`} />
 
            <form onSubmit={submit} className="create-form">

                {isReadOnly && (
                    <ApprovedReadOnlyNotice
                        suratId={surat.id}
                        canRevertDraft={can.revertDraft}
                    />
                )}

              <RegeneratedBanner
                lastRegeneratedAt={surat.last_regenerated_at ?? null}
                variant="warning"
              />

                <div className="create-form-heading">
                    <h1 className="create-form-title">Edit {judulJenis[jenis] ?? 'surat'}</h1>
                    <p className="create-form-subtitle">
                        {isReadOnly
                            ? 'Surat ini hanya dapat dilihat (mode read-only)'
                            : 'Perbarui field yang diperlukan lalu klik Simpan Perubahan'
                        }
                    </p>
                </div>
 
                {/* Error summary */}
                {Object.keys(errors).length > 0 && (
                    <div className="form-error-summary">
                        {Object.entries(errors).map(([key, msg]) => (
                            <p key={key}><strong>{key}:</strong> {msg}</p>
                        ))}
                    </div>
                )}

            <FormFieldProvider
                data={data}
                setData={setData}
                errors={errors as Record<string, string | undefined>}
                disabled={isReadOnly}
            >

                <fieldset
                    disabled={isReadOnly}
                    style={{
                        border:        'none',
                        padding:       0,
                        margin:        0,
                        // ✅ Visual feedback saat read-only
                        opacity:       isReadOnly ? 0.7 : 1,
                        pointerEvents: isReadOnly ? 'none' : 'auto',
                        transition:    'opacity 0.2s ease',
                    }}
                >
 
                {/* Informasi Umum */}
                <div className="form-card">
                    <div className="form-card-header">
                        <div className="form-icon icon-blue">{IconDoc()}</div>
                        <span className="form-card-title">Informasi umum</span>
                    </div>
                    <div className="form-grid">
                        <Field label="Judul"        field="judul"         fullWidth placeholder="Judul surat" />
                        <Field label="Perihal"      field="perihal"       placeholder="Perihal surat" />
                        <Field label="Tujuan"       field="tujuan"        placeholder="Tujuan surat" />
                        <Field label="Tanggal surat" field="tanggal_surat" type="date" />
                    </div>
 
                </div>
 
                {/* BRM-1: Detail pekerjaan */}
                {jenis === 'BRM-1' && (
                    <>
                        <div className="form-card">
                            <div className="form-card-header">
                                <div className="form-icon icon-purple">{IconDetail()}</div>
                                <span className="form-card-title">Detail pekerjaan</span>
                            </div>
                            <div className="form-grid">
                                <Field label="Nama tujuan"   field="nama"           placeholder="Nama penerima" />
                                <Field label="Departemen"    field="departemen"     placeholder="Nama departemen" />
                                <Field label="Lokasi kerja"  field="lokasi_kerja"   placeholder="Lokasi pekerjaan" />
                                <Field label="Waktu"         field="waktu"          placeholder="Contoh: 3 hari" />
                                <Field label="Jam kerja"     field="jam_kerja"      placeholder="Contoh: 08.00–17.00" />
                                <Field label="Jumlah pekerja" field="jumlah_pekerja" placeholder="Contoh: 5 orang" />
                                <Field label="APD"           field="apd"            placeholder="Alat pelindung diri" />
                                <Field label="Periode"       field="periode"        placeholder="Contoh: Maret 2026" />
                                <Field label="No pekerja"    field="no_pekerja"     placeholder="Nomor ID pekerja" />
                                <TextArea label="Jenis pekerjaan" field="jenis_pekerjaan" />
                            </div>
                        </div>
 
                        <CapEditor
                            existingCapUrl={surat.cap_url}
                            newCap={data.cap}
                            onChange={f => setData('cap', f)}
                        />
 
                        <TtdEditor
                            ttds={data.ttds}
                            onChange={t => setData('ttds', t)}
                            minTtds={1}
                            defaultLabel={i => `Penandatangan ${i + 1}`}
                        />
 
                        <JsaEditor
                            jsa={data.jsa}
                            onChange={j => setData('jsa', j)}
                        />
 
                        <PekerjaEditor
                            pekerja={data.pekerja}
                            onChange={p => setData('pekerja', p)}
                        />
                    </>
                )}
 
                {/* IEI-BRM: Surat Garansi Pekerjaan */}
                {jenis === 'IEI-BRM' && (
                    <>
                        <div className="form-card">
                            <div className="form-card-header">
                                <div className="form-icon icon-purple">{IconDetail()}</div>
                                <span className="form-card-title">Detail pekerjaan</span>
                            </div>
                            <div className="form-grid">
                                <Field label="Project"       field="project"      placeholder="Nama project" />
                                <Field label="Lokasi kerja"  field="lokasi_kerja" placeholder="Lokasi pekerjaan" />
                                {/* ✅ Fix: pakai isi_surat (sesuai Create), bukan masa_garansi */}
                                <Field label="Masa garansi"  field="isi_surat"
                                       fullWidth
                                       placeholder="Contoh: 01/03/2026 s/d 01/03/2027" />
                                <TextArea label="Jenis pekerjaan" field="jenis_pekerjaan" />
                            </div>
                        </div>
 
                        <CapEditor
                            existingCapUrl={surat.cap_url}
                            newCap={data.cap}
                            onChange={f => setData('cap', f)}
                        />
 
                        <TtdEditor
                            ttds={data.ttds}
                            onChange={t => setData('ttds', t)}
                            minTtds={2}
                            defaultLabel={i => i === 0 ? 'PT BRM' : 'Penerima'}
                        />
 
                        <DokumenEditor
                            dokumen={data.dokumen}
                            onChange={d => setData('dokumen', d)}
                        />
                    </>
                )}
 
                {/* BRM-2  */}
                {jenis === 'BRM-2' && (
                    <>
                        <div className="form-card">
                            <div className="form-card-header">
                                <div className="form-icon icon-purple">{IconDetail()}</div>
                                <span className="form-card-title">Detail surat</span>
                            </div>
                            <div className="form-grid">
                                <Field label="Merk / jenis"   field="merk"   placeholder="Contoh: Honda Vario" />
                                <Field label="Warna / tahun"  field="warna"  placeholder="Contoh: Putih / 2020" />
                                <Field label="Rangka / mesin" field="rangka" placeholder="Nomor rangka / mesin" />
                            </div>
                        </div>
 
                        <CapEditor
                            existingCapUrl={surat.cap_url}
                            newCap={data.cap}
                            onChange={f => setData('cap', f)}
                        />
 
                        <TtdEditor
                            ttds={data.ttds}
                            onChange={t => setData('ttds', t)}
                            minTtds={1}
                        />
                    </>
                )}
 
                {/* GRS-BRM */}
                {jenis === 'GRS-BRM' && (
                    <>
                        <div className="form-card">
                            <div className="form-card-header">
                                <div className="form-icon icon-purple">{IconDetail()}</div>
                                <span className="form-card-title">Detail garansi</span>
                            </div>
                            <div className="form-grid">
                                <Field label="Project"       field="project"      placeholder="Nama project" />
                                <Field label="Material"      field="material"     placeholder="Jenis material" />
                                <Field label="Masa garansi"  field="masa_garansi" placeholder="Contoh: 12 bulan" />
                                <TextArea label="Jenis project" field="isi_surat" />
                                <TextArea label="Alamat"        field="alamat" />
                            </div>
                        </div>
 
                        <CapEditor
                            existingCapUrl={surat.cap_url}
                            newCap={data.cap}
                            onChange={f => setData('cap', f)}
                        />
 
                        <TtdEditor
                            ttds={data.ttds}
                            onChange={t => setData('ttds', t)}
                            minTtds={1}
                        />
                    </>
                )}
 
                {/* SK-BRM */}
                {jenis === 'SK-BRM' && (
                    <>
                        <div className="form-card">
                            <div className="form-card-header">
                                <div className="form-icon icon-purple">{IconDetail()}</div>
                                <span className="form-card-title">Detail denda</span>
                            </div>
                            <div className="form-grid">
                                <Field label="Hasil perhitungan denda" field="hasil_denda"      placeholder="Contoh: Rp 5.000.000" />
                                <Field label="Keringanan denda"        field="keringanan_denda" placeholder="Contoh: Rp 2.500.000" />
                                <TextArea label="Isi surat" field="isi_surat" />
                            </div>
                        </div>
 
                        <CapEditor
                            existingCapUrl={surat.cap_url}
                            newCap={data.cap}
                            onChange={f => setData('cap', f)}
                        />
 
                        <TtdEditor
                            ttds={data.ttds}
                            onChange={t => setData('ttds', t)}
                            minTtds={1}
                        />
                    </>
                )}
 
                {/* SKP-BRM */}
                {jenis === 'SKP-BRM' && (
                    <>
                        <div className="form-card">
                            <div className="form-card-header">
                                <div className="form-icon icon-purple">{IconDetail()}</div>
                                <span className="form-card-title">Data karyawan</span>
                            </div>
                            <div className="form-grid">
                                <Field label="Nama"             field="nama"             placeholder="Nama lengkap karyawan" />
                                <Field label="Jabatan terakhir" field="jabatan_terakhir" placeholder="Jabatan terakhir" />
                                <Field label="Departemen"       field="departemen"       placeholder="Nama departemen" />
                                <TextArea label="Isi surat" field="isi_surat" />
                            </div>
                        </div>
 
                        <CapEditor
                            existingCapUrl={surat.cap_url}
                            newCap={data.cap}
                            onChange={f => setData('cap', f)}
                        />
 
                        <TtdEditor
                            ttds={data.ttds}
                            onChange={t => setData('ttds', t)}
                            minTtds={1}
                        />
                    </>
                )}
 
                {/* SPD-BRM */}
                {jenis === 'SPD-BRM' && (
                    <>
                        <div className="form-card">
                            <div className="form-card-header">
                                <div className="form-icon icon-purple">{IconDetail()}</div>
                                <span className="form-card-title">Detail dana</span>
                            </div>
                            <div className="form-grid">
                                <Field label="Lampiran"       field="lampiran"       placeholder="Contoh: 1 lembar" />
                                <Field label="Alamat"         field="alamat"         placeholder="Alamat lengkap" fullWidth />
                                <Field label="Item pembelian" field="item_pembelian" placeholder="Nama item" fullWidth />
                                <Field label="Nominal"        field="nominal"        placeholder="Contoh: Rp 10.000.000" />
                                <Field label="Nama bank"      field="nama"           placeholder="Nama bank" />
                                <Field label="Nomor rekening" field="no_ktp"         placeholder="Nomor rekening" />
                                <TextArea label="Cabang" field="isi_surat" />
                            </div>
                        </div>
 
                        <CapEditor
                            existingCapUrl={surat.cap_url}
                            newCap={data.cap}
                            onChange={f => setData('cap', f)}
                        />
 
                        <TtdEditor
                            ttds={data.ttds}
                            onChange={t => setData('ttds', t)}
                            minTtds={1}
                        />
                    </>
                )}
 
                {/* SPI-BRM */}
                {jenis === 'SPI-BRM' && (
                    <>
                        <div className="form-card">
                            <div className="form-card-header">
                                <div className="form-icon icon-purple">{IconDetail()}</div>
                                <span className="form-card-title">Data investor</span>
                            </div>
                            <div className="form-grid">
                                <Field label="Nama investor"      field="nama"             placeholder="Nama lengkap investor" />
                                <Field label="No KTP"             field="no_ktp"           placeholder="Nomor KTP" />
                                <Field label="Alamat"             field="alamat"           placeholder="Alamat lengkap" fullWidth />
                                <Field label="Nominal investasi"  field="nominal"          placeholder="Contoh: Rp 100.000.000" />
                                <Field label="Nominal bagi hasil" field="nominal_bagihasil" placeholder="Contoh: Rp 5.000.000/bulan" />
                            </div>
                        </div>
 
                        <CapEditor
                            existingCapUrl={surat.cap_url}
                            newCap={data.cap}
                            onChange={f => setData('cap', f)}
                        />
 
                        <TtdEditor
                            ttds={data.ttds}
                            onChange={t => setData('ttds', t)}
                            minTtds={2}
                            defaultLabel={i => i === 0 ? 'Pihak Pertama' : 'Pihak Kedua'}
                        />
                    </>
                )}
                </fieldset>
            </FormFieldProvider>
 
                {!isReadOnly && (
                    <button type="submit" className="btn-submit" disabled={processing}>
                        {processing ? 'Menyimpan…' : 'Simpan perubahan'}
                    </button>
                )}
            </form>

            <ApprovedErrorModal
                error={statusError}
                onDismiss={() => setStatusError(null)}
                redirectUrl={route('filing.surat.show', surat.id)}
            />
            
        </AuthenticatedLayout>
    )
}