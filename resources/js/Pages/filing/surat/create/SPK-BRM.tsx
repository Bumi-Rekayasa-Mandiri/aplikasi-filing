import AppLayout from "@/layouts/AppLayout"
import { Head, useForm, router } from "@inertiajs/react"

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

export default function CreateSPK({ surat }: Props) {
  const { data, setData, post, processing, errors } = useForm({
    judul: '',
    tanggal_surat: '',
    nama: '',
    jabatan_terakhir: '',
    departemen: '',
    isi_surat: '',
  })

  const submit = (e: React.FormEvent) => {
    e.preventDefault()
    post(route('filing.surat.generate-pdf', surat.id), {
      preserveScroll: true,
    })
  }


  const capForm = useForm<{ cap: File | null }>({
    cap: null,
  })

  const submitCap = (e: React.FormEvent) => {
    e.preventDefault()
    capForm.post(
      route('filing.surat.upload-cap', surat.id),
      { forceFormData: true }
    )
  }

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
    <AppLayout title="Buat Surat Pemberitahuan PHK">
      <Head title="Buat Surat Pemberitahuan PHK" />

      {/* =======================
          FORM DATA SURAT
      ======================= */}
      <form onSubmit={submit} className="space-y-4 max-w-xl">
        <input
          className="input input-bordered w-full"
          placeholder="Judul"
          onChange={e => setData('judul', e.target.value)}
        />
        {errors.judul && <div className="text-red-500">{errors.judul}</div>}

        <input
          type="date"
          className="input input-bordered w-full"
          onChange={e => setData('tanggal_surat', e.target.value)}
        />

        <input
          className="input input-bordered w-full"
          placeholder="Nama"
          onChange={e => setData('nama', e.target.value)}
        />

        <input
          className="input input-bordered w-full"
          placeholder="Jabatan Terakhir"
          onChange={e => setData('jabatan_terakhir', e.target.value)}
        />

        <input
          className="input input-bordered w-full"
          placeholder="Departemen"
          onChange={e => setData('departemen', e.target.value)}
        />

        <textarea
          className="textarea textarea-bordered w-full"
          placeholder="Isi Surat"
          onChange={e => setData('isi_surat', e.target.value)}
        />

        <button
          className="bg-green-700 px-4 py-2 rounded-full font-semibold text-white"
          disabled={processing}
        >
          Generate PDF
        </button>
      </form>

      <hr className="my-6" />

      {/* =======================
          FORM UPLOAD CAP
      ======================= */}
      <form onSubmit={submitCap} className="space-y-2 max-w-xl">
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

        <button
          className="bg-green-700 px-4 py-2 rounded-full font-semibold text-white"
          disabled={capForm.processing}
        >
          Upload Cap
        </button>
      </form>

      <hr className="my-6" />

      {/* =======================
          FORM UPLOAD TTD
      ======================= */}
      <form onSubmit={submitTtd} className="space-y-2 max-w-xl">
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

        <button
          className="bg-green-700 px-4 py-2 rounded-full font-semibold text-white"
          disabled={ttdForm.processing}
        >
          Tambah TTD
        </button>

        <div className="flex flex-wrap gap-4">
          {(surat.ttds ?? []).map((ttd) => (
            <div key={ttd.id} className="relative">
              <img src={ttd.url} className="w-32 border rounded" />
              <p>{ttd.nama}</p>
              <p className="text-xs text-gray-500">{ttd.jabatan}</p>

              <button
                type="button"
                className="bg-red-700 px-4 py-2 rounded-full font-semibold text-white"
                onClick={() => {
                  if (confirm('Hapus TTD ini?')) {
                    router.delete(
                      route('filing.surat.delete-ttd', ttd.id),
                      { preserveScroll: true }
                    )
                  }
                }}
              >
                Hapus
              </button>
            </div>
          ))}
        </div>
      </form>
    </AppLayout>
  )
}