// resources/js/components/filing/surat/editors/FilePreviewSlot.tsx
//
// Reusable: tampilkan preview file lama (gambar) + tombol "Ganti file"
// Saat user klik Ganti, muncul input file. Saat user pilih file baru,
// preview-nya berubah ke file baru (tetap simpan di state induk).

import { useState } from 'react'

interface Props {
    /** URL file lama yang sudah tersimpan di server (bisa null jika belum ada) */
    existingUrl?: string | null
    /** File baru yang dipilih user (dari state induk) */
    newFile?: File | null
    /** Callback saat user pilih file baru atau hapus pilihan */
    onChange: (file: File | null) => void
    /** Label tombol/area, contoh: "Cap perusahaan", "Tanda tangan", "Foto KTP" */
    label?: string
    /** Hint format file */
    accept?: string
    /** Hint teks bawah */
    hint?: string
    /** Aspect ratio container preview, default: 'auto' */
    aspectRatio?: string
    /** Tinggi maksimum preview, default: 120px */
    maxHeight?: number
}

export default function FilePreviewSlot({
    existingUrl,
    newFile,
    onChange,
    label = 'File',
    accept = 'image/*',
    hint = 'PNG, JPG hingga 2MB',
    aspectRatio,
    maxHeight = 120,
}: Props) {
    const [showInput, setShowInput] = useState(false)

    // URL preview: prioritaskan file baru, fallback ke yang lama
    const newFileUrl = newFile ? URL.createObjectURL(newFile) : null
    const previewUrl = newFileUrl ?? existingUrl

    const hasFile = !!previewUrl

    // Mode: tampil preview existing dengan tombol Ganti
    if (hasFile && !showInput && !newFile) {
        return (
            <div style={{
                border:       '1px solid #e2e8f0',
                borderRadius: '10px',
                background:   '#f8fafc',
                padding:      '12px',
            }}>
                <div style={{
                    fontSize:     '11px',
                    fontWeight:   600,
                    color:        '#64748b',
                    textTransform:'uppercase',
                    letterSpacing:'0.04em',
                    marginBottom: '8px',
                }}>
                    {label} (tersimpan)
                </div>

                <div style={{
                    display:        'flex',
                    alignItems:     'center',
                    justifyContent: 'center',
                    background:     '#ffffff',
                    border:         '1px dashed #cbd5e1',
                    borderRadius:   '8px',
                    padding:        '8px',
                    aspectRatio,
                    maxHeight:      `${maxHeight}px`,
                    overflow:       'hidden',
                }}>
                    <img
                        src={previewUrl!}
                        alt={label}
                        style={{
                            maxHeight: `${maxHeight - 16}px`,
                            maxWidth:  '100%',
                            objectFit: 'contain',
                        }}
                    />
                </div>

                <button
                    type="button"
                    onClick={() => setShowInput(true)}
                    style={{
                        display:        'flex',
                        alignItems:     'center',
                        justifyContent: 'center',
                        gap:            '6px',
                        width:          '100%',
                        marginTop:      '8px',
                        padding:        '7px 12px',
                        fontSize:       '12px',
                        fontWeight:     500,
                        color:          '#185FA5',
                        background:     '#ffffff',
                        border:         '1px solid #bfdbfe',
                        borderRadius:   '7px',
                        cursor:         'pointer',
                    }}
                >
                    <svg width="12" height="12" viewBox="0 0 16 16" fill="none">
                        <path d="M2 13l3-3 5 5M2 13V3a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1Z"
                              stroke="currentColor" strokeWidth="1.3"/>
                        <circle cx="11" cy="6" r="1.5" stroke="currentColor" strokeWidth="1.3"/>
                    </svg>
                    Ganti file
                </button>
            </div>
        )
    }

    // Mode: input file baru (atau preview file baru yang sudah dipilih)
    return (
        <div>
            <label style={{
                display:        'flex',
                alignItems:     'center',
                gap:            '12px',
                padding:        '12px',
                background:     '#f8fafc',
                border:         '1px dashed #cbd5e1',
                borderRadius:   '10px',
                cursor:         'pointer',
                transition:     'border-color 0.15s, background 0.15s',
            }}
            onMouseEnter={e => {
                e.currentTarget.style.borderColor = '#185FA5'
                e.currentTarget.style.background  = '#eff6ff'
            }}
            onMouseLeave={e => {
                e.currentTarget.style.borderColor = '#cbd5e1'
                e.currentTarget.style.background  = '#f8fafc'
            }}>
                <input
                    type="file"
                    accept={accept}
                    style={{
                        position: 'absolute',
                        width:    '1px',
                        height:   '1px',
                        opacity:  0,
                        overflow: 'hidden',
                    }}
                    onChange={e => onChange(e.target.files?.[0] ?? null)}
                />

                {newFile ? (
                    <>
                        {/* Preview file baru */}
                        <img
                            src={newFileUrl!}
                            alt="preview"
                            style={{
                                width:        '48px',
                                height:       '48px',
                                objectFit:    'contain',
                                background:   '#ffffff',
                                border:       '1px solid #e2e8f0',
                                borderRadius: '6px',
                                flexShrink:   0,
                            }}
                        />
                        <div style={{ flex: 1, minWidth: 0 }}>
                            <div style={{
                                fontSize:    '13px',
                                fontWeight:  500,
                                color:       '#0f172a',
                                whiteSpace:  'nowrap',
                                overflow:    'hidden',
                                textOverflow:'ellipsis',
                            }}>
                                {newFile.name}
                            </div>
                            <div style={{ fontSize: '11px', color: '#16a34a' }}>
                                File baru terpilih • klik untuk ganti
                            </div>
                        </div>
                    </>
                ) : (
                    <>
                        <div style={{
                            display:        'flex',
                            alignItems:     'center',
                            justifyContent: 'center',
                            width:          '36px',
                            height:         '36px',
                            background:     '#eff6ff',
                            borderRadius:   '8px',
                            flexShrink:     0,
                        }}>
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <path d="M8 11V5M5.5 7.5L8 5l2.5 2.5"
                                      stroke="#185FA5" strokeWidth="1.3"
                                      strokeLinecap="round" strokeLinejoin="round"/>
                                <path d="M3 13h10" stroke="#185FA5" strokeWidth="1.3" strokeLinecap="round"/>
                            </svg>
                        </div>
                        <div>
                            <div style={{ fontSize: '13px', fontWeight: 500, color: '#0f172a' }}>
                                Unggah {label.toLowerCase()}
                            </div>
                            <div style={{ fontSize: '11px', color: '#94a3b8' }}>
                                {hint}
                            </div>
                        </div>
                    </>
                )}
            </label>

            {/* Tombol batal — kembali ke preview file lama */}
            {existingUrl && (showInput || newFile) && (
                <button
                    type="button"
                    onClick={() => { onChange(null); setShowInput(false) }}
                    style={{
                        marginTop:    '6px',
                        padding:      '5px 12px',
                        fontSize:     '11px',
                        color:        '#64748b',
                        background:   'transparent',
                        border:       'none',
                        cursor:       'pointer',
                        textDecoration:'underline',
                    }}
                >
                    Batal — pakai file lama
                </button>
            )}
        </div>
    )
}