import IconInput from '../../components/Iconinput'
import InputError from '@/components/InputError'
import GuestLayout from '@/layouts/GuestLayout'
import { Head, useForm } from '@inertiajs/react'
import { Loader2, Lock, ShieldCheck } from 'lucide-react'
import React from 'react'

interface ConfirmPasswordForm {
    password: string
}

export default function ConfirmPassword() {
    const { data, setData, post, processing, errors, reset } = useForm<ConfirmPasswordForm>({
        password: '',
    })

    const submit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault()
        post(route('password.confirm'), {
            onFinish: () => reset('password'),
        })
    }

    return (
        <GuestLayout>
            <Head title="Confirm Password" />

            <div className="mb-6 flex flex-col items-center text-center">
                <div className="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-red-50 ring-1 ring-red-100">
                    <ShieldCheck
                        size={26}
                        className="text-red-800"
                        strokeWidth={1.75}
                    />
                </div>
                <h2 className="text-xl font-semibold tracking-tight text-gray-900">
                    Confirm your password
                </h2>
                <p className="mt-2 text-sm leading-relaxed text-gray-500">
                    This is a secure area. Please confirm your password to continue.
                </p>
            </div>

            <form onSubmit={submit} className="space-y-4">
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
                        autoFocus
                        placeholder="Enter your password"
                        onChange={e => setData('password', e.target.value)}
                        error={Boolean(errors.password)}
                    />
                    <InputError message={errors.password} className="mt-1.5" />
                </div>

                <button
                    type="submit"
                    disabled={processing}
                    className="mt-2 flex w-full items-center justify-center gap-2 rounded-lg bg-red-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all duration-150 hover:bg-red-900 active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-70"
                >
                    {processing ? (
                        <>
                            <Loader2 size={16} className="animate-spin" />
                            <span>Confirming...</span>
                        </>
                    ) : (
                        <span>Confirm</span>
                    )}
                </button>
            </form>
        </GuestLayout>
    )
}