import { parseJenisPekerjaan } from '@/types/filing/parseJenisPekerjaan'

export default function DetailIEI({ surat }: { surat: any }) {
    const pekerjaanList = parseJenisPekerjaan(surat.jenis_pekerjaan)

    return (
        <div className="detail-body">
            <div className="detail-row">
                <span className="detail-key">Lokasi kerja</span>
                <span className="detail-val">{surat.lokasi_kerja ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">Project</span>
                <span className="detail-val">{surat.project ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">Masa garansi</span>
                <span className="detail-val">{surat.masa_garansi ?? '—'}</span>
            </div>

            {pekerjaanList.length > 0 && (
                <div className="detail-row detail-row--block">
                    <span className="detail-key">Jenis pekerjaan</span>
                    <ul className="pekerjaan-list">
                        {pekerjaanList.map((item, i) => (
                            <li key={i} className="pekerjaan-item">
                                <span className="pekerjaan-num">{i + 1}</span>
                                {item}
                            </li>
                        ))}
                    </ul>
                </div>
            )}
        </div>
    )
}