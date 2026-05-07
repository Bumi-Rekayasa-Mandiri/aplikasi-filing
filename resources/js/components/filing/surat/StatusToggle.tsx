// resources/js/components/filing/surat/StatusToggle.tsx
import { router } from '@inertiajs/react'
import { useState } from 'react'
import '../../../../css/status-toggle.css'

type Props = {
  suratId: number
  status: 'draft' | 'approved'
  canApprove: boolean
}

type confirmAction = 'approve' | 'revert' | null  

export default function StatusToggle({ suratId, status, canApprove }: Props) {
  const isApproved = status === 'approved'
  const [confirmAction, setConfirmAction] = useState<confirmAction>(null)
  const [processing, setProcessing] = useState(false)

  // Tidak tampilkan toggle jika user tidak punya hak
  if (!canApprove) return null

  const handleToggle = () => {
    setConfirmAction(isApproved ? 'revert' : 'approve')
  }

  const handleConfirm = () => {
    if (!confirmAction) return

    const routeName =
      confirmAction === 'approve'
        ? 'filing.surat.approve'
        : 'filing.surat.revert-draft'

    setProcessing(true)
    setConfirmAction(null)

    router.patch(
      route(routeName, suratId),
      {},
      { onFinish: () => setProcessing(false) }
    )
  }

  const handleCancel = () => setConfirmAction(null)

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
      {confirmAction && (
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
                onClick={handleCancel}
              >
                Batal
              </button>
              <button
                type="button"
                className="confirm-btn-approve"
                onClick={handleConfirm}
              >
                Ya, approve
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Dialog konfirmasi — REVERT */}
      {confirmAction === 'revert' && (
        <div className="confirm-backdrop">
          <div className="confirm-dialog">
            <div
              className="confirm-icon"
              style={{ background: '#FEF3C7', borderColor: '#FDE68A' }}
            >
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                stroke="#92400E" strokeWidth="2"
                strokeLinecap="round" strokeLinejoin="round">
                <polyline points="1 4 1 10 7 10" />
                <path d="M3.51 15a9 9 0 1 0 .49-3.47" />
              </svg>
            </div>
            <div className="confirm-title">Revert ke draft?</div>
            <div className="confirm-desc">
              Surat akan kembali ke status <strong>draft</strong> sehingga bisa diedit ulang.
              Lanjutkan?
            </div>
            <div className="confirm-actions">
              <button type="button" className="confirm-btn-cancel" onClick={handleCancel}>
                Batal
              </button>
              <button
                type="button"
                className="confirm-btn-approve"
                style={{ background: '#D97706' }}
                onMouseEnter={(e) => (e.currentTarget.style.background = '#B45309')}
                onMouseLeave={(e) => (e.currentTarget.style.background = '#D97706')}
                onClick={handleConfirm}
              >
                Ya, revert
              </button>
            </div>
          </div>
        </div>
      )}
    </>
  )
}