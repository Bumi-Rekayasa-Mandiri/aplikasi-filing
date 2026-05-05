interface Props {
    lastRegeneratedAt: string | null
    /** Variant: "info" untuk Show page (sekedar info), "warning" untuk Edit (peringatan akan re-generate) */
    variant?: 'info' | 'warning'
}
 
/**
 * Banner timestamp file regenerate.
 *
 * - variant="info"    → muncul di Show page sebagai transparency
 *                       ("File ini di-build: 5 menit yang lalu")
 * - variant="warning" → muncul di Edit page sebagai peringatan
 *                       ("Menyimpan akan men-regenerate ulang PDF & DOCX")
 */
export default function RegeneratedBanner({
    lastRegeneratedAt,
    variant = 'info',
}: Props) {
    const formatRelative = (iso: string | null): string => {
        if (!iso) return 'Belum pernah'
        try {
            const date = new Date(iso)
            const diff = (Date.now() - date.getTime()) / 1000 // detik
 
            if (diff < 60)         return 'Baru saja'
            if (diff < 3600)       return `${Math.floor(diff / 60)} menit yang lalu`
            if (diff < 86400)      return `${Math.floor(diff / 3600)} jam yang lalu`
            if (diff < 86400 * 7)  return `${Math.floor(diff / 86400)} hari yang lalu`
 
            return date.toLocaleDateString('id-ID', {
                day:   'numeric',
                month: 'long',
                year:  'numeric',
            })
        } catch {
            return '—'
        }
    }
 
    const formatExact = (iso: string | null): string => {
        if (!iso) return ''
        try {
            return new Date(iso).toLocaleString('id-ID', {
                day:    'numeric',
                month:  'long',
                year:   'numeric',
                hour:   '2-digit',
                minute: '2-digit',
            })
        } catch {
            return ''
        }
    }
 
    // ── Style mapping per variant ─────────────────────────────────────────
    const styles = variant === 'warning'
        ? {
            background:   '#fffbeb',
            border:       '1px solid #fde68a',
            color:        '#92400e',
            iconColor:    '#d97706',
        }
        : {
            background:   '#eff6ff',
            border:       '1px solid #bfdbfe',
            color:        '#1e40af',
            iconColor:    '#2563eb',
        }
 
    return (
        <div style={{
            display:      'flex',
            alignItems:   'flex-start',
            gap:          '10px',
            padding:      '10px 14px',
            background:   styles.background,
            border:       styles.border,
            borderRadius: '10px',
            fontSize:     '13px',
            color:        styles.color,
            lineHeight:   1.5,
            marginBottom: '16px',
        }}>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke={styles.iconColor} strokeWidth="2"
                 strokeLinecap="round" strokeLinejoin="round"
                 style={{ flexShrink: 0, marginTop: '1px' }}>
                {variant === 'warning' ? (
                    <>
                        <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/>
                        <line x1="12" y1="9"  x2="12" y2="13"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </>
                ) : (
                    <>
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </>
                )}
            </svg>
 
            <div style={{ flex: 1 }}>
                {variant === 'warning' ? (
                    <>
                        <div style={{ fontWeight: 600, marginBottom: '2px' }}>
                            File akan di-regenerate ulang
                        </div>
                        <div style={{ fontSize: '12px' }}>
                            Saat Anda menyimpan perubahan, PDF & DOCX akan di-build ulang dengan data baru.
                            {lastRegeneratedAt && (
                                <>
                                    {' '}File saat ini di-build: <strong>{formatRelative(lastRegeneratedAt)}</strong>
                                    <span style={{ opacity: 0.7, marginLeft: '4px' }}>
                                        ({formatExact(lastRegeneratedAt)})
                                    </span>
                                </>
                            )}
                        </div>
                    </>
                ) : (
                    <>
                        <span style={{ fontWeight: 600 }}>File terakhir di-build: </span>
                        <span>{formatRelative(lastRegeneratedAt)}</span>
                        {lastRegeneratedAt && (
                            <span style={{ opacity: 0.7, marginLeft: '6px', fontSize: '12px' }}>
                                ({formatExact(lastRegeneratedAt)})
                            </span>
                        )}
                    </>
                )}
            </div>
        </div>
    )
}