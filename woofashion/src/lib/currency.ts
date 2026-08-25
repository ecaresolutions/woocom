export interface CurrencySettings {
    code: string;
    symbol: string;
    position: 'left' | 'right' | 'left_space' | 'right_space';
    decimals: number;
    decimal_separator: string;
    thousand_separator: string;
}

declare global {
    interface Window {
        wpData?: {
            apiUrl?: string;
            nonce?: string;
            homeUrl?: string;
            currency?: CurrencySettings;
        };
    }
}

export const defaultCurrency: CurrencySettings = {
    code: 'BDT',
    symbol: '৳',
    position: 'left',
    decimals: 0,
    decimal_separator: '.',
    thousand_separator: ','
};

let currentCurrency: CurrencySettings = defaultCurrency;

export function setCurrencySettings(settings: Partial<CurrencySettings>) {
    if (!settings) return;
    currentCurrency = { ...currentCurrency, ...settings };
    if (typeof window !== 'undefined') {
        if (!window.wpData) {
            // @ts-ignore
            window.wpData = {};
        }
        window.wpData.currency = {
            ...(window.wpData.currency || defaultCurrency),
            ...settings
        };
    }
}

export function getCurrencySettings(): CurrencySettings {
    if (typeof window !== 'undefined' && window.wpData?.currency) {
        return { ...defaultCurrency, ...window.wpData.currency };
    }
    return currentCurrency;
}

export function getCurrencySymbol(): string {
    return getCurrencySettings().symbol || '৳';
}

export function getCurrencyCode(): string {
    return getCurrencySettings().code || 'BDT';
}

export function formatPrice(amount: number | string | undefined | null): string {
    if (amount === undefined || amount === null || amount === '') return '';
    const num = typeof amount === 'number' ? amount : parseFloat(String(amount));
    if (isNaN(num)) return String(amount);

    const settings = getCurrencySettings();
    const decimals = typeof settings.decimals === 'number' ? settings.decimals : 0;
    
    // Format number with WooCommerce decimals
    const parts = num.toFixed(decimals).split('.');
    let integerPart = parts[0];
    const decimalPart = parts[1];

    // Add thousand separator
    const thousandSep = settings.thousand_separator !== undefined ? settings.thousand_separator : ',';
    const decimalSep = settings.decimal_separator !== undefined ? settings.decimal_separator : '.';
    
    integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, thousandSep);

    const formattedNumber = decimals > 0 && decimalPart !== undefined 
        ? `${integerPart}${decimalSep}${decimalPart}` 
        : integerPart;

    const symbol = settings.symbol || '৳';

    switch (settings.position) {
        case 'right':
            return `${formattedNumber}${symbol}`;
        case 'left_space':
            return `${symbol} ${formattedNumber}`;
        case 'right_space':
            return `${formattedNumber} ${symbol}`;
        case 'left':
        default:
            return `${symbol}${formattedNumber}`;
    }
}
