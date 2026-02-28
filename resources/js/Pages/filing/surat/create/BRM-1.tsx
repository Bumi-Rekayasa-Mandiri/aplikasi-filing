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
    urutan: number
    label: string
    file: File | null
  }

  export default function CreateBRM1({ surat }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        judul: '',
        perihal: '',
        tujuan: '',
        tanggal_surat: '',
        nama: '',
        lokasi_kerja: '',
        jenis_pekerjaan: '',
        waktu: '',
        jam_kerja:'',
        jumlah_pekerja:'',
        departemen: '',
        apd: '' ,
        periode: '' ,
        no_pekerja: '',

        cap: null as File | null,

         ttds: [
          { nama_penandatangan: '', urutan: 1, label: 'Pihak Pertama', file: null },
          { nama_penandatangan: '', urutan: 2, label: 'Pihak Kedua',   file: null },
        ] as TtdItem[],
      })

      {Object.keys(errors).length > 0 && (
        <div className="bg-red-100 border border-red-400 text-red-700 p-4 rounded mb-4">
          {Object.entries(errors).map(([key, msg]) => (
            <p key={key}><strong>{key}:</strong> {msg}</p>
          ))}
        </div>
      )}

       const submit = (e: React.FormEvent) => {
         e.preventDefault()

         if (!surat?.id) {
           console.error('SURAT ID MISSING', surat)
           return
         }

         post(
           route('filing.surat.generateBRM1-pdf', { surat: surat.id }),
            { 
             preserveScroll: true, 
             forceFormData: true
            }
         )
       }

      const addTtd = () => {
          setData('ttds', [
            ...data.ttds,
            { nama_penandatangan: '', urutan: data.ttds.length + 1, label: `Pihak ${data.ttds.length + 1}`, file: null }
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
      <AppLayout title="Buat Surat Izin Kerja dan LK3">
        <Head title="Buat Surat Izin Kerja dan LK3" />

        {/* =======================
            FORM DATA SURAT
        ======================= */}

        <span className="text-lg font-bold">Form Data Surat Izin Kerja dan LK3</span>

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
            placeholder="Nama Tujuan"
            onChange={e => setData('nama', e.target.value)}
          />

          <input
            className="input input-bordered w-full"
            placeholder="Departemen"
            onChange={e => setData('departemen', e.target.value)}
          />

          <input
            className="input input-bordered w-full"
            placeholder="Lokasi Kerja"
            onChange={e => setData('lokasi_kerja', e.target.value)}
          />

          <input
            className="input input-bordered w-full"
            placeholder="Jenis Pekerjaan"
            onChange={e => setData('jenis_pekerjaan', e.target.value)}
          />

          <input
            className="input input-bordered w-full"
            placeholder="Waktu"
            onChange={e => setData('waktu', e.target.value)}
          />

          <input
            className="input input-bordered w-full"
            placeholder="Jam Kerja"
            onChange={e => setData('jam_kerja', e.target.value)}
          />

          <input
            className="input input-bordered w-full"
            placeholder="Jumlah Pekerja"
            onChange={e => setData('jumlah_pekerja', e.target.value)}
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

          <span className="text-lg font-bold mt-10">Lampiran</span>
          
          <input
            className="input input-bordered w-full"
            placeholder="APD"
            onChange={e => setData('apd', e.target.value)}
          />
          
          <input
            className="input input-bordered w-full"
            placeholder="Periode"
            onChange={e => setData('periode', e.target.value)}
          />

          <input
            className="input input-bordered w-full"
            placeholder="No Pekerja"
            onChange={e => setData('no_pekerja', e.target.value)}
          />

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