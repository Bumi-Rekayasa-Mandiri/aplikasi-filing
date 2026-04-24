import InputError from '@/components/InputError'
import Modal from '@/components/Modal'
import { useForm } from '@inertiajs/react'
import { FormEventHandler, useRef, useState } from 'react'
import '../../../../css/create-surat.css'

interface Props { className?: string }

export default function DeleteUserForm({ className = '' }: Props) {
  const [confirmingUserDeletion, setConfirmingUserDeletion] = useState(false)
  const passwordInput = useRef<HTMLInputElement>(null)

  const { data, setData, delete: destroy, processing, reset, errors, clearErrors } = useForm({ password: '' })

  const deleteUser: FormEventHandler = (e) => {
    e.preventDefault()
    destroy(route('filing.profile.destroy'), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
      onError: () => passwordInput.current?.focus(),
      onFinish: () => reset(),
    })
  }

  const closeModal = () => {
    setConfirmingUserDeletion(false)
    clearErrors()
    reset()
  }

  return (
    <section className={`space-y-6 ${className}`}>
      <div className="form-card-header" style={{ marginBottom: '1.25rem', paddingBottom: '0.75rem', borderBottom: '0.5px solid rgba(0,0,0,0.07)' }}>
        <div className="form-icon" style={{ background: '#FCEBEB' }}>
          <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
            <path d="M2 4h12M5 4V2h6v2M6 7v5M10 7v5M3 4l1 9a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1l1-9" stroke="#791F1F" strokeWidth="1.2" strokeLinecap="round" strokeLinejoin="round"/>
          </svg>
        </div>
        <div>
          <div className="form-card-title" style={{ color: '#791F1F' }}>Hapus akun</div>
          <div style={{ fontSize: '12px', color: '#9ca3af', marginTop: '2px' }}>
            Setelah dihapus, semua data akun akan hilang permanen
          </div>
        </div>
      </div>

      <button
        onClick={() => setConfirmingUserDeletion(true)}
        style={{
          padding: '8px 16px', borderRadius: '8px', border: 'none',
          background: '#FCEBEB', color: '#791F1F', fontSize: '13px',
          fontWeight: 500, cursor: 'pointer', fontFamily: 'inherit',
          transition: 'background 0.12s',
        }}
        onMouseOver={e => (e.currentTarget.style.background = '#F7C1C1')}
        onMouseOut={e => (e.currentTarget.style.background = '#FCEBEB')}
      >
        Hapus akun
      </button>

      <Modal show={confirmingUserDeletion} onClose={closeModal}>
        <form onSubmit={deleteUser} style={{ padding: '1.5rem', display: 'flex', flexDirection: 'column', gap: '16px' }}>
          <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
            <div className="form-icon" style={{ background: '#FCEBEB', width: '32px', height: '32px', flexShrink: 0 }}>
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <path d="M8 5v4M8 11v.5" stroke="#791F1F" strokeWidth="1.5" strokeLinecap="round"/>
                <circle cx="8" cy="8" r="6.5" stroke="#791F1F" strokeWidth="1.2"/>
              </svg>
            </div>
            <div>
              <div style={{ fontSize: '14px', fontWeight: 500, color: '#111827' }}>Yakin ingin menghapus akun?</div>
              <div style={{ fontSize: '12px', color: '#9ca3af', marginTop: '2px' }}>Tindakan ini tidak bisa dibatalkan.</div>
            </div>
          </div>

          <div className="field">
            <label className="field-label">Konfirmasi dengan password kamu</label>
            <input type="password" className="field-input" ref={passwordInput}
              value={data.password} placeholder="Password"
              onChange={e => setData('password', e.target.value)} autoFocus />
            <InputError message={errors.password} className="mt-2" />
          </div>

          <div style={{ display: 'flex', justifyContent: 'flex-end', gap: '8px' }}>
            <button type="button" onClick={closeModal}
              style={{ padding: '8px 16px', borderRadius: '8px', border: '0.5px solid rgba(0,0,0,0.15)', background: '#fff', color: '#6b7280', fontSize: '13px', cursor: 'pointer', fontFamily: 'inherit' }}>
              Batal
            </button>
            <button type="submit" disabled={processing}
              style={{ padding: '8px 16px', borderRadius: '8px', border: 'none', background: '#791F1F', color: '#fff', fontSize: '13px', fontWeight: 500, cursor: 'pointer', fontFamily: 'inherit', opacity: processing ? 0.6 : 1 }}>
              {processing ? 'Menghapus…' : 'Ya, hapus akun'}
            </button>
          </div>
        </form>
      </Modal>
    </section>
  )
}