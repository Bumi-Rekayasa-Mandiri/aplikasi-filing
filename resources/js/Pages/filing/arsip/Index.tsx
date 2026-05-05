import AuthenticatedLayout from '@/layouts/AuthenticatedLayout'
import { Link } from '@inertiajs/react'
import Pagination from '@/components/Pagination'
import { Head, router, usePage } from '@inertiajs/react'
import { useEffect, useState } from 'react'
import SortIcon from '@/components/SortIcon'
import '../../../../css/surat-table.css'
import PerPageSelect from '@/components/PerPageSelect'
import RestoreButton from '@/components/filing/RestoreButton'

interface Arsip {
  id: number
  surat_id: number | null
  nomor_surat: string
  judul: string
  tujuan: string
  jenis_surat: string
  tahun: number | null
  archived_at: string | null
  file_url: string | null
  file_name: string | null
}

interface PageProps {
  arsip: { data: Arsip[]; links: any[] }
  filters: { search?: string; tahun?: number | null }
  sort: string
  direction: 'asc' | 'desc'
  per_page: number
  availableYears: number[]
  [key: string]: unknown
}

// ── Mapping jenis_surat → nama route preview surat asli ─────────────────────
// Sesuaikan dengan route names di web.php
const PREVIEW_ROUTE_MAP: Record<string, string> = {
  'BRM-1':   'filing.surat.previewBRM1',
  'BRM-2':   'filing.surat.previewBRM2',
  'SKP-BRM': 'filing.surat.previewSKP',
  'GRS-BRM': 'filing.surat.previewGRS',
  'SPD-BRM': 'filing.surat.previewSPD',
  'SPI-BRM': 'filing.surat.previewSPI',
  'IEI-BRM': 'filing.surat.previewIEI',
  'SK-BRM':  'filing.surat.previewSK',
}

/** Resolve URL preview: prioritas file arsip sendiri, fallback ke surat asli */
function resolvePreviewUrl(item: Arsip): string | null {
  if (item.file_url) return route('filing.arsip.show', item.id)
  if (!item.surat_id) return null
  const routeName = PREVIEW_ROUTE_MAP[item.jenis_surat]
  return routeName ? route(routeName, item.surat_id) : null
}

/** Resolve URL download: prioritas file arsip sendiri, fallback ke PDF surat asli */
function resolveDownloadUrl(item: Arsip): string | null {
  if (item.file_url) return route('filing.arsip.download', item.id)
  if (!item.surat_id) return null
  return route('filing.surat.download', item.surat_id)
}

export default function Index() {
  const { arsip, filters, sort, direction, per_page, availableYears } =
    usePage<PageProps>().props

  const [search, setSearch]   = useState(filters.search ?? '')
  const [tahun, setTahun]     = useState<number | ''>(filters.tahun ?? '')
  const [perPage, setPerPage] = useState<number>(Number(per_page) || 10)

  useEffect(() => {
    const timeout = setTimeout(() => navigate({ search, page: 1 }), 400)
    return () => clearTimeout(timeout)
  }, [search])

  const navigate = (overrides: Record<string, unknown> = {}) => {
    router.get(
      route('filing.arsip.index'),
      {
        search,
        tahun: tahun || undefined,
        sortField: sort,
        sortDirection: direction,
        per_page: perPage,
        page: 1,
        ...overrides,
      } as Record<string, string | number>,
      { preserveState: true, replace: true }
    )
  }

  const toggleSort = (field: string) => {
    const newDirection = sort === field && direction === 'asc' ? 'desc' : 'asc'
    router.get(
      route('filing.arsip.index'),
      { search, tahun: tahun || undefined, sortField: field, sortDirection: newDirection },
      { preserveState: true, preserveScroll: true, replace: true }
    )
  }

  const handlePerPageChange = (value: number) => {
    setPerPage(value)
    navigate({ per_page: value, page: 1 })
  }

  const handleTahunChange = (value: number | '') => {
    setTahun(value)
    navigate({ tahun: value || undefined, page: 1 })
  }

  return (
    <AuthenticatedLayout>
      <Head title="Arsip Surat" />

      <div className="surat-index">

        {/* Header */}
        <div className="surat-index-header">
          <h1 className="surat-index-title">Arsip Surat</h1>
          <Link href={route('filing.arsip.create')} className="btn-create-surat">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
              <path d="M8 3v10M3 8h10" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/>
            </svg>
            Upload Arsip
          </Link>
        </div>

        {/* Filter bar */}
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

          {availableYears.length > 0 && (
            <select
              className="surat-filter-select"
              value={tahun}
              onChange={e => handleTahunChange(e.target.value ? Number(e.target.value) : '')}
            >
              <option value="">Semua tahun</option>
              {availableYears.map(y => (
                <option key={y} value={y}>{y}</option>
              ))}
            </select>
          )}

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
                  Jenis Surat <SortIcon active={sort === 'jenis_surat'} direction={direction} />
                </th>
                <th className="sortable" onClick={() => toggleSort('tujuan')}>
                  Tujuan <SortIcon active={sort === 'tujuan'} direction={direction} />
                </th>
                <th className="sortable center" onClick={() => toggleSort('tahun')}>
                  Tahun <SortIcon active={sort === 'tahun'} direction={direction} />
                </th>
                <th className="center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              {arsip.data.length === 0 && (
                <tr>
                  <td colSpan={6} className="surat-table-empty">
                    Data arsip tidak ditemukan
                  </td>
                </tr>
              )}
              {arsip.data.map(item => {
                const previewUrl  = resolvePreviewUrl(item)
                const downloadUrl = resolveDownloadUrl(item)

                return (
                  <tr key={item.id}>
                    <td><span className="surat-nomor">{item.nomor_surat}</span></td>
                    <td><div className="surat-judul">{item.judul}</div></td>
                    <td>
                      {item.jenis_surat
                        ? <span className="surat-jenis-badge">{item.jenis_surat}</span>
                        : <span style={{ color: '#9ca3af' }}>—</span>}
                    </td>
                    <td className="surat-tujuan">{item.tujuan || '—'}</td>
                    <td className="center">
                      {item.tahun
                        ? <span className="surat-jenis-badge">{item.tahun}</span>
                        : <span style={{ color: '#9ca3af' }}>—</span>}
                    </td>
                    <td className="center">
                      <div className="surat-actions">

                        {previewUrl && (
                          <a
                            href={previewUrl}
                            className="act-btn act-detail"
                            // File arsip buka di tab sama; preview surat asli buka tab baru
                            target={item.file_url ? '_self' : '_blank'}
                            rel="noopener noreferrer"
                          >
                            Preview
                          </a>
                        )}

                        {downloadUrl && (
                          <a
                            href={downloadUrl}
                            className="act-btn act-preview"
                            target="_blank"
                            rel="noopener noreferrer"
                          >
                            Download
                          </a>
                        )}

                        <button
                          className="act-btn act-delete"
                          onClick={() => {
                            if (confirm('Yakin ingin menghapus arsip ini?')) {
                              router.delete(route('filing.arsip.destroy', item.id), {
                                preserveScroll: true,
                              })
                            }
                          }}
                        >
                          Hapus
                        </button>

                        <RestoreButton
                          arsipId={item.id}
                          judul={item.judul}
                          canRestore={item.surat_id !== null}
                        />

                      </div>
                    </td>
                  </tr>
                )
              })}
            </tbody>
          </table>
        </div>

        <Pagination links={arsip.links} />
      </div>
    </AuthenticatedLayout>
  )
}