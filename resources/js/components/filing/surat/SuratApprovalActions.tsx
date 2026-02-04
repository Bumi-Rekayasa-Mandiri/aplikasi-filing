import { router, usePage } from '@inertiajs/react'
import { Surat } from '@/types/filing/surat'

interface Props {
    surat: Surat
}

export default function SuratApprovalActions({ surat }: Props)
{

    const { props } = usePage()
    const can = props.auth.can

    const submit = () => router.post(route('filing.surat.submit', surat.id))
    const approve = () => router.post(route('filing.surat.approve', surat.id))
    const reject = () => router.post(route('filing.surat.reject', surat.id))

    return (
        <div className="flex gap-2">
            {surat.status === 'draft' && can?.submit && (
                <button className="btn btn-xs btn-primary" onClick={submit}>
                    Submit
                </button>
            )}
            {surat.status === 'submitted' && can?.approve && (
                <>
                <button className="btn btn-xs btn-success" onClick={approve}>
                    Approve
                </button>
                <button className="btn btn-xs btn-error" onClick={reject}>
                    Reject
                </button>
                </>
            )}
        </div>
    )
}