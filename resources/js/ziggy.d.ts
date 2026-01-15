export {};

declare global {
    function route(name: string, params?: Record<string, any> | number | string, absolute?: boolean): string;
    }