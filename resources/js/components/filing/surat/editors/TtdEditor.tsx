import FilePreviewSlot from './FilePreviewSlot'
 
export interface TtdItem {
    id?: number                    // ID dari DB (jika sudah tersimpan)
    nama_penandatangan: string
    jabatan: string
    label: string
    urutan: number
    file: File | null              // file baru jika user upload
    existing_ttd_url?: string | null  // URL TTD lama yang sudah tersimpan
}
 
interface Props {
    ttds: TtdItem[]
    onChange: (ttds: TtdItem[]) => void
    /** Minimum jumlah TTD yang harus ada, default: 1 */
    minTtds?: number
    /** Default label saat tambah TTD baru (function index→label) */
    defaultLabel?: (index: number) => string
}
 
const IconTtd = () => (
    <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
        <path d="M2 12c1-2 2-3 3.5-3S8 11 9.5 11s2-1 3-3"
              stroke="#854F0B" strokeWidth="1.2" strokeLinecap="round"/>
        <path d="M11 4l1 1-5 5-1.5.5.5-1.5L11 4Z"
              stroke="#854F0B" strokeWidth="1.2" strokeLinejoin="round"/>
    </svg>
)
 
export default function TtdEditor({
    ttds,
    onChange,
    minTtds = 1,
    defaultLabel = (i) => `Penandatangan ${i + 1}`,
}: Props) {
    const addTtd = () => {
        onChange([...ttds, {
            nama_penandatangan: '',
            jabatan:            '',
            label:              defaultLabel(ttds.length),
            urutan:             ttds.length + 1,
            file:               null,
        }])
    }
 
    const removeTtd = (i: number) => {
        if (ttds.length <= minTtds) return
        onChange(ttds.filter((_, idx) => idx !== i))
    }
 
    const updateTtd = <K extends keyof TtdItem>(
        i: number,
        field: K,
        value: TtdItem[K]
    ) => {
        const updated = [...ttds]
        updated[i] = { ...updated[i], [field]: value }
        onChange(updated)
    }
 
    return (
        <div className="form-card">
            <div className="form-card-header">
                <div className="form-icon icon-amber"><IconTtd /></div>
                <span className="form-card-title">
                    Tanda tangan
                    {ttds.length > 0 && (
                        <span style={{
                            marginLeft:   '8px',
                            fontSize:     '11px',
                            background:   '#FEF3C7',
                            color:        '#854F0B',
                            padding:      '2px 8px',
                            borderRadius: '999px',
                        }}>
                            {ttds.length}
                        </span>
                    )}
                </span>
            </div>
 
            <div className="ttd-list">
                {ttds.map((ttd, i) => (
                    <div key={ttd.id ?? `new-${i}`} className="ttd-item">
                        <div className="ttd-item-header">
                            <div className="ttd-item-title">
                                <span className="ttd-num">{i + 1}</span>
                                Penandatangan {i + 1}
                                {ttd.id && (
                                    <span style={{
                                        marginLeft:   '8px',
                                        fontSize:     '10px',
                                        color:        '#64748b',
                                        background:   '#f1f5f9',
                                        padding:      '2px 6px',
                                        borderRadius: '999px',
                                    }}>
                                        Tersimpan
                                    </span>
                                )}
                            </div>
                            {ttds.length > minTtds && (
                                <button
                                    type="button"
                                    className="ttd-remove"
                                    onClick={() => removeTtd(i)}
                                >
                                    Hapus
                                </button>
                            )}
                        </div>
 
                        <div className="ttd-fields">
                            <div className="field">
                                <label className="field-label">Label</label>
                                <input
                                    className="field-input"
                                    value={ttd.label}
                                    placeholder="Contoh: Hormat Kami"
                                    onChange={e => updateTtd(i, 'label', e.target.value)}
                                />
                            </div>
                            <div className="field">
                                <label className="field-label">Nama penandatangan</label>
                                <input
                                    className="field-input"
                                    value={ttd.nama_penandatangan}
                                    placeholder="Nama lengkap"
                                    onChange={e => updateTtd(i, 'nama_penandatangan', e.target.value)}
                                />
                            </div>
                            <div className="field">
                                <label className="field-label">Jabatan</label>
                                <input
                                    className="field-input"
                                    value={ttd.jabatan}
                                    placeholder="Jabatan"
                                    onChange={e => updateTtd(i, 'jabatan', e.target.value)}
                                />
                            </div>
 
                            {/* File slot dengan preview existing */}
                            <FilePreviewSlot
                                existingUrl={ttd.existing_ttd_url}
                                newFile={ttd.file}
                                onChange={f => updateTtd(i, 'file', f)}
                                label="Tanda tangan"
                                accept="image/*"
                                hint="PNG transparan lebih baik"
                                maxHeight={100}
                            />
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
    )
}