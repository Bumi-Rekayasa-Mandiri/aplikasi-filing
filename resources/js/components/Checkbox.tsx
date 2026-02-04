import { forwardRef, InputHTMLAttributes } from 'react'

interface Props extends InputHTMLAttributes<HTMLInputElement> {}

const Checkbox = forwardRef<HTMLInputElement, Props>(
    ({ className = '', ...props }, ref) => {
        return (
            <input
                {...props}
                type="checkbox"
                className={className}
                ref={ref}
            />
        )
    }
)

Checkbox.displayName = 'Checkbox'

export default Checkbox