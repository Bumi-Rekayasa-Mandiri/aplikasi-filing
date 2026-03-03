// resources/js/types/filing/parseJenisPekerjaan.ts

export const parseJenisPekerjaan = (raw: string | string[] | null | undefined): string[] => {
    if (!raw) return []

    if (Array.isArray(raw)) {
        return raw.filter(Boolean)
    }

    if (typeof raw === 'string') {
        try {
            const parsed = JSON.parse(raw)
            if (Array.isArray(parsed)) return parsed.filter(Boolean)
        } catch {
            return [raw]
        }
    }

    return []
}