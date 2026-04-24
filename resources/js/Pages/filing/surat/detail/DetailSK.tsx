export default function DetailSK({ surat }: { surat: any }) {
    return (
        <div className="detail-body">
            <div className="detail-row">
                <span className="detail-key">Hasil perhitungan denda</span>
                <span className="detail-val">{surat.hasil_denda ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">Keringanan denda</span>
                <span className="detail-val">{surat.keringanan_denda ?? '—'}</span>
            </div>
        </div>
    )
}