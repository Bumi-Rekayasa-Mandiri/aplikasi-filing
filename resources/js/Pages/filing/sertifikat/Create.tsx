import AuthenticatedLayout from '@/layouts/AuthenticatedLayout'
import { Head, useForm } from '@inertiajs/react'
import { FormEvent } from 'react'

export default function Create() {
  const { data, setData, post, processing, errors } = useForm({
    nama_sertifikat: '',
    nomor_sertifikat: '',
    instansi: '',
    jenis_sertifikat: '',
    file: null as File | null,
  })

  const submit = (e: FormEvent) => {
    e.preventDefault()

    post(route('filing.sertifikat.store'), {
      forceFormData: true, // ⬅️ WAJIB UNTUK FILE
      onError: (e) =>
        console.error(e),
    })
  }

  return (
    <AuthenticatedLayout>
      <Head title="Upload Sertifikat" />
      <form onSubmit={submit} encType="multipart/form-data" className="space-y-4 max-w-xl">

        <div>
            <label className="label">Nama Sertifikat</label>
            <input
                type="text"
                className="input input-bordered w-full"
                value={data.nama_sertifikat}
                onChange={(e) => setData('nama_sertifikat', e.target.value)}
            />
            {errors.nama_sertifikat && (
                <div className="text-red-500 text-sm">
                {errors.nama_sertifikat}
                </div>
            )}
        </div>

        <div>
          <label className="label">Nomor Sertifikat</label>
          <input
            type="text"
            className="input input-bordered w-full"
            value={data.nomor_sertifikat}
            onChange={(e) => setData('nomor_sertifikat', e.target.value)}
          />
          {errors.nomor_sertifikat && (
            <div className="text-red-500 text-sm">
              {errors.nomor_sertifikat}
            </div>
          )}
        </div>

        <div>
          <label className="label">Instansi</label>
          <input
            type="text"
            className="input input-bordered w-full"
            value={data.instansi}
            onChange={(e) => setData('instansi', e.target.value)}
          />
          {errors.instansi && (
            <div className="text-red-500 text-sm">
              {errors.instansi}
            </div>
          )}
        </div>

        <div>
          <label className="label">Jenis Sertifikat</label>
          <input
            type="text"
            className="input input-bordered w-full"
            value={data.jenis_sertifikat}
            onChange={(e) => setData('jenis_sertifikat', e.target.value)}
          />
          {errors.jenis_sertifikat && (
            <div className="text-red-500 text-sm">
              {errors.jenis_sertifikat}
            </div>
          )}
        </div>

        <div>
          <label className="label">File</label>
          <input
            type="file"
            name="file"
            accept=".pdf,.jpg,.jpeg,.png"
            className="file-input file-input-bordered w-full"
            onChange={(e) =>
              setData('file', e.target.files?.[0] ?? null)
            }
          />
          {errors.file && (
            <div className="text-red-500 text-sm">
              {errors.file}
            </div>
          )}
        </div>

        <button
          type="submit"
          className="btn btn-primary"
          disabled={processing}
        >
          Upload
        </button>
      </form>
    </AuthenticatedLayout>
  )
}