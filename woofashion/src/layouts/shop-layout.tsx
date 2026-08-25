import Header from '@/components/shop/header';
import Footer from '@/components/shop/footer';
import { useEffect } from 'react';
import { useLocation } from 'react-router-dom';

interface Props {
    children: React.ReactNode;
    isLoaded?: boolean;
}

export default function ShopLayout({ children, isLoaded = true }: Props) {
    const { pathname } = useLocation();

    useEffect(() => {
        window.scrollTo(0, 0);
    }, [pathname]);

    return (
        <div className="shop-layout default_home" suppressHydrationWarning={true}>
            <style>{`
                /* Hide secondary slides before Slick Slider initializes to prevent vertical layout shift on load */
                .banner_2_slider:not(.slick-initialized) > *:not(:first-child),
                .category_2_slider:not(.slick-initialized) > *:not(:first-child),
                .flash_sell_2_slider:not(.slick-initialized) > *:not(:first-child) {
                    display: none !important;
                }

                /* Hide secondary tab content blocks before PWSTabs initializes to prevent layout shifting on load */
                .product_tabs > div[data-pws-tab]:not([data-pws-tab="tab111"]) {
                    display: none;
                }

                /* Skeleton Shimmer Animation */
                @keyframes shimmer {
                    0% {
                        background-position: -200% 0;
                    }
                    100% {
                        background-position: 200% 0;
                    }
                }
                .skeleton-box {
                    background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
                    background-size: 200% 100%;
                    animation: shimmer 1.6s infinite linear;
                    border-radius: 8px;
                }
                .skeleton-circle {
                    border-radius: 50% !important;
                }

                /* Hide FontAwesome pseudo-element arrows on category items to prevent empty squares when FontAwesome is blocked */
                .menu_cat_item > li > a::after {
                    display: none !important;
                    content: none !important;
                }

                /* Equal Height Product Cards and Perfect Alignment */
                .product_item {
                    display: flex !important;
                    flex-direction: column !important;
                    height: 100% !important;
                }
                .product_text {
                    display: flex !important;
                    flex-direction: column !important;
                    flex-grow: 1 !important;
                }
                .product_text .title {
                    font-size: 16px !important;
                    line-height: 1.3 !important;
                    display: -webkit-box !important;
                    -webkit-line-clamp: 2 !important;
                    -webkit-box-orient: vertical !important;
                    overflow: hidden !important;
                    margin-bottom: 2px !important;
                }
                .product_text .color {
                    margin-top: auto !important;
                    padding-top: 8px !important;
                }                @media (max-width: 991.99px) {
                    header.header_2 {
                        height: 70px !important;
                        display: flex !important;
                        align-items: center !important;
                    }
                    header.header_2 .container {
                        height: 100% !important;
                        display: flex !important;
                        align-items: center !important;
                    }
                    header.header_2 .row {
                        height: 100% !important;
                        width: 100% !important;
                        margin: 0 !important;
                        display: flex !important;
                        align-items: center !important;
                    }
                    header.header_2 .col-lg-2 {
                        width: 100% !important;
                        padding: 0 !important;
                        height: 100% !important;
                        display: flex !important;
                        align-items: center !important;
                    }
                    header.header_2 .header_logo_area {
                        width: 100% !important;
                        height: 100% !important;
                        display: flex !important;
                        align-items: center !important;
                        justify-content: space-between !important;
                        padding: 0 15px !important;
                    }
                    header.header_2 .mobile_menu_icon {
                        margin-top: 0 !important;
                        margin-bottom: 0 !important;
                        top: 0 !important;
                        position: relative !important;
                        display: flex !important;
                        align-items: center !important;
                        justify-content: center !important;
                    }
                }

                .header_track_btn {
                    background-color: var(--colorBlack) !important;
                    color: #ffffff !important;
                    font-weight: 600 !important;
                    font-size: 13px !important;
                    text-decoration: none !important;
                    line-height: 1 !important;
                    padding: 9px 18px !important;
                    border-radius: 30px !important;
                    transition: all 0.3s ease !important;
                    display: inline-flex !important;
                    align-items: center !important;
                    gap: 8px !important;
                    position: relative !important;
                    overflow: hidden !important;
                    z-index: 1 !important;
                }
                .header_track_btn span {
                    color: #ffffff !important;
                }
                .header_track_btn::after {
                    position: absolute !important;
                    content: "" !important;
                    width: 0 !important;
                    height: 100% !important;
                    top: 0 !important;
                    right: 0 !important;
                    z-index: -1 !important;
                    background-color: var(--themeColorTwo) !important;
                    transition: all 0.3s ease !important;
                    border-radius: 30px !important;
                }
                .header_track_btn:hover::after {
                    left: 0 !important;
                    width: 100% !important;
                }
                .header_track_btn:hover {
                    color: #ffffff !important;
                    transform: translateY(-2px) !important;
                    box-shadow: none !important;
                }
                .header_track_btn:hover span {
                    color: #ffffff !important;
                }

                /* Global Hover Lift & Shadow for All Shop Buttons */
                .common_btn {
                    transition: all 0.2s ease-in-out !important;
                }
                .common_btn:hover {
                    transform: translateY(-2px) !important;
                    box-shadow: none !important;
                }

                /* Remove box-shadow from details page tab buttons */
                .shop_details_des_area .nav-pills .nav-link,
                .shop_details_des_area .nav-pills .nav-link.active,
                .shop_details_des_area .nav-pills .nav-link:hover {
                    box-shadow: none !important;
                }

                /* Expand Menu Icon Container size for larger Lucide icons */
                .menu_icon li a.header_cart_link {
                    width: 32px !important;
                    height: 32px !important;
                    line-height: 32px !important;
                    display: inline-flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                }
                .menu_icon li a.header_cart_link b {
                    display: inline-flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                }
                .menu_icon li a.header_cart_link span {
                    top: -6px !important;
                    right: -10px !important;
                }

                /* Vertically center the header action list items */
                .menu_icon {
                    display: flex !important;
                    align-items: center !important;
                }
                .menu_icon li {
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                }

                /* Custom 5-column grid helper for static sections */
                @media (min-width: 1200px) {
                    .col-xl-1-5-static {
                        flex: 0 0 20% !important;
                        max-width: 20% !important;
                    }
                }

                /* Product price and color details spacing overrides */
                .product_item .price {
                    margin-top: 0px !important;
                    margin-bottom: 2px !important;
                    padding-bottom: 0px !important;
                }
                .product_item .color {
                    margin-top: 5px !important;
                    padding-top: 0px !important;
                    padding-left: 0px !important;
                    margin-bottom: 0px !important;
                }
                .product_item_2 .product_text {
                    padding-bottom: 2px !important;
                }
                .product_item_2 {
                    padding-bottom: 5px !important;
                }
            `}</style>
            
            {/* Header is always mounted so NiceSelect/Select2 initialize correctly, skeleton behaves as an overlay inside it */}
            <Header isLoaded={isLoaded} />
            
            <main>
                {children}
            </main>
            <Footer />
            {/* Scroll Progress Button */}
            <div className="progress-wrap">
                <svg className="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
                    <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" style={{ transition: 'stroke-dashoffset 10ms linear' }} suppressHydrationWarning={true} />
                </svg>
            </div>
        </div>
    );
}
