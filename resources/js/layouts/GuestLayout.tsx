import ApplicationLogo from '@/components/ApplicationLogo';
import { Link } from '@inertiajs/react';
import { ReactNode } from 'react';

interface Props {
    children: React.ReactNode;
}

export default function GuestLayout({ children }: Props) {
    return (
        <div className="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0">
            <div>
                <Link href="/">
                    <ApplicationLogo className="w-full max-w-sm object-contain text-gray-500" />
                </Link>
            </div>

            <div className="mt-1 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg">
                {children}
            </div>
        </div>
    );
}
