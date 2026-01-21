import AppLayout from '@/layouts/AppLayout'
import { Head, useForm } from '@inertiajs/react'
import UploadPdfForm from '@/Pages/filing/surat/UploadPdfForm'

type Props = {
  surat: {
    id: number
    nomor_surat: string
    judul: string
    tujuan: string
    status: string
    cap_url?: string
    ttds?: {
      id: number
      nama: string
      jabatan: string
      url: string
    }[]
  }
}

export default function Show({ surat }: Props) {

  /* =======================
     FORM UPLOAD CAP
  ======================= */
  const capForm = useForm<{
    cap: File | null
  }>({
    cap: null,
  })

  const submitCap = (e: React.FormEvent) => {
    e.preventDefault()

    capForm.post(
      route('filing.surat.upload-cap', surat.id),
      { forceFormData: true }
    )
  }

  /* =======================
     FORM UPLOAD TTD
  ======================= */
  const ttdForm = useForm<{
    nama_penandatangan: string
    jabatan: string
    ttd: File | null
  }>({
    nama_penandatangan: '',
    jabatan: '',
    ttd: null,
  })

  const submitTtd = (e: React.FormEvent) => {
    e.preventDefault()

    ttdForm.post(
      route('filing.surat.upload-ttd', surat.id),
      { forceFormData: true }
    )
  }

  return (
    <AppLayout title="Detail Surat">
      <Head title="Detail Surat" />

      <div className="space-y-6 max-w-3xl">

        {/* =======================
            INFO SURAT
        ======================= */}
        <div>
          <h1 className="text-xl font-semibold">{surat.nomor_surat}</h1>
          <p><strong>Judul:</strong> {surat.judul}</p>
          <p><strong>Tujuan:</strong> {surat.tujuan}</p>
          <p><strong>Status:</strong> {surat.status}</p>
        </div>

        <hr />

        {/* =======================
            UPLOAD PDF
        ======================= */}
        <UploadPdfForm surat={surat} />

        <hr />

        {/* =======================
            UPLOAD CAP
        ======================= */}
        <form onSubmit={submitCap} className="space-y-2">
          <h2 className="font-semibold">Cap Perusahaan</h2>

          {surat.cap_url && (
            <img src={surat.cap_url} className="w-40 border" />
          )}

          <input
            type="file"
            accept="image/png,image/jpeg"
            onChange={e =>
              capForm.setData('cap', e.target.files?.[0] ?? null)
            }
          />

          {capForm.errors.cap && (
            <div className="text-red-500 text-sm">
              {capForm.errors.cap}
            </div>
          )}

          <button className="btn btn-primary btn-sm" disabled={capForm.processing}>
            Upload Cap
          </button>
        </form>

        <hr />

        {/* =======================
            UPLOAD TTD
        ======================= */}
        <form onSubmit={submitTtd} className="space-y-2">
          <h2 className="font-semibold">Tanda Tangan</h2>

          <input
            className="input input-bordered w-full"
            placeholder="Nama Penandatangan"
            value={ttdForm.data.nama_penandatangan}
            onChange={e => ttdForm.setData('nama_penandatangan', e.target.value)}
          />

          <input
            className="input input-bordered w-full"
            placeholder="Jabatan"
            value={ttdForm.data.jabatan}
            onChange={e => ttdForm.setData('jabatan', e.target.value)}
          />

          <input
            type="file"
            accept="image/png,image/jpeg"
            onChange={e =>
              ttdForm.setData('ttd', e.target.files?.[0] ?? null)
            }
          />

          <button className="btn btn-primary btn-sm" disabled={ttdForm.processing}>
            Tambah TTD
          </button>
        </form>

        {/* =======================
            LIST TTD
        ======================= */}
        {surat.ttds && surat.ttds.length > 0 && (
          <div className="grid grid-cols-2 gap-4">
            {surat.ttds.map(ttd => (
              <div key={ttd.id} className="border p-2">
                <img src={ttd.url} className="w-full" />
                <p className="text-sm font-semibold">{ttd.nama}</p>
                <p className="text-xs text-gray-500">{ttd.jabatan}</p>
              </div>
            ))}
          </div>
        )}

          {surat.status === 'submitted' && user.isAdmin && (
            <div className="flex gap-2">
              <Button onClick={approve}>Approve</Button>
              <Button variant="danger" onClick={reject}>Reject</Button>
            </div>
          )}

      </div>

    </AppLayout>
  )
}