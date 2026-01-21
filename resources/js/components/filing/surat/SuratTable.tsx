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

  const submit = (id: number) => {
    if (confirm('Ajukan surat ini untuk approval?')) {
      router.post(route('filing.surat.submit', id))
    }
  }

  const approve = (id: number) => {
    if (confirm('Setujui surat ini?')) {
      router.post(route('filing.surat.approve', id))
    }
  }

  const reject = (id: number) => {
    if (confirm('Tolak surat ini?')) {
      router.post(route('filing.surat.reject', id))
    }
  }

  const destroy = (id: number) => {
    if (confirm('Yakin ingin menghapus surat ini?')) {
      router.delete(`/filing/surat/${id}`, { preserveScroll: true })
    }
  }

  return (
    <table className="table w-full">
      <thead>
        <tr>
          <th>Nomor</th>
          <th>Judul</th>
          <th>Tujuan</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        {data.map((s) => (
          <tr key={s.id}>
            <td>{s.nomor_surat}</td>
            <td>{s.judul}</td>
            <td>{s.tujuan}</td>
            <td>
              <span className="badge badge-outline">
                {s.status}
              </span>
            </td>
            <td className="flex gap-2">
              {/* DETAIL – selalu ada */}
              <button
                className="btn btn-sm"
                onClick={() => router.visit(`/filing/surat/${s.id}`)}
              >
                Detail
              </button>

              {/* DRAFT */}
              {s.status === 'draft' && (
                <>
                  <button
                    className="btn btn-sm btn-warning"
                    onClick={() => router.visit(`/filing/surat/${s.id}/edit`)}
                  >
                    Edit
                  </button>

                  <button
                    className="btn btn-sm btn-error"
                    onClick={() => destroy(s.id)}
                  >
                    Hapus
                  </button>

                  <button
                    className="btn btn-sm btn-primary"
                    onClick={() => submit(s.id)}
                  >
                    Ajukan
                  </button>
                </>
              )}

              {/* SUBMITTED */}
              {s.status === 'submitted' && (
                <>
                  <button
                    className="btn btn-sm btn-success"
                    onClick={() => approve(s.id)}
                  >
                    Approve
                  </button>

                  <button
                    className="btn btn-sm btn-error"
                    onClick={() => reject(s.id)}
                  >
                    Reject
                  </button>
                </>
              )}
            </td>
          </tr>
        ))}
      </tbody>
    </table>
  )
}