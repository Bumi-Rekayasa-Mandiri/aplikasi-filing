import Checkbox from '@/components/Checkbox'
import InputError from '@/components/InputError'
import InputLabel from '@/components/InputLabel'
import PrimaryButton from '@/components/PrimaryButton'
import TextInput from '@/components/TextInput'
import GuestLayout from '@/layouts/GuestLayout'
import { Head, Link, useForm } from '@inertiajs/react'
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

export default function Login({
    status,
    canResetPassword,
}: LoginProps) {
    const { data, setData, post, processing, errors, reset } =
        useForm<LoginForm>({
            email: '',
            password: '',
            remember: false,
        })

    const submit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault()

        post(route('login'), {
            onFinish: () => reset('password'),
        })
    }

    return (
        <GuestLayout>
            <Head title="Log in" />

            {status && (
                <div className="mb-4 text-sm font-medium text-green-600">
                    {status}
                </div>
            )}

            <form onSubmit={submit}>
                <div>
                    <InputLabel htmlFor="email" value="Email" />

                    <TextInput
                        id="email"
                        type="email"
                        name="email"
                        value={data.email}
                        className="mt-1 block w-full"
                        autoComplete="username"
                        isFocused
                        onChange={(e) =>
                            setData('email', e.target.value)
                        }
                    />

                    <InputError message={errors.email} className="mt-2" />
                </div>

                <div className="mt-4">
                    <InputLabel htmlFor="password" value="Password" />

                    <TextInput
                        id="password"
                        type="password"
                        name="password"
                        value={data.password}
                        className="mt-1 block w-full"
                        autoComplete="current-password"
                        onChange={(e) =>
                            setData('password', e.target.value)
                        }
                    />

                    <InputError message={errors.password} className="mt-2" />
                </div>

                <div className="mt-4 block">
                    <label className="flex items-center">
                        <Checkbox
                            name="remember"
                            checked={data.remember}
                            onChange={(e) =>
                                setData('remember', e.target.checked)
                            }
                        />
                        <span className="ms-2 text-sm text-gray-600">
                            Remember me
                        </span>
                    </label>
                </div>

                <div className="flex justify-center">
                    <button
                    type="submit"
                    className="bg-green-700 mt-4 text-sm text-white w-full max-w-sm object-contain py-2 rounded-full font-semibold"
                    disabled={processing}
                            >
                        Login
                    </button>
                </div>
            

                <div className="mt-2 flex justify-between text-sm">
                    {canResetPassword && (
                        <Link
                            href={route('password.request')}
                            className="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Forgot your password?
                        </Link>
                    )}

                        <Link
                            href={route('register')}
                            className="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Don't have an account?
                        </Link>
                </div>
            </form>
        </GuestLayout>
    )
}