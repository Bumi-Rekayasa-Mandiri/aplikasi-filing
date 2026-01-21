import { useForm, Head } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'

type Props = {kodeJenis: Record<string, string>}

export default function Create({kodeJenis}: Props) {
  const { data, setData, post, processing, errors } = useForm({
    judul: '',
    tanggal_surat: '',
    perihal: '',
    tujuan: '',
    kode_jenis: '',
    isi_surat: '',
  })

  const submit = (e: React.FormEvent) => {
    e.preventDefault()
    post('/filing/surat')
  }

  return (
    <AppLayout title="Buat Surat">
      <Head title="Buat Surat" />

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
          placeholder="Perihal"
          onChange={e => setData('perihal', e.target.value)}
        />

        <input
          className="input input-bordered w-full"
          placeholder="Tujuan"
          onChange={e => setData('tujuan', e.target.value)}
        />

        <select
            className="select select-bordered w-full"
            onChange={e => setData('kode_jenis', e.target.value)}
            >
                <option value="">Pilih Jenis Surat</option>
                
                {Object.entries(kodeJenis).map(([key, label]) => (
                    <option key={key} value={key}>{label}</option>
                    ))}
        </select>

        <textarea
          className="textarea textarea-bordered w-full"
          placeholder="Isi Surat"
          onChange={e => setData('isi_surat', e.target.value)}
        />

        <button
          className="btn btn-primary"
          disabled={processing}
        >
          Simpan
        </button>
      </form>
    </AppLayout>
  )
}