import InputError from '@/components/InputError'
import InputLabel from '@/components/InputLabel'
import PrimaryButton from '@/components/PrimaryButton'
import TextInput from '@/components/TextInput'
import GuestLayout from '@/layouts/GuestLayout'
import { Head, Link, useForm } from '@inertiajs/react'
import React from 'react'

interface RegisterForm {
    name: string
    email: string
    password: string
    password_confirmation: string
}

export default function Register() {
    const { data, setData, post, processing, errors, reset } =
        useForm<RegisterForm>({
            name: '',
            email: '',
            password: '',
            password_confirmation: '',
        })

    const submit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault()

        post(route('register'), {
            onFinish: () =>
                reset('password', 'password_confirmation'),
        })
    }

    return (
        <GuestLayout>
            <Head title="Register" />

            <form onSubmit={submit}>
                <div>
                    <InputLabel htmlFor="name" value="Name" />

                    <TextInput
                        id="name"
                        name="name"
                        value={data.name}
                        className="mt-1 block w-full"
                        autoComplete="name"
                        isFocused
                        onChange={(e) =>
                            setData('name', e.target.value)
                        }
                        required
                    />

                    <InputError message={errors.name} className="mt-2" />
                </div>

                <div className="mt-4">
                    <InputLabel htmlFor="email" value="Email" />

                    <TextInput
                        id="email"
                        type="email"
                        name="email"
                        value={data.email}
                        className="mt-1 block w-full"
                        autoComplete="username"
                        onChange={(e) =>
                            setData('email', e.target.value)
                        }
                        required
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
                        autoComplete="new-password"
                        onChange={(e) =>
                            setData('password', e.target.value)
                        }
                        required
                    />

                    <InputError message={errors.password} className="mt-2" />
                </div>

                <div className="mt-4">
                    <InputLabel
                        htmlFor="password_confirmation"
                        value="Confirm Password"
                    />

                    <TextInput
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        value={data.password_confirmation}
                        className="mt-1 block w-full"
                        autoComplete="new-password"
                        onChange={(e) =>
                            setData(
                                'password_confirmation',
                                e.target.value,
                            )
                        }
                        required
                    />

                    <InputError
                        message={errors.password_confirmation}
                        className="mt-2"
                    />
                </div>

                <div className="flex justify-center">
                    <button
                    type="submit"
                    className="bg-green-700 mt-6 text-sm text-white w-full max-w-sm object-contain py-2 rounded-full font-semibold"
                    disabled={processing}
                    >
                        Register
                    </button>
                </div>

                <div className="mt-3 flex justify-center text-sm">
                    <Link
                        href={route('login')}
                        className="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Already registered?
                    </Link>
                </div>
            </form>
        </GuestLayout>
    )
}