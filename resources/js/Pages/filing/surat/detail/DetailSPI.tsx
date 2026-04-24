export default function DetailSPI({ surat }: { surat: any }) {
    return (
        <div className="detail-body">
            <div className="detail-row">
                <span className="detail-key">Nama investor</span>
                <span className="detail-val">{surat.nama ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">Alamat</span>
                <span className="detail-val">{surat.alamat ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">No KTP</span>
                <span className="detail-val">{surat.no_ktp ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">Nominal investasi</span>
                <span className="detail-val">{surat.nominal ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">Nominal bagi hasil</span>
                <span className="detail-val">{surat.nominal_bagihasil ?? '—'}</span>
            </div>
        </div>
    )
}