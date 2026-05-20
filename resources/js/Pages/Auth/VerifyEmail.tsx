import GuestLayout from '@/layouts/GuestLayout'
import { Head, Link, useForm } from '@inertiajs/react'
import { Loader2, Mail } from 'lucide-react'
import React from 'react'

interface VerifyEmailProps {
    status?: string
}

export default function VerifyEmail({ status }: VerifyEmailProps) {
    const { post, processing } = useForm<Record<string, never>>({})

    const submit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault()
        post(route('verification.send'))
    }

    return (
        <GuestLayout>
            <Head title="Email Verification" />

            <div className="mb-6 flex flex-col items-center text-center">
                <div className="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-red-50 ring-1 ring-red-100">
                    <Mail size={26} className="text-red-800" strokeWidth={1.75} />
                </div>
                <h2 className="text-xl font-semibold tracking-tight text-gray-900">
                    Verify your email
                </h2>
                <p className="mt-2 text-sm leading-relaxed text-gray-500">
                    Thanks for signing up. We&apos;ve sent a verification link to your
                    email. Click the link to activate your account.
                </p>
            </div>

            {status === 'verification-link-sent' && (
                <div className="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-3.5 py-2.5 text-sm font-medium text-emerald-800">
                    A new verification link has been sent to your email.
                </div>
            )}

            <form onSubmit={submit} className="space-y-3">
                <button
                    type="submit"
                    disabled={processing}
                    className="flex w-full items-center justify-center gap-2 rounded-lg bg-red-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all duration-150 hover:bg-red-900 active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-70"
                >
                    {processing ? (
                        <>
                            <Loader2 size={16} className="animate-spin" />
                            <span>Resending...</span>
                        </>
                    ) : (
                        <span>Resend verification email</span>
                    )}
                </button>

                <Link
                    href={route('logout')}
                    method="post"
                    as="button"
                    className="block w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-center text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50"
                >
                    Log out
                </Link>
            </form>
        </GuestLayout>
    )
}