import AppLayout from '@/layouts/AppLayout'
import { Head, router } from '@inertiajs/react'
import DetailRouter from '@/Pages/filing/surat/detail/DetailRouter'

type SuratDetail = {
  id: number
  jenis: string
  nomor_surat: string
  judul: string
  perihal: string
  tujuan: string
  tanggal_surat: string
  status: string
  lampiran?: string
  isi_surat?: string
  nama?: string
  alamat?: string
  no_ktp?: string
  jabatan?: string
  jabatan_terakhir?: string
  departemen?: string
  nominal?: string
  nominal_bagihasil?: string
  item_pembelian?: string
  project?: string
  lokasi_kerja?: string
  jenis_pekerjaan?: string
  waktu?: string
  jam_kerja?: string
  jumlah_pekerja?: string
  masa_garansi?: string
  material?: string
  hasil_denda?: string
  keringanan_denda?: string
  apd?: string
  periode?: string
  no_pekerja?: string
  merk?: string
  warna?: string
  rangka?: string
  cap_url?: string
  ttds?: {
    id: number
    nama: string
    jabatan: string
    label: string
    url: string
  }[]
}

type Props = {
  surat: SuratDetail
}

export default function Show({ surat }: Props) {
  return (
    <AppLayout title="Detail Surat">
      <Head title="Detail Surat" />

      <div className="space-y-6 max-w-3xl">

        {/* INFO UMUM */}
        <div className="bg-white border border-black rounded p-4 space-y-1">
          <h1 className="text-xl font-semibold">{surat.nomor_surat}</h1>
          <p><strong>Jenis:</strong> {surat.jenis}</p>
          <p><strong>Judul:</strong> {surat.judul}</p>
          <p><strong>Perihal:</strong> {surat.perihal}</p>
          <p><strong>Tujuan:</strong> {surat.tujuan}</p>
          <p><strong>Tanggal:</strong> {surat.tanggal_surat}</p>
          <p><strong>Status:</strong> {surat.status}</p>
        </div>

        {/* DETAIL SPESIFIK — dinamis per jenis */}
        <div className="bg-white border border-black rounded p-4">
          <h2 className="font-semibold mb-2">Detail Surat</h2>
          <DetailRouter surat={surat} />  {/* ← hanya satu */}
        </div>

        {/* CAP PERUSAHAAN — tampil saja, tidak ada form upload */}
        {surat.cap_url && (
          <div className="bg-white border border-black rounded p-4">
            <h2 className="font-semibold mb-2">Cap Perusahaan</h2>
            <img src={surat.cap_url} className="w-40 border rounded" />
          </div>
        )}

        {/* TANDA TANGAN — tampil saja, tidak ada form upload */}
        {surat.ttds && surat.ttds.length > 0 && (
          <div className="bg-white border border-black rounded p-4">
            <h2 className="font-semibold mb-2">Tanda Tangan</h2>
            <div className="flex flex-wrap gap-4">
              {surat.ttds.map(ttd => (
                <div key={ttd.id} className="border rounded p-2 text-center w-36">
                  {ttd.url && (
                    <img src={ttd.url} className="w-full border rounded mb-1" />
                  )}
                  <p className="text-sm font-semibold">{ttd.nama}</p>
                  {ttd.jabatan && (
                    <p className="text-xs text-gray-500">{ttd.jabatan}</p>
                  )}
                  {ttd.label && (
                    <p className="text-xs text-gray-400 italic">{ttd.label}</p>
                  )}
                </div>
              ))}
            </div>
          </div>
        )}

      </div>
    </AppLayout>
  )
}