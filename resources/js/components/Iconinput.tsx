import { Eye, EyeOff, LucideIcon } from 'lucide-react'
import { forwardRef, InputHTMLAttributes, useState } from 'react'

interface IconInputProps extends Omit<InputHTMLAttributes<HTMLInputElement>, 'type'> {
    icon: LucideIcon
    type?: string
    error?: boolean
    /**
     * Tampilkan tombol mata untuk show/hide password.
     * Default: true (auto aktif kalau type="password").
     */
    showPasswordToggle?: boolean
}

/**
 * Input dengan icon prefix di kiri, dan tombol show/hide otomatis
 * di kanan kalau type="password". Match warna maroon (red-800) untuk
 * focus state, dan red-300 untuk error border.
 */
const IconInput = forwardRef<HTMLInputElement, IconInputProps>(
    (
        {
            icon: Icon,
            error = false,
            showPasswordToggle = true,
            type = 'text',
            className = '',
            ...props
        },
        ref,
    ) => {
        const [show, setShow] = useState(false)
        const isPassword = type === 'password'
        const inputType = isPassword && show ? 'text' : type
        const hasToggle = isPassword && showPasswordToggle

        return (
            <div className="relative">
                {/* Icon prefix */}
                <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                    <Icon size={18} strokeWidth={1.75} />
                </div>

                <input
                    ref={ref}
                    type={inputType}
                    className={[
                        'block w-full rounded-lg border bg-white py-2.5 pl-10 text-sm text-gray-900',
                        'transition-colors duration-150',
                        'placeholder:text-gray-400',
                        'focus:outline-none focus:ring-2 focus:ring-offset-0',
                        hasToggle ? 'pr-10' : 'pr-3.5',
                        error
                            ? 'border-red-300 focus:border-red-500 focus:ring-red-100'
                            : 'border-gray-200 focus:border-red-800 focus:ring-red-100/60',
                        className,
                    ].join(' ')}
                    {...props}
                />

                {/* Show/hide password toggle */}
                {hasToggle && (
                    <button
                        type="button"
                        onClick={() => setShow(s => !s)}
                        className="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 transition-colors hover:text-gray-700 focus:outline-none focus:text-gray-700"
                        tabIndex={-1}
                        aria-label={show ? 'Hide password' : 'Show password'}
                    >
                        {show ? (
                            <EyeOff size={18} strokeWidth={1.75} />
                        ) : (
                            <Eye size={18} strokeWidth={1.75} />
                        )}
                    </button>
                )}
            </div>
        )
    },
)

IconInput.displayName = 'IconInput'

export default IconInput