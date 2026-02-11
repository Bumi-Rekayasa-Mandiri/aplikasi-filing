import AuthenticatedLayout from '@/layouts/AuthenticatedLayout'
import { Link } from '@inertiajs/react'
import { ArsipSertifikat } from '@/types/filing/sertifikat'
import Pagination from '@/components/Pagination'
import { Head, router, usePage } from '@inertiajs/react'
import { useEffect, useState } from 'react'
import { AppPageProps } from '@/types/filing/inertia'
import SortIcon from '@/components/SortIcon'

interface Sertifikat {
  id: number
  nama_sertifikat: string
  nomor_sertifikat:string
  jenis_sertifikat: string
  instansi: string
  file_url: string | null
  file_name: string | null
}

interface PageProps {
  sertifikat: {
    data: Sertifikat[]
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
  const { sertifikat, filters, sort, direction } =
    usePage<PageProps>().props

  const [search, setSearch] = useState(filters.search ?? '')

  /**
   * SEARCH (debounce)
   */
  useEffect(() => {
    const timeout = setTimeout(() => {
      router.get(
        route('filing.sertifikat.index'),
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
      route('filing.sertifikat.index'),
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
    if (!confirm('Apakah Anda yakin ingin menghapus sertifikat ini?')) 
      return router.delete(route('filing.sertifikat.destroy', id), {
        preserveScroll: true,
      })
    }

  const { flash } = usePage<AppPageProps>().props

    return (
        <AuthenticatedLayout
      header={<div className="inline-flex justify-between items-center">
        <h2 className="text-xl font-semibold">Sertifikat</h2>
            </div>
              }
              >
            <Head title="Arsip Sertifikat" />
            
            <div className="flex flex-col items-end gap-2">
                    <Link href={route('filing.sertifikat.create')}
                        className="bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-green-700">
                            Upload Sertifikat
                    </Link>
                            <input
                                type="text"
                                className="input input-bordered w-64"
                                placeholder="Cari nama / nomor sertifikat..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                            />
            </div>

        {/* TABLE */}
          <table className="table table-zebra w-full text sm mt-4">
            <thead className="bg-green-700 text-white border-b border-black-300">
              <tr>
                    
                <th className="px-3 py-2 font-semibold"
                  onClick={() => toggleSort('nama_sertifikat')}
                >
                    <span className="inline-flex items-center gap-1">
                        Nama Sertifikat {sortIcon('nama_sertifikat')}
                    </span>
                </th>

                <th className="px-3 py-2 font-semibold"
                  onClick={() => toggleSort('nomor_sertifikat')}
                >
                    <span className="inline-flex items-center gap-1">
                        Nomor Sertifikat{sortIcon('nomor_sertifikat')}
                    </span>
                </th>
                
                <th className="px-3 py-2 font-semibold"
                  onClick={() => toggleSort('jenis_sertifikat')}
                >
                    <span className="inline-flex items-center gap-1">
                        Jenis Sertifikat{sortIcon('jenis_sertifikat')}
                    </span>
                </th>

                <th className="px-3 py-2 font-semibold"
                  onClick={() => toggleSort('instansi')}
                >
                    <span className="inline-flex items-center gap-1">
                        Instansi{sortIcon('instansi')}
                    </span>
                </th>

                <th className="px-4 py-3 text-center w-[100px] font-semibold">
                  Aksi
                </th>
              </tr>
            </thead>

            <tbody>
              {sertifikat.data.length === 0 && (
                <tr>
                  <td colSpan={5} className="text-center py-6 text-gray-500">
                    Data sertifikat tidak ditemukan
                  </td>
                </tr>
              )}

              {sertifikat.data.map((item) => (
                <tr key={item.id} className="hover:bg-gray-50">

                  <td className="px-3 py-2 text-center">
                    {item.nama_sertifikat}
                  </td>

                  <td className="px-3 py-2 text-center">
                    {item.nomor_sertifikat}
                  </td>

                  <td className="px-3 py-2 text-center">
                    {item.jenis_sertifikat}
                  </td>
                  
                  <td className="px-3 py-2 text-center">
                    {item.instansi}
                  </td>

                  {/* KOLOM AKSI */}
                  <td className="px-3 py-2 text-center">
                    <div className="flex justify-center gap-2">
                      {item.file_url && (
                        <a
                          href={route('filing.sertifikat.download', item.id)}
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
                          if (confirm('Yakin ingin menghapus sertifikat ini?')) {
                            router.delete(
                              route('filing.sertifikat.destroy', item.id),
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
        <Pagination links={sertifikat.links} />

        </AuthenticatedLayout>
    )
}