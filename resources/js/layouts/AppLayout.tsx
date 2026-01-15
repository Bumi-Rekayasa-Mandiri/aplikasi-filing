import { PropsWithChildren } from 'react'
import { Head } from '@inertiajs/react'

export default function AppLayout({
  children,
  title = 'Dashboard',
}: PropsWithChildren<{ title?: string }>) {
  return (
    <>
      <Head title={title} />
      <div className="min-h-screen bg-gray-100">
        <main className="p-6">{children}</main>
      </div>
    </>
  )
}