import { Head, router, Link } from '@inertiajs/react'
import AppLayout from '@/layouts/AppLayout'
import SuratTable from '@/components/filing/surat/SuratTable'
import Pagination from '@/components/Pagination'
import { Surat } from '@/types/filing/surat'
import { Paginated } from '@/types/pagination'

interface Props {
  surat: Paginated<Surat>
}

export default function Index({ surat }: Props) {
  return (
    <AppLayout title="Manajemen Surat">
      <Head title="Surat" />

      <div className="flex justify-between mb-4">
        <h1 className="text-xl font-semibold">Daftar Surat</h1>

        <Link
          href={route('filing.surat.create')}
          className="btn btn-primary"
        >
          Buat Surat
        </Link>
      </div>

      <SuratTable data={surat.data} />

      <Pagination links={surat.links} />
    </AppLayout>
  )
}