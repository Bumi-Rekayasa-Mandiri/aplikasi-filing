// resources/js/components/filing/surat/ApprovedErrorModal.tsx
//
// Modal popup yang muncul saat user submit edit form tapi backend menolak
// karena status surat sudah approved (misal: race condition — surat
// di-approve oleh admin lain setelah user buka halaman edit).

import { useEffect, useState } from 'react'

interface Props {
    /** Pesan error dari backend (errors.status) */
    error: string | null
    /** Callback dipanggil saat user klik Mengerti */
    onDismiss: () => void
    /** Optional: redirect URL setelah dismiss (misal ke Show page) */
    redirectUrl?: string
}

export default function ApprovedErrorModal({ error, onDismiss, redirectUrl }: Props) {
    const [open, setOpen] = useState(false)

    useEffect(() => {
        if (error) setOpen(true)
    }, [error])

    useEffect(() => {
        document.body.style.overflow = open ? 'hidden' : ''
        return () => { document.body.style.overflow = '' }
    }, [open])

    useEffect(() => {
        if (!open) return
        const onKey = (e: KeyboardEvent) => { if (e.key === 'Escape') handleDismiss() }
        window.addEventListener('keydown', onKey)
        return () => window.removeEventListener('keydown', onKey)
    }, [open])

    const handleDismiss = () => {
        setOpen(false)
        onDismiss()
        if (redirectUrl) {
            setTimeout(() => { window.location.href = redirectUrl }, 200)
        }
    }

    if (!open || !error) return null

    return (
        <div
            role="dialog"
            aria-modal="true"
            aria-labelledby="approved-error-title"
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
                animation:      'approvedFadeIn 0.15s ease',
            }}
        >
            <div style={{
                width:        '100%',
                maxWidth:     '440px',
                background:   '#ffffff',
                borderRadius: '16px',
                boxShadow:    '0 24px 64px rgba(0,0,0,0.20), 0 4px 16px rgba(0,0,0,0.08)',
                overflow:     'hidden',
                animation:    'approvedSlideUp 0.18s ease',
            }}>
                {/* Header */}
                <div style={{
                    display:      'flex',
                    alignItems:   'flex-start',
                    gap:          '14px',
                    padding:      '24px 24px 20px',
                    borderBottom: '1px solid #f1f5f9',
                }}>
                    <div style={{
                        flexShrink:     0,
                        width:          '44px',
                        height:         '44px',
                        borderRadius:   '10px',
                        background:     '#fee2e2',
                        border:         '1px solid #fecaca',
                        display:        'flex',
                        alignItems:     'center',
                        justifyContent: 'center',
                        color:          '#dc2626',
                    }}>
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" strokeWidth="2"
                             strokeLinecap="round" strokeLinejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8"  x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                    </div>
                    <div style={{ flex: 1 }}>
                        <h3 id="approved-error-title" style={{
                            margin:     0,
                            fontSize:   '16px',
                            fontWeight: 600,
                            color:      '#0f172a',
                            lineHeight: 1.3,
                        }}>
                            Surat tidak dapat diubah
                        </h3>
                        <p style={{
                            margin:     '6px 0 0',
                            fontSize:   '13px',
                            color:      '#64748b',
                            lineHeight: 1.55,
                        }}>
                            {error}
                        </p>
                    </div>
                </div>

                {/* Info box — instruksi langkah selanjutnya */}
                <div style={{ padding: '20px 24px' }}>
                    <div style={{
                        display:      'flex',
                        alignItems:   'flex-start',
                        gap:          '10px',
                        padding:      '12px 14px',
                        background:   '#f8fafc',
                        border:       '1px solid #e2e8f0',
                        borderRadius: '8px',
                        fontSize:     '13px',
                        color:        '#475569',
                        lineHeight:   1.55,
                    }}>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                             stroke="#64748b" strokeWidth="2"
                             strokeLinecap="round" strokeLinejoin="round"
                             style={{ flexShrink: 0, marginTop: '2px' }}>
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="16" x2="12" y2="12"/>
                            <line x1="12" y1="8"  x2="12.01" y2="8"/>
                        </svg>
                        <span>
                            Untuk mengubah surat ini, lakukan <strong>Revert ke Draft</strong> terlebih
                            dahulu di halaman detail, lalu edit dan approve ulang.
                        </span>
                    </div>
                </div>

                {/* Footer */}
                <div style={{
                    display:        'flex',
                    justifyContent: 'flex-end',
                    padding:        '0 24px 24px',
                }}>
                    <button
                        onClick={handleDismiss}
                        style={{
                            padding:      '10px 22px',
                            fontSize:     '13px',
                            fontWeight:   600,
                            color:        '#ffffff',
                            background:   '#dc2626',
                            border:       'none',
                            borderRadius: '8px',
                            cursor:       'pointer',
                            transition:   'background 0.15s',
                        }}
                        onMouseEnter={e => e.currentTarget.style.background = '#b91c1c'}
                        onMouseLeave={e => e.currentTarget.style.background = '#dc2626'}
                    >
                        Mengerti
                    </button>
                </div>
            </div>

            <style>{`
                @keyframes approvedFadeIn  { from{opacity:0} to{opacity:1} }
                @keyframes approvedSlideUp {
                    from { opacity:0; transform:translateY(12px) scale(0.98) }
                    to   { opacity:1; transform:translateY(0)    scale(1)    }
                }
            `}</style>
        </div>
    )
}