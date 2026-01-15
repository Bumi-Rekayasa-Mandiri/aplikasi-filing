export default function SuratStatusBadge({ status }: { status: string }) {
  return (
    <span
      className={`badge ${
        status === 'final' ? 'badge-success' : 'badge-warning'
      }`}
    >
      {status.toUpperCase()}
    </span>
  )
}