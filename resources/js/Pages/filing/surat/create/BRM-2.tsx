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

  export default function CreateBRM2({ surat }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        judul: '',
        perihal: '',
        tujuan: '',
        tanggal_surat: '',
        isi_surat: '',
        merk: '',
        warna: '',
        rangka: '',

        cap: null as File | null,

        gambar_materai: null as File | null,

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
           route('filing.surat.generateBRM2-pdf', { surat: surat.id }),
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
      <AppLayout title="Buat Surat Pelepasan Hak">
        <Head title="Buat Surat Pelepasan Hak" />

        {/* =======================
            FORM DATA SURAT
        ======================= */}


        <span className="text-lg font-bold">Form Data Surat Pelepasan Hak</span>

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
            placeholder="Merk / Jenis"
            onChange={e => setData('merk', e.target.value)}
          />

          <input
            className="input input-bordered w-full"
            placeholder="Warna / Tahun"
            onChange={e => setData('warna', e.target.value)}
          />

          <input
            className="input input-bordered w-full"
            placeholder="Rangka / Mesin"
            onChange={e => setData('rangka', e.target.value)}
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

        {/* <hr className="my-6" />*/}

        {/* {/* =======================
            FORM UPLOAD CAP
        ======================= */}
        {/* <form onSubmit={submitCap} className="space-y-2 max-w-xl">
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
        </form> */}

        {/* <hr className="my-6" />

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
          </button> */}


          {/* <div className="flex flex-wrap gap-4">
            {(surat.ttds ?? []).map((ttd) => (
              <div key={ttd.id} className="relative">
                <img src={ttd.url} className="w-32 border rounded" />
                <p>{ttd.nama}</p>
                <p className="text-xs text-gray-500">{ttd.jabatan}</p> */}

                {/* <button
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
                </button> */}
              {/* </div> */}
            {/* ))} */}
          {/* </div> */}
        {/* </form> */}
      </AppLayout> 
    )
  }