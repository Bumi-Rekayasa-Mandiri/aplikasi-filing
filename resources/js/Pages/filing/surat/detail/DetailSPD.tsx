export default function DetailSPD({ surat }: { surat: any }) {
    return (
        <div className="space-y-1 text-sm">
            <p><strong>Alamat:</strong> {surat.alamat}</p>
            <p><strong>Item Pembelian:</strong> {surat.item_pembelian}</p>
            <p><strong>Nominal:</strong> {surat.nominal}</p>
            <p><strong>Nomor Rekening:</strong> {surat.no_ktp}</p>
            <p><strong>Nama Bank:</strong> {surat.nama}</p>
        </div>
    )
}