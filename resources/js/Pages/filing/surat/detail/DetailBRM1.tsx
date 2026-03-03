export default function DetailBRM1({ surat }: { surat: any }) {
    return (
        <div className="space-y-1 text-sm">
            <p><strong>Lokasi Kerja:</strong> {surat.lokasi_kerja}</p>
            <p><strong>Jenis Pekerjaan:</strong> {surat.jenis_pekerjaan}</p>
            <p><strong>Waktu:</strong> {surat.waktu}</p>
            <p><strong>Jam Kerja:</strong> {surat.jam_kerja}</p>
            <p><strong>Jumlah Pekerja:</strong> {surat.jumlah_pekerja}</p>
            <p><strong>Departemen:</strong> {surat.departemen}</p>
            <p><strong>APD:</strong> {surat.apd}</p>
        </div>
    )
}