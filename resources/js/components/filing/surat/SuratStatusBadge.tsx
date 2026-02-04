import { SuratStatus } from "@/types/filing/surat"

const badgeMap: Record<SuratStatus, string> = {
  draft: 'badge',
  submitted: 'badge badge-warning',
  approved: 'badge badge-success',
  rejected: 'badge badge-danger',
}

export default function SuratStatusBadge({ status, }: { status: SuratStatus }) {
  return (
    <span
      className={badgeMap[status]}
    >
      {status.toUpperCase()}
    </span>
  )
}