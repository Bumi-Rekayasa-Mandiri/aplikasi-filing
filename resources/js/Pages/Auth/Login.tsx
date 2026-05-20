import IconInput from '@/components/Iconinput'
import InputError from '@/components/InputError'
import GuestLayout from '@/layouts/GuestLayout'
import { Head, Link, useForm } from '@inertiajs/react'
import { Loader2, Lock, Mail } from 'lucide-react'
import React from 'react'

interface LoginProps {
    status?: string | null
    canResetPassword: boolean
}

interface LoginForm {
    email: string
    password: string
    remember: boolean
}

export default function Login({ status, canResetPassword }: LoginProps) {
    const { data, setData, post, processing, errors, reset } = useForm<LoginForm>({
        email: '',
        password: '',
        remember: false,
    })

    const submit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault()
        post(route('login'), { onFinish: () => reset('password') })
    }

    return (
        <GuestLayout>
            <Head title="Log in" />

            <div className="mb-6">
                <h2 className="text-xl font-semibold tracking-tight text-gray-900">
                    Welcome back
                </h2>
                <p className="mt-1 text-sm text-gray-500">
                    Sign in to continue managing your filing system.
                </p>
            </div>

            {status && (
                <div className="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-3.5 py-2.5 text-sm font-medium text-emerald-800">
                    {status}
                </div>
            )}

            <form onSubmit={submit} className="space-y-4">
                <div>
                    <label
                        htmlFor="email"
                        className="mb-1.5 block text-xs font-medium text-gray-700"
                    >
                        Email address
                    </label>
                    <IconInput
                        id="email"
                        name="email"
                        type="email"
                        icon={Mail}
                        value={data.email}
                        autoComplete="username"
                        autoFocus
                        placeholder="you@example.com"
                        onChange={e => setData('email', e.target.value)}
                        error={Boolean(errors.email)}
                    />
                    <InputError message={errors.email} className="mt-1.5" />
                </div>

                <div>
                    <div className="mb-1.5 flex items-center justify-between">
                        <label
                            htmlFor="password"
                            className="block text-xs font-medium text-gray-700"
                        >
                            Password
                        </label>
                        {canResetPassword && (
                            <Link
                                href={route('password.request')}
                                className="text-xs font-medium text-red-800 hover:text-red-900 hover:underline"
                            >
                                Forgot password?
                            </Link>
                        )}
                    </div>
                    <IconInput
                        id="password"
                        name="password"
                        type="password"
                        icon={Lock}
                        value={data.password}
                        autoComplete="current-password"
                        placeholder="••••••••"
                        onChange={e => setData('password', e.target.value)}
                        error={Boolean(errors.password)}
                    />
                    <InputError message={errors.password} className="mt-1.5" />
                </div>

                <label className="flex cursor-pointer select-none items-center gap-2">
                    <input
                        type="checkbox"
                        name="remember"
                        checked={data.remember}
                        onChange={e => setData('remember', e.target.checked)}
                        className="h-4 w-4 rounded border-gray-300 text-red-800 focus:ring-red-200"
                    />
                    <span className="text-sm text-gray-600">
                        Remember me on this device
                    </span>
                </label>

                <button
                    type="submit"
                    disabled={processing}
                    className="mt-2 flex w-full items-center justify-center gap-2 rounded-lg bg-red-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all duration-150 hover:bg-red-900 active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-70"
                >
                    {processing ? (
                        <>
                            <Loader2 size={16} className="animate-spin" />
                            <span>Signing in...</span>
                        </>
                    ) : (
                        <span>Sign in</span>
                    )}
                </button>
            </form>

            <p className="mt-6 text-center text-sm text-gray-500">
                Don&apos;t have an account?{' '}
                <Link
                    href={route('register')}
                    className="font-medium text-red-800 hover:text-red-900 hover:underline"
                >
                    Create one
                </Link>
            </p>
        </GuestLayout>
    )
}