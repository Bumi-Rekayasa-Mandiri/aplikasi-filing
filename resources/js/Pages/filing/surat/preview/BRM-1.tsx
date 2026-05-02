import AuthenticatedLayout from "@/layouts/AuthenticatedLayout"
import { Head } from "@inertiajs/react"

type Props = {
  surat: {
    id: number
    nomor_surat: string
    status: string
    pdf_url: string
  }
}

export default function PreviewBRM1({ surat }: Props) {
  return (
    <AuthenticatedLayout>
      <Head title="Preview Surat Izin Kerja dan LK3" />

      <div className="space-y-4">
        <h1 className="text-xl font-semibold">
          Preview Surat Izin Kerja dan LK3
        </h1>

        <div className="flex gap-3">
          <a
            href={surat.pdf_url}
            download
            className="bg-blue-700 px-4 py-2 rounded-full text-white font-semibold"
          >
            Download PDF
          </a>

         <a
            href={route('filing.surat.downloadBRM1-docx', surat.id)}
            className="bg-blue-700 text-white px-4 py-2 rounded-full font-semibold"
          >
            Download Word
          </a>

          <button
            onClick={() => window.open(surat.pdf_url, '_blank')}
            className="bg-green-700 px-4 py-2 rounded-full text-white font-semibold"
          >
            Print
          </button>
        </div>

        <iframe
          src={surat.pdf_url}
          className="w-full h-[85vh] border rounded"
        />
      </div>
    </AuthenticatedLayout>
  )
}