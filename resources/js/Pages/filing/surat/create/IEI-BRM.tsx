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

  type JenisPekerjaanItem = {
  deskripsi: string
}

  export default function CreateIEI({ surat }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        judul: '',
        perihal: '',
        tujuan: '',
        tanggal_surat: '',
        project: '',
        lokasi_kerja: '',
        jenis_pekerjaan: [{ deskripsi: '' }] as JenisPekerjaanItem[],
        isi_surat: '',
        lampiran: '',

        cap: null as File | null,

        nama_penandatangan: '',
        jabatan: '',
        ttd: null as File | null,
      })

       const submit = (e: React.FormEvent) => {
         e.preventDefault()

         if (!surat?.id) {
           console.error('SURAT ID MISSING', surat)
           return
         }

         post(
           route('filing.surat.generateIEI-pdf', { surat: surat.id }),
            { 
             preserveScroll: true, 
             forceFormData: true
            }
         )
       }

      if (!surat?.id) {
        return (
          <AppLayout title="Loading">
            <p className="text-gray-500">Menyiapkan draft surat…</p>
          </AppLayout>
        )
      }

      const addJenisPekerjaan = () => {
        setData('jenis_pekerjaan', [...data.jenis_pekerjaan, { deskripsi: '' }])
      }

      const removeProject = (index: number) => {
        setData('jenis_pekerjaan', data.jenis_pekerjaan.filter((_, i) => i !== index))
      }

      const updateProject = (index: number, value: string) => {
        const updated = [...data.jenis_pekerjaan]
        updated[index] = { deskripsi: value }
        setData('jenis_pekerjaan', updated)
      }

    return (
      <AppLayout title="Buat Surat Garansi Pekerjaan">
        <Head title="Buat Surat Garansi Pekerjaan" />

        {/* =======================
            FORM DATA SURAT
        ======================= */}


        <span className="text-lg font-bold">Form Data Surat Garansi Pekerjaan</span>

        <br />
        <br />
        <form onSubmit={submit} className="space-y-4 max-w-xl">
          <input
            className="input input-bordered w-full"
            placeholder="Judul"
            onChange={e => setData('judul', e.target.value)}
          />
          {errors.judul && <div className="text-red-500">{errors.judul}</div>}

          <input
            className="input input-bordered w-full"
            placeholder="Perihal"
            onChange={e => setData('perihal', e.target.value)}
          />
          {errors.perihal && <div className="text-red-500">{errors.perihal}</div>}

          <input
            className="input input-bordered w-full"
            placeholder="Tujuan"
            onChange={e => setData('tujuan', e.target.value)}
          />
          {errors.tujuan && <div className="text-red-500">{errors.tujuan}</div>}

          <input
            type="date"
            className="input input-bordered w-full"
            onChange={e => setData('tanggal_surat', e.target.value)}
          />

          <input
            className="input input-bordered w-full"
            placeholder="Project"
            onChange={e => setData('project', e.target.value)}
          />

          <input
            className="input input-bordered w-full"
            placeholder="Lokasi"
            onChange={e => setData('lokasi_kerja', e.target.value)}
          />

          <input
            className="textarea textarea-bordered w-full"
            placeholder="Masa Garansi (Tanggal Mulai) s/d (Tanggal Berakhir)"
            onChange={e => setData('isi_surat', e.target.value)}
          />

          {/* Ganti input project lama dengan ini */}
          <span className="text-sm font-semibold">Daftar Pekerjaan (maks. 6)</span>

          {data.jenis_pekerjaan.map((jenis_pekerjaan, index) => (
            <div key={index} className="flex items-center gap-2">
              <span className="text-sm text-gray-500 w-5 shrink-0">{index + 1}.</span>
              <input
                className="input input-bordered w-full"
                placeholder={`Pekerjaan ${index + 1}`}
                value={jenis_pekerjaan.deskripsi}
                onChange={e => updateProject(index, e.target.value)}
              />
              {data.jenis_pekerjaan.length > 1 && (
                <button
                  type="button"
                  className="text-red-500 text-sm shrink-0"
                  onClick={() => removeProject(index)}
                >
                  ✕
                </button>
              )}
            </div>
          ))}

          {data.jenis_pekerjaan.length < 6 && (
            <button
              type="button"
              className="border border-green-700 text-green-700 px-4 py-2 rounded-full text-sm"
              onClick={addJenisPekerjaan}
            >
              + Tambah Pekerjaan
            </button>
          )}

          <br />
          <br />

          <span className="text-lg font-bold mt-10">Cap Perusahaan</span>
          <br />
          <input
            type="file"
            placeholder="Cap Perusahaan"
            onChange={e =>
              setData('cap', e.target.files?.[0] ?? null)
            }
          />

          <br />
          <br />

          <span className="text-lg font-bold mt-10">Tanda Tangan</span>
          <br />

          <input
            className="input input-bordered w-full"
            placeholder="Nama Penandatangan"
            onChange={e => setData('nama_penandatangan', e.target.value)}
          />

          <input
            className="input input-bordered w-full"
            placeholder="Jabatan"
            onChange={e => setData('jabatan', e.target.value)}
          />

          <input
            type="file"
            onChange={e =>
              setData('ttd', e.target.files?.[0] ?? null)
            }
          />
          <br />

          <button
            className="bg-green-700 px-4 py-2 rounded-full font-semibold text-white"
            disabled={processing}
          >
            Generate PDF
          </button>
        </form>
      </AppLayout> 
    )
  }