// resources/js/Components/filing/ArchiveYearModal.tsx
import { router } from '@inertiajs/react'
import { useEffect, useState } from 'react'

interface Props {
    availableYears?: number[]
}

export default function ArchiveYearModal({ availableYears = [] }: Props) {
    const [open, setOpen]       = useState(false)
    const [tahun, setTahun]     = useState<number>(new Date().getFullYear() - 1)
    const [loading, setLoading] = useState(false)

    const currentYear = new Date().getFullYear()
    const years = (availableYears.length > 0
        ? availableYears
        : Array.from({ length: 5 }, (_, i) => currentYear - 1 - i)
    ).filter(y => y < currentYear)

    // Tutup dengan Escape
    useEffect(() => {
        if (!open) return
        const onKey = (e: KeyboardEvent) => { if (e.key === 'Escape') setOpen(false) }
        window.addEventListener('keydown', onKey)
        return () => window.removeEventListener('keydown', onKey)
    }, [open])

    // Lock body scroll saat modal terbuka
    useEffect(() => {
        document.body.style.overflow = open ? 'hidden' : ''
        return () => { document.body.style.overflow = '' }
    }, [open])

    const handleSubmit = () => {
        setLoading(true)
        router.post(
            route('filing.surat.archive-year'),
            { tahun },
            {
                onFinish:       () => { setLoading(false); setOpen(false) },
                preserveScroll: true,
            }
        )
    }

    return (
        <>
            {/* ── Trigger Button ─────────────────────────────────── */}
            <button
                onClick={() => setOpen(true)}
                style={{
                    display:      'inline-flex',
                    alignItems:   'center',
                    gap:          '6px',
                    padding:      '6px 14px',
                    fontSize:     '13px',
                    fontWeight:   500,
                    color:        '#92400e',
                    background:   '#fef3c7',
                    border:       '1px solid #fcd34d',
                    borderRadius: '8px',
                    cursor:       'pointer',
                    lineHeight:   '1',
                    whiteSpace:   'nowrap',
                    transition:   'all 0.15s',
                }}
                onMouseEnter={e => {
                    const b = e.currentTarget
                    b.style.background   = '#fde68a'
                    b.style.borderColor  = '#f59e0b'
                }}
                onMouseLeave={e => {
                    const b = e.currentTarget
                    b.style.background   = '#fef3c7'
                    b.style.borderColor  = '#fcd34d'
                }}
            >
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" strokeWidth="2"
                     strokeLinecap="round" strokeLinejoin="round">
                    <polyline points="21 8 21 21 3 21 3 8"/>
                    <rect x="1" y="3" width="22" height="5"/>
                    <line x1="10" y1="12" x2="14" y2="12"/>
                </svg>
                Arsipkan Tahun Lalu
            </button>

            {/* ── Modal Portal ──────────────────────────────────── */}
            {open && (
                /* Overlay — position:fixed agar selalu di atas segalanya */
                <div
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="archive-modal-title"
                    onClick={() => !loading && setOpen(false)}
                    style={{
                        position:       'fixed',
                        inset:          0,
                        zIndex:         9999,
                        display:        'flex',
                        alignItems:     'center',
                        justifyContent: 'center',
                        padding:        '16px',
                        background:     'rgba(15, 23, 42, 0.5)',
                        backdropFilter: 'blur(3px)',
                        animation:      'archiveFadeIn 0.15s ease',
                    }}
                >
                    {/* Modal Box */}
                    <div
                        onClick={e => e.stopPropagation()}
                        style={{
                            width:        '100%',
                            maxWidth:     '440px',
                            background:   '#ffffff',
                            borderRadius: '16px',
                            boxShadow:    '0 24px 64px rgba(0,0,0,0.20), 0 4px 16px rgba(0,0,0,0.08)',
                            overflow:     'hidden',
                            animation:    'archiveSlideUp 0.18s ease',
                        }}
                    >
                        {/* Header */}
                        <div style={{
                            display:      'flex',
                            alignItems:   'flex-start',
                            gap:          '14px',
                            padding:      '22px 24px 18px',
                            borderBottom: '1px solid #f1f5f9',
                        }}>
                            <div style={{
                                flexShrink:     0,
                                width:          '42px',
                                height:         '42px',
                                borderRadius:   '10px',
                                background:     '#fef3c7',
                                border:         '1px solid #fde68a',
                                display:        'flex',
                                alignItems:     'center',
                                justifyContent: 'center',
                                color:          '#d97706',
                            }}>
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" strokeWidth="2"
                                     strokeLinecap="round" strokeLinejoin="round">
                                    <polyline points="21 8 21 21 3 21 3 8"/>
                                    <rect x="1" y="3" width="22" height="5"/>
                                    <line x1="10" y1="12" x2="14" y2="12"/>
                                </svg>
                            </div>
                            <div style={{ flex: 1 }}>
                                <h3 id="archive-modal-title" style={{
                                    margin:     0,
                                    fontSize:   '16px',
                                    fontWeight: 600,
                                    color:      '#0f172a',
                                    lineHeight: 1.3,
                                }}>
                                    Arsipkan Surat Tahunan
                                </h3>
                                <p style={{
                                    margin:     '4px 0 0',
                                    fontSize:   '13px',
                                    color:      '#64748b',
                                    lineHeight: 1.55,
                                }}>
                                    Surat yang dipilih akan <strong style={{ color: '#374151' }}>disembunyikan</strong> dari
                                    index manajemen dan dipindahkan ke <strong style={{ color: '#374151' }}>Arsip Surat</strong>.
                                    Surat masih bisa di-restore kapan saja.
                                </p>
                            </div>
                        </div>

                        {/* Body */}
                        <div style={{ padding: '20px 24px 4px' }}>
                            <label style={{
                                display:      'block',
                                fontSize:     '12px',
                                fontWeight:   600,
                                letterSpacing: '0.05em',
                                textTransform: 'uppercase',
                                color:        '#94a3b8',
                                marginBottom: '8px',
                            }}>
                                Pilih Tahun
                            </label>
                            <select
                                value={tahun}
                                onChange={e => setTahun(Number(e.target.value))}
                                disabled={loading}
                                style={{
                                    width:        '100%',
                                    padding:      '10px 12px',
                                    fontSize:     '15px',
                                    fontWeight:   600,
                                    color:        '#0f172a',
                                    background:   '#f8fafc',
                                    border:       '1.5px solid #e2e8f0',
                                    borderRadius: '8px',
                                    outline:      'none',
                                    cursor:       'pointer',
                                    marginBottom: '16px',
                                }}
                            >
                                {years.length > 0
                                    ? years.map(y => <option key={y} value={y}>{y}</option>)
                                    : <option value={currentYear - 1}>{currentYear - 1}</option>
                                }
                            </select>

                            {/* Warning */}
                            <div style={{
                                display:      'flex',
                                gap:          '10px',
                                padding:      '12px 14px',
                                background:   '#fffbeb',
                                border:       '1px solid #fde68a',
                                borderRadius: '8px',
                                fontSize:     '13px',
                                color:        '#92400e',
                                lineHeight:   1.55,
                                marginBottom: '20px',
                            }}>
                                <span style={{ flexShrink: 0 }}>⚠️</span>
                                <span>
                                    Semua surat tahun&nbsp;<strong>{tahun}</strong>&nbsp;
                                    (tanpa filter status) akan diarsipkan.
                                </span>
                            </div>
                        </div>

                        {/* Footer */}
                        <div style={{
                            display:        'flex',
                            justifyContent: 'flex-end',
                            gap:            '10px',
                            padding:        '0 24px 24px',
                        }}>
                            <button
                                onClick={() => setOpen(false)}
                                disabled={loading}
                                style={{
                                    padding:      '9px 18px',
                                    fontSize:     '13px',
                                    fontWeight:   500,
                                    color:        '#64748b',
                                    background:   '#f1f5f9',
                                    border:       '1px solid #e2e8f0',
                                    borderRadius: '8px',
                                    cursor:       loading ? 'not-allowed' : 'pointer',
                                    opacity:      loading ? 0.5 : 1,
                                }}
                            >
                                Batal
                            </button>
                            <button
                                onClick={handleSubmit}
                                disabled={loading}
                                style={{
                                    display:      'inline-flex',
                                    alignItems:   'center',
                                    gap:          '7px',
                                    padding:      '9px 18px',
                                    fontSize:     '13px',
                                    fontWeight:   600,
                                    color:        '#ffffff',
                                    background:   loading ? '#9ca3af' : '#d97706',
                                    border:       'none',
                                    borderRadius: '8px',
                                    cursor:       loading ? 'not-allowed' : 'pointer',
                                    transition:   'background 0.15s',
                                }}
                            >
                                {loading ? (
                                    <>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" strokeWidth="2.5"
                                             style={{ animation: 'archiveSpin 0.8s linear infinite' }}>
                                            <path d="M21 12a9 9 0 1 1-6.22-8.56"/>
                                        </svg>
                                        Memproses...
                                    </>
                                ) : (
                                    <>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" strokeWidth="2.5"
                                             strokeLinecap="round" strokeLinejoin="round">
                                            <polyline points="21 8 21 21 3 21 3 8"/>
                                            <rect x="1" y="3" width="22" height="5"/>
                                            <line x1="10" y1="12" x2="14" y2="12"/>
                                        </svg>
                                        Arsipkan Surat {tahun}
                                    </>
                                )}
                            </button>
                        </div>
                    </div>

                    {/* Keyframe CSS — inject sekali saat modal render */}
                    <style>{`
                        @keyframes archiveFadeIn  { from{opacity:0} to{opacity:1} }
                        @keyframes archiveSlideUp {
                            from { opacity:0; transform:translateY(10px) scale(0.98) }
                            to   { opacity:1; transform:translateY(0)    scale(1)    }
                        }
                        @keyframes archiveSpin { to { transform: rotate(360deg) } }
                    `}</style>
                </div>
            )}
        </>
    )
}