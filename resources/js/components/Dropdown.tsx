import { ReactNode, useEffect, useRef, useState } from 'react'
import { Link } from '@inertiajs/react'

interface DropdownProps {
    align?: 'left' | 'right'
    width?: '48'
    trigger: ReactNode
    children: ReactNode
}

export default function Dropdown({
    align = 'right',
    width = '48',
    trigger,
    children,
}: DropdownProps) {
    const [open, setOpen] = useState(false)
    const dropdownRef = useRef<HTMLDivElement>(null)

    useEffect(() => {
        const closeOnEscape = (e: KeyboardEvent) => {
            if (e.key === 'Escape') setOpen(false)
        }

        document.addEventListener('keydown', closeOnEscape)
        return () => document.removeEventListener('keydown', closeOnEscape)
    }, [])

    useEffect(() => {
        const closeOnClickOutside = (e: MouseEvent) => {
            if (
                dropdownRef.current &&
                !dropdownRef.current.contains(e.target as Node)
            ) {
                setOpen(false)
            }
        }

        document.addEventListener('mousedown', closeOnClickOutside)
        return () =>
            document.removeEventListener('mousedown', closeOnClickOutside)
    }, [])

    let alignmentClasses = 'origin-top-right right-0'
    if (align === 'left') {
        alignmentClasses = 'origin-top-left left-0'
    }

    let widthClasses = 'w-48'
    if (width === '48') {
        widthClasses = 'w-48'
    }

    return (
        <div className="relative" ref={dropdownRef}>
            <div onClick={() => setOpen((prev) => !prev)}>
                {trigger}
            </div>

            {open && (
                <div
                    className={`absolute z-50 mt-2 rounded-md shadow-lg ${alignmentClasses} ${widthClasses}`}
                >
                    <div className="rounded-md ring-1 ring-black ring-opacity-5 bg-white py-1">
                        {children}
                    </div>
                </div>
            )}
        </div>
    )
}

/**
 * Dropdown Link
 */
interface DropdownLinkProps {
    href: string
    method?: 'get' | 'post' | 'put' | 'patch' | 'delete'
    as?: 'a' | 'button'
    children: ReactNode
}

Dropdown.Link = function DropdownLink({
    href,
    method = 'get',
    as = 'a',
    children,
}: DropdownLinkProps) {
    return (
        <Link
            href={href}
            method={method}
            as={as}
            className="block w-full px-4 py-2 text-start text-sm text-gray-700 hover:bg-gray-100 focus:outline-none transition"
        >
            {children}
        </Link>
    )
}