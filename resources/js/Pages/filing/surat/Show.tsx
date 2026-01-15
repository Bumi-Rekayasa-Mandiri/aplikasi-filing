import { useForm } from '@inertiajs/react';

export default function UploadPdfForm({ surat }: { surat: any }) {
    const { data, setData, post, processing, errors } = useForm({
        pdf: null as File | null,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();

        post(route('filing.surat.upload-pdf', surat.id), {
            forceFormData: true,
        });
    };

return (
    <>
        <form onSubmit={submit} className="space-y-4">
            <input
                type="file"
                accept="application/pdf"
                onChange={(e) =>
                    setData('pdf', e.target.files ? e.target.files[0] : null)
                }
            />

            {errors.pdf && (
                <div className="text-red-600 text-sm">{errors.pdf}</div>
            )}

            <button
                type="submit"
                disabled={processing}
                className="px-4 py-2 bg-blue-600 text-white rounded"
            >
                {processing ? 'Uploading...' : 'Upload PDF'}
            </button>
        </form>

        {surat.pdf_url && (
            <div className="mt-6">
                <iframe
                    src={surat.pdf_url}
                    className="w-full h-[600px] border"
                />
            </div>
        )}
    </>
);
}