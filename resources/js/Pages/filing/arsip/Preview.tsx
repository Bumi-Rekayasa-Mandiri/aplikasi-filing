import AuthenticatedLayout from '@/layouts/AuthenticatedLayout'
import { Head, Link } from '@inertiajs/react'
import { usePage } from '@inertiajs/react'

interface Arsip {
    id: number
    nomor_surat: string
    judul: string
    tujuan: string
    jenis_surat: string
    file_url: string | null
    file_name: string | null
    file_mime: string | null
}

interface PageProps {
    arsip: Arsip
    [key: string]: unknown
}

export default function Preview() {
    const { arsip } = usePage<PageProps>().props
    const isPdf = arsip.file_mime === 'application/pdf'
    const isImage = arsip.file_mime?.startsWith('image/')

    return (
        <AuthenticatedLayout
            header={
                <div className="flex justify-between items-center">
                    <h2 className="text-xl font-semibold">Preview Arsip Surat</h2>
                    <Link
                        href={route('filing.arsip.index')}
                        className="bg-gray-500 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-600"
                    >
                        ← Kembali
                    </Link>
                </div>
            }
        >
            <Head title={`Preview - ${arsip.judul}`} />

            <div className="max-w-5xl mx-auto space-y-6">

                {/* ── INFO ARSIP ── */}
                <div className="bg-white rounded-xl shadow p-6">
                    <h3 className="text-lg font-semibold text-green-700 border-b pb-2 mb-4">
                        Informasi Arsip
                    </h3>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span className="text-gray-500 font-medium">Nomor Surat</span>
                            <p className="mt-1 font-semibold">{arsip.nomor_surat || '-'}</p>
                        </div>
                        <div>
                            <span className="text-gray-500 font-medium">Judul</span>
                            <p className="mt-1 font-semibold">{arsip.judul || '-'}</p>
                        </div>
                        <div>
                            <span className="text-gray-500 font-medium">Jenis Surat</span>
                            <p className="mt-1">{arsip.jenis_surat || '-'}</p>
                        </div>
                        <div>
                            <span className="text-gray-500 font-medium">Tujuan</span>
                            <p className="mt-1">{arsip.tujuan || '-'}</p>
                        </div>
                    </div>
                </div>

                {/* ── FILE PREVIEW ── */}
                <div className="bg-white rounded-xl shadow p-6">
                    <div className="flex justify-between items-center border-b pb-2 mb-4">
                        <h3 className="text-lg font-semibold text-green-700">File Arsip</h3>
                        {arsip.file_url && (
                            <a
                                href={route('filing.arsip.download', arsip.id)}
                                className="bg-green-700 text-white px-4 py-2 rounded-full text-sm font-semibold hover:bg-green-800"
                            >
                                Download
                            </a>
                        )}
                    </div>

                    {!arsip.file_url && (
                        <p className="text-gray-400 text-sm text-center py-10">
                            File belum diupload
                        </p>
                    )}

                    {arsip.file_url && isPdf && (
                        <iframe
                            src={arsip.file_url}
                            className="w-full rounded border"
                            style={{ height: '75vh' }}
                            title={arsip.file_name ?? 'Preview PDF'}
                        />
                    )}

                    {arsip.file_url && isImage && (
                        <img
                            src={arsip.file_url}
                            alt={arsip.file_name ?? 'Preview'}
                            className="max-w-full mx-auto rounded shadow"
                        />
                    )}

                    {arsip.file_url && !isPdf && !isImage && (
                        <div className="text-center py-10 text-gray-500">
                            <p className="mb-3">Preview tidak tersedia untuk format ini.</p>
                            <a
                                href={route('filing.arsip.download', arsip.id)}
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