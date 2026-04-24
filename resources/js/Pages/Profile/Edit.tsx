import AuthenticatedLayout from '@/layouts/AuthenticatedLayout'
import { Head } from '@inertiajs/react'
import DeleteUserForm from './Partials/DeleteUserForm'
import UpdatePasswordForm from './Partials/UpdatePasswordForm'
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm'
import '../../../css/create-surat.css'

interface Props {
  mustVerifyEmail: boolean
  status?: string
}

export default function Edit({ mustVerifyEmail, status }: Props) {
  return (
    <AuthenticatedLayout>
      <Head title="Profil" />

      <div className="create-form">

        <div className="create-form-heading">
          <h1 className="create-form-title">Pengaturan akun</h1>
          <p className="create-form-subtitle">Kelola informasi profil dan keamanan akun kamu</p>
        </div>

        <div className="form-card">
          <UpdateProfileInformationForm
            mustVerifyEmail={mustVerifyEmail}
            status={status}
          />
        </div>

        <div className="form-card">
          <UpdatePasswordForm />
        </div>

        <div className="form-card">
          <DeleteUserForm />
        </div>

      </div>
    </AuthenticatedLayout>
  )
}