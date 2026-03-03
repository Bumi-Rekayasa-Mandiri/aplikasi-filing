export default function DetailSPI({ surat }: { surat: any }) {
    return (
        <div className="space-y-1 text-sm">
            <p><strong>Nama Investor:</strong> {surat.nama}</p>
            <p><strong>Alamat:</strong> {surat.alamat}</p>
            <p><strong>No KTP:</strong> {surat.no_ktp}</p>
            <p><strong>Nominal Investasi:</strong> {surat.nominal}</p>
            <p><strong>Nominal Bagi Hasil:</strong> {surat.nominal_bagihasil}</p>
        </div>
    )
}