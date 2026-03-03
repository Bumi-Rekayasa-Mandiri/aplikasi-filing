export const getPreviewRoute = (jenis: string, id: number): string => {
    // ✅ Lazy — route() hanya dipanggil untuk jenis yang cocok
    const map: Record<string, () => string> = {
        'SKP-BRM': () => route('filing.surat.previewSKP',  id),
        'SK-BRM' : () => route('filing.surat.previewSK',   id),
        'SPI-BRM': () => route('filing.surat.previewSPI',  id),
        'SPD-BRM': () => route('filing.surat.previewSPD',  id),
        'IEI-BRM': () => route('filing.surat.previewIEI',  id),
        'GRS-BRM': () => route('filing.surat.previewGRS',  id),
        'BRM-1'  : () => route('filing.surat.previewBRM1', id),
        'BRM-2'  : () => route('filing.surat.previewBRM2', id),
    }

    return map[jenis]?.() ?? '#'
}