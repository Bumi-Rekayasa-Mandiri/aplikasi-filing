import { PageProps as InertiaPageProps } from '@inertiajs/core'

    export interface User {
        id: number
        name: string
        email: string
    }
    export interface AppPageProps extends InertiaPageProps {
        auth: {
            user: User
        }

        flash?: {
            success?: string
            error?: string
        }
    }
