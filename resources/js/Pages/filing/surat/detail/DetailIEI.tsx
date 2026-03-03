import { parseJenisPekerjaan } from '@/types/filing/parseJenisPekerjaan'

export default function DetailIEI({ surat }: { surat: any }) {

    const pekerjaanList = parseJenisPekerjaan(surat.jenis_pekerjaan)
    return (
        <div className="space-y-1 text-sm">
            <p><strong>Lokasi Kerja:</strong> {surat.lokasi_kerja}</p>
            <p><strong>Project:</strong> {surat.project}</p>
            <p><strong>Masa Garansi:</strong> {surat.masa_garansi}</p>

            {pekerjaanList.length > 0 && (
                <div>
                    <strong>Jenis Pekerjaan:</strong>
                    <ol className="list-decimal ml-5 mt-1 space-y-0.5">
                        {pekerjaanList.map((item, i) => (
                            <li key={i}>{item}</li>
                        ))}
                    </ol>
                </div>
            )}
        </div>
    )
}