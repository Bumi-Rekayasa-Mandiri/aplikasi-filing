import IconInput from '@/components/Iconinput'
import InputError from '@/components/InputError'
import GuestLayout from '@/layouts/GuestLayout'
import { Head, Link, useForm } from '@inertiajs/react'
import { Loader2, Lock, Mail, User } from 'lucide-react'
import React from 'react'

interface RegisterForm {
    name: string
    email: string
    password: string
    password_confirmation: string
}

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm<RegisterForm>({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    })

    const submit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault()
        post(route('register'), {
            onFinish: () => reset('password', 'password_confirmation'),
        })
    }

    return (
        <GuestLayout>
            <Head title="Register" />

            <div className="mb-6">
                <h2 className="text-xl font-semibold tracking-tight text-gray-900">
                    Create your account
                </h2>
                <p className="mt-1 text-sm text-gray-500">
                    Join the filing system to start managing documents.
                </p>
            </div>

            <form onSubmit={submit} className="space-y-4">
                <div>
                    <label
                        htmlFor="name"
                        className="mb-1.5 block text-xs font-medium text-gray-700"
                    >
                        Full name
                    </label>
                    <IconInput
                        id="name"
                        name="name"
                        icon={User}
                        value={data.name}
                        autoComplete="name"
                        autoFocus
                        placeholder="John Doe"
                        onChange={e => setData('name', e.target.value)}
                        error={Boolean(errors.name)}
                        required
                    />
                    <InputError message={errors.name} className="mt-1.5" />
                </div>

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
                        placeholder="you@example.com"
                        onChange={e => setData('email', e.target.value)}
                        error={Boolean(errors.email)}
                        required
                    />
                    <InputError message={errors.email} className="mt-1.5" />
                </div>

                <div>
                    <label
                        htmlFor="password"
                        className="mb-1.5 block text-xs font-medium text-gray-700"
                    >
                        Password
                    </label>
                    <IconInput
                        id="password"
                        name="password"
                        type="password"
                        icon={Lock}
                        value={data.password}
                        autoComplete="new-password"
                        placeholder="At least 8 characters"
                        onChange={e => setData('password', e.target.value)}
                        error={Boolean(errors.password)}
                        required
                    />
                    <InputError message={errors.password} className="mt-1.5" />
                </div>

                <div>
                    <label
                        htmlFor="password_confirmation"
                        className="mb-1.5 block text-xs font-medium text-gray-700"
                    >
                        Confirm password
                    </label>
                    <IconInput
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        icon={Lock}
                        value={data.password_confirmation}
                        autoComplete="new-password"
                        placeholder="Repeat your password"
                        onChange={e => setData('password_confirmation', e.target.value)}
                        error={Boolean(errors.password_confirmation)}
                        required
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
                            <span>Creating account...</span>
                        </>
                    ) : (
                        <span>Create account</span>
                    )}
                </button>
            </form>

            <p className="mt-6 text-center text-sm text-gray-500">
                Already have an account?{' '}
                <Link
                    href={route('login')}
                    className="font-medium text-red-800 hover:text-red-900 hover:underline"
                >
                    Sign in
                </Link>
            </p>
        </GuestLayout>
    )
}