interface Props {
  active: boolean
  direction?: 'asc' | 'desc'
}

export default function SortIcon({ active, direction }: Props) {
  if (!active) {
    return <span className="ml-1 text-gray-400">⬍</span>
  }

  return direction === 'asc' ? (
    <span className="ml-1 text-gray-700">▲</span>
  ) : (
    <span className="ml-1 text-gray-700">▼</span>
  )
}