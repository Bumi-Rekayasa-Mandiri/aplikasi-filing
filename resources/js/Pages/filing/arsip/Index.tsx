import AuthenticatedLayout from '@/layouts/AuthenticatedLayout'
import { Link } from '@inertiajs/react'
import { ArsipSurat } from '@/types/filing/arsip'
import Pagination from '@/components/Pagination'
import { Head, router, usePage } from '@inertiajs/react'
import { useEffect, useState } from 'react'
import { AppPageProps } from '@/types/filing/inertia'
import SortIcon from '@/components/SortIcon'

interface Arsip {
  id: number
  nomor_surat: string
  judul: string
  tujuan: string
  jenis_surat: string
  file_url: string | null
  file_name: string | null
}

interface PageProps {
  arsip: {
    data: Arsip[]
    links: any[]
  }
  filters: {
    search?: string
  }
  sort: string
  direction: 'asc' | 'desc'
  [key: string]: unknown
}

export default function Index() {
  const { arsip, filters, sort, direction } =
    usePage<PageProps>().props

  const [search, setSearch] = useState(filters.search ?? '')

  /**
   * SEARCH (debounce)
   */
  useEffect(() => {
    const timeout = setTimeout(() => {
      router.get(
        route('filing.arsip.index'),
        {
          search,
          sortField: sort,
          sortDirection: direction,
        },
        {
          preserveState: true,
          replace: true,
        }
      )
    }, 400)

    return () => clearTimeout(timeout)
  }, [search])

  /**
   * SORT
   */
  const toggleSort = (field: string) => {
    const newDirection =
      sort === field && direction === 'asc' ? 'desc' : 'asc'

    router.get(
      route('filing.arsip.index'),
      {
        search,
        sortField: field,
        sortDirection: newDirection,
      },
      {
        preserveState: true,
        preserveScroll: true,
        replace: true,
      }
    )
  }

  const sortIcon = (field: string) => {
    if (sort !== field) return null
    return direction === 'asc' ? ' ▲' : ' ▼'
  }

  const handleDelete = (id: number) => {
    if (!confirm('Apakah Anda yakin ingin menghapus arsip ini?')) 
      return router.delete(route('filing.arsip.destroy', id), {
        preserveScroll: true,
      })
    }

  const { flash } = usePage<AppPageProps>().props

    return (
        <AuthenticatedLayout
      header={<div className="inline-flex justify-between items-center">
        <h2 className="text-xl font-semibold">Arsip Surat</h2>
            </div>
              }
              >
            <Head title="Arsip Surat" />

            
            <div className="flex flex-col items-end gap-2">
                    <Link href={route('filing.arsip.create')}
                        className="bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-green-700">
                            Upload Arsip
                    </Link>
                            <input
                                type="text"
                                className="input input-bordered w-64"
                                placeholder="Cari judul / nomor surat..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                            />
            </div>

        {/* TABLE */}
          <table className="table table-zebra w-full text sm mt-4">
            <thead className="bg-green-700 border-black-100 text-white">
              <tr>
                <th
                  className="px-4 py-3 text-center w-[180px] font-semibold"
                  onClick={() => toggleSort('nomor_surat')}
                >
                    <span className="inline-flex items-center gap-1">
                        Nomor{sortIcon('nomor_surat')}
                    </span>
                </th>
                    
                <th className="px-3 py-2 font-semibold"
                  onClick={() => toggleSort('judul')}
                >
                    <span className="inline-flex items-center gap-1">
                        Judul{sortIcon('judul')}
                    </span>
                </th>
                    
                <th
                  className="px-4 py-3 text-center w-[140px] font-semibold"
                  onClick={() => toggleSort('jenis_surat')}
                >
                    <span className="inline-flex items-center gap-1">
                        Jenis Surat{sortIcon('jenis_surat')}
                    </span>
                </th>

                <th
                  className="px-3 py-2 text-center font-semibold"
                  onClick={() => toggleSort('tujuan')}
                >
                    <span className="inline-flex items-center gap-1">
                        Tujuan{sortIcon('tujuan')}
                    </span>
                </th>
                
                <th className="px-4 py-3 text-center w-[140px] font-semibold">
                  Aksi
                </th>
              </tr>
            </thead>

            <tbody className="bg-gray-100">
              {arsip.data.length === 0 && (
                <tr>
                  <td colSpan={5} className="text-center py-6 text-gray-500 border">
                    Data arsip tidak ditemukan
                  </td>
                </tr>
              )}

              {arsip.data.map((item) => (
                <tr key={item.id} className="hover:bg-gray-50 border-b border-gray-200">
                  <td className="px-4 py-3 text-center whitespace-nowrap">
                    {item.nomor_surat}
                  </td>

                  <td className="px-4 py-3 text-center">
                    {item.judul}
                  </td>

                  <td className="px-4 py-3 text-center">
                    {item.jenis_surat || '-'}
                  </td>

                  <td className="px-4 py-3 text-center capitalize">
                    {item.tujuan || '-'}
                  </td>

                  {/* KOLOM AKSI */}
                  <td className="px-4 py-3 text-center text-sm">
                    <div className="flex justify-center gap-2">
                      {item.file_url && (
                        <a
                          href={route('filing.arsip.show', item.id)}
                          target="_blank"
                          rel="noopener noreferrer"
                          className="btn btn-sm bg-green-700 px-4 py-2 rounded-full text-white font-semibold"
                        >
                          Preview
                        </a>
                      )}

                      {item.file_url && (
                        <a
                          href={route('filing.arsip.download', item.id)}
                          target="_blank"
                          rel="noopener noreferrer"
                          className="btn btn-sm bg-green-700 px-4 py-2 rounded-full text-white font-semibold"
                        >
                          Download
                        </a>
                      )}

                     <button
                        className="btn btn-sm bg-red-600 px-4 py-2 rounded-full text-white font-semibold"
                        onClick={() => {
                          if (confirm('Yakin ingin menghapus arsip ini?')) {
                            router.delete(
                              route('filing.arsip.destroy', item.id),
                              {
                                preserveScroll: true,
                              }
                            )
                          }
                        }}
                      >
                        {flash?.success && (
                          <div className="alert alert-success mb-4">
                            {flash.success}
                          </div>
                        )}
                        Hapus
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>

        {/* PAGINATION */}
        <Pagination links={arsip.links} />

        </AuthenticatedLayout>
    )
}