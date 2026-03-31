import { type ClassValue, clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

/**
 * Convert a route/href to a URL string.
 * If the input is already a URL path, return it as-is.
 * If it's a route name (from Ziggy), use the route() function.
 */
export function toUrl(href: string): string {
    if (typeof href !== 'string') return '';
    // If it looks like a path or full URL, return as-is
    if (href.startsWith('/') || href.startsWith('http')) return href;
    // Otherwise assume it's a route name - use Ziggy if available
    try {
        const route = (window as any).route;
        if (typeof route === 'function') {
            return route(href) || href;
        }
    } catch {
        // Ziggy not loaded
    }
    return href;
}

/**
 * Check if the given URL is the current/active page.
 */
export function urlIsActive(url: string, currentUrl: string): boolean {
    if (!url || !currentUrl) return false;
    const normalizedUrl = url.split('?')[0];
    const normalizedCurrent = currentUrl.split('?')[0];
    return normalizedCurrent === normalizedUrl || normalizedCurrent.startsWith(normalizedUrl + '/');
}
