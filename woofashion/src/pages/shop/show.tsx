import { useState, useEffect, useRef, useMemo } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import ShopLayout from '@/layouts/shop-layout';
import { Star, Check, Minus, Plus, ArrowRight } from 'lucide-react';
import { toast } from 'sonner';
import { formatPrice } from '@/lib/currency';

const ProductDetailsSkeleton = () => (
    <div className="shop_details mt_100">
        <div className="container">
            <div className="row">
                <div className="col-xxl-10 col-lg-12">
                    <div className="row">
                        {/* Gallery Skeleton */}
                        <div className="col-lg-6">
                            <div className="row">
                                <div className="col-xl-2 col-lg-3 col-md-3 order-2 order-md-1">
                                    <div className="d-flex flex-column gap-2">
                                        {[...Array(4)].map((_, i) => (
                                            <div key={i} className="skeleton-box" style={{ height: '70px', width: '100%' }}></div>
                                        ))}
                                    </div>
                                </div>
                                <div className="col-xl-10 col-lg-9 col-md-9 order-1 order-md-2 mb-3 mb-md-0">
                                    <div className="skeleton-box" style={{ height: '420px', width: '100%' }}></div>
                                </div>
                            </div>
                        </div>
                        {/* Specifications Skeleton */}
                        <div className="col-lg-6 mt-4 mt-lg-0">
                            <div className="d-flex flex-column gap-3">
                                <div className="skeleton-box" style={{ height: '40px', width: '80%' }}></div>
                                <div className="skeleton-box" style={{ height: '20px', width: '40%' }}></div>
                                <div className="skeleton-box" style={{ height: '35px', width: '30%' }}></div>
                                <div className="skeleton-box" style={{ height: '80px', width: '100%' }}></div>
                                <div className="d-flex gap-2">
                                    {[...Array(4)].map((_, i) => (
                                        <div key={i} className="skeleton-box skeleton-circle" style={{ height: '30px', width: '30px' }}></div>
                                    ))}
                                </div>
                                <div className="d-flex gap-2">
                                    {[...Array(4)].map((_, i) => (
                                        <div key={i} className="skeleton-box" style={{ height: '30px', width: '50px' }}></div>
                                    ))}
                                </div>
                                <div className="skeleton-box mt-3" style={{ height: '50px', width: '60%' }}></div>
                            </div>
                        </div>
                    </div>

                    {/* Tabs Content Skeleton */}
                    <div className="mt_60">
                        <div className="d-flex gap-4 border-bottom pb-2">
                            <div className="skeleton-box" style={{ height: '30px', width: '120px' }}></div>
                            <div className="skeleton-box" style={{ height: '30px', width: '120px' }}></div>
                            <div className="skeleton-box" style={{ height: '30px', width: '120px' }}></div>
                        </div>
                        <div className="mt-4 d-flex flex-column gap-2">
                            <div className="skeleton-box" style={{ height: '16px', width: '100%' }}></div>
                            <div className="skeleton-box" style={{ height: '16px', width: '95%' }}></div>
                            <div className="skeleton-box" style={{ height: '16px', width: '98%' }}></div>
                            <div className="skeleton-box" style={{ height: '16px', width: '80%' }}></div>
                        </div>
                    </div>

                    {/* Related Products Skeleton */}
                    <div className="related_products mt_95 pb_100">
                        <div className="skeleton-box mb-4" style={{ height: '40px', width: '300px' }}></div>
                        <div className="row">
                            {[...Array(5)].map((_, i) => (
                                <div key={i} className="col-xl-1-5 col-6 col-md-4 col-sm-6 mb-4">
                                    <div className="skeleton-box" style={{ height: '320px', width: '100%', borderRadius: '16px' }}></div>
                                </div>
                            ))}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
);

interface Color {
    name: string;
    hex: string;
}

interface Product {
    id: number;
    name: string;
    category: string;
    price: number;
    oldPrice?: number;
    stockStatus: string;
    rating: number;
    reviewsCount: number;
    description: string;
    images: string[];
    colors: Color[];
    sizes: string[];
    sku: string;
    tags: string[];
}

interface RelatedProduct {
    id: number;
    name: string;
    price: number;
    oldPrice?: number;
    discount?: number;
    isNew?: boolean;
    rating: number;
    reviewsCount: number;
    img: string;
    colors: string[];
}

interface Props {
    product: Product;
    relatedProducts: RelatedProduct[];
}

import { useParams } from 'react-router-dom';
import { useMemo } from 'react';

