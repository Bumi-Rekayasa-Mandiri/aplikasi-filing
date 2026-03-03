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

    type TtdItem = {
    nama_penandatangan: string
    jabatan: string
    urutan: number
    label: string
    file: File | null
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

        ttds: [
          { nama_penandatangan: '', jabatan: '', urutan: 1, label: 'Pihak Pertama', file: null },
          { nama_penandatangan: '', jabatan: '', urutan: 2, label: 'Pihak Kedua',   file: null },
        ] as TtdItem[],
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

      const addTtd = () => {
          setData('ttds', [
            ...data.ttds,
            { nama_penandatangan: '', jabatan: '', urutan: data.ttds.length + 1, label: `Pihak ${data.ttds.length + 1}`, file: null }
          ])
        }

      const removeTtd = (index: number) => {
          setData('ttds', data.ttds.filter((_, i) => i !== index))
        }

      const updateTtd = (index: number, field: keyof TtdItem, value: string | File | null) => {
          const updated = [...data.ttds]
          updated[index] = { ...updated[index], [field]: value }
          setData('ttds', updated)
        }

    return (
      <AppLayout title="Buat Surat Pelepasan Hak">
        <Head title="Buat Surat Pelepasan Hak" />

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

          {data.ttds.map((ttd, index) => (
            <div key={index} className="border p-4 rounded-lg space-y-2">
              <div className="flex justify-between items-center">
                <span className="font-semibold">Penandatangan {index + 1}</span>
                {data.ttds.length > 1 && (
                  <button
                    type="button"
                    className="text-red-500 text-sm"
                    onClick={() => removeTtd(index)}
                  >
                    Hapus
                  </button>
                )}
              </div>

              <input
                className="input input-bordered w-full"
                placeholder="Label (contoh: Pihak Pertama, Pihak Kedua, Hormat Kami)"
                value={ttd.label}
                onChange={e => updateTtd(index, 'label', e.target.value)}
              />

              <input
                className="input input-bordered w-full"
                placeholder="Nama Penandatangan"
                value={ttd.nama_penandatangan}
                onChange={e => updateTtd(index, 'nama_penandatangan', e.target.value)}
              />

              <input
                className="input input-bordered w-full"
                placeholder="Jabatan"
                value={ttd.jabatan}
                onChange={e => updateTtd(index, 'jabatan', e.target.value)}
              />

              <input
                type="file"
                accept="image/*"
                onChange={e => updateTtd(index, 'file', e.target.files?.[0] ?? null)}
              />
            </div>
          ))}

          <button
            type="button"
            className="border border-green-700 text-green-700 px-4 py-2 rounded-full text-sm"
            onClick={addTtd}
          >
            + Tambah Tanda Tangan
          </button>
          <br></br>

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