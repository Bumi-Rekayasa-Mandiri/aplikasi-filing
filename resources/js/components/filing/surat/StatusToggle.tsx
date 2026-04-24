// resources/js/components/filing/surat/StatusToggle.tsx
import { router } from '@inertiajs/react'
import { useState } from 'react'
import '../../../../css/status-toggle.css'

type Props = {
  suratId: number
  status: 'draft' | 'approved'
  canApprove: boolean
}

export default function StatusToggle({ suratId, status, canApprove }: Props) {
  const isApproved = status === 'approved'
  const [showConfirm, setShowConfirm] = useState(false)
  const [processing, setProcessing] = useState(false)

  // Tidak tampilkan toggle jika user tidak punya hak
  if (!canApprove) return null

  const handleToggle = () => {
    if (isApproved) {
      // Langsung revert ke draft tanpa konfirmasi
      setProcessing(true)
      router.patch(
        route('filing.surat.revert-draft', suratId),
        {},
        { onFinish: () => setProcessing(false) }
      )
    } else {
      // Tampilkan dialog konfirmasi sebelum approve
      setShowConfirm(true)
    }
  }

  const handleConfirmApprove = () => {
    setProcessing(true)
    setShowConfirm(false)
    router.patch(
      route('filing.surat.approve', suratId),
      {},
      { onFinish: () => setProcessing(false) }
    )
  }

  return (
    <>
      <div className="status-toggle-wrap">
        <span className="status-toggle-label">Status surat</span>
        <div className="status-toggle-row">
          <button
            type="button"
            className={`status-toggle-track ${isApproved ? 'is-approved' : 'is-draft'} ${processing ? 'is-processing' : ''}`}
            onClick={handleToggle}
            disabled={processing}
            aria-label={isApproved ? 'Kembalikan ke draft' : 'Approve surat'}
          >
            <span className="status-toggle-thumb" />
          </button>
          <span className={`status-toggle-text ${isApproved ? 'text-approved' : 'text-draft'}`}>
            {processing ? 'Memproses…' : isApproved ? 'Approved' : 'Draft'}
          </span>
        </div>
      </div>

      {/* Dialog konfirmasi */}
      {showConfirm && (
        <div className="confirm-backdrop">
          <div className="confirm-dialog">
            <div className="confirm-icon">
              <svg width="22" height="22" viewBox="0 0 16 16" fill="none">
                <path d="M3 8l3.5 3.5L13 4" stroke="#27500A" strokeWidth="1.6"
                  strokeLinecap="round" strokeLinejoin="round"/>
              </svg>
            </div>
            <div className="confirm-title">Approve surat ini?</div>
            <div className="confirm-desc">
              Setelah diapprove, surat tidak bisa diedit. Pastikan semua data sudah benar.
            </div>
            <div className="confirm-actions">
              <button
                type="button"
                className="confirm-btn-cancel"
                onClick={() => setShowConfirm(false)}
              >
                Batal
              </button>
              <button
                type="button"
                className="confirm-btn-approve"
                onClick={handleConfirmApprove}
              >
                Ya, approve
              </button>
            </div>
          </div>
        </div>
      )}
    </>
  )
}