// Mock data for when we are navigating via React Router without backend Inertia props
const dummyProductFallback: Product = {
    id: 1,
    name: "Men's Premium Hoodie",
    category: "Men's Fashion",
    price: 55.00,
    oldPrice: 85.00,
    stockStatus: "In Stock",
    rating: 4.5,
    reviewsCount: 124,
    description: "Experience premium comfort and style with this high-quality hoodie. Crafted from organic cotton and recycled polyester, it offers a perfect blend of sustainability and durability.",
    images: [
        window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/best_sell_pro_img_1.jpg',
        window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/best_sell_pro_img_2.jpg',
        window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/best_sell_pro_img_3.jpg',
        window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/best_sell_pro_img_4.jpg',
        window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/best_sell_pro_img_1.jpg'
    ],
    colors: [
        { name: 'Red', hex: '#ff0000' },
        { name: 'Blue', hex: '#0000ff' },
        { name: 'Black', hex: '#000000' }
    ],
    sizes: ['S', 'M', 'L', 'XL', 'XXL'],
    sku: "LF-MD-1240",
    tags: ["Hoodie", "Winter", "Premium", "Men"]
};

const dummyRelatedFallback: RelatedProduct[] = [
    {
        id: 2,
        name: "Classic Denim Jacket",
        price: 45.00,
        oldPrice: 65.00,
        discount: 30,
        isNew: true,
        rating: 4.8,
        reviewsCount: 89,
        img: window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/best_sell_pro_img_1.jpg',
        colors: ['#1C58F2', '#000000']
    },
    {
        id: 3,
        name: "Casual T-Shirt",
        price: 25.00,
        rating: 4.2,
        reviewsCount: 45,
        img: window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/best_sell_pro_img_2.jpg',
        colors: ['#ffffff', '#000000', '#ff0000']
    },
    {
        id: 4,
        name: "Summer Shorts",
        price: 35.00,
        oldPrice: 45.00,
        rating: 4.0,
        reviewsCount: 32,
        img: window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/best_sell_pro_img_3.jpg',
        colors: ['#DB4437', '#638C34']
    },
    {
        id: 5,
        name: "Formal Shirt",
        price: 55.00,
        isNew: true,
        rating: 5.0,
        reviewsCount: 12,
        img: window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/best_sell_pro_img_4.jpg',
        colors: ['#ffffff', '#e2e8f0']
    },
    {
        id: 6,
        name: "Winter Jacket",
        price: 120.00,
        oldPrice: 150.00,
        discount: 20,
        rating: 4.9,
        reviewsCount: 230,
        img: window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/best_sell_pro_img_1.jpg',
        colors: ['#000000', '#64748b']
    }
];

import { api, Product as ApiProduct } from '@/services/api';

