import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import ShopLayout from '@/layouts/shop-layout';
import HeroSlider from '@/components/shop/hero-slider';
import CategorySlider from '@/components/shop/category-slider';
import FlashSale from '@/components/shop/flash-sale';
import TrendingProducts from '@/components/shop/trending-products';
import BrandMarquee from '@/components/shop/brand-marquee';
import BlogSection from '@/components/shop/blog-section';
import Subscription from '@/components/shop/subscription';
import SpecialProducts from '@/components/shop/special-products';
import BestSelling from '@/components/shop/best-selling';
import NewArrivals from '@/components/shop/new-arrivals';
import FavouriteProducts from '@/components/shop/favourite-products';
import { isSectionVisible, getThemeSettings } from '@/lib/theme-settings';

/* SKELETON COMPONENTS FOR ELEGANT SHIMMER OVERLAY */

const HeroSliderSkeleton = () => (
    <div className="skeleton-box" style={{ width: '100%', height: '580px', borderRadius: 0 }}></div>
);

const FeaturesSkeleton = () => (
    <section className="features mt_20">
        <div className="container">
            <div className="row">
                {[...Array(4)].map((_, i) => (
                    <div key={i} className="col-xl-3 col-md-6 mb-4 mb-xl-0">
                        <div className="skeleton-box" style={{ height: '100px', width: '100%', borderRadius: '12px' }}></div>
                    </div>
                ))}
            </div>
        </div>
    </section>
);

const FlashSaleSkeleton = () => (
    <section className="flash_sell_2 mt_95">
        <div className="container">
            <div className="row align-items-center mb-4">
                <div className="col-6">
                    <div className="skeleton-box" style={{ height: '35px', width: '150px' }}></div>
                </div>
                <div className="col-6 text-end">
                    <div className="skeleton-box ms-auto" style={{ height: '35px', width: '200px' }}></div>
                </div>
            </div>
            <div className="row mt_25">
                {[...Array(5)].map((_, i) => (
                    <div key={i} className="col-xl-1-5 col-md-4 col-sm-6 mb-4">
                        <div className="skeleton-box" style={{ height: '320px', width: '100%', borderRadius: '16px' }}></div>
                    </div>
                ))}
            </div>
        </div>
    </section>
);

const CategorySliderSkeleton = () => (
    <section className="category_2 mt_95">
        <div className="container">
            <div className="row mb-4">
                <div className="col-12 text-center">
                    <div className="skeleton-box mx-auto" style={{ height: '35px', width: '200px' }}></div>
                </div>
            </div>
            <div className="row justify-content-center mt_25">
                {[...Array(6)].map((_, i) => (
                    <div key={i} className="col-xl-2 col-md-3 col-sm-4 col-6 text-center mb-4">
                        <div className="skeleton-box skeleton-circle mx-auto" style={{ height: '130px', width: '130px' }}></div>
                        <div className="skeleton-box mx-auto mt-3" style={{ height: '15px', width: '80px' }}></div>
                    </div>
                ))}
            </div>
        </div>
    </section>
);

const TrendingProductsSkeleton = () => (
    <section className="trending_products mt_95">
        <div className="container">
            <div className="row justify-content-center mb-4">
                <div className="col-md-6 text-center">
                    <div className="skeleton-box mx-auto mb-3" style={{ height: '35px', width: '250px' }}></div>
                    <div className="d-flex justify-content-center gap-2 mt-3">
                        {[...Array(4)].map((_, i) => (
                            <div key={i} className="skeleton-box" style={{ height: '35px', width: '100px', borderRadius: '20px' }}></div>
                        ))}
                    </div>
                </div>
            </div>
            <div className="row mt_30">
                {[...Array(5)].map((_, i) => (
                    <div key={i} className="col-xl-1-5 col-md-4 col-sm-6 mb-4">
                        <div className="skeleton-box" style={{ height: '320px', width: '100%', borderRadius: '16px' }}></div>
                    </div>
                ))}
            </div>
        </div>
    </section>
);

