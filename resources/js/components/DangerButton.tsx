import { ButtonHTMLAttributes, PropsWithChildren } from 'react'

type Props = PropsWithChildren<
    ButtonHTMLAttributes<HTMLButtonElement> & {
        className?: string
        disabled?: boolean
    }
>

export default function DangerButton({
    className = '',
    disabled,
    children,
    ...props
}: Props) {
    return (
        <button
            {...props}
            className={className}
            disabled={disabled}
        >
            {children}
        </button>
    )
}