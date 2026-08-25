/**
 * API service for communicating with WordPress / WooCommerce REST API
 */

export interface Color {
    name: string;
    hex: string;
}

export interface Product {
    id: number;
    name: string;
    slug: string;
    category: string;
    category_slug: string;
    price: number;
    oldPrice?: number;
    discount?: number;
    isNew?: boolean;
    stockStatus: string;
    rating: number;
    reviewsCount: number;
    description: string;
    shortDescription: string;
    img: string;
    images: string[];
    colors: Color[];
    sizes: string[];
    sku: string;
    tags: string[];
}

export interface Category {
    id: number;
    name: string;
    slug: string;
    count: number;
    icon?: string;
    img?: string;
}

export interface SliderItem {
    id: number;
    title: string;
    subtitle: string;
    discount: string;
    btnText: string;
    link: string;
    bgImg: string;
}

export interface HomeData {
    sliders: SliderItem[];
    categories: Category[];
    flashSale: Product[];
    bestSelling: Product[];
    featured: Product[];
    allProducts: Product[];
}

export interface ProductsResponse {
    products: Product[];
    total: number;
    totalPages: number;
    page: number;
    categories: Category[];
}

export interface ShippingMethod {
    id: string;
    method_id: string;
    title: string;
    cost: number;
    free_min?: number;
}

export interface PaymentGateway {
    id: string;
    title: string;
    description: string;
}

export interface CheckoutOptions {
    shippingMethods: ShippingMethod[];
    paymentGateways: PaymentGateway[];
}

export interface OrderPayload {
    fullName: string;
    phone: string;
    address: string;
    email?: string;
    items: Array<{
        id: number;
        name: string;
        price: number;
        quantity: number;
        color?: string;
        size?: string;
    }>;
    paymentMethod: string;
    shippingCost: number;
}

export interface OrderResponse {
    success: boolean;
    orderId: number;
    orderKey: string;
    orderTotal: number;
    status: string;
    message: string;
}

import { setCurrencySettings } from '@/lib/currency';
import { setThemeSettings, ThemeSettings } from '@/lib/theme-settings';

const getBaseUrl = (): string => {
    // @ts-ignore
    if (window.wpData && window.wpData.apiUrl) {
        // @ts-ignore
        const url = window.wpData.apiUrl;
        return url.endsWith('/') ? `${url}woofashion/v1` : `${url}/woofashion/v1`;
    }
    return '/wp-json/woofashion/v1';
};

export const api = {
    async getHome(): Promise<HomeData> {
        const res = await fetch(`${getBaseUrl()}/home`);
        if (!res.ok) throw new Error('Failed to fetch home data');
        const data = await res.json();
        if (data.currency) {
            setCurrencySettings(data.currency);
        }
        if (data.themeSettings) {
            setThemeSettings(data.themeSettings);
        }
        return data;
    },

    async getThemeSettings(): Promise<ThemeSettings> {
        const res = await fetch(`${getBaseUrl()}/theme-settings`);
        if (!res.ok) throw new Error('Failed to fetch theme settings');
        const data = await res.json();
        setThemeSettings(data);
        return data;
    },

    async getProducts(params: {
        category?: string;
        search?: string;
        sort?: string;
        min_price?: number;
        max_price?: number;
        page?: number;
        per_page?: number;
    } = {}): Promise<ProductsResponse> {
        const searchParams = new URLSearchParams();
        if (params.category && params.category !== 'all') searchParams.set('category', params.category);
        if (params.search) searchParams.set('search', params.search);
        if (params.sort) searchParams.set('sort', params.sort);
        if (params.min_price) searchParams.set('min_price', params.min_price.toString());
        if (params.max_price) searchParams.set('max_price', params.max_price.toString());
        if (params.page) searchParams.set('page', params.page.toString());
        if (params.per_page) searchParams.set('per_page', params.per_page.toString());

        const query = searchParams.toString();
        const url = `${getBaseUrl()}/products${query ? `?${query}` : ''}`;
        const res = await fetch(url);
        if (!res.ok) throw new Error('Failed to fetch products');
        const data = await res.json();
        if (data.currency) {
            setCurrencySettings(data.currency);
        }
        return data;
    },

    async getSingleProduct(slug: string): Promise<{ product: Product; relatedProducts: Product[] }> {
        const res = await fetch(`${getBaseUrl()}/products/${slug}`);
        if (!res.ok) throw new Error(`Failed to fetch product ${slug}`);
        const data = await res.json();
        if (data.currency) {
            setCurrencySettings(data.currency);
        }
        return data;
    },

    async getCheckoutOptions(): Promise<CheckoutOptions> {
        const res = await fetch(`${getBaseUrl()}/checkout-options`);
        if (!res.ok) throw new Error('Failed to fetch checkout options');
        const data = await res.json();
        if (data.currency) {
            setCurrencySettings(data.currency);
        }
        return data;
    },

    async createOrder(data: OrderPayload): Promise<OrderResponse> {
        const res = await fetch(`${getBaseUrl()}/order`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                // @ts-ignore
                'X-WP-Nonce': window.wpData?.nonce || '',
            },
            body: JSON.stringify(data),
        });
        if (!res.ok) {
            const err = await res.json().catch(() => ({ message: 'Order submission failed' }));
            throw new Error(err.message || 'Order submission failed');
        }
        return res.json();
    },

    async trackOrder(orderId: string, phone?: string): Promise<any> {
        const searchParams = new URLSearchParams();
        searchParams.set('order_id', orderId);
        if (phone) searchParams.set('phone', phone);

        const res = await fetch(`${getBaseUrl()}/track-order?${searchParams.toString()}`);
        if (!res.ok) {
            const err = await res.json().catch(() => ({ message: 'Order not found' }));
            throw new Error(err.message || 'Order not found');
        }
        return res.json();
    },
};
