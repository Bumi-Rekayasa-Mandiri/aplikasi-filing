import { parseJenisPekerjaan } from '@/types/filing/parseJenisPekerjaan'

export default function DetailIEI({ surat }: { surat: any }) {
    const pekerjaanList: string[] = parseJenisPekerjaan(surat.jenis_pekerjaan)
    const dokumenList:   string[] = surat.meta?.dokumen ?? []

    return (
        <div className="detail-body">
            <div className="detail-row">
                <span className="detail-key">Lokasi kerja</span>
                <span className="detail-val">{surat.lokasi_kerja ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">Project</span>
                <span className="detail-val">{surat.project ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">Masa garansi</span>
                <span className="detail-val">{surat.isi_surat ?? surat.masa_garansi ?? '—'}</span>
            </div>

            {/* Jenis Pekerjaan */}
            {pekerjaanList.length > 0 && (
                <div className="detail-row detail-row--block">
                    <span className="detail-key">Jenis pekerjaan</span>
                    <ul className="pekerjaan-list">
                        {pekerjaanList.map((item, i) => (
                            <li key={i} className="pekerjaan-item">
                                <span className="pekerjaan-num">{i + 1}</span>
                                {item}
                            </li>
                        ))}
                    </ul>
                </div>
            )}

            {/* ── Daftar Dokumen Lampiran ──────────────────────── */}
            <div style={{ marginTop: '20px' }}>
                <div style={{
                    display:      'flex',
                    alignItems:   'center',
                    gap:          '8px',
                    marginBottom: '10px',
                }}>
                    <span style={{
                        fontSize:      '12px',
                        fontWeight:    600,
                        letterSpacing: '0.05em',
                        textTransform: 'uppercase',
                        color:         '#64748b',
                    }}>
                        Daftar Dokumen Lampiran
                    </span>
                    {dokumenList.length > 0 && (
                        <span style={{
                            fontSize:     '11px',
                            fontWeight:   600,
                            color:        '#3C3489',
                            background:   '#EEEDFE',
                            padding:      '2px 8px',
                            borderRadius: '999px',
                        }}>
                            {dokumenList.length} dokumen
                        </span>
                    )}
                </div>

                {dokumenList.length === 0 ? (
                    <p style={{ fontSize: '13px', color: '#94a3b8', fontStyle: 'italic' }}>
                        Tidak ada dokumen lampiran.
                    </p>
                ) : (
                    <div style={{
                        border:       '1px solid #e2e8f0',
                        borderRadius: '10px',
                        overflow:     'hidden',
                    }}>
                        {dokumenList.map((dok, i) => (
                            <div key={i} style={{
                                display:      'flex',
                                alignItems:   'center',
                                gap:          '12px',
                                padding:      '10px 14px',
                                borderBottom: i < dokumenList.length - 1 ? '1px solid #f1f5f9' : 'none',
                                background:   i % 2 === 0 ? '#ffffff' : '#fafafa',
                            }}>
                                {/* Nomor urut */}
                                <span style={{
                                    display:        'inline-flex',
                                    alignItems:     'center',
                                    justifyContent: 'center',
                                    flexShrink:     0,
                                    width:          '22px',
                                    height:         '22px',
                                    borderRadius:   '6px',
                                    background:     '#EEEDFE',
                                    color:          '#3C3489',
                                    fontSize:       '11px',
                                    fontWeight:     700,
                                }}>
                                    {i + 1}
                                </span>

                                {/* Ikon dokumen */}
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none"
                                     style={{ flexShrink: 0, color: '#94a3b8' }}>
                                    <path d="M3 2.5A1.5 1.5 0 0 1 4.5 1h5.086a1.5 1.5 0 0 1 1.06.44l2.415 2.414A1.5 1.5 0 0 1 13.5 4.914V13.5A1.5 1.5 0 0 1 12 15H4.5A1.5 1.5 0 0 1 3 13.5v-11Z"
                                          stroke="currentColor" strokeWidth="1.2"/>
                                    <path d="M6 8h4M6 10.5h2.5" stroke="currentColor" strokeWidth="1.2" strokeLinecap="round"/>
                                </svg>

                                {/* Nama dokumen */}
                                <span style={{
                                    fontSize:   '13px',
                                    fontWeight: 500,
                                    color:      '#1e293b',
                                }}>
                                    {dok || '—'}
                                </span>
                            </div>
                        ))}
                    </div>
                )}
            </div>
        </div>
    )
}