import AuthenticatedLayout from '@/layouts/AuthenticatedLayout'
import { Head, useForm } from '@inertiajs/react'
import { FormEvent } from 'react'

export default function Create() {
  const { data, setData, post, processing, errors } = useForm({
    nomor_surat: '',
    judul: '',
    tujuan : '',
    jenis_surat : '',
    file: null as File | null,
  })

  const submit = (e: FormEvent) => {
    e.preventDefault()

    post(route('filing.arsip.store'), {
      forceFormData: true, // ⬅️ WAJIB UNTUK FILE
      onError: (e) =>
        console.error(e),
    })
  }

  return (
    <AuthenticatedLayout>
      <Head title="Upload Arsip" />

      <form onSubmit={submit} encType="multipart/form-data" className="space-y-4 max-w-xl">

        <div>
            <label className="label">Judul Arsip</label>
            <input
                type="text"
                className="input input-bordered w-full"
                value={data.judul}
                onChange={(e) => setData('judul', e.target.value)}
            />
            {errors.judul && (
                <div className="text-red-500 text-sm">
                {errors.judul}
                </div>
            )}
        </div>

        <div>
          <label className="label">Nomor Surat</label>
          <input
            type="text"
            className="input input-bordered w-full"
            value={data.nomor_surat}
            onChange={(e) => setData('nomor_surat', e.target.value)}
          />
          {errors.nomor_surat && (
            <div className="text-red-500 text-sm">
              {errors.nomor_surat}
            </div>
          )}
        </div>

        <div>
          <label className="label">Tujuan</label>
          <input
            type="text"
            className="input input-bordered w-full"
            value={data.tujuan}
            onChange={(e) => setData('tujuan', e.target.value)}
          />
          {errors.tujuan && (
            <div className="text-red-500 text-sm">
              {errors.tujuan}
            </div>
          )}

        </div>
        <div>
          <label className="label">Jenis Surat</label>
          <select
            className="select select-bordered w-full"
            value={data.jenis_surat}
            onChange={(e) => setData('jenis_surat', e.target.value)}
          >
            <option value="">Pilih Jenis Surat</option>
            <option value="Surat Pemberitahuan atau PHK">Surat Pemberitahuan atau PHK</option>
            <option value="Surat Pengajuan Garansi Material">Surat Pengajuan Garansi Material</option>
            <option value="Surat Pengembalian Dana">Surat Pengembalian Dana</option>
            <option value="Surat Garansi Pemasangan">Surat Garansi Pemasangan</option>
            <option value="Surat Pelepasan Hak">Surat Pelepasan Hak</option>
            <option value="Surat izin Kerja dan LK3">Surat izin Kerja dan LK3</option>
            <option value="Surat Permohonan Keringanan Denda">Surat Permohonan Keringanan Denda</option>
            <option value="Surat Lainnya">Surat Lainnya</option>
          </select>
        </div>

        <div>
          <label className="label">File PDF</label>
          <input
            type="file"
            name="file"
            accept=".pdf,.doc,.docx"
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