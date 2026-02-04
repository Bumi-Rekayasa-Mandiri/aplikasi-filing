import { forwardRef, useEffect, useRef } from 'react'

interface Props extends React.InputHTMLAttributes<HTMLInputElement> {
  isFocused?: boolean
}

export default forwardRef<HTMLInputElement, Props>(
  ({ type = 'text', className = '', isFocused = false, ...props }, ref) => {
    const inputRef = useRef<HTMLInputElement>(null)

    useEffect(() => {
      if (isFocused) {
        inputRef.current?.focus()
      }
    }, [isFocused])

    return (
      <input
        {...props}            // isFocused SUDAH DIKELUARKAN
        ref={ref ?? inputRef}
        type={type}
        className={className}
      />
    )
  }
)