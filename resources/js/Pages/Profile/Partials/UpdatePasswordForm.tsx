import InputError from '@/components/InputError'
import { Transition } from '@headlessui/react'
import { useForm } from '@inertiajs/react'
import { FormEventHandler, useRef } from 'react'
import '../../../../css/create-surat.css'

interface Props { className?: string }

export default function UpdatePasswordForm({ className = '' }: Props) {
  const passwordInput = useRef<HTMLInputElement>(null)
  const currentPasswordInput = useRef<HTMLInputElement>(null)

  const { data, setData, errors, put, reset, processing, recentlySuccessful } = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
  })

  const updatePassword: FormEventHandler = (e) => {
    e.preventDefault()
    put(route('password.update'), {
      preserveScroll: true,
      onSuccess: () => reset(),
      onError: (formErrors) => {
        if (formErrors.password) {
          reset('password', 'password_confirmation')
          passwordInput.current?.focus()
        }
        if (formErrors.current_password) {
          reset('current_password')
          currentPasswordInput.current?.focus()
        }
      },
    })
  }

  return (
    <section className={className}>
      <div className="form-card-header" style={{ marginBottom: '1.25rem', paddingBottom: '0.75rem', borderBottom: '0.5px solid rgba(0,0,0,0.07)' }}>
        <div className="form-icon icon-amber">
          <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
            <rect x="3" y="7" width="10" height="7" rx="2" stroke="#854F0B" strokeWidth="1.2"/>
            <path d="M5 7V5a3 3 0 0 1 6 0v2" stroke="#854F0B" strokeWidth="1.2" strokeLinecap="round"/>
          </svg>
        </div>
        <div>
          <div className="form-card-title">Ubah password</div>
          <div style={{ fontSize: '12px', color: '#9ca3af', marginTop: '2px' }}>
            Gunakan password yang panjang dan acak agar akun tetap aman
          </div>
        </div>
      </div>

      <form onSubmit={updatePassword} style={{ display: 'flex', flexDirection: 'column', gap: '14px' }}>
        <div className="field">
          <label className="field-label">Password saat ini</label>
          <input type="password" className="field-input" ref={currentPasswordInput}
            value={data.current_password} autoComplete="current-password"
            onChange={e => setData('current_password', e.target.value)} />
          <InputError message={errors.current_password} className="mt-2" />
        </div>

        <div className="field">
          <label className="field-label">Password baru</label>
          <input type="password" className="field-input" ref={passwordInput}
            value={data.password} autoComplete="new-password"
            onChange={e => setData('password', e.target.value)} />
          <InputError message={errors.password} className="mt-2" />
        </div>

        <div className="field">
          <label className="field-label">Konfirmasi password baru</label>
          <input type="password" className="field-input"
            value={data.password_confirmation} autoComplete="new-password"
            onChange={e => setData('password_confirmation', e.target.value)} />
          <InputError message={errors.password_confirmation} className="mt-2" />
        </div>

        <div style={{ display: 'flex', alignItems: 'center', gap: '12px' }}>
          <button type="submit" className="btn-submit"
            style={{ width: 'auto', padding: '8px 20px', fontSize: '13px' }}
            disabled={processing}>
            {processing ? 'Menyimpan…' : 'Simpan'}
          </button>
          <Transition show={recentlySuccessful} enter="transition ease-in-out"
            enterFrom="opacity-0" leave="transition ease-in-out" leaveTo="opacity-0">
            <p style={{ fontSize: '12px', color: '#27500A' }}>✓ Tersimpan</p>
          </Transition>
        </div>
      </form>
    </section>
  )
}