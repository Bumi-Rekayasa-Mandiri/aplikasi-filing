import { Link } from "@inertiajs/react";


export default function Sidebar() {
    const baselinkClasses = 'block p-2 rounded transition-colors duration-200';
    const activeClasses = 'bg-blue-100 text-blue-700 font-semibold';
    const inactiveClasses = 'text-gray-700 hover:bg-gray-200';

    return (
        <aside className="w-64 bg-gray-100 p-4 min-h-screen">
            <div className="p-4 font-semibold text-lg">
                Filing System
            </div>

            <nav className="px-4 space-y-2">
                <Link href={route('dashboard')} className="block px-3 py-2 rounded hover:bg-gray-100">
                Dashboard</Link>

            <ul className="space-y-2">
                <li>
                <Link href={route('filing.surat.index')} className="block px-3 py-2 rounded hover:bg-gray-100">
                Manajemen Surat
                </Link> 
                </li>

                <li>
                    <Link href={route('filing.arsip.Index')} className="block px-3 py-2 rounded hover:bg-gray-100">
                    Arsip Surat
                    </Link>
                </li>
            </ul>
            </nav>
        </aside>
    )
}