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

  export default function CreateSPD({ surat }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        judul: '',
        perihal: '',
        lampiran:'',
        tujuan: '',
        tanggal_surat: '',
        nama: '',
        alamat: '',
        item_pembelian: '',
        nominal: '',
        isi_surat: '',
        no_ktp:'',

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
           route('filing.surat.generateSPD-pdf', { surat: surat.id }),
            { 
             preserveScroll: true, 
             forceFormData: true
            }
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

      if (!surat?.id) {
        return (
          <AppLayout title="Loading">
            <p className="text-gray-500">Menyiapkan draft surat…</p>
          </AppLayout>
        )
      }

    return (
      <AppLayout title="Buat Surat Pengembalian Dana">
        <Head title="Buat Surat Pengembalian Dana" />

        {/* =======================
            FORM DATA SURAT
        ======================= */}


        <span className="text-lg font-bold">Form Data Surat Pengembalian Dana</span>

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
            placeholder="Lampiran"
            onChange={e => setData('lampiran', e.target.value)}
          />
          {errors.perihal && <div className="text-red-500">{errors.lampiran}</div>}

          <input
            type="date"
            className="input input-bordered w-full"
            onChange={e => setData('tanggal_surat', e.target.value)}
          />

          <input
            className="input input-bordered w-full"
            placeholder="Hal"
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
            className="input input-bordered w-full"
            placeholder="Alamat"
            onChange={e => setData('alamat', e.target.value)}
          />
          {errors.tujuan && <div className="text-red-500">{errors.alamat}</div>}
          
          <input
            className="input input-bordered w-full"
            placeholder="Item Pembelian"
            onChange={e => setData('item_pembelian', e.target.value)}
          />
          {errors.tujuan && <div className="text-red-500">{errors.item_pembelian}</div>}

          <input
            className="input input-bordered w-full"
            placeholder="Nominal"
            onChange={e => setData('nominal', e.target.value)}
          />
          <input
            className="input input-bordered w-full"
            placeholder="Nama Bank"
            onChange={e => setData('nama', e.target.value)}
          />

          <textarea
            className="textarea textarea-bordered w-full"
            placeholder="Cabang"
            onChange={e => setData('isi_surat', e.target.value)}
          />

          <input
            className="input input-bordered w-full"
            placeholder="Nomor Rekening"
            onChange={e => setData('no_ktp', e.target.value)}
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