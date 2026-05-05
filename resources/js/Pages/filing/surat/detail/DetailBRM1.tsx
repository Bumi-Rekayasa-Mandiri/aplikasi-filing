// resources/js/Components/filing/detail/DetailBRM1.tsx

type JsaItem = {
    urutan_kerja:       string
    potensi_bahaya:     string
    upaya_pengendalian: string
}

type PekerjaItem = {
    nama: string
    role: string
}

export default function DetailBRM1({ surat }: { surat: any }) {
    const jsaList:     JsaItem[]     = surat.meta?.jsa     ?? []
    const pekerjaList: PekerjaItem[] = surat.meta?.pekerja ?? []

    return (
        <div className="detail-body">

            {/* ── Info Pekerjaan ─────────────────────────────── */}
            <div className="detail-row">
                <span className="detail-key">Lokasi kerja</span>
                <span className="detail-val">{surat.lokasi_kerja ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">Jenis pekerjaan</span>
                <span className="detail-val">{surat.jenis_pekerjaan ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">Waktu</span>
                <span className="detail-val">{surat.waktu ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">Jam kerja</span>
                <span className="detail-val">{surat.jam_kerja ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">Jumlah pekerja</span>
                <span className="detail-val">{surat.jumlah_pekerja ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">Departemen</span>
                <span className="detail-val">{surat.departemen ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">APD</span>
                <span className="detail-val">{surat.apd ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">Periode</span>
                <span className="detail-val">{surat.periode ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">No. Pekerja</span>
                <span className="detail-val">{surat.no_pekerja ?? '—'}</span>
            </div>

            {/* ── Lampiran A: JSA ────────────────────────────── */}
            <div style={{ marginTop: '20px' }}>
                <div style={{
                    display:      'flex',
                    alignItems:   'center',
                    gap:          '8px',
                    marginBottom: '10px',
                }}>
                    <span style={{
                        fontSize:     '12px',
                        fontWeight:   600,
                        letterSpacing:'0.05em',
                        textTransform:'uppercase',
                        color:        '#64748b',
                    }}>
                        Lampiran A — JSA
                    </span>
                    {jsaList.length > 0 && (
                        <span style={{
                            fontSize:     '11px',
                            fontWeight:   600,
                            color:        '#3C3489',
                            background:   '#EEEDFE',
                            padding:      '2px 8px',
                            borderRadius: '999px',
                        }}>
                            {jsaList.length} item
                        </span>
                    )}
                </div>

                {jsaList.length === 0 ? (
                    <p style={{ fontSize: '13px', color: '#94a3b8', fontStyle: 'italic' }}>
                        Tidak ada data JSA.
                    </p>
                ) : (
                    <div style={{ display: 'flex', flexDirection: 'column', gap: '10px' }}>
                        {jsaList.map((item, i) => (
                            <div key={i} style={{
                                border:       '1px solid #e2e8f0',
                                borderRadius: '10px',
                                overflow:     'hidden',
                            }}>
                                {/* Header item */}
                                <div style={{
                                    display:        'flex',
                                    alignItems:     'center',
                                    gap:            '8px',
                                    padding:        '8px 14px',
                                    background:     '#f8fafc',
                                    borderBottom:   '1px solid #e2e8f0',
                                }}>
                                    <span style={{
                                        display:        'inline-flex',
                                        alignItems:     'center',
                                        justifyContent: 'center',
                                        width:          '20px',
                                        height:         '20px',
                                        borderRadius:   '6px',
                                        background:     '#EEEDFE',
                                        color:          '#3C3489',
                                        fontSize:       '11px',
                                        fontWeight:     700,
                                        flexShrink:     0,
                                    }}>
                                        {i + 1}
                                    </span>
                                    <span style={{
                                        fontSize:   '13px',
                                        fontWeight: 600,
                                        color:      '#1e293b',
                                    }}>
                                        {item.urutan_kerja || '—'}
                                    </span>
                                </div>

                                {/* Dua kolom: Potensi Bahaya | Upaya Pengendalian */}
                                <div style={{
                                    display:             'grid',
                                    gridTemplateColumns: '1fr 1fr',
                                }}>
                                    {/* Potensi Bahaya */}
                                    <div style={{
                                        padding:      '10px 14px',
                                        borderRight:  '1px solid #e2e8f0',
                                    }}>
                                        <div style={{
                                            fontSize:     '11px',
                                            fontWeight:   600,
                                            color:        '#ef4444',
                                            marginBottom: '6px',
                                            textTransform:'uppercase',
                                            letterSpacing:'0.04em',
                                        }}>
                                            Potensi Bahaya
                                        </div>
                                        {item.potensi_bahaya
                                            ? item.potensi_bahaya.split('\n').filter(Boolean).map((line, j) => (
                                                <div key={j} style={{
                                                    display:     'flex',
                                                    gap:         '6px',
                                                    fontSize:    '13px',
                                                    color:       '#374151',
                                                    lineHeight:  1.5,
                                                    marginBottom:'2px',
                                                }}>
                                                    <span style={{ color: '#ef4444', flexShrink: 0 }}>•</span>
                                                    <span>{line.trim()}</span>
                                                </div>
                                            ))
                                            : <span style={{ fontSize: '13px', color: '#94a3b8' }}>—</span>
                                        }
                                    </div>

                                    {/* Upaya Pengendalian */}
                                    <div style={{ padding: '10px 14px' }}>
                                        <div style={{
                                            fontSize:     '11px',
                                            fontWeight:   600,
                                            color:        '#16a34a',
                                            marginBottom: '6px',
                                            textTransform:'uppercase',
                                            letterSpacing:'0.04em',
                                        }}>
                                            Upaya Pengendalian
                                        </div>
                                        {item.upaya_pengendalian
                                            ? item.upaya_pengendalian.split('\n').filter(Boolean).map((line, j) => (
                                                <div key={j} style={{
                                                    display:     'flex',
                                                    gap:         '6px',
                                                    fontSize:    '13px',
                                                    color:       '#374151',
                                                    lineHeight:  1.5,
                                                    marginBottom:'2px',
                                                }}>
                                                    <span style={{ color: '#16a34a', flexShrink: 0 }}>✓</span>
                                                    <span>{line.trim()}</span>
                                                </div>
                                            ))
                                            : <span style={{ fontSize: '13px', color: '#94a3b8' }}>—</span>
                                        }
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            {/* ── Lampiran B: Daftar Pekerja ─────────────────── */}
            <div style={{ marginTop: '20px' }}>
                <div style={{
                    display:      'flex',
                    alignItems:   'center',
                    gap:          '8px',
                    marginBottom: '10px',
                }}>
                    <span style={{
                        fontSize:     '12px',
                        fontWeight:   600,
                        letterSpacing:'0.05em',
                        textTransform:'uppercase',
                        color:        '#64748b',
                    }}>
                        Lampiran B — Daftar Pekerja
                    </span>
                    {pekerjaList.length > 0 && (
                        <span style={{
                            fontSize:     '11px',
                            fontWeight:   600,
                            color:        '#27500A',
                            background:   '#EAF3DE',
                            padding:      '2px 8px',
                            borderRadius: '999px',
                        }}>
                            {pekerjaList.length} pekerja
                        </span>
                    )}
                </div>

                {pekerjaList.length === 0 ? (
                    <p style={{ fontSize: '13px', color: '#94a3b8', fontStyle: 'italic' }}>
                        Tidak ada data pekerja.
                    </p>
                ) : (
                    <div style={{
                        border:       '1px solid #e2e8f0',
                        borderRadius: '10px',
                        overflow:     'hidden',
                    }}>
                        {/* Header tabel */}
                        <div style={{
                            display:             'grid',
                            gridTemplateColumns: '36px 1fr 100px',
                            padding:             '8px 14px',
                            background:          '#f8fafc',
                            borderBottom:        '1px solid #e2e8f0',
                            fontSize:            '11px',
                            fontWeight:          700,
                            color:               '#64748b',
                            textTransform:       'uppercase',
                            letterSpacing:       '0.04em',
                        }}>
                            <span>#</span>
                            <span>Nama</span>
                            <span>Role</span>
                        </div>

                        {/* Baris pekerja */}
                        {pekerjaList.map((p, i) => (
                            <div key={i} style={{
                                display:             'grid',
                                gridTemplateColumns: '36px 1fr 100px',
                                alignItems:          'center',
                                padding:             '9px 14px',
                                borderBottom:        i < pekerjaList.length - 1 ? '1px solid #f1f5f9' : 'none',
                                background:          i % 2 === 0 ? '#ffffff' : '#fafafa',
                            }}>
                                <span style={{
                                    fontSize:   '12px',
                                    fontWeight: 600,
                                    color:      '#94a3b8',
                                }}>
                                    {i + 1}
                                </span>
                                <span style={{
                                    fontSize:   '13px',
                                    fontWeight: 500,
                                    color:      '#1e293b',
                                }}>
                                    {p.nama || '—'}
                                </span>
                                <span style={{
                                    display:      'inline-flex',
                                    alignItems:   'center',
                                    fontSize:     '11px',
                                    fontWeight:   600,
                                    padding:      '2px 8px',
                                    borderRadius: '999px',
                                    ...(p.role === 'Pengawas'
                                        ? { background: '#fef3c7', color: '#92400e' }
                                        : p.role === 'Teknisi'
                                        ? { background: '#EEEDFE', color: '#3C3489' }
                                        : p.role === 'Driver'
                                        ? { background: '#e0f2fe', color: '#075985' }
                                        : { background: '#f1f5f9', color: '#475569' }
                                    ),
                                }}>
                                    {p.role || '—'}
                                </span>
                            </div>
                        ))}
                    </div>
                )}
            </div>

        </div>
    )
}