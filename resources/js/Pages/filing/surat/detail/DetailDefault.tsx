import { parseJenisPekerjaan } from '@/types/filing/parseJenisPekerjaan'

export default function DetailDefault({ surat }: { surat: any }) {
    const pekerjaanList = parseJenisPekerjaan(surat.jenis_pekerjaan)
    // Kumpulkan semua field yang tidak null/kosong secara otomatis
    const fields: Record<string, string> = {
        'Perihal':         surat.perihal,
        'Isi Surat':       surat.isi_surat,
        'Nama':            surat.nama,
        'Alamat':          surat.alamat,
        'No KTP':          surat.no_ktp,
        'Departemen':      surat.departemen,
        'Nominal':         surat.nominal,
        'Item Pembelian':  surat.item_pembelian,
        'Project':         surat.project,
        'Lokasi Kerja':    surat.lokasi_kerja,
        'Jenis Pekerjaan': surat.jenis_pekerjaan,
        'Waktu':           surat.waktu,
        'Jam Kerja':       surat.jam_kerja,
        'Jumlah Pekerja':  surat.jumlah_pekerja,
        'Merk':            surat.merk,
        'Warna':           surat.warna,
        'Rangka':          surat.rangka,
        'Lampiran':        surat.lampiran,
    }

    // Filter hanya yang ada nilainya
    const filledFields = Object.entries(fields).filter(([_, v]) => v)

    if (filledFields.length === 0) {
        return (
            <p className="text-sm text-gray-400 italic">
                Tidak ada detail tambahan untuk jenis surat ini.
            </p>
        )
    }

    return (
        <div className="space-y-1 text-sm">
            {filledFields.map(([label, value]) => (
                <p key={label}>
                    <strong>{label}:</strong> {value}
                </p>
            ))}
            {pekerjaanList.length > 0 && (
                <div>
                    <strong>Jenis Pekerjaan:</strong>
                    <ol className="list-decimal ml-5 mt-1 space-y-0.5">
                        {pekerjaanList.map((item, i) => (
                            <li key={i}>{item}</li>
                        ))}
                    </ol>
                </div>
            )}
        </div>
    )
}