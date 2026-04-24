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
import '../../../../css/surat-table.css'
import PerPageSelect from '@/components/PerPageSelect'

interface PageProps {
  surat: {
    data: Surat[]
    links: any[]
  }
  sort: string
  direction: 'asc' | 'desc'
  per_page: number
  filters: {
    search?: string
    status?: string
  }
}

export default function Index() {
  const { surat, sort, direction, filters, per_page } = usePage<AppPageProps&PageProps>().props

  // ── State ──────────────────────────────────────
  const [search, setSearch]   = useState(filters.search ?? '')
  const [status, setStatus]   = useState(filters.status ?? '')
  const [perPage, setPerPage] = useState<number>(Number(per_page) || 10)

  const { url } = usePage()
  const getCurrentPage = () => {
    const params = new URLSearchParams(url.split('?')[1])
    return params.get('page') ?? '1'
  }

  // ── Core navigate — satu fungsi untuk semua filter ──
  const navigate = (overrides: Record<string, string | number> = {}) => {
    router.get(
      route('filing.surat.index'),
      {
        search,
        status,
        sortField:     sort,
        sortDirection: direction,
        per_page:      perPage,
        page:          1,
        ...overrides,  // override field yang berubah
      } as unknown as Record<string, string | number>,
      { preserveState: true, replace: true }
    )
  }

  // ── Handlers ───────────────────────────────────
  const handleSearch = (value: string) => {
    setSearch(value)
    navigate({ search: value })
  }

  const handleStatus = (value: string) => {
    setStatus(value)
    navigate({ status: value })
  }

  const handlePerPageChange = (value: number) => {
    setPerPage(value)
    navigate({ per_page: value })
  }

  const handleDelete = (id: number) => {
    router.delete(route('filing.surat.destroy', id), {
      data: {
        status,
        search,
        sortField:     sort,
        sortDirection: direction,
        per_page:      perPage,
        page:          getCurrentPage(),
      } as unknown as Record<string, string | number>,
      preserveScroll: false,
    })
  }

  const toggleSort = (field: string) => {
    const newDirection = field === sort && direction === 'asc' ? 'desc' : 'asc'
    navigate({ sortField: field, sortDirection: newDirection, page: getCurrentPage() })
  }

  // ── JSX ────────────────────────────────────────
  return (
    <AuthenticatedLayout>
      <Head title="Surat" />
      <div className="surat-index">

        <div className="surat-index-header">
          <h1 className="surat-index-title">Surat</h1>
          <Dropdown trigger={
            <button className="btn-create-surat">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <path d="M8 3v10M3 8h10" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/>
              </svg>
              Buat surat
            </button>
          }>
            <Dropdown.Link href={route('filing.surat.create', 'SKP-BRM')}>Surat Pemberitahuan PHK</Dropdown.Link>
            <Dropdown.Link href={route('filing.surat.create', 'GRS-BRM')}>Surat Garansi Material</Dropdown.Link>
            <Dropdown.Link href={route('filing.surat.create', 'SPD-BRM')}>Surat Pengembalian Dana</Dropdown.Link>
            <Dropdown.Link href={route('filing.surat.create', 'SPI-BRM')}>Surat Permohonan Investasi</Dropdown.Link>
            <Dropdown.Link href={route('filing.surat.create', 'IEI-BRM')}>Surat Garansi Pekerjaan</Dropdown.Link>
            <Dropdown.Link href={route('filing.surat.create', 'SK-BRM')}>Surat Permohonan Keringanan Denda</Dropdown.Link>
            <Dropdown.Link href={route('filing.surat.create', 'BRM-1')}>Surat Izin Kerja dan LK3</Dropdown.Link>
            <Dropdown.Link href={route('filing.surat.create', 'BRM-2')}>Surat Pelepasan Hak</Dropdown.Link>
          </Dropdown>
        </div>

        <div className="surat-filter-bar">
          <div className="surat-search-wrap">
            <svg className="surat-search-icon" width="14" height="14" viewBox="0 0 16 16" fill="none">
              <circle cx="6.5" cy="6.5" r="4.5" stroke="currentColor" strokeWidth="1.3"/>
              <path d="M10 10l3 3" stroke="currentColor" strokeWidth="1.3" strokeLinecap="round"/>
            </svg>
            <input className="surat-search-input"
              placeholder="Cari judul / nomor surat..."
              value={search}
              onChange={e => handleSearch(e.target.value)} />
          </div>
          <select className="surat-status-select"
            value={status}
            onChange={e => handleStatus(e.target.value)}>
            <option value="">Semua status</option>
            <option value="draft">Draft</option>
            <option value="approved">Approved</option>
          </select>
          <PerPageSelect value={perPage} onChange={handlePerPageChange} />
        </div>

        <div className="surat-table-card">
          <SuratTable
            data={surat.data}
            sortField={sort}
            sortDirection={direction}
            onSort={toggleSort}
            onDelete={handleDelete}
          />
        </div>

        <Pagination links={surat.links} />
      </div>
    </AuthenticatedLayout>
  )
}