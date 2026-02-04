import { Link } from '@inertiajs/react'
import { ReactNode } from 'react'

interface ResponsiveNavLinkProps {
    href: string
    method?: 'get' | 'post' | 'put' | 'patch' | 'delete'
    as?: 'a' | 'button'
    active?: boolean
    children: ReactNode
}

export default function ResponsiveNavLink({
    href,
    method = 'get',
    as = 'a',
    active = false,
    children,
}: ResponsiveNavLinkProps) {
    return (
        <Link
            href={href}
            method={method}
            as={as}
            className={
                'block w-full ps-3 pe-4 py-2 border-l-4 text-start text-base font-medium focus:outline-none transition duration-150 ease-in-out ' +
                (active
                    ? 'border-indigo-400 text-indigo-700 bg-indigo-50 focus:text-indigo-800 focus:bg-indigo-100 focus:border-indigo-700'
                    : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300')
            }
        >
            {children}
        </Link>
    )
}