const SpecialProductsSkeleton = () => (
    <section className="special_product_2 pt_85">
        <div className="container">
            <div className="row">
                <div className="col-12 mb-4">
                    <div className="skeleton-box" style={{ height: '35px', width: '250px' }}></div>
                </div>
            </div>
            <div className="row pt_15">
                <div className="col-xl-4">
                    <div className="skeleton-box" style={{ height: '450px', width: '100%', borderRadius: '16px' }}></div>
                </div>
                <div className="col-xl-8">
                    <div className="row">
                        {[...Array(4)].map((_, i) => (
                            <div key={i} className="col-md-6 mb-4">
                                <div className="skeleton-box" style={{ height: '140px', width: '100%', borderRadius: '12px' }}></div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </div>
    </section>
);

const BestSellingSkeleton = () => (
    <section className="best_selling_product_2 mt_95">
        <div className="container">
            <div className="row mb-4">
                <div className="col-12">
                    <div className="skeleton-box" style={{ height: '35px', width: '250px' }}></div>
                </div>
            </div>
            <div className="row mt_15">
                <div className="col-xl-7">
                    <div className="row">
                        {[...Array(3)].map((_, i) => (
                            <div key={i} className="col-xl-4 col-sm-6 mb-4">
                                <div className="skeleton-box" style={{ height: '220px', width: '100%', borderRadius: '12px' }}></div>
                            </div>
                        ))}
                    </div>
                </div>
                <div className="col-xl-5">
                    <div className="skeleton-box" style={{ height: '220px', width: '100%', borderRadius: '12px' }}></div>
                </div>
            </div>
        </div>
    </section>
);

const NewArrivalsSkeleton = () => (
    <section className="new_arrival_2 mt_95">
        <div className="container">
            <div className="row mb-4">
                <div className="col-12">
                    <div className="skeleton-box" style={{ height: '35px', width: '250px' }}></div>
                </div>
            </div>
            <div className="row mt_15">
                {[...Array(5)].map((_, i) => (
                    <div key={i} className="col-xl-1-5 col-6 mb-4">
                        <div className="skeleton-box" style={{ height: '320px', width: '100%', borderRadius: '16px' }}></div>
                    </div>
                ))}
            </div>
        </div>
    </section>
);

const FavouriteProductsSkeleton = () => (
    <section className="favourite_product_2 mt_100">
        <div className="container">
            <div className="row">
                <div className="col-xl-3 col-lg-4">
                    <div className="skeleton-box" style={{ height: '400px', width: '100%', borderRadius: '16px' }}></div>
                </div>
                <div className="col-xl-9 col-lg-8">
                    <div className="row mb-4">
                        <div className="col-12">
                            <div className="skeleton-box" style={{ height: '35px', width: '250px' }}></div>
                        </div>
                    </div>
                    <div className="row">
                        {[...Array(3)].map((_, i) => (
                            <div key={i} className="col-xl-4 mb-4">
                                <div className="skeleton-box" style={{ height: '320px', width: '100%', borderRadius: '16px' }}></div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </div>
    </section>
);

const BrandMarqueeSkeleton = () => (
    <div className="container mt_60">
        <div className="skeleton-box" style={{ height: '60px', width: '100%', borderRadius: '12px' }}></div>
    </div>
);

const BlogSectionSkeleton = () => (
    <section className="blog mt_95 mb_70">
        <div className="container">
            <div className="row mb-4">
                <div className="col-12 text-center">
                    <div className="skeleton-box mx-auto" style={{ height: '35px', width: '220px' }}></div>
                </div>
            </div>
            <div className="row mt_25">
                {[...Array(3)].map((_, i) => (
                    <div key={i} className="col-lg-4 col-md-6 mb-4">
                        <div className="skeleton-box" style={{ height: '240px', width: '100%', borderRadius: '16px' }}></div>
                        <div className="skeleton-box mt-3" style={{ height: '20px', width: '90%' }}></div>
                        <div className="skeleton-box mt-2" style={{ height: '15px', width: '60%' }}></div>
                    </div>
                ))}
            </div>
        </div>
    </section>
);

const SubscriptionSkeleton = () => (
    <div className="container mt_50 mb_80">
        <div className="skeleton-box" style={{ height: '180px', width: '100%', borderRadius: '16px' }}></div>
    </div>
);

import { api, HomeData, Product } from '@/services/api';

export default function Welcome({ products: initialProducts = [] }: { products?: any[] }) {
    const [isLoaded, setIsLoaded] = useState(false);
    const [homeData, setHomeData] = useState<HomeData | null>(null);

    useEffect(() => {
        let isMounted = true;
        api.getHome()
            .then(data => {
                if (isMounted) {
                    setHomeData(data);
                    setIsLoaded(true);
                }
            })
            .catch(err => {
                console.error("Failed to load WooCommerce home data:", err);
                if (isMounted) {
                    setIsLoaded(true);
                }
            });

        return () => {
            isMounted = false;
        };
    }, []);

    const allProducts = homeData?.allProducts || initialProducts;
    const flashSaleProducts = homeData?.flashSale?.length ? homeData.flashSale : allProducts.filter((p: any) => p.oldPrice || p.discount).slice(0, 6);
    const bestSellingProducts = homeData?.bestSelling?.length ? homeData.bestSelling : allProducts.slice(0, 6);
    const newArrivalProducts = homeData?.allProducts?.length ? homeData.allProducts.slice(0, 5) : allProducts.filter((p: any) => p.isNew).slice(0, 5);
    const specialProducts = homeData?.featured?.length >= 12 ? homeData.featured.slice(0, 12) : allProducts.slice(0, 12);
    const trendingProducts = allProducts;


    return (
        <ShopLayout isLoaded={isLoaded}>

            <div style={{ position: 'relative', overflow: 'hidden' }} suppressHydrationWarning={true}>
                {/* SKELETON OVERLAY - Fades out smoothly when page is ready */}
                <div 
                    style={{ 
                        position: 'absolute', 
                        top: 0, 
                        left: 0, 
                        right: 0, 
                        zIndex: 99, 
                        background: '#fff',
                        opacity: isLoaded ? 0 : 1,
                        visibility: isLoaded ? 'hidden' : 'visible',
                        transition: 'opacity 0.4s ease-in-out, visibility 0.4s ease-in-out',
                        pointerEvents: 'none'
                    }}
                >
                    <HeroSliderSkeleton />
                    <FeaturesSkeleton />
                    <FlashSaleSkeleton />
                    <CategorySliderSkeleton />
                    <SpecialProductsSkeleton />
                    <TrendingProductsSkeleton />
                    <BestSellingSkeleton />
                    <NewArrivalsSkeleton />
                    <FavouriteProductsSkeleton />
                    <BrandMarqueeSkeleton />
                    <BlogSectionSkeleton />
                    <SubscriptionSkeleton />
                </div>

                {/* REAL PAGE CONTENT - Always mounted so Slick initializes correctly, fades in smoothly */}
                <div style={{ opacity: isLoaded ? 1 : 0, transition: 'opacity 0.4s ease-in-out' }}>
                    {/* Hero Banner Slider */}
                    {isSectionVisible('hero_slider') && <HeroSlider />}

                    {/*============================
                        FEATURES START
                    ==============================*/}
                    {isSectionVisible('features_bar') && (
                        <section className="features mt_20">
                            <div className="container">
                                <div className="row">
                                    <div className="col-xl-3 col-md-6 wow fadeInUp">
                                        <div className="features_item purple">
                                            <div className="icon">
                                                <img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/feature-icon_1.svg'} alt="feature" />
                                            </div>
                                            <div className="text">
                                                <h3>Return & refund</h3>
                                                <p>Money back guarantee</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="col-xl-3 col-md-6 wow fadeInUp">
                                        <div className="features_item green">
                                            <div className="icon">
                                                <img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/feature-icon_3.svg'} alt="feature" />
                                            </div>
                                            <div className="text">
                                                <h3>Quality Support</h3>
                                                <p>Always online 24/7</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="col-xl-3 col-md-6 wow fadeInUp">
                                        <div className="features_item orange">
                                            <div className="icon">
                                                <img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/feature-icon_2.svg'} alt="feature" />
                                            </div>
                                            <div className="text">
                                                <h3>Secure Payment</h3>
                                                <p>30% off by subscribing</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="col-xl-3 col-md-6 wow fadeInUp">
                                        <div className="features_item">
                                            <div className="icon">
                                                <img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/feature-icon_4.svg'} alt="feature" />
                                            </div>
                                            <div className="text">
                                                <h3>Daily Offers</h3>
                                                <p>20% off by subscribing</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    )}
                    {/*============================
                        FEATURES END
                    ==============================*/}

                    {/* Flash Sale Component (with countdown timer) */}
                    {isSectionVisible('flash_sale') && <FlashSale products={flashSaleProducts} />}

                    {/* Category Slider Component */}
                    {isSectionVisible('category_slider') && <CategorySlider />}

                    {/* Special Brand Products Component */}
                    {isSectionVisible('special_products') && <SpecialProducts products={specialProducts} />}

                    {/* Trending Products Component (with Tabs) */}
                    {isSectionVisible('trending_products') && <TrendingProducts products={trendingProducts} />}

                    {/* Best Selling Products Component */}
                    {isSectionVisible('best_selling') && <BestSelling products={bestSellingProducts} />}

                    {/* New Arrival Products Component */}
                    {isSectionVisible('new_arrivals') && <NewArrivals products={newArrivalProducts} />}

                    {/* Favourite Products Component */}
                    {isSectionVisible('favourite_products') && <FavouriteProducts products={allProducts.slice(0, 4)} />}

                    {/* Brand Logo Slider/Marquee Component */}
                    {isSectionVisible('brand_marquee') && <BrandMarquee />}

                    {/* Blog Posts Component */}
                    {isSectionVisible('blog_section') && <BlogSection />}

                    {/* Newsletter Subscription Component */}
                    {isSectionVisible('subscription') && <Subscription />}
                </div>
            </div>
        </ShopLayout>
    );
}



