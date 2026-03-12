import AuthenticatedLayout from '@/layouts/AuthenticatedLayout'
import { Head, Link } from '@inertiajs/react'
import { usePage } from '@inertiajs/react'

interface Sertifikat {
    id: number
    nama_sertifikat: string
    nomor_sertifikat: string
    jenis_sertifikat: string
    instansi: string
    file_url: string | null
    file_name: string | null
    file_mime: string | null
}

interface PageProps {
    sertifikat: Sertifikat
    [key: string]: unknown
}

export default function Preview() {
    const { sertifikat } = usePage<PageProps>().props
    const isPdf = sertifikat.file_mime === 'application/pdf'
    const isImage = sertifikat.file_mime?.startsWith('image/')

    return (
        <AuthenticatedLayout
            header={
                <div className="flex justify-between items-center">
                    <h2 className="text-xl font-semibold">Preview Sertifikat</h2>
                    <Link
                        href={route('filing.sertifikat.index')}
                        className="bg-gray-500 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-600"
                    >
                        ← Kembali
                    </Link>
                </div>
            }
        >
            <Head title={`Preview - ${sertifikat.nama_sertifikat}`} />

            <div className="max-w-5xl mx-auto space-y-6">

                {/* ── INFO SERTIFIKAT ── */}
                <div className="bg-white rounded-xl shadow p-6">
                    <h3 className="text-lg font-semibold text-green-700 border-b pb-2 mb-4">
                        Informasi Sertifikat
                    </h3>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span className="text-gray-500 font-medium">Nama Sertifikat</span>
                            <p className="mt-1 font-semibold">{sertifikat.nama_sertifikat || '-'}</p>
                        </div>
                        <div>
                            <span className="text-gray-500 font-medium">Nomor Sertifikat</span>
                            <p className="mt-1 font-semibold">{sertifikat.nomor_sertifikat || '-'}</p>
                        </div>
                        <div>
                            <span className="text-gray-500 font-medium">Jenis Sertifikat</span>
                            <p className="mt-1">{sertifikat.jenis_sertifikat || '-'}</p>
                        </div>
                        <div>
                            <span className="text-gray-500 font-medium">Instansi</span>
                            <p className="mt-1">{sertifikat.instansi || '-'}</p>
                        </div>
                    </div>
                </div>

                {/* ── FILE PREVIEW ── */}
                <div className="bg-white rounded-xl shadow p-6">
                    <div className="flex justify-between items-center border-b pb-2 mb-4">
                        <h3 className="text-lg font-semibold text-green-700">File Sertifikat</h3>
                        {sertifikat.file_url && (
                            <a
                                href={route('filing.sertifikat.download', sertifikat.id)}
                                className="bg-green-700 text-white px-4 py-2 rounded-full text-sm font-semibold hover:bg-green-800"
                            >
                                Download
                            </a>
                        )}
                    </div>

                    {!sertifikat.file_url && (
                        <p className="text-gray-400 text-sm text-center py-10">
                            File belum diupload
                        </p>
                    )}

                    {sertifikat.file_url && isPdf && (
                        <iframe
                            src={sertifikat.file_url}
                            className="w-full rounded border"
                            style={{ height: '75vh' }}
                            title={sertifikat.file_name ?? 'Preview PDF'}
                        />
                    )}

                    {sertifikat.file_url && isImage && (
                        <img
                            src={sertifikat.file_url}
                            alt={sertifikat.file_name ?? 'Preview'}
                            className="max-w-full mx-auto rounded shadow"
                        />
                    )}

                    {sertifikat.file_url && !isPdf && !isImage && (
                        <div className="text-center py-10 text-gray-500">
                            <p className="mb-3">Preview tidak tersedia untuk format ini.</p>
                            <a
                                href={route('filing.sertifikat.download', sertifikat.id)}
                                className="bg-green-700 text-white px-4 py-2 rounded-full text-sm font-semibold"
                            >
                                Download File
                            </a>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    )
}