import { SuratStatus } from "@/types/filing/surat"

const badgeMap: Record<SuratStatus, string> = {
  draft: 'badge',
  approved: 'badge badge-success',
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