import { useEffect } from 'react'

interface ModalProps {
    show: boolean
    onClose: () => void
    children: React.ReactNode
}

export default function Modal({ show, onClose, children }: ModalProps) {
    useEffect(() => {
        const closeOnEscape = (e: KeyboardEvent) => {
            if (e.key === 'Escape') {
                onClose()
            }
        }

        document.addEventListener('keydown', closeOnEscape)

        return () => {
            document.removeEventListener('keydown', closeOnEscape)
        }
    }, [onClose])

    if (!show) {
        return null
    }

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center">
            <div
                className="absolute inset-0 bg-black opacity-50"
                onClick={onClose}
            />

            <div className="relative bg-white rounded-lg shadow-lg z-10">
                {children}
            </div>
        </div>
    )
}