export default function DetailBRM1({ surat }: { surat: any }) {
    return (
        <div className="space-y-1 text-sm">
            <p><strong>Merk / Jenis:</strong> {surat.merk}</p>
            <p><strong>Warna / Tahun:</strong> {surat.warna}</p>
            <p><strong>Rangka / Mesin:</strong> {surat.rangka}</p>
        </div>
    )
}