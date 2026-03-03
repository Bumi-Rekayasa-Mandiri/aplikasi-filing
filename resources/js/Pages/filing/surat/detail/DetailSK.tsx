export default function DetailSK({ surat }: { surat: any }) {
    return (
        <div className="space-y-1 text-sm">
            <p><strong>Hasil Perhitungan Denda:</strong> {surat.hasil_denda}</p>
            <p><strong>Keringanan Denda:</strong> {surat.keringanan_denda}</p>
        </div>
    )
}