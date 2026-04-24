export default function DetailGRS({ surat }: { surat: any }) {
    return (
        <div className="detail-body">
            <div className="detail-row">
                <span className="detail-key">Jenis project</span>
                <span className="detail-val">{surat.isi_surat ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">Project</span>
                <span className="detail-val">{surat.project ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">Masa garansi</span>
                <span className="detail-val">{surat.masa_garansi ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">Material</span>
                <span className="detail-val">{surat.material ?? '—'}</span>
            </div>
            <div className="detail-row">
                <span className="detail-key">Alamat</span>
                <span className="detail-val">{surat.alamat ?? '—'}</span>
            </div>
        </div>
    )
}