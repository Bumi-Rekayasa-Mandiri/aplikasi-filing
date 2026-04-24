import { Surat } from '@/types/filing/surat'
import { router } from '@inertiajs/react'
import SortIcon from '@/components/SortIcon'
import { getPreviewRoute } from '@/types/filing/suratRoutes'

interface Props {
  data: Surat[]
  sortField: string
  sortDirection: 'asc' | 'desc'
  onSort: (field: string) => void
  onDelete: (id: number) => void
}

const statusClass: Record<string, string> = {
  approved:  'st-badge st-approved',
  draft:     'st-badge st-draft',
  submitted: 'st-badge st-submitted',
  rejected:  'st-badge st-rejected',
}

const statusLabel: Record<string, string> = {
  approved:  'Approved',
  draft:     'Draft',
  submitted: 'Submitted',
  rejected:  'Rejected',
}

export default function SuratTable({ data, sortField, sortDirection, onSort, onDelete }: Props) {
  if (!data || data.length === 0) {
    return (
      <div className="surat-table-empty">
        Tidak ada surat untuk ditampilkan
      </div>
    )
  }

  const destroy = (id: number) => {
    if (confirm('Yakin ingin menghapus surat ini?')) onDelete(id)
  }

  return (
    <table className="surat-table">
      <thead>
        <tr>
          <th onClick={() => onSort('nomor_surat')} className="sortable">
            Nomor <SortIcon active={sortField === 'nomor_surat'} direction={sortDirection} />
          </th>
          <th onClick={() => onSort('judul')} className="sortable">
            Judul <SortIcon active={sortField === 'judul'} direction={sortDirection} />
          </th>
          <th>Tujuan</th>
          <th onClick={() => onSort('status')} className="sortable center">
            Status <SortIcon active={sortField === 'status'} direction={sortDirection} />
          </th>
          <th className="center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        {data.map(s => (
          <tr key={s.id}>
            <td><span className="surat-nomor">{s.nomor_surat}</span></td>
            <td>
              <div className="surat-judul">{s.judul}</div>
              <span className="surat-jenis-badge">{s.jenis}</span>
            </td>
            <td className="surat-tujuan">{s.tujuan}</td>
            <td className="center">
              <span className={statusClass[s.status] ?? 'st-badge st-draft'}>
                {statusLabel[s.status] ?? s.status}
              </span>
            </td>
            <td className="center">
              <div className="surat-actions">
                <button className="act-btn act-detail"
                  onClick={() => router.visit(route('filing.surat.show', s.id))}>
                  Detail
                </button>
                <button className="act-btn act-preview"
                  onClick={() => router.visit(getPreviewRoute(s.jenis, s.id))}>
                  Preview
                </button>
                {s.status === 'draft' && (
                  <>
                    <button className="act-btn act-edit"
                      onClick={() => router.visit(route('filing.surat.edit', { surat: s.id, jenis: s.jenis }))}>
                      Edit
                    </button>
                    <button className="act-btn act-delete"
                      onClick={() => destroy(s.id)}>
                      Hapus
                    </button>
                  </>
                )}
              </div>
            </td>
          </tr>
        ))}
      </tbody>
    </table>
  )
}