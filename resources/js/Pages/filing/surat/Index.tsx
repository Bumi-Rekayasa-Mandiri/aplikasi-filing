import { Head, router } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'
import SuratTable from '@/components/filing/surat/SuratTable'
import Pagination from '@/components/Pagination'
import { Surat } from '@/types/filing/surat'
import { Paginated } from '@/types/pagination'

const destroy = (id: number) => {
        if (confirm('Yakin ingin menghapus surat ini?')) {
          router.delete(`/filing/surat/${id}`)
        }
      }
      
interface Props {
  surat: Paginated<Surat>
}

export default function SuratIndex({ surat }: Props) {
  return (
    <AppLayout title="Manajemen Surat">
      <Head title="Surat" />


      <div className="flex justify-between mb-4">
        <h1 className="text-xl font-semibold">Daftar Surat</h1>
        <button
          className="btn btn-primary"
          onClick={() => router.visit('/filing/surat/create')}
        >
          Buat Surat
        </button>
      </div>
      
      <SuratTable data={surat.data} />

      <Pagination links={surat.links} />
    </AppLayout>
  )
}