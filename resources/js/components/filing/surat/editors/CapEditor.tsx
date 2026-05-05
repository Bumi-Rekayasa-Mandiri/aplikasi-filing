import FilePreviewSlot from './FilePreviewSlot'
 
interface Props {
    /** URL cap yang sudah tersimpan di server */
    existingCapUrl?: string | null
    /** File cap baru yang dipilih user */
    newCap?: File | null
    onChange: (file: File | null) => void
}
 
const IconCap = () => (
    <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
        <circle cx="8" cy="8" r="6" stroke="#0F6E56" strokeWidth="1.2"/>
        <circle cx="8" cy="8" r="3" stroke="#0F6E56" strokeWidth="1.2"/>
        <path d="M8 2v2M8 12v2M2 8h2M12 8h2"
              stroke="#0F6E56" strokeWidth="1.2" strokeLinecap="round"/>
    </svg>
)
 
export default function CapEditor({ existingCapUrl, newCap, onChange }: Props) {
    return (
        <div className="form-card">
            <div className="form-card-header">
                <div className="form-icon icon-teal"><IconCap /></div>
                <span className="form-card-title">Cap perusahaan</span>
            </div>
 
            <FilePreviewSlot
                existingUrl={existingCapUrl}
                newFile={newCap}
                onChange={onChange}
                label="Cap perusahaan"
                accept="image/*"
                hint="PNG transparan disarankan, maks. 2MB"
                maxHeight={140}
            />
        </div>
    )
}