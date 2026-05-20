import IconInput from '@/components/Iconinput'
import InputError from '@/components/InputError'
import GuestLayout from '@/layouts/GuestLayout'
import { Head, useForm } from '@inertiajs/react'
import { Loader2, Lock, Mail } from 'lucide-react'
import React from 'react'

interface ResetPasswordProps {
    token: string
    email: string
}

interface ResetPasswordForm {
    token: string
    email: string
    password: string
    password_confirmation: string
}

export default function ResetPassword({ token, email }: ResetPasswordProps) {
    const { data, setData, post, processing, errors, reset } = useForm<ResetPasswordForm>({
        token,
        email,
        password: '',
        password_confirmation: '',
    })

    const submit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault()
        post(route('password.store'), {
            onFinish: () => reset('password', 'password_confirmation'),
        })
    }

    return (
        <GuestLayout>
            <Head title="Reset Password" />

            <div className="mb-6">
                <h2 className="text-xl font-semibold tracking-tight text-gray-900">
                    Set a new password
                </h2>
                <p className="mt-1 text-sm text-gray-500">
                    Choose a strong password you haven&apos;t used before.
                </p>
            </div>

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
                        onChange={e => setData('email', e.target.value)}
                        error={Boolean(errors.email)}
                    />
                    <InputError message={errors.email} className="mt-1.5" />
                </div>

                <div>
                    <label
                        htmlFor="password"
                        className="mb-1.5 block text-xs font-medium text-gray-700"
                    >
                        New password
                    </label>
                    <IconInput
                        id="password"
                        name="password"
                        type="password"
                        icon={Lock}
                        value={data.password}
                        autoComplete="new-password"
                        autoFocus
                        placeholder="At least 8 characters"
                        onChange={e => setData('password', e.target.value)}
                        error={Boolean(errors.password)}
                    />
                    <InputError message={errors.password} className="mt-1.5" />
                </div>

                <div>
                    <label
                        htmlFor="password_confirmation"
                        className="mb-1.5 block text-xs font-medium text-gray-700"
                    >
                        Confirm new password
                    </label>
                    <IconInput
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        icon={Lock}
                        value={data.password_confirmation}
                        autoComplete="new-password"
                        placeholder="Repeat your password"
                        onChange={e =>
                            setData('password_confirmation', e.target.value)
                        }
                        error={Boolean(errors.password_confirmation)}
                    />
                    <InputError
                        message={errors.password_confirmation}
                        className="mt-1.5"
                    />
                </div>

                <button
                    type="submit"
                    disabled={processing}
                    className="mt-2 flex w-full items-center justify-center gap-2 rounded-lg bg-red-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all duration-150 hover:bg-red-900 active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-70"
                >
                    {processing ? (
                        <>
                            <Loader2 size={16} className="animate-spin" />
                            <span>Resetting...</span>
                        </>
                    ) : (
                        <span>Reset password</span>
                    )}
                </button>
            </form>
        </GuestLayout>
    )
}