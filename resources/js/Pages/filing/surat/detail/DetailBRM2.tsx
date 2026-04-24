export default function DetailBRM2({ surat }: { surat: any }) {
    return (
        <div className="detail-body">
            <div className="detail-row">
                <span className="detail-key">Merk / jenis</span>
                <span className="detail-val">{surat.merk ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">Warna / tahun</span>
                <span className="detail-val">{surat.warna ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">Rangka / mesin</span>
                <span className="detail-val">{surat.rangka ?? '—'}</span>
            </div>
        </div>
    )
}