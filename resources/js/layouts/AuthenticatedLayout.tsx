import { PropsWithChildren, ReactNode } from 'react'
import { Link, usePage } from '@inertiajs/react'
import Dropdown from '@/components/Dropdown'



export default function AuthenticatedLayout({
  header,
  children,
}: PropsWithChildren<{ header?: ReactNode }>) {
  const { auth } = usePage().props as any
  console.log('AUTH PROPS:', auth)
  

  return (
    <div className="min-h-screen flex bg-gray-100">
      {/* SIDEBAR */}
      <aside className="w-64 bg-gradient-to-b from-red-900 to-red-700 text-white flex flex-col">
        <div className="px-6 py-4 text-xl font-bold border-b border-red-800">
          Filing System
        </div>

        <nav className="flex-1 px-4 py-6 space-y-3">
          <Link href={route('dashboard')} className="block hover:underline">
            Dashboard
          </Link>
          <Link href={route('filing.surat.index')} className="block hover:underline">
            Manajemen Surat
          </Link>
          <Link href={route('filing.arsip.index')} className="block hover:underline">
            Arsip Surat
          </Link>
        </nav>
      </aside>

      {/* MAIN AREA */}
      <div className="flex-1 flex flex-col">
        {/* TOPBAR */}
        <header className="bg-gradient-to-r from-red-900 to-red-700 text-white px-8 py-5 flex justify-between items-center">
          <div>
            <h1 className="text-2xl font-bold">PT. Bumi Rekayasa Mandiri</h1>
            <p className="text-sm opacity-90">
              Precision Building. Sustainable Value.
            </p>
          </div>

          {/* USER DROPDOWN */}
          <Dropdown
            trigger={
              <button className="bg-green-700 px-4 py-2 rounded-full font-semibold">
                {auth.user.name}
              </button>
            }
          >
            <Dropdown.Link href={route('filing.profile.edit')}>
              Profile
            </Dropdown.Link>
            <Dropdown.Link href={route('logout')} method="post" as="button">
              Logout
            </Dropdown.Link>
          </Dropdown>
        </header>

        {/* PAGE CONTENT */}
        <main className="p-8">
          <div className="bg-white rounded-xl shadow p-6">
            {header && <div className="mb-4">{header}</div>}
            {children}
          </div>
        </main>
      </div>
    </div>
  )
}