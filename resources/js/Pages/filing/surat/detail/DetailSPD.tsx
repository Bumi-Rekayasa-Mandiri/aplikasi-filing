export default function DetailSPD({ surat }: { surat: any }) {
    return (
        <div className="detail-body">
            <div className="detail-row">
                <span className="detail-key">Alamat</span>
                <span className="detail-val">{surat.alamat ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">Item pembelian</span>
                <span className="detail-val">{surat.item_pembelian ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">Nominal</span>
                <span className="detail-val">{surat.nominal ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">Nomor rekening</span>
                <span className="detail-val">{surat.no_ktp ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">Nama bank</span>
                <span className="detail-val">{surat.nama ?? '—'}</span>
            </div>
        </div>
    )
}