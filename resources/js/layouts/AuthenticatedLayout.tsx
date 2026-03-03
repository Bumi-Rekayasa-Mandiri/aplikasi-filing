import { PropsWithChildren, ReactNode, useState, useEffect } from 'react'
import { Link, usePage } from '@inertiajs/react'
import Dropdown from '@/components/Dropdown'
import { Home, FileText, Archive, Award, Shield } from 'lucide-react'
import AppLogo from '@/components/AppLogo'


export default function AuthenticatedLayout({
  header,
  children,
}: PropsWithChildren<{ header?: ReactNode }>) {
  const [SidebarOpen, setSidebarOpen] = useState(true);

  useEffect(() => { const saved = localStorage.getItem('sidebarOpen');
    if (saved !== null) {
      setSidebarOpen(saved === 'true');
    }
  }, []);

  useEffect(() => {
    localStorage.setItem('sidebarOpen', SidebarOpen.toString());
  }, [SidebarOpen]);

  const { auth } = usePage().props as any
  console.log('AUTH PROPS:', auth)
  

  return (
    <div className="min-h-screen flex bg-gray-100">
      {/* SIDEBAR */}
      
      <aside className={`
          bg-gradient-to-b from-red-900 to-red-700 text-white
          flex flex-col
          transition-all duration-300
          ${SidebarOpen ? 'w-64' : 'w-20'}
        `}
      >

      <div className="flex items-center gap-3 px-4 py-3">
        <button onClick={() => setSidebarOpen(!SidebarOpen)} className="btn btn-sm bg-green-700 px-4 py-2 rounded text-white font-semibold mt-2">
        {SidebarOpen}
          ☰
        </button>
        {SidebarOpen && <span className="text-lg font-bold mt-2">Filing System</span>}
      </div>

        <nav className="flex-1 px-4 py-6 space-y-3">
          <Link href={route('dashboard')} className="flex items-center gap-3">
            <Home size={20} className='ml-3' />            
            {SidebarOpen && <span className="text-lg font-bold">Dashboard</span>}
          </Link>

          <Link href={route('filing.surat.index')} className="flex items-center gap-3">
          <FileText size={20} className='ml-3' /> {SidebarOpen && <span className="text-lg font-bold">Manajemen Surat</span>}
          </Link>

          <Link href={route('filing.arsip.index')} className="flex items-center gap-3">
          <Archive size={20} className='ml-3' />{SidebarOpen && <span className="text-lg font-bold">Arsip Surat</span>}
          </Link>

          <Link href={route('filing.sertifikat.index')} className="flex items-center gap-3">
          <Award size={20} className='ml-3' /> {SidebarOpen && <span className="text-lg font-bold">Arsip Sertifikat</span>}
          </Link>

          <Link href={route('roles.index')} className="flex items-center gap-3">
          <Shield size={20} className='ml-3' /> {SidebarOpen && <span className="text-lg font-bold">Super Admin</span>}
          </Link>

        </nav>
      </aside>

      {/* MAIN AREA */}
      <div className={`flex-1 flex flex-col transition-all duration-300`}>
        {/* TOPBAR */}
        <header className="bg-gradient-to-r from-red-900 to-red-700 text-white px-8 py-5 flex justify-between items-center">
        <div className="flex flex-col">
          <AppLogo />
        </div>


          {/* USER DROPDOWN */}
          <Dropdown
            trigger={
              <button className="bg-green-700 px-4 py-2 rounded-full font-semibold">
                {auth.user.name}
              </button>
            }
          >
            <Dropdown.Link href={route('profile.edit')}>
              Profile
            </Dropdown.Link>
            <Dropdown.Link href={route('logout')} method="post" as="button" >
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