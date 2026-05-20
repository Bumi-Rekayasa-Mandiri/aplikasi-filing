import ApplicationLogo from '@/components/ApplicationLogo'
import { Link } from '@inertiajs/react'
import { ReactNode } from 'react'

interface Props {
    children: ReactNode
}

export default function GuestLayout({ children }: Props) {
    return (
        <div className="relative flex min-h-screen items-center justify-center bg-stone-50 px-4 py-12 font-sans antialiased">
            {/* Atmospheric backdrop: dua color blob subtle (maroon + emerald) + grid pattern */}
            <div className="pointer-events-none absolute inset-0 overflow-hidden">
                <div className="absolute -top-40 -right-40 h-96 w-96 rounded-full bg-red-100/40 blur-3xl" />
                <div className="absolute -bottom-40 -left-40 h-96 w-96 rounded-full bg-emerald-100/40 blur-3xl" />
                <div className="absolute inset-0 bg-[linear-gradient(rgba(0,0,0,0.018)_1px,transparent_1px),linear-gradient(90deg,rgba(0,0,0,0.018)_1px,transparent_1px)] bg-[size:24px_24px]" />
            </div>

            <div className="relative w-full max-w-md">
                {/* Card */}
                <div className="overflow-hidden rounded-2xl border border-gray-200/70 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
                    {/* Header: logo + brand name + tagline + gradient divider */}
                    <div className="px-8 pb-6 pt-8">
                        <Link href="/" className="block focus:outline-none">
                            <div className="flex items-center gap-3">
                                <ApplicationLogo className="h-50 w-100 shrink-0 text-emerald-700" />
                            </div>
                        </Link>
                        {/* Gradient divider — signature hybrid theme (maroon → emerald) */}
                        <div className="mt-5 h-px w-full bg-gradient-to-r from-red-800/40 via-gray-200 to-emerald-700/40" />
                    </div>

                    {/* Form content area */}
                    <div className="px-8 pb-8 pt-2">{children}</div>
                </div>

                {/* Footer note */}
                <p className="mt-6 text-center text-xs text-gray-400">
                    Secure access to your filing management portal
                </p>
            </div>
        </div>
    )
}