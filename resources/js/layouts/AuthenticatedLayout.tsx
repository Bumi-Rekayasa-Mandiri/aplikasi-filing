import { PropsWithChildren, ReactNode, useState, useEffect } from 'react'
import { Link, usePage } from '@inertiajs/react'
import Dropdown from '@/components/Dropdown'
import { Home, FileText, Archive, Award, Shield, ChevronLeft, Menu, X } from 'lucide-react'
import AppLogo from '@/components/AppLogo'
 
interface NavItem {
    label: string
    icon: typeof Home
    routeName: string
    /** URL path prefix untuk active state matching */
    pathPrefix: string
}
 
const NAV_ITEMS: NavItem[] = [
    { label: 'Dashboard',         icon: Home,     routeName: 'dashboard',               pathPrefix: '/dashboard' },
    { label: 'Manajemen Surat',   icon: FileText, routeName: 'filing.surat.index',      pathPrefix: '/filing/surat' },
    { label: 'Arsip Surat',       icon: Archive,  routeName: 'filing.arsip.index',      pathPrefix: '/filing/arsip' },
    { label: 'Arsip Sertifikat',  icon: Award,    routeName: 'filing.sertifikat.index', pathPrefix: '/filing/sertifikat' },
    { label: 'Super Admin',       icon: Shield,   routeName: 'roles.index',             pathPrefix: '/roles' },
]
 
