import InputError from '@/components/InputError'
import { Transition } from '@headlessui/react'
import { Link, useForm, usePage } from '@inertiajs/react'
import '../../../../css/create-surat.css'

interface Props {
  mustVerifyEmail: boolean
  status?: string
  className?: string
}

// ✅ Definisikan tipe sendiri agar auth.user tidak error
interface AuthPageProps {
  auth: { user: { name: string; email: string; email_verified_at: string | null } }
  [key: string]: unknown
}

export default function UpdateProfileInformationForm({ mustVerifyEmail, status, className = '' }: Props) {
  const { auth } = usePage<AuthPageProps>().props
  const user = auth.user

  const { data, setData, patch, errors, processing, recentlySuccessful } = useForm({
    name: user.name,
    email: user.email,
  })

  const submit = (e: React.FormEvent) => {
    e.preventDefault()
    patch(route('filing.profile.update'), { preserveScroll: true })
  }

  return (
    <section className={className}>
      <div className="form-card-header" style={{ marginBottom: '1.25rem', paddingBottom: '0.75rem', borderBottom: '0.5px solid rgba(0,0,0,0.07)' }}>
        <div className="form-icon icon-blue">
          <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
            <circle cx="8" cy="5" r="3" stroke="#185FA5" strokeWidth="1.2"/>
            <path d="M2 14c0-3 2.5-5 6-5s6 2 6 5" stroke="#185FA5" strokeWidth="1.2" strokeLinecap="round"/>
          </svg>
        </div>
        <div>
          <div className="form-card-title">Informasi profil</div>
          <div style={{ fontSize: '12px', color: '#9ca3af', marginTop: '2px' }}>
            Perbarui nama dan alamat email akun kamu
          </div>
        </div>
      </div>

      <form onSubmit={submit} style={{ display: 'flex', flexDirection: 'column', gap: '14px' }}>
        <div className="field">
          <label className="field-label">Nama</label>
          <input className="field-input" value={data.name} autoFocus autoComplete="name"
            onChange={e => setData('name', e.target.value)} />
          <InputError message={errors.name} className="mt-2" />
        </div>

        <div className="field">
          <label className="field-label">Email</label>
          <input type="email" className="field-input" value={data.email} autoComplete="username"
            onChange={e => setData('email', e.target.value)} />
          <InputError message={errors.email} className="mt-2" />
        </div>

        {mustVerifyEmail && user.email_verified_at === null && (
          <div style={{ background: '#FAEEDA', borderRadius: '8px', padding: '10px 14px', fontSize: '12px', color: '#633806' }}>
            Email kamu belum terverifikasi.{' '}
            <Link href={route('verification.send')} method="post" as="button"
              style={{ textDecoration: 'underline', color: '#633806', fontWeight: 500 }}>
              Kirim ulang email verifikasi
            </Link>
            {status === 'verification-link-sent' && (
              <div style={{ marginTop: '6px', color: '#27500A', fontWeight: 500 }}>
                Link verifikasi baru telah dikirim ke email kamu.
              </div>
            )}
          </div>
        )}

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