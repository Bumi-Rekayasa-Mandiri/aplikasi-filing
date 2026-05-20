import IconInput from '@/components/Iconinput'
import InputError from '@/components/InputError'
import GuestLayout from '@/layouts/GuestLayout'
import { Head, Link, useForm } from '@inertiajs/react'
import { ArrowLeft, Loader2, Mail } from 'lucide-react'
import React from 'react'

interface ForgotPasswordProps {
    status?: string | null
}

interface ForgotPasswordForm {
    email: string
}

export default function ForgotPassword({ status }: ForgotPasswordProps) {
    const { data, setData, post, processing, errors } = useForm<ForgotPasswordForm>({
        email: '',
    })

    const submit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault()
        post(route('password.email'))
    }

    return (
        <GuestLayout>
            <Head title="Forgot Password" />

            <div className="mb-6">
                <h2 className="text-xl font-semibold tracking-tight text-gray-900">
                    Forgot your password?
                </h2>
                <p className="mt-1 text-sm leading-relaxed text-gray-500">
                    Enter your email and we&apos;ll send you a secure link to reset it.
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
                        autoFocus
                        placeholder="you@example.com"
                        onChange={e => setData('email', e.target.value)}
                        error={Boolean(errors.email)}
                    />
                    <InputError message={errors.email} className="mt-1.5" />
                </div>

                <button
                    type="submit"
                    disabled={processing}
                    className="mt-2 flex w-full items-center justify-center gap-2 rounded-lg bg-red-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all duration-150 hover:bg-red-900 active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-70"
                >
                    {processing ? (
                        <>
                            <Loader2 size={16} className="animate-spin" />
                            <span>Sending link...</span>
                        </>
                    ) : (
                        <span>Send reset link</span>
                    )}
                </button>
            </form>

            <Link
                href={route('login')}
                className="mt-6 inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 transition-colors hover:text-red-800"
            >
                <ArrowLeft size={14} />
                Back to sign in
            </Link>
        </GuestLayout>
    )
}