export default function Show({ product: initialProduct, relatedProducts: initialRelatedProducts }: Partial<Props>) {
    const { slug } = useParams<{ slug: string }>();
    const navigate = useNavigate();
    const [dynamicProduct, setDynamicProduct] = useState<any | null>(null);
    const [dynamicRelated, setDynamicRelated] = useState<any[]>([]);

    useEffect(() => {
        if (!slug) return;
        let isMounted = true;
        setIsLoaded(false);
        setDynamicProduct(null);

        api.getSingleProduct(slug)
            .then(res => {
                if (isMounted && res.product) {
                    setDynamicProduct(res.product);
                    if (res.relatedProducts && res.relatedProducts.length > 0) {
                        setDynamicRelated(res.relatedProducts);
                    }
                    setIsLoaded(true);
                }
            })
            .catch(err => {
                console.error(`Failed to fetch product ${slug}:`, err);
                if (isMounted) {
                    setIsLoaded(true);
                }
            });

        return () => {
            isMounted = false;
        };
    }, [slug]);

    const product = useMemo(() => {
        if (dynamicProduct) return dynamicProduct;
        if (initialProduct) return initialProduct;
        return {
            ...dummyProductFallback,
            name: slug ? slug.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : dummyProductFallback.name
        };
    }, [dynamicProduct, initialProduct, slug]);

    const parsedColors = useMemo(() => {
        if (!product.colors || !Array.isArray(product.colors)) return [{ name: 'Default', hex: '#3b82f6' }];
        const result: { name: string; hex: string }[] = [];
        product.colors.forEach((c: any) => {
            if (typeof c === 'string') {
                c.split(/[|,]/).map(s => s.trim()).filter(Boolean).forEach(name => {
                    result.push({ name, hex: '#3b82f6' });
                });
            } else if (c && c.name) {
                if (typeof c.name === 'string' && (c.name.includes('|') || c.name.includes(','))) {
                    c.name.split(/[|,]/).map((s: string) => s.trim()).filter(Boolean).forEach((name: string) => {
                        result.push({ name, hex: c.hex || '#3b82f6' });
                    });
                } else {
                    result.push(c);
                }
            }
        });
        return result.length > 0 ? result : [{ name: 'Default', hex: '#3b82f6' }];
    }, [product.colors]);

    const parsedSizes = useMemo(() => {
        if (!product.sizes || !Array.isArray(product.sizes)) return ['M'];
        const result: string[] = [];
        product.sizes.forEach((s: any) => {
            if (typeof s === 'string') {
                s.split(/[|,]/).map(item => item.trim()).filter(Boolean).forEach(size => {
                    result.push(size);
                });
            }
        });
        return result.length > 0 ? result : ['M'];
    }, [product.sizes]);

    const relatedProducts = dynamicRelated.length > 0 ? dynamicRelated : (initialRelatedProducts || dummyRelatedFallback);

    const [selectedColor, setSelectedColor] = useState(parsedColors[0]?.name || '');
    const [selectedSize, setSelectedSize] = useState(parsedSizes[0] || 'M');
    const [quantity, setQuantity] = useState(1);
    const [activeTab, setActiveTab] = useState('description');
    const [isLoaded, setIsLoaded] = useState(false);

    const addToCart = (shouldRedirect: boolean = false) => {
        const newItem = {
            id: product.id,
            name: product.name,
            price: product.price,
            img: product.images[0] || '',
            quantity: quantity,
            color: selectedColor,
            size: selectedSize
        };

        const existingCartString = localStorage.getItem('cart');
        let cart: any[] = [];
        if (existingCartString) {
            try {
                cart = JSON.parse(existingCartString);
            } catch (e) {
                cart = [];
            }
        }

        const existingItemIndex = cart.findIndex(item => 
            item.id === newItem.id && 
            item.color === newItem.color && 
            item.size === newItem.size
        );

        if (existingItemIndex > -1) {
            cart[existingItemIndex].quantity += newItem.quantity;
        } else {
            cart.push(newItem);
        }

        localStorage.setItem('cart', JSON.stringify(cart));
        window.dispatchEvent(new Event('cart-updated'));

        toast.success(`${newItem.name} (${newItem.size} / ${newItem.color}) added to cart!`);

        if (shouldRedirect) {
            setTimeout(() => {
                const cartBtn = document.querySelector('.header_cart_link') as HTMLElement;
                if (cartBtn) {
                    cartBtn.click();
                }
            }, 100);
        }
    };

    const handleBuyNow = () => {
        addToCart(false);
        navigate('/checkout');
    };

    const thumbSliderRef = useRef<HTMLDivElement>(null);
    const navSliderRef = useRef<HTMLDivElement>(null);
    const relatedSliderRef = useRef<HTMLDivElement>(null);

    // Sync state when product changes (navigating between products)
    useEffect(() => {
        if (parsedColors[0]?.name) setSelectedColor(parsedColors[0].name);
        if (parsedSizes[0]) setSelectedSize(parsedSizes[0]);
        setQuantity(1);
    }, [parsedColors, parsedSizes]);

    const galleryKey = `${product.id}-${product.images.length}`;
    const relatedKey = relatedProducts.map((p: any) => p.id).join('-');

    useEffect(() => {
        // @ts-ignore
        const $ = window.$;
        let timer: any;

        if ($) {
            timer = setTimeout(() => {
                // Initialize gallery thumb slider
                if (product.images.length > 1 && thumbSliderRef.current && navSliderRef.current) {
                    try {
                        // @ts-ignore
                        if ($(thumbSliderRef.current).hasClass('slick-initialized')) {
                            // @ts-ignore
                            $(thumbSliderRef.current).slick('unslick');
                        }
                        // @ts-ignore
                        if ($(navSliderRef.current).hasClass('slick-initialized')) {
                            // @ts-ignore
                            $(navSliderRef.current).slick('unslick');
                        }

                        // @ts-ignore
                        $(thumbSliderRef.current).slick({
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            arrows: false,
                            vertical: true,
                            asNavFor: navSliderRef.current,
                        });

                        // @ts-ignore
                        $(navSliderRef.current).slick({
                            slidesToShow: Math.min(product.images.length, 5),
                            slidesToScroll: 1,
                            asNavFor: thumbSliderRef.current,
                            autoplay: true,
                            autoplaySpeed: 3000,
                            dots: false,
                            arrows: false,
                            centerMode: true,
                            focusOnSelect: true,
                            vertical: true,
                            responsive: [
                                { breakpoint: 1200, settings: { slidesToShow: 4, vertical: false } },
                                { breakpoint: 992, settings: { slidesToShow: 4, vertical: false } },
                                { breakpoint: 768, settings: { slidesToShow: 4, vertical: false } },
                                { breakpoint: 576, settings: { slidesToShow: 3, vertical: false } }
                            ]
                        });
                    } catch (e) {
                        console.error('Gallery slick init error:', e);
                    }
                }

                // Initialize related products slider
                if (relatedSliderRef.current) {
                    try {
                        // @ts-ignore
                        if ($(relatedSliderRef.current).hasClass('slick-initialized')) {
                            // @ts-ignore
                            $(relatedSliderRef.current).slick('unslick');
                        }
                        // @ts-ignore
                        $(relatedSliderRef.current).slick({
                            slidesToShow: 5,
                            slidesToScroll: 1,
                            autoplay: true,
                            autoplaySpeed: 3000,
                            dots: false,
                            arrows: true,
                            nextArrow: '<i class="nextArrow" style="font-style: normal; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></i>',
                            prevArrow: '<i class="prevArrow" style="font-style: normal; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg></i>',
                            responsive: [
                                { breakpoint: 1400, settings: { slidesToShow: 4 } },
                                { breakpoint: 1200, settings: { slidesToShow: 3 } },
                                { breakpoint: 992, settings: { slidesToShow: 3 } },
                                { breakpoint: 768, settings: { slidesToShow: 2, arrows: false } },
                                { breakpoint: 576, settings: { slidesToShow: 2 } }
                            ]
                        });
                    } catch (e) {
                        console.error('Related slick init error:', e);
                    }
                }
            }, 100);
        }

        return () => {
            clearTimeout(timer);
        };
    }, [galleryKey, relatedKey]);


    const handleQuantityChange = (type: 'plus' | 'minus') => {
        if (type === 'plus') {
            setQuantity(prev => prev + 1);
        } else if (type === 'minus' && quantity > 1) {
            setQuantity(prev => prev - 1);
        }
    };

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
                    <ProductDetailsSkeleton />
                </div>

                {/* REAL PAGE CONTENT - Always mounted so Slick initializes correctly, fades in smoothly */}
                <div style={{ opacity: isLoaded ? 1 : 0, transition: 'opacity 0.4s ease-in-out' }}>
                    {/*============================
                        SHOP DETAILS START
                    =============================*/}
                    <section className="shop_details mt_100">
                <div className="container">
                    <div className="row">
                        <div className="col-xxl-10 col-lg-12">
                            <div className="row">
                                {/* Product Gallery */}
                                <div className="col-lg-6 col-md-10 wow fadeInLeft">
                                    <div key={galleryKey} className="shop_details_slider_area">
                                        <div className="row">
                                            {product.images.length > 1 ? (
                                                <>
                                                    {/* Synced Thumbnails Gallery Navigation */}
                                                    <div className="col-xl-2 col-lg-3 col-md-3 order-2 order-md-1">
                                                        <div ref={navSliderRef} className="row details_slider_nav">
                                                            {product.images.map((img, index) => (
                                                                <div key={index} className="col-12">
                                                                    <div className="details_slider_nav_item">
                                                                        <img src={img} alt={`Product Thumbnail ${index}`} className="img-fluid w-100" />
                                                                    </div>
                                                                </div>
                                                            ))}
                                                        </div>
                                                    </div>

                                                    {/* Synced Main Image Thumbnails */}
                                                    <div className="col-xl-10 col-lg-9 col-md-9 order-1 order-md-2 mb-3 mb-md-0">
                                                        <div ref={thumbSliderRef} className="row details_slider_thumb">
                                                            {product.images.map((img, index) => (
                                                                <div key={index} className="col-12">
                                                                    <div className="details_slider_thumb_item">
                                                                        <img src={img} alt={product.name} className="img-fluid w-100" />
                                                                    </div>
                                                                </div>
                                                            ))}
                                                        </div>
                                                    </div>
                                                </>
                                            ) : (
                                                /* Single Main Image Only */
                                                <div className="col-12">
                                                    <div className="details_slider_thumb_item border rounded-3 p-3 bg-white d-flex align-items-center justify-content-center" style={{ minHeight: '400px' }}>
                                                        <img src={product.images[0]} alt={product.name} className="img-fluid" style={{ maxHeight: '500px', objectFit: 'contain' }} />
                                                    </div>
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                </div>

                                {/* Product Specifications Info */}
                                <div className="col-lg-6 wow fadeInUp">
                                    <div className="shop_details_text">
                                        <p className="category">{product.category}</p>
                                        <h2 className="details_title">{product.name}</h2>
                                        
                                        <div className="d-flex flex-wrap align-items-center">
                                            <p className="stock" style={{ background: '#e1f8eb', color: '#14b8a6', padding: '3px 10px', borderRadius: '4px', fontSize: '14px', fontWeight: 'bold' }}>
                                                {product.stockStatus}
                                            </p>
                                            <p className="rating ms-3">
                                                {[...Array(5)].map((_, i) => (
                                                    <Star key={i} size={14} fill={i < Math.floor(product.rating) ? "#f59e0b" : "none"} color={i < Math.floor(product.rating) ? "#f59e0b" : "#cbd5e1"} style={{ display: 'inline-block', marginRight: '2px' }} />
                                                ))}
                                                <span className="ms-1">({product.reviewsCount} reviews)</span>
                                            </p>
                                        </div>

                                        <h3 className="price" style={{ fontSize: '28px', color: '#0f172a', fontWeight: 'bold', margin: '20px 0' }}>
                                            {formatPrice(product.price)}
                                            {product.oldPrice && (
                                                <del style={{ fontSize: '18px', color: '#94a3b8', marginLeft: '10px', fontWeight: 'normal' }}>
                                                    {formatPrice(product.oldPrice)}
                                                </del>
                                            )}
                                        </h3>

                                        <p className="short_description">{product.description}</p>
                                        
                                        {/* Injecting clean CSS styling to fix theme FontAwesome 404 font box and vertical text alignments */}
                                        <style>{`
                                            .details_variant_color li.active::after {
                                                display: none !important;
                                            }
                                            .details_single_variant .variant_title {
                                                min-width: 100px !important;
                                                margin-right: 15px !important;
                                            }
                                            .details_variant_size li {
                                                text-transform: uppercase;
                                                border-radius: 4px;
                                                border: 1px solid #ddd;
                                                display: inline-flex;
                                                align-items: center;
                                                justify-content: center;
                                                font-weight: bold;
                                                font-size: 14px;
                                                height: 32px !important;
                                                width: 52px !important;
                                                line-height: 32px !important;
                                                cursor: pointer;
                                                transition: all 0.2s ease;
                                            }
                                            .details_variant_size li.active {
                                                border-color: var(--themeColorTwo) !important;
                                                background-color: var(--themeColorTwo) !important;
                                                color: #fff !important;
                                            }
                                            .shop_details_des_area .nav-pills button {
                                                background: transparent !important;
                                                color: #555 !important;
                                                border: 1px solid #ddd !important;
                                                border-radius: 50px !important;
                                                padding: 8px 22px !important;
                                                font-weight: 500 !important;
                                                text-transform: capitalize !important;
                                            }
                                            .shop_details_des_area .nav-pills button.active {
                                                background-color: var(--themeColorTwo) !important;
                                                color: #fff !important;
                                                border-color: var(--themeColorTwo) !important;
                                                box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px !important;
                                            }
                                            @media (max-width: 1199px) {
                                                .details_slider_nav_item {
                                                    width: 70px !important;
                                                    height: 70px !important;
                                                    margin: 0 auto 5px auto !important;
                                                }
                                            }
                                            @media (max-width: 575px) {
                                                .details_slider_nav_item {
                                                    width: 60px !important;
                                                    height: 60px !important;
                                                    margin: 0 auto 5px auto !important;
                                                }
                                                .shop_details_des_area .nav-pills {
                                                    display: flex !important;
                                                    flex-wrap: nowrap !important;
                                                    overflow-x: auto !important;
                                                    white-space: nowrap !important;
                                                    padding-bottom: 8px !important;
                                                    -webkit-overflow-scrolling: touch;
                                                    border-bottom: none !important;
                                                }
                                                .shop_details_des_area .nav-pills::-webkit-scrollbar {
                                                    height: 4px;
                                                }
                                                .shop_details_des_area .nav-pills::-webkit-scrollbar-thumb {
                                                    background-color: rgba(0, 0, 0, 0.1);
                                                    border-radius: 4px;
                                                }
                                                .shop_details_des_area .nav-pills .nav-item {
                                                    flex: 0 0 auto !important;
                                                }
                                            }
                                        `}</style>

                                        {/* Color Variant Selector */}
                                        <div className="details_single_variant mt-4">
                                            <p className="variant_title" style={{ fontSize: '15px', color: '#1e293b', marginBottom: '8px' }}>
                                                Color : <strong style={{ color: '#0f172a' }}>{selectedColor}</strong>
                                            </p>
                                            <div className="d-flex flex-wrap gap-2 align-items-center">
                                                {parsedColors.map((color) => {
                                                    const isSelected = selectedColor === color.name;
                                                    const isLight = ['#ffffff', '#fff', '#fffdd0', '#f5f5dc', '#fde047'].includes(color.hex?.toLowerCase());
                                                    return (
                                                        <button
                                                            key={color.name}
                                                            type="button"
                                                            onClick={() => setSelectedColor(color.name)}
                                                            title={color.name}
                                                            style={{ 
                                                                width: '34px', 
                                                                height: '34px', 
                                                                borderRadius: '50%',
                                                                backgroundColor: color.hex, 
                                                                border: isSelected ? '2px solid #ffffff' : '1px solid #cbd5e1',
                                                                boxShadow: isSelected ? '0 0 0 2px #ffa500' : '0 1px 2px rgba(0,0,0,0.08)',
                                                                cursor: 'pointer',
                                                                display: 'inline-flex',
                                                                alignItems: 'center',
                                                                justifyContent: 'center',
                                                                padding: 0,
                                                                transition: 'transform 0.15s ease'
                                                            }}
                                                        >
                                                            {isSelected && (
                                                                <Check size={14} color={isLight ? '#0f172a' : '#ffffff'} strokeWidth={3} />
                                                            )}
                                                        </button>
                                                    );
                                                })}
                                            </div>
                                        </div>

                                        {/* Size Variant Selector */}
                                        <div className="details_single_variant mt-4">
                                            <p className="variant_title" style={{ fontSize: '15px', color: '#1e293b', marginBottom: '8px' }}>
                                                Size : <strong style={{ color: '#0f172a' }}>{selectedSize}</strong>
                                            </p>
                                            <div className="d-flex flex-wrap gap-2 align-items-center">
                                                {parsedSizes.map((size) => {
                                                    const isSelected = selectedSize === size;
                                                    return (
                                                        <button
                                                            key={size}
                                                            type="button"
                                                            onClick={() => setSelectedSize(size)}
                                                            style={{
                                                                minWidth: '46px',
                                                                height: '38px',
                                                                padding: '0 14px',
                                                                borderRadius: '6px',
                                                                border: isSelected ? '2px solid #ffa500' : '1px solid #cbd5e1',
                                                                backgroundColor: isSelected ? '#fff7ed' : '#ffffff',
                                                                color: isSelected ? '#ea580c' : '#334155',
                                                                fontWeight: isSelected ? '700' : '500',
                                                                fontSize: '14px',
                                                                cursor: 'pointer',
                                                                display: 'inline-flex',
                                                                alignItems: 'center',
                                                                justifyContent: 'center',
                                                                transition: 'all 0.15s ease'
                                                            }}
                                                        >
                                                            {size}
                                                        </button>
                                                    );
                                                })}
                                            </div>
                                        </div>

                                        {/* Quantity & Actions */}
                                        <div className="d-flex flex-wrap align-items-center mt-4" style={{ gap: '15px' }}>
                                            <div className="details_qty_input" style={{ border: '1px solid #cbd5e1', borderRadius: '6px', overflow: 'hidden', height: '46px', width: 'auto', backgroundColor: '#ffffff', display: 'inline-flex', flexWrap: 'nowrap', alignItems: 'center' }}>
                                                <button 
                                                    type="button"
                                                    className="minus" 
                                                    onClick={() => handleQuantityChange('minus')} 
                                                    style={{ 
                                                        background: '#f1f5f9', 
                                                        border: 'none', 
                                                        width: '40px',
                                                        height: '46px', 
                                                        color: '#0f172a', 
                                                        display: 'inline-flex', 
                                                        alignItems: 'center', 
                                                        justifyContent: 'center', 
                                                        cursor: 'pointer',
                                                        flexShrink: 0
                                                    }}
                                                >
                                                    <Minus size={18} strokeWidth={2.8} color="#0f172a" />
                                                </button>
                                                <input 
                                                    type="text" 
                                                    value={quantity.toString().padStart(2, '0')} 
                                                    readOnly 
                                                    style={{ width: '48px', height: '46px', textAlign: 'center', border: 'none', fontWeight: 700, fontSize: '16px', color: '#0f172a', backgroundColor: '#ffffff', flexShrink: 0 }} 
                                                />
                                                <button 
                                                    type="button"
                                                    className="plus" 
                                                    onClick={() => handleQuantityChange('plus')} 
                                                    style={{ 
                                                        background: '#f1f5f9', 
                                                        border: 'none', 
                                                        width: '40px',
                                                        height: '46px', 
                                                        color: '#0f172a', 
                                                        display: 'inline-flex', 
                                                        alignItems: 'center', 
                                                        justifyContent: 'center', 
                                                        cursor: 'pointer',
                                                        flexShrink: 0
                                                    }}
                                                >
                                                    <Plus size={18} strokeWidth={2.8} color="#0f172a" />
                                                </button>
                                            </div>
                                            <div className="details_btn_area d-flex flex-wrap" style={{ gap: '12px', display: 'flex', flexDirection: 'row' }}>
                                                {/* 1. Add To Cart Button First (Orange) */}
                                                <button 
                                                    className="common_btn" 
                                                    onClick={() => addToCart(false)} 
                                                    style={{ 
                                                        order: 1,
                                                        border: 'none', 
                                                        padding: '12px 24px', 
                                                        display: 'inline-flex', 
                                                        alignItems: 'center', 
                                                        gap: '8px',
                                                        backgroundColor: '#ff9900',
                                                        color: '#ffffff',
                                                        borderRadius: '6px',
                                                        fontWeight: 600,
                                                        cursor: 'pointer'
                                                    }}
                                                >
                                                    <span style={{ color: '#ffffff', fontWeight: 600 }}>Add To Cart</span> <ArrowRight size={16} color="#ffffff" />
                                                </button>

                                                {/* 2. Buy Now Button Second (Green - Direct Checkout) */}
                                                <button 
                                                    className="common_btn buy_now" 
                                                    onClick={handleBuyNow} 
                                                    style={{ 
                                                        order: 2,
                                                        border: 'none', 
                                                        padding: '12px 24px', 
                                                        display: 'inline-flex', 
                                                        alignItems: 'center', 
                                                        gap: '8px',
                                                        backgroundColor: '#16a34a',
                                                        color: '#ffffff',
                                                        borderRadius: '6px',
                                                        fontWeight: 600,
                                                        cursor: 'pointer'
                                                    }}
                                                >
                                                    <span style={{ color: '#ffffff', fontWeight: 600 }}>Buy Now</span> <ArrowRight size={16} color="#ffffff" />
                                                </button>
                                            </div>
                                        </div>

                                        {/* Removed Wishlist, SKU/Tags and Social share links */}
                                    </div>
                                </div>
                            </div>

                            {/* Descriptions, Spec Tabs */}
                            <div className="row mt_90 wow fadeInUp">
                                <div className="col-12">
                                    <div className="shop_details_des_area">
                                        <ul className="nav nav-pills" id="pills-tab2" role="tablist" style={{ borderBottom: '1px solid #eee', paddingBottom: '10px' }}>
                                            {['description', 'additional', 'vendor', 'reviews'].map((tab) => (
                                                <li key={tab} className="nav-item" role="presentation">
                                                    <button 
                                                        className={`nav-link ${activeTab === tab ? 'active' : ''}`}
                                                        onClick={() => setActiveTab(tab)}
                                                    >
                                                        {tab === 'additional' ? 'Additional info' : tab}
                                                    </button>
                                                </li>
                                            ))}
                                        </ul>

                                        <div className="tab-content mt-4" id="pills-tabContent2">
                                            {activeTab === 'description' && (
                                                <div className="shop_details_description">
                                                    <h3>Description</h3>
                                                    <p>Uninhibited carnally hired played in whimpered dear gorilla koala depending and much yikes off far quetzal goodness and from for grimaced goodness unaccountably and meadowlark near unblushingly crucial scallop tightly neurotic hungrily some and dear furiously this apart.</p>
                                                    <ul>
                                                        <li>Organic raw pecans, organic raw cashews.</li>
                                                        <li>This butter was produced using a LTG (Low Temperature Grinding) process</li>
                                                        <li>Made in machinery that processes tree nuts but does not process peanuts, gluten, dairy or soy</li>
                                                    </ul>
                                                    <p className="mt-3">Laconic overheard dear woodchuck wow this outrageously taut beaver hey hello far meadowlark imitatively egregiously hugged that yikes minimally unanimous pouted flirtatiously as beaver beheld above forward energetic across this jeepers beneficently cockily less a the raucously that magic.</p>
                                                </div>
                                            )}

                                            {activeTab === 'additional' && (
                                                <div className="shop_details_additional_info">
                                                    <h3>Additional info</h3>
                                                    <table className="table table-bordered mt-3">
                                                        <tbody>
                                                            <tr>
                                                                <td style={{ width: '30%', fontWeight: 'bold', background: '#f8fafc' }}>Weight</td>
                                                                <td>0.5 kg</td>
                                                            </tr>
                                                            <tr>
                                                                <td style={{ fontWeight: 'bold', background: '#f8fafc' }}>Dimensions</td>
                                                                <td>12 × 15 × 10 cm</td>
                                                            </tr>
                                                            <tr>
                                                                <td style={{ fontWeight: 'bold', background: '#f8fafc' }}>Material</td>
                                                                <td>80% Cotton, 20% Polyester</td>
                                                            </tr>
                                                            <tr>
                                                                <td style={{ fontWeight: 'bold', background: '#f8fafc' }}>Warranty</td>
                                                                <td>1 Year Manufacturer Warranty</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            )}

                                            {activeTab === 'vendor' && (
                                                <div className="shop_details_vendor">
                                                    <h3>Vendor Information</h3>
                                                    <div className="d-flex align-items-center mt-3" style={{ gap: '15px' }}>
                                                        <div style={{ width: '60px', height: '60px', borderRadius: '50%', backgroundColor: '#ffa500', display: 'flex', alignItems: 'center', justifyContent: 'center', color: '#fff', fontSize: '24px', fontWeight: 'bold' }}>
                                                            WF
                                                        </div>
                                                        <div>
                                                            <h5 style={{ margin: 0, fontWeight: 'bold' }}>WoocomFashion Store</h5>
                                                            <p style={{ margin: 0, color: '#64748b' }}>Premium Official Partner Store</p>
                                                        </div>
                                                    </div>
                                                    <p className="mt-3">Highly rated official store with 99.8% positive feedback. Delivering original styles and curated premium outfits since 2020.</p>
                                                </div>
                                            )}

                                            {activeTab === 'reviews' && (
                                                <div className="shop_details_reviews">
                                                    <h3>Customer Reviews ({product.reviewsCount})</h3>
                                                    <div className="review_list mt-3" style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>
                                                        <div className="review_item d-flex" style={{ gap: '15px', borderBottom: '1px solid #f1f5f9', paddingBottom: '15px' }}>
                                                            <div style={{ width: '48px', height: '48px', borderRadius: '50%', backgroundColor: '#f1f5f9', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '18px', fontWeight: 'bold', color: '#64748b' }}>
                                                                SA
                                                            </div>
                                                            <div>
                                                                <div className="d-flex align-items-center justify-content-between">
                                                                    <h5 className="mb-0" style={{ fontWeight: 'bold', fontSize: '16px' }}>Sozib Alahi</h5>
                                                                    <span className="text-muted ms-3" style={{ fontSize: '12px' }}>July 19, 2026</span>
                                                                </div>
                                                                <div style={{ color: '#f59e0b', display: 'flex', gap: '2px', margin: '4px 0' }}>
                                                                    <Star size={12} fill="#f59e0b" color="#f59e0b" />
                                                                    <Star size={12} fill="#f59e0b" color="#f59e0b" />
                                                                    <Star size={12} fill="#f59e0b" color="#f59e0b" />
                                                                    <Star size={12} fill="#f59e0b" color="#f59e0b" />
                                                                    <Star size={12} fill="#f59e0b" color="#f59e0b" />
                                                                </div>
                                                                <p className="mb-0" style={{ color: '#334155' }}>Excellent hoodie! Super comfortable material, warm, and fits perfectly. Highly recommended product!</p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {/* Add a Review Form */}
                                                    <div className="add_review_form mt-5" style={{ background: '#f8fafc', padding: '25px', borderRadius: '8px' }}>
                                                        <h4 style={{ fontWeight: 'bold' }}>Add a Review</h4>
                                                        <form className="mt-3">
                                                            <div className="mb-3">
                                                                <label className="form-label" style={{ fontWeight: 'bold' }}>Your Rating</label>
                                                                <div style={{ color: '#cbd5e1', display: 'flex', gap: '4px', margin: '8px 0', cursor: 'pointer' }}>
                                                                    <Star size={20} color="#cbd5e1" />
                                                                    <Star size={20} color="#cbd5e1" />
                                                                    <Star size={20} color="#cbd5e1" />
                                                                    <Star size={20} color="#cbd5e1" />
                                                                    <Star size={20} color="#cbd5e1" />
                                                                </div>
                                                            </div>
                                                            <div className="row">
                                                                <div className="col-md-6 mb-3">
                                                                    <label className="form-label" style={{ fontWeight: 'bold' }}>Name</label>
                                                                    <input type="text" className="form-control" placeholder="Your Name" />
                                                                </div>
                                                                <div className="col-md-6 mb-3">
                                                                    <label className="form-label" style={{ fontWeight: 'bold' }}>Email</label>
                                                                    <input type="email" className="form-control" placeholder="Your Email Address" />
                                                                </div>
                                                            </div>
                                                            <div className="mb-3">
                                                                <label className="form-label" style={{ fontWeight: 'bold' }}>Review</label>
                                                                <textarea className="form-control" rows={4} placeholder="Write your review here..."></textarea>
                                                            </div>
                                                            <button type="submit" className="common_btn" style={{ padding: '10px 25px', border: 'none' }}>
                                                                Submit Review
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/*============================
                RELATED PRODUCTS START
            =============================*/}
            <section className="related_products mt_90 mb_70 wow fadeInUp">
                <div className="container">
                    <div className="row">
                        <div className="col-xl-6">
                            <div className="section_heading_2 section_heading">
                                <h3><span>Related</span> Products</h3>
                            </div>
                        </div>
                    </div>
                    
                    <div key={relatedKey} ref={relatedSliderRef} className="row mt_25 flash_sell_2_slider">
                        {relatedProducts.map((relProduct) => {
                            const getSlug = (str: string) => {
                                return str
                                    .toLowerCase()
                                    .replace(/[^a-z0-9]+/g, '-')
                                    .replace(/(^-|-$)+/g, '');
                            };
                            return (
                                <div key={relProduct.id} className="col-xl-1-5 px-2">
                                    <div className="product_item_2 product_item">
                                        <div className="product_img">
                                            <Link to={`/shop/product/${getSlug(relProduct.name)}`} className="d-block">
                                                <img src={relProduct.img} alt={relProduct.name} className="img-fluid w-100" />
                                            </Link>
                                            <ul className="discount_list">
                                                {relProduct.discount && <li className="discount">-{relProduct.discount}%</li>}
                                                {relProduct.isNew && <li className="new">new</li>}
                                            </ul>
                                            <ul className="btn_list">
                                                <li><a href="#"><img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/compare_icon_white.svg'} alt="Compare" className="img-fluid" /></a></li>
                                                <li><a href="#"><img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/love_icon_white.svg'} alt="Love" className="img-fluid" /></a></li>
                                                <li><a href="#"><img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/cart_icon_white.svg'} alt="Cart" className="img-fluid" /></a></li>
                                            </ul>
                                        </div>
                                        <div className="product_text">
                                            <Link className="title" to={`/shop/product/${getSlug(relProduct.name)}`}>
                                                {relProduct.name}
                                            </Link>
                                        <p className="price">
                                            {formatPrice(relProduct.price)}
                                            {relProduct.oldPrice && <del className="ms-2">{formatPrice(relProduct.oldPrice)}</del>}
                                        </p>
                                        <ul className="color" style={{ padding: 0 }}>
                                            {relProduct.colors.map((colorHex, index) => (
                                                <li key={index} className={index === 0 ? 'active' : ''} style={{ background: colorHex }}></li>
                                            ))}
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            );
                        })}
                    </div>
                </div>
            </section>
                </div>
            </div>
        </ShopLayout>
    );
}




