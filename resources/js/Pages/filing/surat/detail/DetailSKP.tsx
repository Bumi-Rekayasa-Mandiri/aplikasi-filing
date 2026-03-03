export default function DetailSKP({ surat }: { surat: any }) {
    return (
        <div className="space-y-1 text-sm">
            <p><strong>Nama:</strong> {surat.nama}</p>
            <p><strong>Jabatan Terakhir:</strong> {surat.jabatan_terakhir}</p>
            <p><strong>Departemen:</strong> {surat.departemen}</p>
        </div>
    )
}