export default function DetailSKP({ surat }: { surat: any }) {
    return (
        <div className="detail-body">
            <div className="detail-row">
                <span className="detail-key">Nama</span>
                <span className="detail-val">{surat.nama ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">Jabatan terakhir</span>
                <span className="detail-val">{surat.jabatan_terakhir ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">Departemen</span>
                <span className="detail-val">{surat.departemen ?? '—'}</span>
            </div>
        </div>
    )
}