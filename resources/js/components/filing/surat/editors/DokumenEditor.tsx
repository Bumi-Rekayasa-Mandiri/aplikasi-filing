interface Props {
    dokumen: string[]
    onChange: (dokumen: string[]) => void
}
 
const IconDoc = () => (
    <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
        <rect x="2" y="2" width="12" height="12" rx="2" stroke="#534AB7" strokeWidth="1.2"/>
        <path d="M5 5.5h6M5 8h6M5 10.5h4" stroke="#534AB7" strokeWidth="1.2" strokeLinecap="round"/>
    </svg>
)
 
export default function DokumenEditor({ dokumen, onChange }: Props) {
    const add = () => onChange([...dokumen, ''])
 
    const remove = (i: number) =>
        onChange(dokumen.filter((_, idx) => idx !== i))
 
    const update = (i: number, value: string) => {
        const updated = [...dokumen]
        updated[i] = value
        onChange(updated)
    }
 
    return (
        <div className="form-card">
            <div className="form-card-header">
                <div className="form-icon icon-purple"><IconDoc /></div>
                <span className="form-card-title">
                    Daftar dokumen lampiran
                    {dokumen.length > 0 && (
                        <span style={{
                            marginLeft:   '8px',
                            fontSize:     '11px',
                            background:   '#EEEDFE',
                            color:        '#3C3489',
                            padding:      '2px 8px',
                            borderRadius: '999px',
                        }}>
                            {dokumen.length} dokumen
                        </span>
                    )}
                </span>
            </div>
 
            <div className="ttd-list">
                {dokumen.map((dok, i) => (
                    <div key={i} className="ttd-item">
                        <div className="ttd-item-header">
                            <div className="ttd-item-title">
                                <span className="ttd-num">{i + 1}</span>
                                Dokumen {i + 1}
                            </div>
                            <button
                                type="button"
                                className="ttd-remove"
                                onClick={() => remove(i)}
                            >
                                Hapus
                            </button>
                        </div>
                        <div className="field">
                            <input
                                className="field-input"
                                placeholder="Contoh: Guarantee Letter"
                                value={dok}
                                onChange={e => update(i, e.target.value)}
                            />
                        </div>
                    </div>
                ))}
 
                <button type="button" className="btn-add" onClick={add}>
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                        <path d="M8 3v10M3 8h10" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/>
                    </svg>
                    Tambah dokumen
                </button>
            </div>
        </div>
    )
}