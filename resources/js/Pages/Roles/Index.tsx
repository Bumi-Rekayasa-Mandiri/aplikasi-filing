import AuthenticatedLayout from '@/layouts/AuthenticatedLayout'
import { Head, usePage, Link } from '@inertiajs/react'
import { useState } from 'react'
import type { AppPageProps } from '@/types/filing/inertia'
import '../../../css/surat-table.css'

interface Permission { id: number; name: string }
interface Role {
  id: number; name: string
  permissions: Permission[]
  users: { id: number; name: string }[]
}
interface UserRow {
  id: number; name: string; email: string
  roles: string[]; permissions: string[]
}

type Props = AppPageProps & { roles: Role[]; users: UserRow[] }

// Kelompokkan permission berdasarkan prefix (surat, arsip, dll)
function groupPermissions(permissions: Permission[]) {
  return permissions.reduce((acc, p) => {
    const group = p.name.split(' ').pop() ?? 'lainnya'
    if (!acc[group]) acc[group] = []
    acc[group].push(p)
    return acc
  }, {} as Record<string, Permission[]>)
}

const groupColors: Record<string, { bg: string; color: string }> = {
  surat:      { bg: '#E6F1FB', color: '#0C447C' },
  arsip:      { bg: '#EAF3DE', color: '#27500A' },
  sertifikat: { bg: '#EEEDFE', color: '#3C3489' },
  roles:      { bg: '#FAEEDA', color: '#633806' },
  users:      { bg: '#FCEBEB', color: '#791F1F' },
}

function getGroupColor(name: string) {
  for (const [key, val] of Object.entries(groupColors)) {
    if (name.includes(key)) return val
  }
  return { bg: '#F1EFE8', color: '#444441' }
}

export default function Index() {
  const { roles, users } = usePage<Props>().props
  const [activeTab, setActiveTab] = useState<'roles' | 'users'>('roles')

  return (
    <AuthenticatedLayout>
      <Head title="Roles & Permissions" />

      <div className="surat-index">

        {/* Header */}
        <div className="surat-index-header">
          <h1 className="surat-index-title">Roles & permissions</h1>
          <Link href={route('roles.create')} className="btn-create-surat">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
              <path d="M8 3v10M3 8h10" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/>
            </svg>
            Buat role
          </Link>
        </div>

        {/* Tabs */}
        <div style={{ display: 'flex', gap: '4px', borderBottom: '0.5px solid rgba(0,0,0,0.1)', marginBottom: '4px' }}>
          {(['roles', 'users'] as const).map(tab => (
            <button key={tab} onClick={() => setActiveTab(tab)}
              style={{
                padding: '8px 16px', fontSize: '13px', fontWeight: 500,
                border: 'none', background: 'transparent', cursor: 'pointer',
                fontFamily: 'inherit', borderBottom: activeTab === tab ? '2px solid #185FA5' : '2px solid transparent',
                color: activeTab === tab ? '#185FA5' : '#9ca3af',
                transition: 'color 0.12s',
              }}>
              {tab === 'roles' ? 'Roles' : 'Users'}
            </button>
          ))}
        </div>

        {/* Tab: Roles */}
        {activeTab === 'roles' && (
          <div style={{ display: 'flex', flexDirection: 'column', gap: '10px' }}>
            {roles.length === 0 && (
              <div className="surat-table-empty">Tidak ada role ditemukan</div>
            )}
            {roles.map(role => (
              <div key={role.id} className="surat-table-card" style={{ padding: '16px 20px' }}>
                <div style={{ display: 'flex', alignItems: 'flex-start', gap: '16px', flexWrap: 'wrap' }}>

                  {/* Role name */}
                  <div style={{ minWidth: '120px' }}>
                    <div style={{ fontSize: '11px', fontWeight: 500, color: '#9ca3af', textTransform: 'uppercase', letterSpacing: '0.05em', marginBottom: '4px' }}>Role</div>
                    <span style={{ fontSize: '13px', fontWeight: 500, color: '#111827', background: '#EEEDFE', padding: '3px 10px', borderRadius: '999px', display: 'inline-block' }}>
                      {role.name}
                    </span>
                  </div>

                  {/* Users */}
                  <div style={{ minWidth: '140px' }}>
                    <div style={{ fontSize: '11px', fontWeight: 500, color: '#9ca3af', textTransform: 'uppercase', letterSpacing: '0.05em', marginBottom: '4px' }}>Users</div>
                    {role.users.length === 0
                      ? <span style={{ fontSize: '12px', color: '#9ca3af', fontStyle: 'italic' }}>Tidak ada user</span>
                      : role.users.map(u => (
                        <div key={u.id} style={{ fontSize: '13px', color: '#374151' }}>{u.name}</div>
                      ))}
                  </div>

                  {/* Permissions */}
                  <div style={{ flex: 1 }}>
                    <div style={{ fontSize: '11px', fontWeight: 500, color: '#9ca3af', textTransform: 'uppercase', letterSpacing: '0.05em', marginBottom: '6px' }}>
                      Permissions ({role.permissions.length})
                    </div>
                    {role.permissions.length === 0
                      ? <span style={{ fontSize: '12px', color: '#9ca3af', fontStyle: 'italic' }}>Tidak ada permission</span>
                      : (
                        <div style={{ display: 'flex', flexWrap: 'wrap', gap: '5px' }}>
                          {role.permissions.map(p => {
                            const { bg, color } = getGroupColor(p.name)
                            return (
                              <span key={p.id} style={{ fontSize: '11px', fontWeight: 500, padding: '2px 8px', borderRadius: '999px', background: bg, color }}>
                                {p.name}
                              </span>
                            )
                          })}
                        </div>
                      )}
                  </div>

                </div>
              </div>
            ))}
          </div>
        )}

        {/* Tab: Users */}
        {activeTab === 'users' && (
          <div className="surat-table-card">
            <table className="surat-table">
              <thead>
                <tr>
                  <th>Nama</th>
                  <th>Email</th>
                  <th>Roles</th>
                  <th>Permissions</th>
                </tr>
              </thead>
              <tbody>
                {users.length === 0 && (
                  <tr><td colSpan={4} className="surat-table-empty">Tidak ada user ditemukan</td></tr>
                )}
                {users.map(user => (
                  <tr key={user.id}>
                    <td><div className="surat-judul">{user.name}</div></td>
                    <td><span className="surat-nomor">{user.email}</span></td>
                    <td>
                      <div style={{ display: 'flex', flexWrap: 'wrap', gap: '4px' }}>
                        {user.roles.length === 0
                          ? <span style={{ fontSize: '12px', color: '#9ca3af', fontStyle: 'italic' }}>—</span>
                          : user.roles.map(r => (
                            <span key={r} className="surat-jenis-badge">{r}</span>
                          ))}
                      </div>
                    </td>
                    <td>
                      <div style={{ display: 'flex', flexWrap: 'wrap', gap: '4px' }}>
                        {user.permissions.length === 0
                          ? <span style={{ fontSize: '12px', color: '#9ca3af', fontStyle: 'italic' }}>—</span>
                          : user.permissions.map(p => {
                            const { bg, color } = getGroupColor(p)
                            return (
                              <span key={p} style={{ fontSize: '11px', fontWeight: 500, padding: '2px 7px', borderRadius: '999px', background: bg, color }}>
                                {p}
                              </span>
                            )
                          })}
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}

      </div>
    </AuthenticatedLayout>
  )
}