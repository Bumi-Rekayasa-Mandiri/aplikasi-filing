import { PropsWithChildren } from 'react'
import { Head } from '@inertiajs/react'
import Sidebar from '@/components/Sidebar'

interface AppLayoutProps {
    title?: string
}

export default function AppLayout({
    children,
    title = 'Dashboard',
}: PropsWithChildren<AppLayoutProps>) {
    return (
        <>
            <Head title={title} />

            <div className="min-h-screen bg-gray-100 flex">
                {/* Sidebar */}
                <aside className="w-64 bg-white border-r">
                    <Sidebar />
                </aside>

                {/* Main Content */}
                <main className="flex-1 p-6 overflow-y-auto">
                    {children}
                </main>
            </div>
        </>
    )
}