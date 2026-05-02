// resources/js/Components/filing/RestoreButton.tsx
import { router } from '@inertiajs/react'
import { useState } from 'react'

interface Props {
    arsipId:    number
    judul:      string
    canRestore: boolean // false jika surat_id null (arsip lama tanpa FK)
}

export default function RestoreButton({ arsipId, judul, canRestore }: Props) {
    const [confirming, setConfirming] = useState(false)
    const [loading, setLoading]       = useState(false)

    // Arsip lama tanpa FK ke surat asli — tidak bisa di-restore otomatis
    if (!canRestore) {
        return (
            <span
                title="Arsip ini tidak memiliki referensi ke surat asli"
                style={{
                    display:      'inline-flex',
                    alignItems:   'center',
                    padding:      '4px 10px',
                    fontSize:     '12px',
                    color:        '#cbd5e1',
                    background:   '#f8fafc',
                    border:       '1px solid #e2e8f0',
                    borderRadius: '6px',
                    cursor:       'not-allowed',
                    userSelect:   'none',
                }}
            >
                —
            </span>
        )
    }

    const handleRestore = () => {
        setLoading(true)
        router.delete(route('filing.arsip.restore', arsipId), {
            onFinish:       () => { setLoading(false); setConfirming(false) },
            preserveScroll: true,
        })
    }

    // ── State: konfirmasi inline ────────────────────────────
    if (confirming) {
        return (
            <>
                <span style={{
                    display:    'inline-flex',
                    alignItems: 'center',
                    gap:        '6px',
                    flexWrap:   'nowrap',
                }}>
                    <span style={{
                        fontSize:   '12px',
                        color:      '#475569',
                        whiteSpace: 'nowrap',
                    }}>
                        Restore?
                    </span>

                    {/* Tombol Ya */}
                    <button
                        onClick={handleRestore}
                        disabled={loading}
                        style={{
                            display:      'inline-flex',
                            alignItems:   'center',
                            gap:          '4px',
                            padding:      '4px 10px',
                            fontSize:     '12px',
                            fontWeight:   600,
                            color:        '#ffffff',
                            background:   loading ? '#93c5fd' : '#3b82f6',
                            border:       'none',
                            borderRadius: '6px',
                            cursor:       loading ? 'not-allowed' : 'pointer',
                            whiteSpace:   'nowrap',
                            lineHeight:   '1',
                            transition:   'background 0.15s',
                        }}
                        onMouseEnter={e => {
                            if (!loading) (e.currentTarget).style.background = '#2563eb'
                        }}
                        onMouseLeave={e => {
                            if (!loading) (e.currentTarget).style.background = '#3b82f6'
                        }}
                    >
                        {loading ? (
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" strokeWidth="2.5"
                                 style={{ animation: 'restoreSpin 0.8s linear infinite' }}>
                                <path d="M21 12a9 9 0 1 1-6.22-8.56"/>
                            </svg>
                        ) : (
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" strokeWidth="2.5"
                                 strokeLinecap="round" strokeLinejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        )}
                        {loading ? '...' : 'Ya'}
                    </button>

                    {/* Tombol Batal */}
                    <button
                        onClick={() => setConfirming(false)}
                        disabled={loading}
                        style={{
                            padding:      '4px 10px',
                            fontSize:     '12px',
                            fontWeight:   500,
                            color:        '#64748b',
                            background:   '#f1f5f9',
                            border:       '1px solid #e2e8f0',
                            borderRadius: '6px',
                            cursor:       loading ? 'not-allowed' : 'pointer',
                            opacity:      loading ? 0.5 : 1,
                            whiteSpace:   'nowrap',
                            lineHeight:   '1',
                        }}
                    >
                        Batal
                    </button>
                </span>

                <style>{`
                    @keyframes restoreSpin { to { transform: rotate(360deg) } }
                `}</style>
            </>
        )
    }

    // ── State: tombol restore default ───────────────────────
    return (
        <button
            onClick={() => setConfirming(true)}
            title={`Restore "${judul}" ke manajemen surat`}
            style={{
                display:      'inline-flex',
                alignItems:   'center',
                gap:          '5px',
                padding:      '4px 10px',
                fontSize:     '12px',
                fontWeight:   500,
                color:        '#2563eb',
                background:   '#eff6ff',
                border:       '1px solid #bfdbfe',
                borderRadius: '6px',
                cursor:       'pointer',
                whiteSpace:   'nowrap',
                lineHeight:   '1',
                transition:   'all 0.15s',
            }}
            onMouseEnter={e => {
                const b = e.currentTarget
                b.style.background  = '#dbeafe'
                b.style.borderColor = '#93c5fd'
                b.style.color       = '#1d4ed8'
            }}
            onMouseLeave={e => {
                const b = e.currentTarget
                b.style.background  = '#eff6ff'
                b.style.borderColor = '#bfdbfe'
                b.style.color       = '#2563eb'
            }}
        >
            {/* Undo/restore icon */}
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" strokeWidth="2.5"
                 strokeLinecap="round" strokeLinejoin="round">
                <polyline points="1 4 1 10 7 10"/>
                <path d="M3.51 15a9 9 0 1 0 .49-3.47"/>
            </svg>
            Restore
        </button>
    )
}