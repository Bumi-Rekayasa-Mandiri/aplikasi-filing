// resources/js/components/filing/surat/ApprovedReadOnlyNotice.tsx
//
// Banner notice yang muncul di atas form Edit ketika halaman dibuka
// dengan status surat = approved. Berfungsi sebagai blok preventif
// (visual + accessibility) sehingga user tahu tidak bisa edit.

import { router } from '@inertiajs/react'
import { useState } from 'react'

interface Props {
    suratId: number
    /** Flag: apakah user punya permission untuk revert ke draft */
    canRevertDraft?: boolean
}

export default function ApprovedReadOnlyNotice({ suratId, canRevertDraft = false }: Props) {
    const [reverting, setReverting] = useState(false)

    const handleRevert = () => {
        if (!confirm('Yakin ingin revert surat ini ke status draft? Anda akan bisa mengedit setelahnya.')) {
            return
        }
        setReverting(true)
        router.patch(
            route('filing.surat.revert-draft', suratId),
            {},
            {
                onFinish:       () => setReverting(false),
                preserveScroll: false,
            }
        )
    }

    return (
        <div
            role="alert"
            style={{
                display:      'flex',
                alignItems:   'flex-start',
                gap:          '14px',
                padding:      '14px 18px',
                background:   '#fef2f2',
                border:       '1px solid #fecaca',
                borderRadius: '12px',
                marginBottom: '20px',
            }}
        >
            {/* Icon */}
            <div style={{
                flexShrink:     0,
                width:          '36px',
                height:         '36px',
                borderRadius:   '8px',
                background:     '#fee2e2',
                display:        'flex',
                alignItems:     'center',
                justifyContent: 'center',
                color:          '#dc2626',
                marginTop:      '2px',
            }}>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" strokeWidth="2"
                     strokeLinecap="round" strokeLinejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>

            {/* Body */}
            <div style={{ flex: 1, minWidth: 0 }}>
                <div style={{
                    display:      'flex',
                    alignItems:   'center',
                    gap:          '8px',
                    marginBottom: '4px',
                }}>
                    <h3 style={{
                        margin:     0,
                        fontSize:   '14px',
                        fontWeight: 600,
                        color:      '#991b1b',
                    }}>
                        Surat sudah Approved — mode hanya-baca
                    </h3>
                    <span style={{
                        fontSize:      '10px',
                        fontWeight:    700,
                        letterSpacing: '0.05em',
                        textTransform: 'uppercase',
                        padding:       '2px 7px',
                        background:    '#dc2626',
                        color:         '#ffffff',
                        borderRadius:  '999px',
                    }}>
                        Read-only
                    </span>
                </div>

                <p style={{
                    margin:     '0 0 12px',
                    fontSize:   '13px',
                    color:      '#7f1d1d',
                    lineHeight: 1.55,
                }}>
                    Surat dengan status <strong>approved</strong> tidak dapat diedit untuk menjaga
                    integritas dokumen yang sudah disetujui. Untuk mengubah surat ini, revert ke
                    status draft terlebih dahulu, lalu edit dan approve ulang.
                </p>

                {/* Action buttons */}
                <div style={{ display: 'flex', gap: '8px', flexWrap: 'wrap' }}>
                    {canRevertDraft && (
                        <button
                            onClick={handleRevert}
                            disabled={reverting}
                            style={{
                                display:      'inline-flex',
                                alignItems:   'center',
                                gap:          '6px',
                                padding:      '8px 14px',
                                fontSize:     '12px',
                                fontWeight:   600,
                                color:        '#ffffff',
                                background:   reverting ? '#9ca3af' : '#dc2626',
                                border:       'none',
                                borderRadius: '7px',
                                cursor:       reverting ? 'not-allowed' : 'pointer',
                                transition:   'background 0.15s',
                            }}
                            onMouseEnter={e => {
                                if (!reverting) e.currentTarget.style.background = '#b91c1c'
                            }}
                            onMouseLeave={e => {
                                if (!reverting) e.currentTarget.style.background = '#dc2626'
                            }}
                        >
                            {reverting ? (
                                <>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" strokeWidth="2.5"
                                         style={{ animation: 'noticeSpin 0.8s linear infinite' }}>
                                        <path d="M21 12a9 9 0 1 1-6.22-8.56"/>
                                    </svg>
                                    Memproses...
                                </>
                            ) : (
                                <>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" strokeWidth="2.5"
                                         strokeLinecap="round" strokeLinejoin="round">
                                        <polyline points="1 4 1 10 7 10"/>
                                        <path d="M3.51 15a9 9 0 1 0 .49-3.47"/>
                                    </svg>
                                    Revert ke Draft
                                </>
                            )}
                        </button>
                    )}

                    <button
                        onClick={() => router.visit(route('filing.surat.show', suratId))}
                        style={{
                            display:      'inline-flex',
                            alignItems:   'center',
                            gap:          '6px',
                            padding:      '8px 14px',
                            fontSize:     '12px',
                            fontWeight:   500,
                            color:        '#7f1d1d',
                            background:   '#ffffff',
                            border:       '1px solid #fecaca',
                            borderRadius: '7px',
                            cursor:       'pointer',
                            transition:   'background 0.15s',
                        }}
                        onMouseEnter={e => e.currentTarget.style.background = '#fef2f2'}
                        onMouseLeave={e => e.currentTarget.style.background = '#ffffff'}
                    >
                        Kembali ke Detail
                    </button>
                </div>
            </div>

            <style>{`
                @keyframes noticeSpin { to { transform: rotate(360deg) } }
            `}</style>
        </div>
    )
}