export default function AuthenticatedLayout({
    header,
    children,
}: PropsWithChildren<{ header?: ReactNode }>) {
    const page = usePage()
    const { auth } = page.props as any
    const currentPath = page.url ?? ''  // contoh: "/filing/surat/3/edit/BRM-1"
 
    // ── State ─────────────────────────────────────────────────────────────
    const [sidebarOpen,    setSidebarOpen]    = useState(true)   // desktop: full vs collapsed
    const [mobileOpen,     setMobileOpen]     = useState(false)  // mobile: drawer overlay
    const [isMobile,       setIsMobile]       = useState(false)  // detect viewport
 
    // Detect viewport (mobile = <1024px)
    useEffect(() => {
        const check = () => setIsMobile(window.innerWidth < 1024)
        check()
        window.addEventListener('resize', check)
        return () => window.removeEventListener('resize', check)
    }, [])
 
    // Restore desktop preference dari localStorage
    useEffect(() => {
        const saved = localStorage.getItem('sidebarOpen')
        if (saved !== null) setSidebarOpen(saved === 'true')
    }, [])
 
    // Persist desktop preference
    useEffect(() => {
        localStorage.setItem('sidebarOpen', sidebarOpen.toString())
    }, [sidebarOpen])
 
    // Tutup mobile drawer saat navigasi
    useEffect(() => {
        setMobileOpen(false)
    }, [currentPath])
 
    // Lock scroll body saat mobile drawer terbuka
    useEffect(() => {
        if (isMobile && mobileOpen) {
            document.body.style.overflow = 'hidden'
        } else {
            document.body.style.overflow = ''
        }
        return () => { document.body.style.overflow = '' }
    }, [isMobile, mobileOpen])
 
    // ── Computed ──────────────────────────────────────────────────────────
    // Di mobile: sidebar selalu "expanded" tapi posisi fixed off-screen
    // Di desktop: sidebar push content, lebar berubah sesuai sidebarOpen
    const showLabels = isMobile ? true : sidebarOpen
    const sidebarWidth = isMobile ? '260px' : (sidebarOpen ? '260px' : '76px')
 
    const isActive = (pathPrefix: string): boolean => {
        if (pathPrefix === '/dashboard') {
            // Dashboard: exact match (selain dashboard, jangan trigger)
            return currentPath === '/dashboard' || currentPath === '/'
        }
        // Lainnya: match prefix (semua sub-path ikut aktif)
        return currentPath === pathPrefix || currentPath.startsWith(pathPrefix + '/')
    }
 
    return (
        <div style={{ minHeight: '100vh', display: 'flex', background: '#f3f4f6' }}>
 
            {/* ═══ MOBILE BACKDROP ═══════════════════════════════════════ */}
            {isMobile && mobileOpen && (
                <div
                    onClick={() => setMobileOpen(false)}
                    style={{
                        position:       'fixed',
                        inset:          0,
                        background:     'rgba(15, 23, 42, 0.5)',
                        backdropFilter: 'blur(2px)',
                        zIndex:         40,
                        animation:      'fadeIn 0.2s ease',
                    }}
                />
            )}
 
            {/* ═══ SIDEBAR ═══════════════════════════════════════════════ */}
            <aside style={{
                width:           sidebarWidth,
                minWidth:        sidebarWidth,
                background:      'linear-gradient(180deg, #7f1d1d 0%, #991b1b 50%, #7f1d1d 100%)',
                color:           '#ffffff',
                display:         'flex',
                flexDirection:   'column',
                position:        isMobile ? 'fixed' : 'sticky',
                top:             0,
                left:            0,
                bottom:          0,
                height:          '100vh',
                zIndex:          50,
                transform:       isMobile && !mobileOpen ? 'translateX(-100%)' : 'translateX(0)',
                transition:      'width 0.3s cubic-bezier(0.4, 0, 0.2, 1), min-width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1)',
                boxShadow:       isMobile ? '4px 0 24px rgba(0,0,0,0.25)' : '2px 0 8px rgba(0,0,0,0.08)',
                overflow:        'hidden',
            }}>
 
                {/* ── Sidebar Header ───────────────────────────────────── */}
                <div style={{
                    display:        'flex',
                    alignItems:     'center',
                    justifyContent: showLabels ? 'space-between' : 'center',
                    padding:        '18px 16px',
                    borderBottom:   '1px solid rgba(255,255,255,0.1)',
                    minHeight:      '68px',
                }}>
                    {showLabels && (
                        <div style={{
                            display:     'flex',
                            alignItems:  'center',
                            gap:         '10px',
                            opacity:     showLabels ? 1 : 0,
                            transition:  'opacity 0.2s ease 0.1s',
                            whiteSpace:  'nowrap',
                            overflow:    'hidden',
                        }}>
                            <div style={{
                                width:          '32px',
                                height:         '32px',
                                borderRadius:   '8px',
                                background:     'rgba(255,255,255,0.15)',
                                display:        'flex',
                                alignItems:     'center',
                                justifyContent: 'center',
                                flexShrink:     0,
                            }}>
                                <FileText size={18} />
                            </div>
                            <span style={{
                                fontSize:   '15px',
                                fontWeight: 700,
                                letterSpacing: '-0.01em',
                            }}>
                                Filing System
                            </span>
                        </div>
                    )}
 
                    {/* Toggle button */}
                    <button
                        onClick={() => isMobile ? setMobileOpen(false) : setSidebarOpen(!sidebarOpen)}
                        title={sidebarOpen ? 'Sembunyikan sidebar' : 'Tampilkan sidebar'}
                        style={{
                            display:        'flex',
                            alignItems:     'center',
                            justifyContent: 'center',
                            width:          '32px',
                            height:         '32px',
                            background:     'rgba(255,255,255,0.1)',
                            border:         '1px solid rgba(255,255,255,0.15)',
                            borderRadius:   '8px',
                            color:          '#ffffff',
                            cursor:         'pointer',
                            transition:     'background 0.15s, transform 0.3s ease',
                            transform:      showLabels ? 'rotate(0deg)' : 'rotate(180deg)',
                            flexShrink:     0,
                        }}
                        onMouseEnter={e => e.currentTarget.style.background = 'rgba(255,255,255,0.2)'}
                        onMouseLeave={e => e.currentTarget.style.background = 'rgba(255,255,255,0.1)'}
                    >
                        {isMobile ? <X size={16} /> : <ChevronLeft size={16} />}
                    </button>
                </div>
 
                {/* ── Nav Items ────────────────────────────────────────── */}
                <nav style={{
                    flex:          1,
                    padding:       '16px 12px',
                    display:       'flex',
                    flexDirection: 'column',
                    gap:           '4px',
                    overflowY:     'auto',
                    overflowX:     'hidden',
                }}>
                    {NAV_ITEMS.map(item => {
                        const Icon   = item.icon
                        const active = isActive(item.pathPrefix)
 
                        return (
                            <Link
                                key={item.routeName}
                                href={route(item.routeName)}
                                title={!showLabels ? item.label : undefined}
                                style={{
                                    display:        'flex',
                                    alignItems:     'center',
                                    gap:            '12px',
                                    padding:        showLabels ? '11px 14px' : '12px',
                                    justifyContent: showLabels ? 'flex-start' : 'center',
                                    borderRadius:   '10px',
                                    textDecoration: 'none',
                                    color:          active ? '#ffffff' : 'rgba(255,255,255,0.78)',
                                    background:     active ? 'rgba(255,255,255,0.15)' : 'transparent',
                                    fontWeight:     active ? 600 : 500,
                                    fontSize:       '13.5px',
                                    transition:     'background 0.2s ease, color 0.2s ease',
                                    whiteSpace:     'nowrap',
                                    overflow:       'hidden',
                                    position:       'relative',
                                }}
                                onMouseEnter={e => {
                                    if (!active) {
                                        e.currentTarget.style.background = 'rgba(255,255,255,0.08)'
                                        e.currentTarget.style.color      = '#ffffff'
                                    }
                                }}
                                onMouseLeave={e => {
                                    if (!active) {
                                        e.currentTarget.style.background = 'transparent'
                                        e.currentTarget.style.color      = 'rgba(255,255,255,0.78)'
                                    }
                                }}
                            >
                                {/* Active indicator bar (kiri) */}
                                {active && (
                                    <span style={{
                                        position:     'absolute',
                                        left:         0,
                                        top:          '50%',
                                        transform:    'translateY(-50%)',
                                        width:        '3px',
                                        height:       '20px',
                                        background:   '#fbbf24',
                                        borderRadius: '0 3px 3px 0',
                                    }} />
                                )}
 
                                <Icon size={18} style={{ flexShrink: 0 }} />
 
                                {showLabels && (
                                    <span style={{
                                        opacity:    showLabels ? 1 : 0,
                                        transition: 'opacity 0.2s ease 0.05s',
                                    }}>
                                        {item.label}
                                    </span>
                                )}
                            </Link>
                        )
                    })}
                </nav>
 
                {/* ── Footer (versi/info) ──────────────────────────────── */}
                {showLabels && (
                    <div style={{
                        padding:    '14px 18px',
                        borderTop:  '1px solid rgba(255,255,255,0.1)',
                        fontSize:   '11px',
                        color:      'rgba(255,255,255,0.5)',
                        textAlign:  'center',
                    }}>
                        © PT. Bumi Rekayasa Mandiri
                    </div>
                )}
            </aside>
 
            {/* ═══ MAIN AREA ═════════════════════════════════════════════ */}
            <div style={{
                flex:          1,
                display:       'flex',
                flexDirection: 'column',
                minWidth:      0,
                width:         isMobile ? '100%' : 'auto',
            }}>
                {/* TOPBAR */}
                <header style={{
                    background:     'linear-gradient(90deg, #7f1d1d 0%, #991b1b 50%, #7f1d1d 100%)',
                    color:          '#ffffff',
                    padding:        '14px 24px',
                    display:        'flex',
                    justifyContent: 'space-between',
                    alignItems:     'center',
                    boxShadow:      '0 1px 3px rgba(0,0,0,0.08)',
                    position:       'sticky',
                    top:            0,
                    zIndex:         30,
                }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '12px' }}>
                        {/* Mobile hamburger */}
                        {isMobile && (
                            <button
                                onClick={() => setMobileOpen(true)}
                                style={{
                                    display:        'flex',
                                    alignItems:     'center',
                                    justifyContent: 'center',
                                    width:          '38px',
                                    height:         '38px',
                                    background:     'rgba(255,255,255,0.12)',
                                    border:         '1px solid rgba(255,255,255,0.18)',
                                    borderRadius:   '8px',
                                    color:          '#ffffff',
                                    cursor:         'pointer',
                                }}
                            >
                                <Menu size={20} />
                            </button>
                        )}
 
                        <AppLogo />
                    </div>
 
                    {/* USER DROPDOWN */}
                    <Dropdown
                        trigger={
                            <button style={{
                                display:      'inline-flex',
                                alignItems:   'center',
                                gap:          '8px',
                                padding:      '7px 14px 7px 7px',
                                background:   'rgba(255,255,255,0.12)',
                                border:       '1px solid rgba(255,255,255,0.18)',
                                borderRadius: '999px',
                                color:        '#ffffff',
                                fontWeight:   600,
                                fontSize:     '13px',
                                cursor:       'pointer',
                                transition:   'background 0.15s',
                            }}>
                                <span style={{
                                    display:        'flex',
                                    alignItems:     'center',
                                    justifyContent: 'center',
                                    width:          '28px',
                                    height:         '28px',
                                    borderRadius:   '50%',
                                    background:     'rgba(255,255,255,0.2)',
                                    fontSize:       '11px',
                                    fontWeight:     700,
                                }}>
                                    {(auth?.user?.name ?? 'U').charAt(0).toUpperCase()}
                                </span>
                                <span style={{
                                    maxWidth:    '140px',
                                    overflow:    'hidden',
                                    textOverflow:'ellipsis',
                                    whiteSpace:  'nowrap',
                                }}>
                                    {auth?.user?.name ?? 'User'}
                                </span>
                            </button>
                        }
                    >
                        <Dropdown.Link href={route('profile.edit')}>
                            Profile
                        </Dropdown.Link>
                        <Dropdown.Link href={route('logout')} method="post" as="button">
                            Logout
                        </Dropdown.Link>
                    </Dropdown>
                </header>
 
                {/* PAGE CONTENT */}
                <main style={{ padding: '24px', flex: 1 }}>
                    <div style={{
                        background:   '#ffffff',
                        borderRadius: '12px',
                        boxShadow:    '0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.04)',
                        padding:      '24px',
                    }}>
                        {header && (
                            <div style={{ marginBottom: '16px' }}>
                                {header}
                            </div>
                        )}
                        {children}
                    </div>
                </main>
            </div>
 
            {/* Keyframe animations */}
            <style>{`
                @keyframes fadeIn {
                    from { opacity: 0 }
                    to   { opacity: 1 }
                }
                /* Custom scrollbar for nav */
                aside nav::-webkit-scrollbar {
                    width: 4px;
                }
                aside nav::-webkit-scrollbar-track {
                    background: transparent;
                }
                aside nav::-webkit-scrollbar-thumb {
                    background: rgba(255,255,255,0.15);
                    border-radius: 2px;
                }
                aside nav::-webkit-scrollbar-thumb:hover {
                    background: rgba(255,255,255,0.25);
                }
            `}</style>
        </div>
    )
}