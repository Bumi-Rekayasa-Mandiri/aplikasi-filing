import AuthenticatedLayout from '@/layouts/AuthenticatedLayout'
import { Link } from '@inertiajs/react'

export default function Index({ data }) {
    return (
        <AuthenticatedLayout header={<h2>Arsip Surat</h2>}>

            <Link
                href={route('filing.arsip.create')}
                className="btn btn-primary mb-4"
            >
                Upload Arsip
            </Link>

            <table className="table w-full">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Nomor</th>
                        <th>Tahun</th>
                    </tr>
                </thead>
                <tbody>
                    {data.map(a => (
                        <tr key={a.id}>
                            <td>{a.judul}</td>
                            <td>{a.nomor_surat}</td>
                            <td>{a.tahun}</td>
                        </tr>
                    ))}
                </tbody>
            </table>

        </AuthenticatedLayout>
    )
}