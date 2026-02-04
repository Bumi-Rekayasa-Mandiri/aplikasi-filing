import { forwardRef } from 'react'

type Props = {
    message?: string
    className?: string
}

const InputError = forwardRef<HTMLParagraphElement, Props>(
    ({ message, className = '' }, ref) => {
        if (!message) {
            return null
        }

        return (
            <p
                ref={ref}
                className={`text-sm text-red-600 dark:text-red-400 ${className}`}
            >
                {message}
            </p>
        )
    }
)

InputError.displayName = 'InputError'

export default InputError