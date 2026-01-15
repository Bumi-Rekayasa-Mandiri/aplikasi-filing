import { Link } from '@inertiajs/react'

interface Props {
  links: {
    url: string | null
    label: string
    active: boolean
  }[]
}

export default function Pagination({ links }: Props) {
  if (links.length <= 3) return null

  return (
    <div className="flex gap-1 mt-6">
      {links.map((link, i) => (
        <Link
          key={i}
          href={link.url ?? '#'}
          className={`px-3 py-1 rounded text-sm ${
            link.active
              ? 'bg-blue-600 text-white'
              : 'bg-gray-200 hover:bg-gray-300'
          } ${!link.url && 'opacity-50 pointer-events-none'}`}
          dangerouslySetInnerHTML={{ __html: link.label }}
        />
      ))}
    </div>
  )
}