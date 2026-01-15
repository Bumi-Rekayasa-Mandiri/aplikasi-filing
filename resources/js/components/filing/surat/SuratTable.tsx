import { Surat } from '@/types/filing/surat'
import { router } from '@inertiajs/react'

export default function SuratTable({ data }: { data: Surat[] }) {
  if (data.length === 0) {
    return (
      <div className="text-center text-gray-500 py-6">
        Tidak ada surat untuk ditampilkan
      </div>
    )
  }

  return (
    <table className="table w-full">
      <thead>
        <tr>
          <th>Nomor</th>
          <th>Judul</th>
          <th>Tujuan</th>
          <th>Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        {data.map((s) => (
          <tr key={s.id}>
            <td>{s.nomor_surat}</td>
            <td>{s.judul}</td>
            <td>{s.tujuan}</td>
            <td>{s.status}</td>
            <td>
              <button
                className="btn btn-sm"
                onClick={() => router.visit(`/filing/surat/${s.id}`)}
              >
                Detail
              </button>
              <button
                className="btn btn-sm btn-warning"
                onClick={() => router.visit(`/filing/surat/${s.id}/edit`)}
              >
                Edit
              </button>

              <button
                className="btn btn-sm btn-error"
                onClick={() => {
                  if (confirm('Yakin ingin menghapus surat ini?')) {
                    router.delete(`/filing/surat/${s.id}`)
                  }
                }}
              >
                Hapus
              </button>

            </td>
          </tr>
        ))}
      </tbody>
    </table>
  )
}