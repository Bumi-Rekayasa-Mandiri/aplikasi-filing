import { Surat } from '@/types/filing/surat'
import { router } from '@inertiajs/react'
import SuratApprovalActions from '@/components/filing/surat/SuratApprovalActions'
import SuratDownloadButton from '@/components/filing/surat/SuratDownloadButton'
import SortIcon from '@/components/SortIcon'

interface Props {
  data: Surat[]
  sortField: string
  sortDirection: 'asc' | 'desc'
  onSort: (field: string) => void
}

export default function SuratTable({ data, sortField, sortDirection, onSort }: Props) {
  if (!data || data.length === 0) {
    return (
      <div className="text-center text-gray-500 py-6">
        Tidak ada surat untuk ditampilkan
      </div>
    )
  }

  const destroy = (id: number) => {
    if (confirm('Yakin ingin menghapus surat ini?')) {
      router.delete(route('filing.surat.destroy', id), {
        preserveScroll: true,
      })
    }
  }

  return (
    <table className="table w-full">
      <thead>
        <tr className='bg-gray-100'>
          <th className="px-4 py-3 text-center text-sm font-semibold" onClick={() => onSort('nomor_surat')}>
            <span className="inline-flex items-center gap-1">
            Nomor
            <SortIcon
              active={sortField === 'nomor_surat'}
              direction={sortDirection}
            />
            </span>
          </th>
          <th className="px-4 py-3 text-center text-sm font-semibold" onClick={() => onSort('judul')}>
            <span className="inline-flex items-center gap-1">
            Judul
            <SortIcon
              active={sortField === 'judul'}
              direction={sortDirection}
            />
            </span>
          </th>
          <th className="px-4 py-3 text-center text-sm font-semibold">
            <span className="inline-flex items-center gap-1">
            Tujuan<SortIcon
              active={sortField === 'tujuan'}
              direction={sortDirection}
            />
            </span>
            </th>
          <th className="px-4 py-3 text-center text-sm font-semibold" onClick={() => onSort('status')}>
            <span className="inline-flex items-center gap-1">
            Status
            <SortIcon
              active={sortField === 'status'}
              direction={sortDirection}
            />
            </span>
          </th>
          <th className="px-4 py-3 text-center text-sm font-semibold">Aksi</th>
        </tr>
      </thead>
      <tbody>
        {data.map((s) => (
          <tr key={s.id} className="border-t">
            <td className="px-4 py-3 text-center text-sm">{s.nomor_surat}</td>
            <td className="px-4 py-3 text-center text-sm">{s.judul}</td>
            <td className="px-4 py-3 text-center text-sm">{s.tujuan}</td>
            <td className="px-4 py-3 text-center text-sm">
              <span className="badge badge-outline">
                {s.status}
              </span>
            </td>
            <td className="px-4 py-3 text-center text-sm">
              <div className="flex justify-center gap-2">
                <button
                  className="btn btn-sm bg-green-700 px-4 py-2 rounded-full text-white font-semibold"
                  onClick={() =>
                    router.visit(route('filing.surat.show', s.id))
                  }
                >
                  Detail
                </button>

                <button
                  className="btn btn-sm bg-blue-700 px-4 py-2 rounded-full text-white font-semibold"
                  onClick={() =>
                    router.visit(route('filing.surat.preview', s.id))
                  }
                >
                  Preview
                </button>

                {s.status === 'draft' && (
                  <>
                    <button
                      className="btn btn-sm bg-yellow-600 px-4 py-2 rounded-full text-white font-semibold"
                      onClick={() =>
                        router.visit(route('filing.surat.edit', s.id))
                      }
                    >
                      Edit
                    </button>

                    <button
                      className="btn btn-sm bg-red-600 px-4 py-2 rounded-full text-white font-semibold"
                      onClick={() => destroy(s.id)}
                    >
                      Hapus
                    </button>
                  </>
                )}

                {(s.status === 'submitted' || s.status === 'approved') && (
                  <SuratDownloadButton surat={s} />
                )}

                {s.status === 'submitted' && (
                  <SuratApprovalActions surat={s} />
                )}
              </div>
            </td>
          </tr>
        ))}
      </tbody>
    </table>
  )
}