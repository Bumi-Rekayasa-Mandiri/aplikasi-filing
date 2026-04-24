import AuthenticatedLayout from '@/layouts/AuthenticatedLayout'
import { Link } from '@inertiajs/react'
import Pagination from '@/components/Pagination'
import { Head, router, usePage } from '@inertiajs/react'
import { useEffect, useState } from 'react'
import { AppPageProps } from '@/types/filing/inertia'
import SortIcon from '@/components/SortIcon'
import '../../../../css/surat-table.css'
import '@/components/PerPageSelect'
import PerPageSelect from '@/components/PerPageSelect'

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
  arsip: { data: Arsip[]; links: any[] }
  filters: { search?: string }
  sort: string
  direction: 'asc' | 'desc'
  [key: string]: unknown
  per_page: number
}

export default function Index() {
  const { arsip, filters, sort, direction, per_page } = usePage<PageProps>().props
  const [search, setSearch] = useState(filters.search ?? '')
  const [perPage, setPerPage] = useState<number>(Number(per_page) || 10)

  useEffect(() => {
    const timeout = setTimeout(() => {
      router.get(route('filing.arsip.index'),
        { search, sortField: sort, sortDirection: direction, per_page: perPage, page: 1 } as Record<string, string | number>,
        { preserveState: true, replace: true }
      )
    }, 400)
    return () => clearTimeout(timeout)
  }, [search])

  const toggleSort = (field: string) => {
    const newDirection = sort === field && direction === 'asc' ? 'desc' : 'asc'
    router.get(route('filing.arsip.index'),
      { search, sortField: field, sortDirection: newDirection },
      { preserveState: true, preserveScroll: true, replace: true }
    )
  }

    const handlePerPageChange = (value: number) => {
    setPerPage(value)
    router.get(route('filing.arsip.index'),
      { search, sortField: sort, sortDirection: direction,
        per_page: value, page: 1 } as Record<string, string | number>,
      { preserveState: true, replace: true }
    )
  } 

  return (
    <AuthenticatedLayout>
      <Head title="Arsip Surat" />

      <div className="surat-index">

        {/* Header */}
        <div className="surat-index-header">
          <h1 className="surat-index-title">Arsip surat</h1>
          <Link href={route('filing.arsip.create')} className="btn-create-surat">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
              <path d="M8 3v10M3 8h10" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/>
            </svg>
            Upload arsip
          </Link>
        </div>

        {/* Filter */}
        <div className="surat-filter-bar">
          <div className="surat-search-wrap">
            <svg className="surat-search-icon" width="14" height="14" viewBox="0 0 16 16" fill="none">
              <circle cx="6.5" cy="6.5" r="4.5" stroke="currentColor" strokeWidth="1.3"/>
              <path d="M10 10l3 3" stroke="currentColor" strokeWidth="1.3" strokeLinecap="round"/>
            </svg>
            <input
              className="surat-search-input"
              placeholder="Cari judul / nomor surat..."
              value={search}
              onChange={e => setSearch(e.target.value)}
            />
          </div>
          <PerPageSelect value={perPage} onChange={handlePerPageChange} />
        </div>

        {/* Tabel */}
        <div className="surat-table-card">
          <table className="surat-table">
            <thead>
              <tr>
                <th className="sortable" onClick={() => toggleSort('nomor_surat')}>
                  Nomor <SortIcon active={sort === 'nomor_surat'} direction={direction} />
                </th>
                <th className="sortable" onClick={() => toggleSort('judul')}>
                  Judul <SortIcon active={sort === 'judul'} direction={direction} />
                </th>
                <th className="sortable" onClick={() => toggleSort('jenis_surat')}>
                  Jenis surat <SortIcon active={sort === 'jenis_surat'} direction={direction} />
                </th>
                <th className="sortable" onClick={() => toggleSort('tujuan')}>
                  Tujuan <SortIcon active={sort === 'tujuan'} direction={direction} />
                </th>
                <th className="center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              {arsip.data.length === 0 && (
                <tr>
                  <td colSpan={5} className="surat-table-empty">
                    Data arsip tidak ditemukan
                  </td>
                </tr>
              )}
              {arsip.data.map(item => (
                <tr key={item.id}>
                  <td><span className="surat-nomor">{item.nomor_surat}</span></td>
                  <td><div className="surat-judul">{item.judul}</div></td>
                  <td>
                    {item.jenis_surat
                      ? <span className="surat-jenis-badge">{item.jenis_surat}</span>
                      : <span style={{color:'#9ca3af'}}>—</span>}
                  </td>
                  <td className="surat-tujuan">{item.tujuan || '—'}</td>
                  <td className="center">
                    <div className="surat-actions">
                      {item.file_url && (
                        <a href={route('filing.arsip.show', item.id)}
                          className="act-btn act-detail">
                          Preview
                        </a>
                      )}
                      {item.file_url && (
                        <a href={route('filing.arsip.download', item.id)}
                          target="_blank" rel="noopener noreferrer"
                          className="act-btn act-preview">
                          Download
                        </a>
                      )}
                      <button
                        className="act-btn act-delete"
                        onClick={() => {
                          if (confirm('Yakin ingin menghapus arsip ini?')) {
                            router.delete(route('filing.arsip.destroy', item.id),
                              { preserveScroll: true })
                          }
                        }}
                      >
                        Hapus
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        <Pagination links={arsip.links} />
      </div>
    </AuthenticatedLayout>
  )
}