import { useForm, Head, router } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'

type Props = {
  surat: {
    id: number
    judul: string
    perihal: string
    tujuan: string
    isi_surat: string
    kode_jenis: string
    tanggal_surat: string
  }
  kodeJenis: Record<string, string>
}

export default function Edit({ surat, kodeJenis }: Props) {
  const { data, setData, put, processing, errors } = useForm({
    judul: surat.judul,
    tanggal_surat: surat.tanggal_surat,
    perihal: surat.perihal,
    tujuan: surat.tujuan,
    kode_jenis: surat.kode_jenis,
    isi_surat: surat.isi_surat,
  })

  const submit = (e: React.FormEvent) => {
    e.preventDefault()
    router.put(`/filing/surat/${surat.id}`, data)
  }

  return (
    <AppLayout title="Edit Surat">
      <Head title="Edit Surat" />

      <form onSubmit={submit} className="space-y-4 max-w-xl">
        <input
          className="input input-bordered w-full"
          value={data.judul}
          onChange={e => setData('judul', e.target.value)}
        />
        {errors.judul && <div className="text-red-500">{errors.judul}</div>}

        <input
          type="date"
          className="input input-bordered w-full"
          value={data.tanggal_surat}
          onChange={e => setData('tanggal_surat', e.target.value)}
        />

        <input
          className="input input-bordered w-full"
          value={data.perihal}
          onChange={e => setData('perihal', e.target.value)}
        />

        <input
          className="input input-bordered w-full"
          value={data.tujuan}
          onChange={e => setData('tujuan', e.target.value)}
        />

        <select
          className="select select-bordered w-full"
          value={data.kode_jenis}
          onChange={e => setData('kode_jenis', e.target.value)}
        >
          {Object.entries(kodeJenis).map(([key, label]) => (
            <option key={key} value={key}>{label}</option>
          ))}
        </select>

        <textarea
          className="textarea textarea-bordered w-full"
          value={data.isi_surat}
          onChange={e => setData('isi_surat', e.target.value)}
        />

        <button className="btn btn-primary" disabled={processing}>
          Update
        </button>
      </form>
    </AppLayout>
  )
}