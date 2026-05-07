import { createContext, useContext, type ReactNode, type ChangeEvent } from 'react'

type FormCtx = {
    data:     Record<string, any>
    setData:  (field: any, value: any) => void
    errors:   Record<string, string | undefined>
    disabled: boolean
}

const FormFieldContext = createContext<FormCtx | null>(null)

type ProviderProps = FormCtx & { children: ReactNode }

export function FormFieldProvider({
    data, setData, errors, disabled, children,
}: ProviderProps) {
    return (
        <FormFieldContext.Provider value={{ data, setData, errors, disabled }}>
            {children}
        </FormFieldContext.Provider>
    )
}

function useFormCtx(): FormCtx {
    const ctx = useContext(FormFieldContext)
    if (!ctx) {
        throw new Error('Field/TextArea harus dipakai di dalam <FormFieldProvider>')
    }
    return ctx
}

type FieldProps = {
    label:        string
    field:        string
    type?:        string
    placeholder?: string
    fullWidth?:   boolean
}

export function Field({
    label, field, type = 'text', placeholder = '', fullWidth = false,
}: FieldProps) {
    const { data, setData, errors, disabled } = useFormCtx()
    const value = (data[field] as string | undefined) ?? ''
    const error = errors[field]

    return (
        <div className={`field ${fullWidth ? 'col-span-2' : ''}`}>
            <label className="field-label">{label}</label>
            <input
                type={type}
                className="field-input"
                value={value}
                placeholder={placeholder}
                disabled={disabled}
                onChange={(e: ChangeEvent<HTMLInputElement>) =>
                    setData(field, e.target.value)
                }
            />
            {error && <span className="field-error">{error}</span>}
        </div>
    )
}

type TextAreaProps = {
    label:      string
    field:      string
    fullWidth?: boolean
    rows?:      number
}

export function TextArea({
    label, field, fullWidth = true, rows = 4,
}: TextAreaProps) {
    const { data, setData, errors, disabled } = useFormCtx()
    const value = (data[field] as string | undefined) ?? ''
    const error = errors[field]

    return (
        <div className={`field ${fullWidth ? 'col-span-2' : ''}`}>
            <label className="field-label">{label}</label>
            <textarea
                className="field-input"
                value={value}
                rows={rows}
                disabled={disabled}
                onChange={(e: ChangeEvent<HTMLTextAreaElement>) =>
                    setData(field, e.target.value)
                }
            />
            {error && <span className="field-error">{error}</span>}
        </div>
    )
}