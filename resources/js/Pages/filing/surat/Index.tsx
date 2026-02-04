import AuthenticatedLayout from '@/layouts/AuthenticatedLayout'
import SuratTable from '@/components/filing/surat/SuratTable'
import Pagination from '@/components/Pagination'
import { Head, router, usePage } from '@inertiajs/react'
import { useEffect, useState } from 'react'
import { Surat } from '@/types/filing/surat'
import { AppPageProps } from '@/types/filing/inertia'
import SortIcon from '@/components/SortIcon'
import { Link } from '@inertiajs/react'
import Dropdown from '@/components/Dropdown'

interface PageProps {
  surat: {
    data: Surat[]
    links: any[]
  }
  sort: string
  direction: 'asc' | 'desc'
  filters: {
    search?: string
    status?: string
  }
}

export default function Index() {
  const { surat, sort, direction, filters } =
    usePage<AppPageProps & PageProps & {sort: string
      direction: 'asc' | 'desc'
    }>().props

  const [search, setSearch] = useState(filters.search ?? '')
  const [status, setStatus] = useState(filters.status ?? '')

  const { url } = usePage()

  const getCurrentPage = () => {
    const params = new URLSearchParams(url.split('?')[1])
    return params.get('page') ?? '1'
  }

  useEffect(() => {
    const timeout = setTimeout(() => {
      router.get(
        route('filing.surat.index'),
        {
          search,
          status,
          sortField: sort,
          sortDirection: direction,
          page: getCurrentPage(),
        },
        {
          preserveState: true,
          replace: true,
        }
      )
    }, 400)

    return () => clearTimeout(timeout)
  }, [search, status])

  const toggleSort = (field: string) => {
    const sortDirection = field === sort && direction === 'asc' ? 'desc' : 'asc'
    router.get(
      route('filing.surat.index'),
      {
        search,
        status,
        sortField: field,
        sortDirection,
        page: getCurrentPage(),
      },
      {
        preserveScroll: true,
        preserveState: true,
        replace: true,
      }
    )
  }

  return (
    <AuthenticatedLayout
      header={<div className="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 className="text-xl font-semibold">Surat</h2>
        <Dropdown
                trigger={
                  <button className="bg-green-700 px-4 py-2 rounded-full font-semibold text-white">
                        + Buat Surat
                  </button>
                }
            >
                <Dropdown.Link href={route('filing.surat.create', 'SPK-BRM')}>
                   Surat Pemberitahuan PHK
                </Dropdown.Link>
                <Dropdown.Link href={route('filing.surat.create', 'GRS-BRM')}>
                   Surat Garansi Material
                </Dropdown.Link>
                <Dropdown.Link href={route('filing.surat.create', 'SPD-BRM')}>
                   Surat Pengembalian Dana
                </Dropdown.Link>
                <Dropdown.Link href={route('filing.surat.create', 'SPI-BRM')}>
                   Surat Permohonan Investasi
                </Dropdown.Link>
                <Dropdown.Link href={route('filing.surat.create', 'IEI-BRM')}>
                   Surat Garansi Pekerjaan
                </Dropdown.Link>
                <Dropdown.Link href={route('filing.surat.create', 'SK-BRM')}>
                   Surat Permohonan Keringanan Denda
                </Dropdown.Link>
                <Dropdown.Link href={route('filing.surat.create', 'BRM1')}>
                   Surat Pelepasan Hak
                </Dropdown.Link>
                <Dropdown.Link href={route('filing.surat.create', 'BRM2')}>
                   Surat Izin Kerja dan LK3
                </Dropdown.Link>
              </Dropdown>
            </div>
              }
              >
      <Head title="Surat" />
      

      {/* FILTER BAR */}
      <div className="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <input
              type="text"
              className="input input-bordered w-full sm:max-w-xs"
              placeholder="Cari judul / nomor surat..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
            />

                  <select
                    value={status}
                    onChange={(e) => setStatus(e.target.value)} className="border rounded-lg px-3 py-2 text-sm min-w-[180px]"
                  >
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="submitted">Submitted</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                  </select>
      </div>

      {/* TABLE */}
      <div className="overflow-x-auto">
        <table className="table w-full">
          
          <tbody>
            <tr>
              <td colSpan={5} className="p-0">
                <SuratTable data={surat.data} sortField={sort} sortDirection={direction} onSort={toggleSort} />
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <Pagination links={surat.links} />
    </AuthenticatedLayout>
  )
}