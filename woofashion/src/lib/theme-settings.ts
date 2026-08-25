import { useState, useEffect } from 'react';

/**
 * WooFashion Theme Settings State & Helpers
 */

export interface ThemeSettings {
    general?: {
        site_title?: string;
        site_tagline?: string;
        brand_name?: string;
        logo_url?: string;
        footer_logo_url?: string;
        logo_height?: string | number;
        primary_color?: string;
        whatsapp_number?: string;
        whatsapp_enable?: string;
    };
    header?: {
        top_announcement?: string;
        hotline_phone?: string;
        support_email?: string;
        enable_track_order?: string;
    };
    homepage_sections?: {
        hero_slider?: string;
        features_bar?: string;
        flash_sale?: string;
        category_slider?: string;
        special_products?: string;
        trending_products?: string;
        best_selling?: string;
        new_arrivals?: string;
        favourite_products?: string;
        brand_marquee?: string;
        blog_section?: string;
        subscription?: string;
    };
    section_titles?: {
        special_title?: string;
        trending_title?: string;
        best_selling_title?: string;
        new_arrivals_title?: string;
        flash_sale_title?: string;
        categories_title?: string;
        blog_title?: string;
    };
    footer?: {
        about_bio?: string;
        contact_address?: string;
        contact_phone?: string;
        contact_email?: string;
        copyright_text?: string;
        facebook?: string;
        twitter?: string;
        instagram?: string;
        linkedin?: string;
        youtube?: string;
    };
}

const defaultThemeSettings: ThemeSettings = {
    general: {
        brand_name: 'WoocomFashion',
        logo_url: '',
        footer_logo_url: '',
        primary_color: '#f59e0b',
        whatsapp_number: '+8801700000000',
        whatsapp_enable: 'yes',
    },
    header: {
        top_announcement: 'Free Shipping on orders over ৳1000 | 24/7 Dedicated Support',
        hotline_phone: '+880 9612-888999',
        support_email: 'support@woocomfashion.com',
        enable_track_order: 'yes',
    },
    homepage_sections: {
        hero_slider: 'yes',
        features_bar: 'yes',
        flash_sale: 'yes',
        category_slider: 'yes',
        special_products: 'yes',
        trending_products: 'yes',
        best_selling: 'yes',
        new_arrivals: 'yes',
        favourite_products: 'yes',
        brand_marquee: 'yes',
        blog_section: 'yes',
        subscription: 'yes',
    },
    section_titles: {
        special_title: 'Our Spatial Brand Products',
        trending_title: 'Our Trending Products',
        best_selling_title: 'Our Best Selling Products',
        new_arrivals_title: 'Our New Arrival Products',
        flash_sale_title: 'Flash Sale',
        categories_title: 'Popular Categories',
        blog_title: 'Latest News & Articles',
    },
    footer: {
        about_bio: 'WoocomFashion is your premium destination for the latest fashion trends. Discover curated collections crafted for comfort and style.',
        contact_address: '37 W 24th St, New York, NY / Dhaka, Bangladesh',
        contact_phone: '+123 324 5879 39',
        contact_email: 'info@WoocomFashion.com',
        copyright_text: 'Copyright @ WoocomFashion 2026. All rights reserved.',
        facebook: 'https://facebook.com',
        twitter: 'https://twitter.com',
        instagram: 'https://instagram.com',
        linkedin: 'https://linkedin.com',
    }
};

let currentThemeSettings: ThemeSettings = {
    ...defaultThemeSettings,
    ...(typeof window !== 'undefined' && (window as any).wpData?.themeSettings ? (window as any).wpData.themeSettings : {})
};

export function getThemeSettings(): ThemeSettings {
    if (typeof window !== 'undefined' && (window as any).wpData?.themeSettings) {
        return {
            ...defaultThemeSettings,
            ...(window as any).wpData.themeSettings,
            ...currentThemeSettings
        };
    }
    return currentThemeSettings;
}

export function setThemeSettings(newSettings: Partial<ThemeSettings>): void {
    if (!newSettings) return;
    currentThemeSettings = {
        ...currentThemeSettings,
        ...newSettings,
        general: { ...currentThemeSettings.general, ...newSettings.general },
        header: { ...currentThemeSettings.header, ...newSettings.header },
        homepage_sections: { ...currentThemeSettings.homepage_sections, ...newSettings.homepage_sections },
        section_titles: { ...currentThemeSettings.section_titles, ...newSettings.section_titles },
        footer: { ...currentThemeSettings.footer, ...newSettings.footer },
    };

    if (typeof window !== 'undefined') {
        window.dispatchEvent(new CustomEvent('theme-settings-changed', { detail: currentThemeSettings }));
    }
}

export function useThemeSettings(): ThemeSettings {
    const [settings, setSettings] = useState<ThemeSettings>(getThemeSettings());

    useEffect(() => {
        const handler = () => {
            setSettings(getThemeSettings());
        };
        window.addEventListener('theme-settings-changed', handler);
        return () => window.removeEventListener('theme-settings-changed', handler);
    }, []);

    return settings;
}

export function isSectionVisible(sectionKey: keyof NonNullable<ThemeSettings['homepage_sections']>): boolean {
    const settings = getThemeSettings();
    return (settings.homepage_sections?.[sectionKey] ?? 'yes') === 'yes';
}

export function getSectionHeading(titleKey: keyof NonNullable<ThemeSettings['section_titles']>, fallback: string): string {
    const settings = getThemeSettings();
    return settings.section_titles?.[titleKey] || fallback;
}
