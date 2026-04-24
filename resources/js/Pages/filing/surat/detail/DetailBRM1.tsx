export default function DetailBRM1({ surat }: { surat: any }) {
    return (
        <div className="detail-body">
            <div className="detail-row">
                <span className="detail-key">Lokasi kerja</span>
                <span className="detail-val">{surat.lokasi_kerja ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">Jenis pekerjaan</span>
                <span className="detail-val">{surat.jenis_pekerjaan ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">Waktu</span>
                <span className="detail-val">{surat.waktu ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">Jam kerja</span>
                <span className="detail-val">{surat.jam_kerja ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">Jumlah pekerja</span>
                <span className="detail-val">{surat.jumlah_pekerja ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">Departemen</span>
                <span className="detail-val">{surat.departemen ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">APD</span>
                <span className="detail-val">{surat.apd ?? '—'}</span>
            </div>
        </div>
    )
}