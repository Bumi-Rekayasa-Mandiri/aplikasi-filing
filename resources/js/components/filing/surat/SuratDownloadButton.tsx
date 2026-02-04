import { Surat } from '@/types/filing/surat'
import { Link } from '@inertiajs/react'

interface Props {
    surat: Surat
}

export default function SuratDownloadButton({surat}: Props)
{
    if (!surat.has_file) return null

    return (
        <Link href={route('filing.surat.download', surat.id)}
        className="btn btn-xs btn-outline" target="_blank" rel="noopener norefferer">
            Download PDF
        </Link>
    )
}