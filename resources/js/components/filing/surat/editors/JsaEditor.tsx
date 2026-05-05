export interface JsaItem {
    urutan_kerja:       string
    potensi_bahaya:     string
    upaya_pengendalian: string
}
 
interface Props {
    jsa: JsaItem[]
    onChange: (jsa: JsaItem[]) => void
}
 
const IconJsa = () => (
    <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
        <rect x="2" y="2" width="12" height="12" rx="2" stroke="#534AB7" strokeWidth="1.2"/>
        <path d="M5 5.5h6M5 8h6M5 10.5h4" stroke="#534AB7" strokeWidth="1.2" strokeLinecap="round"/>
    </svg>
)
 
export default function JsaEditor({ jsa, onChange }: Props) {
    const add = () => onChange([
        ...jsa,
        { urutan_kerja: '', potensi_bahaya: '', upaya_pengendalian: '' }
    ])
 
    const remove = (i: number) =>
        onChange(jsa.filter((_, idx) => idx !== i))
 
    const update = (i: number, field: keyof JsaItem, value: string) => {
        const updated = [...jsa]
        updated[i] = { ...updated[i], [field]: value }
        onChange(updated)
    }
 
    return (
        <div className="form-card">
            <div className="form-card-header">
                <div className="form-icon icon-purple"><IconJsa /></div>
                <span className="form-card-title">
                    Lampiran A — JSA
                    {jsa.length > 0 && (
                        <span style={{
                            marginLeft:   '8px',
                            fontSize:     '11px',
                            background:   '#EEEDFE',
                            color:        '#3C3489',
                            padding:      '2px 8px',
                            borderRadius: '999px',
                        }}>
                            {jsa.length} item
                        </span>
                    )}
                </span>
            </div>
 
            <div className="ttd-list">
                {jsa.map((item, i) => (
                    <div key={i} className="ttd-item">
                        <div className="ttd-item-header">
                            <div className="ttd-item-title">
                                <span className="ttd-num">{i + 1}</span>
                                Item JSA {i + 1}
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
                            <div className="field col-span-2">
                                <label className="field-label">Urutan kerja</label>
                                <input
                                    className="field-input"
                                    placeholder="Contoh: Penggalian pondasi"
                                    value={item.urutan_kerja}
                                    onChange={e => update(i, 'urutan_kerja', e.target.value)}
                                />
                            </div>
                            <div className="field">
                                <label className="field-label">Potensi bahaya</label>
                                <textarea
                                    className="field-input"
                                    placeholder={"Tulis tiap potensi dipisah baris baru"}
                                    value={item.potensi_bahaya}
                                    rows={4}
                                    onChange={e => update(i, 'potensi_bahaya', e.target.value)}
                                />
                            </div>
                            <div className="field">
                                <label className="field-label">Upaya pengendalian</label>
                                <textarea
                                    className="field-input"
                                    placeholder={"Tulis tiap upaya dipisah baris baru"}
                                    value={item.upaya_pengendalian}
                                    rows={4}
                                    onChange={e => update(i, 'upaya_pengendalian', e.target.value)}
                                />
                            </div>
                        </div>
                    </div>
                ))}
 
                <button type="button" className="btn-add" onClick={add}>
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                        <path d="M8 3v10M3 8h10" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/>
                    </svg>
                    Tambah item JSA
                </button>
            </div>
        </div>
    )
}