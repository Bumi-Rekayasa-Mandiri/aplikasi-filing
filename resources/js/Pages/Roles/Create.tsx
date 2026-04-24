import AuthenticatedLayout from '@/layouts/AuthenticatedLayout'
import { useForm, usePage, Link } from '@inertiajs/react'
import { Head } from '@inertiajs/react'
import type { AppPageProps } from '@/types/filing/inertia'
import '../../../css/create-surat.css'

interface Permission { id: number; name: string }
type Props = AppPageProps & { permissions: Permission[] }

// Kelompokkan permission berdasarkan kata terakhir
function groupPermissions(permissions: Permission[]) {
  return permissions.reduce((acc, p) => {
    const words = p.name.split(' ')
    const group = words[words.length - 1]
    if (!acc[group]) acc[group] = []
    acc[group].push(p)
    return acc
  }, {} as Record<string, Permission[]>)
}

const groupColors: Record<string, string> = {
  surat: '#E6F1FB', arsip: '#EAF3DE',
  sertifikat: '#EEEDFE', roles: '#FAEEDA', users: '#FCEBEB',
}

function getGroupBg(group: string) {
  return groupColors[group] ?? '#F1EFE8'
}

export default function Create() {
  const { permissions } = usePage<Props>().props
  const grouped = groupPermissions(permissions)

  const form = useForm({ name: '', permissions: [] as string[] })

  const togglePermission = (name: string) => {
    form.setData('permissions',
      form.data.permissions.includes(name)
        ? form.data.permissions.filter(p => p !== name)
        : [...form.data.permissions, name]
    )
  }

  const toggleGroup = (perms: Permission[]) => {
    const names = perms.map(p => p.name)
    const allChecked = names.every(n => form.data.permissions.includes(n))
    if (allChecked) {
      form.setData('permissions', form.data.permissions.filter(p => !names.includes(p)))
    } else {
      const merged = Array.from(new Set([...form.data.permissions, ...names]))
      form.setData('permissions', merged)
    }
  }

  const submit = (e: React.FormEvent) => {
    e.preventDefault()
    form.post(route('roles.store'))
  }

  return (
    <AuthenticatedLayout>
      <Head title="Buat Role Baru" />

      <form onSubmit={submit} className="create-form">

        <div className="create-form-heading">
          <h1 className="create-form-title">Buat role baru</h1>
          <p className="create-form-subtitle">Tentukan nama role dan pilih permissions yang sesuai</p>
        </div>

        {/* Nama Role */}
        <div className="form-card">
          <div className="form-card-header">
            <div className="form-icon icon-blue">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <circle cx="8" cy="5" r="3" stroke="#185FA5" strokeWidth="1.2"/>
                <path d="M2 14c0-3 2.5-5 6-5s6 2 6 5" stroke="#185FA5" strokeWidth="1.2" strokeLinecap="round"/>
              </svg>
            </div>
            <span className="form-card-title">Informasi role</span>
          </div>
          <div className="field">
            <label className="field-label">Nama role</label>
            <input className="field-input" placeholder="Contoh: manager, staff, viewer"
              value={form.data.name} onChange={e => form.setData('name', e.target.value)} />
            {form.errors.name && <span className="field-error">{form.errors.name}</span>}
          </div>
        </div>

        {/* Permissions */}
        <div className="form-card">
          <div className="form-card-header">
            <div className="form-icon icon-purple">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <rect x="3" y="7" width="10" height="7" rx="2" stroke="#534AB7" strokeWidth="1.2"/>
                <path d="M5 7V5a3 3 0 0 1 6 0v2" stroke="#534AB7" strokeWidth="1.2" strokeLinecap="round"/>
              </svg>
            </div>
            <span className="form-card-title">
              Permissions
              {form.data.permissions.length > 0 && (
                <span style={{ marginLeft: '8px', fontSize: '11px', background: '#E6F1FB', color: '#0C447C', padding: '2px 8px', borderRadius: '999px', fontWeight: 500 }}>
                  {form.data.permissions.length} dipilih
                </span>
              )}
            </span>
          </div>

          <div style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
            {Object.entries(grouped).map(([group, perms]) => {
              const allChecked = perms.every(p => form.data.permissions.includes(p.name))
              const someChecked = perms.some(p => form.data.permissions.includes(p.name))
              const bg = getGroupBg(group)

              return (
                <div key={group}>
                  {/* Group header */}
                  <div style={{ display: 'flex', alignItems: 'center', gap: '8px', marginBottom: '8px' }}>
                    <button type="button"
                      onClick={() => toggleGroup(perms)}
                      style={{
                        width: '16px', height: '16px', borderRadius: '4px', flexShrink: 0,
                        border: '1.5px solid', cursor: 'pointer',
                        borderColor: allChecked ? '#185FA5' : someChecked ? '#185FA5' : 'rgba(0,0,0,0.2)',
                        background: allChecked ? '#185FA5' : someChecked ? '#B5D4F4' : '#fff',
                        display: 'flex', alignItems: 'center', justifyContent: 'center',
                      }}>
                      {(allChecked || someChecked) && (
                        <svg width="9" height="9" viewBox="0 0 10 10" fill="none">
                          <path d="M2 5l2.5 2.5L8 2.5" stroke="white" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
                        </svg>
                      )}
                    </button>
                    <span style={{ fontSize: '12px', fontWeight: 500, color: '#374151', textTransform: 'capitalize' }}>
                      {group}
                    </span>
                    <span style={{ fontSize: '11px', color: '#9ca3af' }}>({perms.length})</span>
                  </div>

                  {/* Permission items */}
                  <div style={{ display: 'flex', flexWrap: 'wrap', gap: '6px', paddingLeft: '24px' }}>
                    {perms.map(p => {
                      const checked = form.data.permissions.includes(p.name)
                      return (
                        <button key={p.id} type="button"
                          onClick={() => togglePermission(p.name)}
                          style={{
                            fontSize: '11px', fontWeight: 500,
                            padding: '4px 10px', borderRadius: '999px',
                            border: checked ? 'none' : '0.5px solid rgba(0,0,0,0.15)',
                            background: checked ? bg : '#f9fafb',
                            color: checked ? '#111827' : '#6b7280',
                            cursor: 'pointer', fontFamily: 'inherit',
                            transition: 'all 0.12s',
                            display: 'inline-flex', alignItems: 'center', gap: '4px',
                          }}>
                          {checked && (
                            <svg width="10" height="10" viewBox="0 0 10 10" fill="none">
                              <path d="M2 5l2 2 4-4" stroke="#185FA5" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
                            </svg>
                          )}
                          {p.name}
                        </button>
                      )
                    })}
                  </div>
                </div>
              )
            })}
          </div>
        </div>

        {/* Actions */}
        <div style={{ display: 'flex', gap: '8px' }}>
          <button type="submit" className="btn-submit"
            style={{ flex: 1 }} disabled={form.processing}>
            {form.processing ? 'Menyimpan…' : 'Simpan role'}
          </button>
          <Link href={route('roles.index')}
            style={{
              padding: '11px 20px', borderRadius: '8px',
              border: '0.5px solid rgba(0,0,0,0.15)',
              background: '#fff', color: '#6b7280',
              fontSize: '14px', fontWeight: 500,
              textDecoration: 'none', display: 'inline-flex',
              alignItems: 'center', transition: 'background 0.12s',
            }}>
            Batal
          </Link>
        </div>

      </form>
    </AuthenticatedLayout>
  )
}