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

export default function PreviewSKP({ surat }: Props) {
  if (!surat?.pdf_url) {

    console.log("PDF URL:", surat.pdf_url)
      
    return (
      <AuthenticatedLayout>
        <Head title="Preview SKP BRM" />
        <p className="text-gray-500">PDF belum tersedia.</p>
      </AuthenticatedLayout>
    )
  }

  return (
    <AuthenticatedLayout>
      <Head title="Preview SKP BRM" />

      <div className="space-y-4">
        <h1 className="text-xl font-semibold">
          Preview Surat Pemberitahuan PHK
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
            href={route('filing.surat.downloadSKP-docx', surat.id)}
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
          className="w-full h-screen rounded"
        />
      </div>
    </AuthenticatedLayout>
  )
}
