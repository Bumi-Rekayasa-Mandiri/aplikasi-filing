import { parseJenisPekerjaan } from '@/types/filing/parseJenisPekerjaan'

export default function DetailDefault({ surat }: { surat: any }) {
    const pekerjaanList = parseJenisPekerjaan(surat.jenis_pekerjaan)

    const fields: Record<string, string> = {
        'Perihal':         surat.perihal,
        'Isi surat':       surat.isi_surat,
        'Nama':            surat.nama,
        'Alamat':          surat.alamat,
        'No KTP':          surat.no_ktp,
        'Departemen':      surat.departemen,
        'Nominal':         surat.nominal,
        'Item pembelian':  surat.item_pembelian,
        'Project':         surat.project,
        'Lokasi kerja':    surat.lokasi_kerja,
        'Waktu':           surat.waktu,
        'Jam kerja':       surat.jam_kerja,
        'Jumlah pekerja':  surat.jumlah_pekerja,
        'Merk':            surat.merk,
        'Warna':           surat.warna,
        'Rangka':          surat.rangka,
        'Lampiran':        surat.lampiran,
    }

    // Hapus 'Jenis Pekerjaan' dari fields biasa karena dirender terpisah sebagai list
    const filledFields = Object.entries(fields).filter(([_, v]) => v)

    if (filledFields.length === 0 && pekerjaanList.length === 0) {
        return (
            <p className="detail-empty">
                Tidak ada detail tambahan untuk jenis surat ini.
            </p>
        )
    }

    return (
        <div className="detail-body">
            {filledFields.map(([label, value]) => (
                <div key={label} className="detail-row">
                    <span className="detail-key">{label}</span>
                    <span className="detail-val">{value}</span>
                </div>
            ))}

            {pekerjaanList.length > 0 && (
                <div className="detail-row detail-row--block">
                    <span className="detail-key">Jenis pekerjaan</span>
                    <ul className="pekerjaan-list">
                        {pekerjaanList.map((item, i) => (
                            <li key={i} className="pekerjaan-item">
                                <span className="pekerjaan-num">{i + 1}</span>
                                {item}
                            </li>
                        ))}
                    </ul>
                </div>
            )}
        </div>
    )
}