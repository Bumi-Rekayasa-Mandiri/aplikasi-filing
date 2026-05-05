import FilePreviewSlot from './FilePreviewSlot'
 
export interface PekerjaItem {
    nama: string
    role: string
    ktp:  File | null
    /** URL KTP lama yang sudah tersimpan */
    existing_ktp_url?: string | null
}
 
interface Props {
    pekerja: PekerjaItem[]
    onChange: (pekerja: PekerjaItem[]) => void
}
 
const ROLES = ['Pengawas', 'Pekerja', 'Driver', 'Teknisi']
 
const IconPekerja = () => (
    <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
        <circle cx="8" cy="5" r="3" stroke="#0F6E56" strokeWidth="1.2"/>
        <path d="M2 14c0-3 2.5-5 6-5s6 2 6 5"
              stroke="#0F6E56" strokeWidth="1.2" strokeLinecap="round"/>
    </svg>
)
 
export default function PekerjaEditor({ pekerja, onChange }: Props) {
    const add = () => onChange([
        ...pekerja,
        { nama: '', role: 'Pekerja', ktp: null }
    ])
 
    const remove = (i: number) =>
        onChange(pekerja.filter((_, idx) => idx !== i))
 
    const update = <K extends keyof PekerjaItem>(
        i: number,
        field: K,
        value: PekerjaItem[K]
    ) => {
        const updated = [...pekerja]
        updated[i] = { ...updated[i], [field]: value }
        onChange(updated)
    }
 
    return (
        <div className="form-card">
            <div className="form-card-header">
                <div className="form-icon icon-teal"><IconPekerja /></div>
                <span className="form-card-title">
                    Lampiran B — Daftar pekerja
                    {pekerja.length > 0 && (
                        <span style={{
                            marginLeft:   '8px',
                            fontSize:     '11px',
                            background:   '#EAF3DE',
                            color:        '#27500A',
                            padding:      '2px 8px',
                            borderRadius: '999px',
                        }}>
                            {pekerja.length} pekerja
                        </span>
                    )}
                </span>
            </div>
 
            <div className="ttd-list">
                {pekerja.map((p, i) => (
                    <div key={i} className="ttd-item">
                        <div className="ttd-item-header">
                            <div className="ttd-item-title">
                                <span className="ttd-num">{i + 1}</span>
                                Pekerja {i + 1}
                            </div>
                            <button
                                type="button"
                                className="ttd-remove"
                                onClick={() => remove(i)}
                            >
                                Hapus
                            </button>
                        </div>
 
                        <div className="ttd-fields">
                            <div className="field">
                                <label className="field-label">Nama pekerja</label>
                                <input
                                    className="field-input"
                                    placeholder="Nama lengkap"
                                    value={p.nama}
                                    onChange={e => update(i, 'nama', e.target.value)}
                                />
                            </div>
                            <div className="field">
                                <label className="field-label">Role</label>
                                <select
                                    className="field-input"
                                    value={p.role}
                                    onChange={e => update(i, 'role', e.target.value)}
                                >
                                    {ROLES.map(r => (
                                        <option key={r} value={r}>{r}</option>
                                    ))}
                                </select>
                            </div>
 
                            <div className="col-span-2">
                                <FilePreviewSlot
                                    existingUrl={p.existing_ktp_url}
                                    newFile={p.ktp}
                                    onChange={f => update(i, 'ktp', f)}
                                    label="Foto KTP"
                                    accept="image/*"
                                    hint="JPG, PNG hingga 2MB"
                                    maxHeight={140}
                                />
                            </div>
                        </div>
                    </div>
                ))}
 
                <button type="button" className="btn-add" onClick={add}>
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                        <path d="M8 3v10M3 8h10" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/>
                    </svg>
                    Tambah pekerja
                </button>
            </div>
        </div>
    )
}