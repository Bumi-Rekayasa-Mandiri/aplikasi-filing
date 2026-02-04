import { LabelHTMLAttributes } from 'react'

type Props = LabelHTMLAttributes<HTMLLabelElement> & {
    value?: string
}

export default function InputLabel({
    value,
    children,
    ...props
}: Props) {
    return (
        <label {...props}>
            {value ? value : children}
        </label>
    )
}