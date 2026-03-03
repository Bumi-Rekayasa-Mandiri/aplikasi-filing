export default function DetailGRS({ surat }: { surat: any }) {
    return (
        <div className="space-y-1 text-sm">
            <p><strong>Jenis Project:</strong> {surat.isi_surat}</p>
            <p><strong>Project:</strong> {surat.project}</p>
            <p><strong>Masa Garansi:</strong> {surat.masa_garansi}</p>
            <p><strong>Material:</strong> {surat.material}</p>
            <p><strong>Alamat:</strong> {surat.alamat}</p>
        </div>
    )
}