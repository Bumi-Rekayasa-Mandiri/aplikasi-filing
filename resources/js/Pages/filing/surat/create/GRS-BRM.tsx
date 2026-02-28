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

  export default function CreateGRS({ surat }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        judul: '',
        perihal: '',
        tujuan: '',
        tanggal_surat: '',
        isi_surat:'',
        project: '',
        material: '',
        alamat: '',
        masa_garansi: '',

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
           route('filing.surat.generateGRS-pdf', { surat: surat.id }),
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

    return (
      <AppLayout title="Buat Surat Pengajuan Garansi Material">
        <Head title="Buat Surat Pengajuan Garansi Material" />

        {/* =======================
            FORM DATA SURAT
        ======================= */}


        <span className="text-lg font-bold">Form Data Surat Pengajuan Garansi Material</span>

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

          <textarea
            className="textarea textarea-bordered w-full"
            placeholder="Jenis Project"
            onChange={e => setData('isi_surat', e.target.value)}
          />

          <input
            className="input input-bordered w-full"
            placeholder="Project"
            onChange={e => setData('project', e.target.value)}
          />

          <input
            className="input input-bordered w-full"
            placeholder="Material"
            onChange={e => setData('material', e.target.value)}
          />

          <textarea
            className="textarea textarea-bordered w-full"
            placeholder="Alamat"
            onChange={e => setData('alamat', e.target.value)}
          />

          <input
            className="input input-bordered w-full"
            placeholder="Masa Garansi"
            onChange={e => setData('masa_garansi', e.target.value)}
          />

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