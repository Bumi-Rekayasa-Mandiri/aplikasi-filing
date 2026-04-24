import AuthenticatedLayout from '@/layouts/AuthenticatedLayout'
import { Link } from '@inertiajs/react'
import Pagination from '@/components/Pagination'
import { Head, router, usePage } from '@inertiajs/react'
import { useEffect, useState } from 'react'
import SortIcon from '@/components/SortIcon'
import '../../../../css/surat-table.css'
import '@/components/PerPageSelect'
import PerPageSelect from '@/components/PerPageSelect'

interface Sertifikat {
  id: number
  nama_sertifikat: string
  nomor_sertifikat: string
  jenis_sertifikat: string
  instansi: string
  file_url: string | null
  file_name: string | null
}

interface PageProps {
  sertifikat: { data: Sertifikat[]; links: any[] }
  filters: { search?: string }
  sort: string
  direction: 'asc' | 'desc'
  [key: string]: unknown
  per_page: number
}

export default function Index() {
  const { sertifikat, filters, sort, direction, per_page } = usePage<PageProps>().props
  const [search, setSearch] = useState(filters.search ?? '')
  const [perPage, setPerPage] = useState<number>(Number(per_page) || 10)

  useEffect(() => {
    const timeout = setTimeout(() => {
      router.get(route('filing.sertifikat.index'),
        { search, sortField: sort, sortDirection: direction, per_page: perPage, page: 1 } as Record<string, string | number>,
        { preserveState: true, replace: true }
      )
    }, 400)
    return () => clearTimeout(timeout)
  }, [search])

  const toggleSort = (field: string) => {
    const newDirection = sort === field && direction === 'asc' ? 'desc' : 'asc'
    router.get(route('filing.sertifikat.index'),
      { search, sortField: field, sortDirection: newDirection },
      { preserveState: true, preserveScroll: true, replace: true }
    )
  }

    const handlePerPageChange = (value: number) => {
    setPerPage(value)
    router.get(route('filing.sertifikat.index'),
      { search, sortField: sort, sortDirection: direction,
        per_page: value, page: 1 } as Record<string, string | number>,
      { preserveState: true, replace: true }
    )
  }

  return (
    <AuthenticatedLayout>
      <Head title="Arsip Sertifikat" />

      <div className="surat-index">

        <div className="surat-index-header">
          <h1 className="surat-index-title">Sertifikat</h1>
          <Link href={route('filing.sertifikat.create')} className="btn-create-surat">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
              <path d="M8 3v10M3 8h10" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/>
            </svg>
            Upload sertifikat
          </Link>
        </div>

        <div className="surat-filter-bar">
            <div className="surat-search-wrap">
              <svg className="surat-search-icon" width="14" height="14" viewBox="0 0 16 16" fill="none">
                <circle cx="6.5" cy="6.5" r="4.5" stroke="currentColor" strokeWidth="1.3"/>
                <path d="M10 10l3 3" stroke="currentColor" strokeWidth="1.3" strokeLinecap="round"/>
              </svg>
              <input className="surat-search-input"
                placeholder="Cari nama / nomor sertifikat..."
                value={search} onChange={e => setSearch(e.target.value)} />
            </div>
          <PerPageSelect value={perPage} onChange={handlePerPageChange} />
        </div>

        <div className="surat-table-card">
          <table className="surat-table">
            <thead>
              <tr>
                <th className="sortable" onClick={() => toggleSort('nama_sertifikat')}>
                  Nama sertifikat <SortIcon active={sort === 'nama_sertifikat'} direction={direction} />
                </th>
                <th className="sortable" onClick={() => toggleSort('nomor_sertifikat')}>
                  Nomor <SortIcon active={sort === 'nomor_sertifikat'} direction={direction} />
                </th>
                <th className="sortable" onClick={() => toggleSort('jenis_sertifikat')}>
                  Jenis <SortIcon active={sort === 'jenis_sertifikat'} direction={direction} />
                </th>
                <th className="sortable" onClick={() => toggleSort('instansi')}>
                  Instansi <SortIcon active={sort === 'instansi'} direction={direction} />
                </th>
                <th className="center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              {sertifikat.data.length === 0 && (
                <tr>
                  <td colSpan={5} className="surat-table-empty">
                    Data sertifikat tidak ditemukan
                  </td>
                </tr>
              )}
              {sertifikat.data.map(item => (
                <tr key={item.id}>
                  <td><div className="surat-judul">{item.nama_sertifikat}</div></td>
                  <td><span className="surat-nomor">{item.nomor_sertifikat || '—'}</span></td>
                  <td>
                    {item.jenis_sertifikat
                      ? <span className="surat-jenis-badge">{item.jenis_sertifikat}</span>
                      : <span style={{ color: '#9ca3af' }}>—</span>}
                  </td>
                  <td className="surat-tujuan">{item.instansi || '—'}</td>
                  <td className="center">
                    <div className="surat-actions">
                      {item.file_url && (
                        <a href={route('filing.sertifikat.show', item.id)}
                          className="act-btn act-detail">
                          Preview
                        </a>
                      )}
                      {item.file_url && (
                        <a href={route('filing.sertifikat.download', item.id)}
                          target="_blank" rel="noopener noreferrer"
                          className="act-btn act-preview">
                          Download
                        </a>
                      )}
                      <button className="act-btn act-delete"
                        onClick={() => {
                          if (confirm('Yakin ingin menghapus sertifikat ini?')) {
                            router.delete(route('filing.sertifikat.destroy', item.id),
                              { preserveScroll: true })
                          }
                        }}>
                        Hapus
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        <Pagination links={sertifikat.links} />
      </div>
    </AuthenticatedLayout>
  )
}