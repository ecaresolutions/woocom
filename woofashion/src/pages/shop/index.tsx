import { useState, useEffect } from 'react';
import { Link, useParams } from 'react-router-dom';
import ShopLayout from '@/layouts/shop-layout';
import ProductCard from '@/components/shop/product-card';
import { Star, Check, Minus, Plus, ArrowRight, Grid, List } from 'lucide-react';
import { formatPrice, getCurrencySymbol } from '@/lib/currency';

const getSlug = (str: string) => {
    return str
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)+/g, '');
};

interface Product {
    id: number;
    name: string;
    category: string;
    price: number;
    oldPrice?: number;
    discount?: number;
    isNew?: boolean;
    onSale?: boolean;
    inStock?: boolean;
    rating: number;
    reviewsCount: number;
    img: string;
    colors: string[];
}

interface Category {
    name: string;
    count: number;
}

interface Props {
    products: Product[];
    categories: Category[];
}

const ShopSkeleton = () => (
    <div className="shop_page mb_100">
        <div className="container">
            <div className="row">
                {/* Sidebar Skeleton */}
                <div className="col-xxl-3 col-lg-4 col-xl-3">
                    <div className="d-flex flex-column gap-4">
                        <div className="skeleton-box" style={{ height: '120px', width: '100%' }}></div>
                        <div className="skeleton-box" style={{ height: '140px', width: '100%' }}></div>
                        <div className="skeleton-box" style={{ height: '300px', width: '100%' }}></div>
                        <div className="skeleton-box" style={{ height: '200px', width: '100%' }}></div>
                    </div>
                </div>
                {/* Products Grid Skeleton */}
                <div className="col-xxl-9 col-lg-8 col-xl-9">
                    <div className="skeleton-box mb-4" style={{ height: '50px', width: '100%' }}></div>
                    <div className="row">
                        {[...Array(8)].map((_, i) => (
                            <div key={i} className="col-xl-3 col-6 col-md-4 col-sm-6 mb-4">
                                <div className="skeleton-box" style={{ height: '320px', width: '100%', borderRadius: '16px' }}></div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </div>
    </div>
);

const DualRangeSlider = ({ 
    min, 
    max, 
    value, 
    onChange,
    onChangeEnd
}: { 
    min: number; 
    max: number; 
    value: [number, number]; 
    onChange: (val: [number, number]) => void; 
    onChangeEnd: (val: [number, number]) => void;
}) => {
    const minVal = value[0];
    const maxVal = value[1];

    const minPercent = ((minVal - min) / (max - min)) * 100;
    const maxPercent = ((maxVal - min) / (max - min)) * 100;

    const handleMouseUp = () => {
        onChangeEnd(value);
    };

    return (
        <div className="al-range-slider" style={{ height: '35px', paddingBottom: '30px', position: 'relative', width: '100%', display: 'block' }}>
            <div className="al-range-slider__track" style={{ position: 'relative', width: '100%' }}>
                <div 
                    className="al-range-slider__bar" 
                    style={{ 
                        left: `${minPercent}%`, 
                        width: `${maxPercent - minPercent}%`, 
                        position: 'absolute',
                        background: '#ff3366',
                        height: '100%'
                    }} 
                />
                <div 
                    className="al-range-slider__knob" 
                    style={{ 
                        left: `${minPercent}%`, 
                        position: 'absolute',
                        top: '50%',
                        transform: 'translate(-50%, -55%)'
                    }} 
                >
                    <span className="al-range-slider__tooltip">{getCurrencySymbol()}{minVal}</span>
                </div>
                <div 
                    className="al-range-slider__knob" 
                    style={{ 
                        left: `${maxPercent}%`, 
                        position: 'absolute',
                        top: '50%',
                        transform: 'translate(-50%, -55%)'
                    }} 
                >
                    <span className="al-range-slider__tooltip">{getCurrencySymbol()}{maxVal}</span>
                </div>
            </div>
            <input 
                type="range"
                min={min}
                max={max}
                value={minVal}
                onChange={e => {
                    const val = Math.min(Number(e.target.value), maxVal - 1);
                    onChange([val, maxVal]);
                }}
                onMouseUp={handleMouseUp}
                onTouchEnd={handleMouseUp}
                className="thumb-range-input-min"
                style={{
                    position: 'absolute',
                    width: '100%',
                    height: '6px',
                    top: '0px',
                    left: 0,
                    background: 'none',
                    pointerEvents: 'none',
                    appearance: 'none',
                    zIndex: 4,
                    margin: 0,
                    padding: 0
                }}
            />
            <input 
                type="range"
                min={min}
                max={max}
                value={maxVal}
                onChange={e => {
                    const val = Math.max(Number(e.target.value), minVal + 1);
                    onChange([minVal, val]);
                }}
                onMouseUp={handleMouseUp}
                onTouchEnd={handleMouseUp}
                className="thumb-range-input-max"
                style={{
                    position: 'absolute',
                    width: '100%',
                    height: '6px',
                    top: '0px',
                    left: 0,
                    background: 'none',
                    pointerEvents: 'none',
                    appearance: 'none',
                    zIndex: 5,
                    margin: 0,
                    padding: 0
                }}
            />
        </div>
    );
};

// Mock data for when we are navigating via React Router without backend Inertia props
const dummyCategoriesFallback: Category[] = [
    { name: "Men's Fashion", count: 45 },
    { name: "Women's Fashion", count: 68 },
    { name: "Kids' Fashion", count: 32 },
    { name: "Denim Collection", count: 24 },
    { name: "Western Wear", count: 56 },
    { name: "Sport Wear", count: 18 },
    { name: "Footwear", count: 42 },
    { name: "Fashion Jewellery", count: 29 },
    { name: "Beauty & Cosmetics", count: 15 }
];

const dummyProductsFallback: Product[] = [
    {
        id: 1,
        name: "Men's Premium Hoodie",
        category: "Men's Fashion",
        price: 55.00,
        oldPrice: 85.00,
        discount: 35,
        isNew: true,
        onSale: true,
        inStock: true,
        rating: 4.5,
        reviewsCount: 124,
        img: window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/product_1.jpg',
        colors: ['#DB4437', '#638C34', '#1C58F2']
    },
    {
        id: 2,
        name: "Classic Denim Jacket",
        category: "Denim Collection",
        price: 45.00,
        oldPrice: 65.00,
        discount: 30,
        onSale: true,
        inStock: true,
        rating: 4.8,
        reviewsCount: 89,
        img: window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/product_2.jpg',
        colors: ['#1C58F2', '#000000']
    },
    {
        id: 3,
        name: "Casual T-Shirt",
        category: "Men's Fashion",
        price: 25.00,
        inStock: true,
        rating: 4.2,
        reviewsCount: 45,
        img: window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/product_3.jpg',
        colors: ['#ffffff', '#000000', '#DB4437']
    },
    {
        id: 4,
        name: "Summer Shorts",
        category: "Men's Fashion",
        price: 35.00,
        oldPrice: 45.00,
        onSale: true,
        inStock: false,
        rating: 4.0,
        reviewsCount: 32,
        img: window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/product_4.jpg',
        colors: ['#DB4437', '#638C34']
    },
    {
        id: 5,
        name: "Formal Shirt",
        category: "Men's Fashion",
        price: 55.00,
        isNew: true,
        inStock: true,
        rating: 5.0,
        reviewsCount: 12,
        img: window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/product_5.jpg',
        colors: ['#ffffff', '#8e8e8e']
    },
    {
        id: 6,
        name: "Winter Jacket",
        category: "Western Wear",
        price: 120.00,
        oldPrice: 150.00,
        discount: 20,
        onSale: true,
        inStock: true,
        rating: 4.9,
        reviewsCount: 230,
        img: window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/product_6.jpg',
        colors: ['#000000', '#8e8e8e']
    },
    {
        id: 7,
        name: "Women's Summer Dress",
        category: "Women's Fashion",
        price: 65.00,
        isNew: true,
        inStock: true,
        rating: 4.6,
        reviewsCount: 56,
        img: window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/product_7.jpg',
        colors: ['#ff3366', '#ffffff']
    },
    {
        id: 8,
        name: "Sport Running Shoes",
        category: "Footwear",
        price: 85.00,
        oldPrice: 110.00,
        discount: 22,
        onSale: true,
        inStock: true,
        rating: 4.7,
        reviewsCount: 142,
        img: window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/product_8.jpg',
        colors: ['#1C58F2', '#41CF0F', '#000000']
    }
];

import { api, Product as ApiProduct, Category as ApiCategory } from '@/services/api';

export default function Index({ products: initialProducts, categories: initialCategories }: Partial<Props>) {
    const [dynamicProducts, setDynamicProducts] = useState<any[]>([]);
    const [dynamicCategories, setDynamicCategories] = useState<any[]>([]);

    const [selectedCategory, setSelectedCategory] = useState<string | null>(null);
    const [priceRange, setPriceRange] = useState<[number, number]>([0, 150]);
    const [sliderRange, setSliderRange] = useState<[number, number]>([0, 150]);
    const [statusFilter, setStatusFilter] = useState({ onSale: false, inStock: false });
    const [selectedColors, setSelectedColors] = useState<string[]>([]);
    const [selectedRatings, setSelectedRatings] = useState<number[]>([]);
    const [sortBy, setSortBy] = useState('default');
    const [itemsPerPage, setItemsPerPage] = useState(12);
    const [currentPage, setCurrentPage] = useState(1);
    const [viewMode, setViewMode] = useState<'grid' | 'list'>('grid');
    const [isLoaded, setIsLoaded] = useState(false);

    useEffect(() => {
        let isMounted = true;
        api.getProducts()
            .then(res => {
                if (isMounted) {
                    if (res.products && res.products.length > 0) {
                        setDynamicProducts(res.products);
                    }
                    if (res.categories && res.categories.length > 0) {
                        setDynamicCategories(res.categories);
                    }
                    setIsLoaded(true);
                }
            })
            .catch(err => {
                console.error("Failed to load products from API:", err);
                if (isMounted) setIsLoaded(true);
            });

        return () => {
            isMounted = false;
        };
    }, []);

    const products = dynamicProducts.length > 0 ? dynamicProducts : (initialProducts || dummyProductsFallback);
    const categories = dynamicCategories.length > 0 ? dynamicCategories : (initialCategories || dummyCategoriesFallback);

    const { categorySlug } = useParams<{ categorySlug?: string }>();

    useEffect(() => {
        const queryParams = new URLSearchParams(window.location.search);
        const catParam = categorySlug || queryParams.get('category');
        if (catParam) {
            const getSlugVal = (s: string) => s.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
            const matchedCat = categories.find((c: any) => getSlugVal(c.name) === getSlugVal(catParam) || (c.slug && getSlugVal(c.slug) === getSlugVal(catParam)));
            if (matchedCat) {
                setSelectedCategory(matchedCat.name);
            } else {
                setSelectedCategory(catParam.replace(/-/g, ' '));
            }
        } else {
            setSelectedCategory(null);
        }
    }, [categories, categorySlug, window.location.search]);

    useEffect(() => {
        setSliderRange(priceRange);
    }, [priceRange]);


    // Reset pagination when filter changes
    useEffect(() => {
        setCurrentPage(1);
    }, [selectedCategory, priceRange, statusFilter, selectedColors, selectedRatings, sortBy, itemsPerPage]);

    // Hardcoded color options with human labels matching template hexes
    const colorOptions = [
        { label: 'Red', hex: '#DB4437' },
        { label: 'Green', hex: '#41CF0F' },
        { label: 'Gray', hex: '#8e8e8e' },
        { label: 'Orange', hex: '#ffa500' },
        { label: 'Purple', hex: '#B615FD' },
        { label: 'Yellow', hex: '#FFD747' },
        { label: 'Olive', hex: '#AB9774' },
        { label: 'Dark Blue', hex: '#1C58F2' }
    ];

    // Filter logic
    const filteredProducts = products.filter(product => {
        if (selectedCategory && product.category.toLowerCase() !== selectedCategory.toLowerCase()) {
            return false;
        }
        if (product.price < priceRange[0] || product.price > priceRange[1]) {
            return false;
        }
        if (statusFilter.onSale && !product.oldPrice) {
            return false;
        }
        if (statusFilter.inStock && !product.inStock) {
            return false;
        }
        if (selectedColors.length > 0) {
            const hasColor = product.colors.some(c => 
                selectedColors.some(sc => sc.toLowerCase() === c.toLowerCase())
            );
            if (!hasColor) return false;
        }
        if (selectedRatings.length > 0) {
            const minRating = Math.min(...selectedRatings);
            if (product.rating < minRating) return false;
        }
        return true;
    });

    // Sort logic
    const sortedProducts = [...filteredProducts].sort((a, b) => {
        if (sortBy === 'low-high') return a.price - b.price;
        if (sortBy === 'high-low') return b.price - a.price;
        if (sortBy === 'newest') return (b.isNew ? 1 : 0) - (a.isNew ? 1 : 0);
        if (sortBy === 'on-sale') return (b.oldPrice ? 1 : 0) - (a.oldPrice ? 1 : 0);
        return 0;
    });

    // Pagination
    const totalItems = sortedProducts.length;
    const totalPages = Math.ceil(totalItems / itemsPerPage) || 1;
    const startIndex = (currentPage - 1) * itemsPerPage;
    const paginatedProducts = sortedProducts.slice(
        startIndex,
        currentPage * itemsPerPage
    );

    const handleColorToggle = (hex: string) => {
        setSelectedColors(prev => 
            prev.includes(hex) ? prev.filter(c => c !== hex) : [...prev, hex]
        );
    };

    const handleRatingToggle = (stars: number) => {
        setSelectedRatings(prev => 
            prev.includes(stars) ? prev.filter(r => r !== stars) : [...prev, stars]
        );
    };

    return (
        <ShopLayout isLoaded={isLoaded}>

            <style>{`
                /* Custom dual range slider overlays */
                .thumb-range-input-min, .thumb-range-input-max {
                    pointer-events: none;
                    outline: none;
                    background: none !important;
                    border: none !important;
                    appearance: none !important;
                    -webkit-appearance: none !important;
                }
                .thumb-range-input-min::-webkit-slider-runnable-track, 
                .thumb-range-input-max::-webkit-slider-runnable-track {
                    background: none !important;
                    border: none !important;
                }
                .thumb-range-input-min::-moz-range-track, 
                .thumb-range-input-max::-moz-range-track {
                    background: none !important;
                    border: none !important;
                }
                .thumb-range-input-min::-webkit-slider-thumb, .thumb-range-input-max::-webkit-slider-thumb {
                    pointer-events: auto;
                    width: 24px;
                    height: 24px;
                    border-radius: 50%;
                    appearance: none;
                    -webkit-appearance: none;
                    background: transparent !important;
                    border: none !important;
                    box-shadow: none !important;
                    cursor: pointer;
                }
                .thumb-range-input-min::-moz-range-thumb, .thumb-range-input-max::-moz-range-thumb {
                    pointer-events: auto;
                    width: 24px;
                    height: 24px;
                    border-radius: 50%;
                    background: transparent !important;
                    border: none !important;
                    box-shadow: none !important;
                    cursor: pointer;
                }

                /* Hide number input spinners and style */
                .range-inputs input[type=number]::-webkit-inner-spin-button, 
                .range-inputs input[type=number]::-webkit-outer-spin-button { 
                    -webkit-appearance: none; 
                    margin: 0; 
                }
                .range-inputs input[type=number] {
                    -moz-appearance: textfield;
                    text-align: center;
                }

                /* Price Range Slider inputs styling */
                .price-slider-container {
                    padding: 10px 0;
                }
                .range-inputs {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    margin-top: 10px;
                }
                .range-inputs input {
                    width: 70px;
                    padding: 4px 8px;
                    border: 1px solid #cbd5e1;
                    border-radius: 6px;
                    font-size: 13px;
                    outline: none;
                    transition: border-color 0.2s;
                }
                .range-inputs input:focus {
                    border-color: #ff3366;
                }
                .sidebar_status .form-check-input:checked {
                    background-color: #ff3366 !important;
                    border-color: #ff3366 !important;
                }
                .color-box-checkbox {
                    width: 16px;
                    height: 16px;
                    border-radius: 3px;
                    border: 1px solid rgba(0,0,0,0.1);
                    cursor: pointer;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                }
                /* Product List Item layout custom styling */
                .product_list_item {
                    border: 1px solid #e2e8f0;
                    border-radius: 12px;
                    padding: 15px;
                    margin-bottom: 20px;
                    background: #fff;
                    transition: box-shadow 0.3s ease;
                }
                .product_list_item:hover {
                    box-shadow: 0 8px 24px rgba(0,0,0,0.06);
                }
                .product_list_item .short_description {
                    font-size: 14px;
                    color: #64748b;
                    line-height: 1.6;
                    margin-top: 10px;
                }
                .product_list_item .common_btn {
                    padding: 8px 18px;
                    font-size: 14px;
                    border-radius: 6px;
                }
                .sidebar_category ul li.active a {
                    color: #ff3366 !important;
                    font-weight: 600;
                }
                .view-toggle-btn {
                    background: none;
                    border: none;
                    padding: 8px;
                    cursor: pointer;
                    color: #94a3b8;
                    transition: color 0.2s;
                }
                .view-toggle-btn.active {
                    color: #ff3366;
                }
            `}</style>

            <div className="mt_40">
                <div className="container mb-5">
                    <div className="row">
                        <div className="col-12 text-start">
                            <h2 style={{ fontSize: '32px', fontWeight: 700, color: '#1e293b', textTransform: 'capitalize', marginBottom: '4px' }}>
                                {selectedCategory || 'Shop'}
                            </h2>
                            <nav style={{ fontSize: '14px', color: '#64748b' }}>
                                <Link to="/" className="text-decoration-none" style={{ color: '#64748b' }}>Home</Link>
                                <span className="mx-2">/</span>
                                <Link to="/shop" className="text-decoration-none" style={{ color: selectedCategory ? '#64748b' : '#1e293b', fontWeight: selectedCategory ? 400 : 600 }} onClick={(e) => {
                                    if (!selectedCategory) return;
                                    e.preventDefault();
                                    setSelectedCategory(null);
                                }}>Shop</Link>
                                {selectedCategory && (
                                    <>
                                        <span className="mx-2">/</span>
                                        <span style={{ color: '#1e293b', fontWeight: 600, textTransform: 'capitalize' }}>{selectedCategory}</span>
                                    </>
                                )}
                            </nav>
                        </div>
                    </div>
                </div>

                <div style={{ position: 'relative', overflow: 'hidden' }}>
                {/* SKELETON OVERLAY - Fades out smoothly when filtering completes */}
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
                        transition: 'opacity 0.3s ease-in-out, visibility 0.3s ease-in-out',
                        pointerEvents: 'none'
                    }}
                >
                    <ShopSkeleton />
                </div>

                {/* REAL PAGE CONTENT */}
                <div style={{ opacity: isLoaded ? 1 : 0, transition: 'opacity 0.3s ease-in-out' }}>
                    {/*============================
                        SHOP PAGE START
                    =============================*/}
                    <section className="shop_page mb_100">
                        <div className="container">
                            <div className="row">
                                {/* Sidebar Filters */}
                                <div className="col-xxl-3 col-lg-4 col-xl-3">
                                    <div id="sticky_sidebar">
                                        <div className="shop_filter_area p-4 border rounded bg-white">
                                            
                                            <div className="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                                                <h4 className="m-0" style={{ fontSize: '18px', fontWeight: 600 }}>Filters</h4>
                                                <button 
                                                    onClick={() => {
                                                        setSelectedCategory(null);
                                                        setPriceRange([0, 150]);
                                                        setStatusFilter({ onSale: false, inStock: false });
                                                        setSelectedColors([]);
                                                        setSelectedRatings([]);
                                                    }}
                                                    className="btn btn-link p-0 text-decoration-none text-muted"
                                                    style={{ fontSize: '12px', fontWeight: 500 }}
                                                >
                                                    Clear All
                                                </button>
                                            </div>

                                            {/* Categories Filter (Top Priority) */}
                                            <div className="sidebar_category">
                                                <h3>Categories</h3>
                                                <ul className="list-unstyled">
                                                    <li className={selectedCategory === null ? 'active mb-2' : 'mb-2'}>
                                                        <a 
                                                            href="#" 
                                                            onClick={(e) => { e.preventDefault(); setSelectedCategory(null); }}
                                                            className="d-flex justify-content-between align-items-center text-decoration-none"
                                                            style={{ color: selectedCategory === null ? '#ff3366' : '#1e293b', fontSize: '14px', fontWeight: selectedCategory === null ? 600 : 400 }}
                                                        >
                                                            <span>All Categories</span>
                                                            <span className="badge bg-light text-dark rounded-pill px-2 py-1" style={{ fontSize: '10px' }}>{products.length}</span>
                                                        </a>
                                                    </li>
                                                    {categories.map((cat, idx) => (
                                                        <li key={idx} className={selectedCategory === cat.name ? 'active mb-2' : 'mb-2'}>
                                                            <a 
                                                                href="#" 
                                                                onClick={(e) => { e.preventDefault(); setSelectedCategory(cat.name); }}
                                                                className="d-flex justify-content-between align-items-center text-decoration-none"
                                                                style={{ color: selectedCategory === cat.name ? '#ff3366' : '#1e293b', fontSize: '14px', fontWeight: selectedCategory === cat.name ? 600 : 400 }}
                                                            >
                                                                <span>{cat.name}</span>
                                                                <span className="badge bg-light text-dark rounded-pill px-2 py-1" style={{ fontSize: '10px' }}>{cat.count}</span>
                                                            </a>
                                                        </li>
                                                    ))}
                                                </ul>
                                            </div>

                                            {/* Price Range Filter */}
                                            <div className="sidebar_range mt-4 pt-2 border-top">
                                                <h3>Price Range</h3>
                                                <div className="price-slider-container pt-3 pb-2">
                                                    <DualRangeSlider 
                                                        min={0}
                                                        max={150}
                                                        value={sliderRange}
                                                        onChange={setSliderRange}
                                                        onChangeEnd={setPriceRange}
                                                    />
                                                </div>
                                            </div>

                                            {/* Color Filters - Circle Palette Grid */}
                                            <div className="sidebar_color mt-4 pt-2 border-top">
                                                <h3>Filter by Color</h3>
                                                <div className="d-flex flex-wrap gap-2 pt-2">
                                                    {colorOptions.map((color, idx) => {
                                                        const isSelected = selectedColors.includes(color.hex);
                                                        return (
                                                            <button
                                                                key={idx}
                                                                onClick={() => handleColorToggle(color.hex)}
                                                                className={`color-box-checkbox position-relative d-flex align-items-center justify-content-center`}
                                                                style={{
                                                                    width: '28px',
                                                                    height: '28px',
                                                                    borderRadius: '50%',
                                                                    background: color.hex,
                                                                    border: isSelected ? '2px solid #ff3366' : '1px solid rgba(0,0,0,0.15)',
                                                                    boxShadow: isSelected ? '0 0 8px rgba(255, 51, 102, 0.4)' : 'none',
                                                                    cursor: 'pointer',
                                                                    padding: 0,
                                                                    transition: 'all 0.2s ease'
                                                                }}
                                                                title={color.label}
                                                            >
                                                                {isSelected && (
                                                                    <Check size={12} color={color.label === 'Yellow' ? '#000' : '#fff'} style={{ fontWeight: 'bold' }} />
                                                                )}
                                                            </button>
                                                        );
                                                    })}
                                                </div>
                                            </div>

                                            {/* Rating Filter */}
                                            <div className="sidebar_rating mt-4 pt-2 border-top">
                                                <h3>Rating</h3>
                                                {[5, 4, 3, 2, 1].map((stars) => (
                                                    <div key={stars} className="form-check mb-2">
                                                        <input 
                                                            className="form-check-input" 
                                                            type="checkbox" 
                                                            checked={selectedRatings.includes(stars)}
                                                            id={`rating_${stars}`}
                                                            onChange={() => handleRatingToggle(stars)}
                                                        />
                                                        <label className="form-check-label d-flex align-items-center gap-1" htmlFor={`rating_${stars}`} style={{ cursor: 'pointer' }}>
                                                            {[...Array(5)].map((_, i) => (
                                                                <Star key={i} size={13} fill={i < stars ? '#f59e0b' : 'none'} color={i < stars ? '#f59e0b' : '#ccc'} />
                                                            ))}
                                                            <span className="ms-1" style={{ fontSize: '13px' }}>{stars} star{stars > 1 ? 's' : ''} {stars < 5 && 'or above'}</span>
                                                        </label>
                                                    </div>
                                                ))}
                                            </div>

                                            {/* Product Status Filter */}
                                            <div className="sidebar_status mt-4 pt-2 border-top">
                                                <h3>Product Status</h3>
                                                <div className="form-check mb-2">
                                                    <input 
                                                        className="form-check-input" 
                                                        type="checkbox" 
                                                        checked={statusFilter.onSale} 
                                                        id="filterSale"
                                                        onChange={e => setStatusFilter(prev => ({ ...prev, onSale: e.target.checked }))}
                                                    />
                                                    <label className="form-check-label" htmlFor="filterSale" style={{ cursor: 'pointer' }}>
                                                        On sale
                                                    </label>
                                                </div>
                                                <div className="form-check">
                                                    <input 
                                                        className="form-check-input" 
                                                        type="checkbox" 
                                                        checked={statusFilter.inStock} 
                                                        id="filterStock"
                                                        onChange={e => setStatusFilter(prev => ({ ...prev, inStock: e.target.checked }))}
                                                    />
                                                    <label className="form-check-label" htmlFor="filterStock" style={{ cursor: 'pointer' }}>
                                                        In Stock
                                                    </label>
                                                </div>
                                            </div>

                                            {/* Top Rated Products Widget */}
                                            <div className="sidebar_related_product mt-4 pt-2 border-top">
                                                <h3 className="mb-3">Top Rated Products</h3>
                                                <ul className="list-unstyled p-0 m-0">
                                                    {[
                                                        { id: 24, name: "women's long full Shoes", price: 65, rating: 5, reviewsCount: 30, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_24.png" },
                                                        { id: 4, name: "Comfortable Sports Sneakers", price: 75, rating: 4, reviewsCount: 58, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_4.png" },
                                                        { id: 14, name: "Kids Cotton Combo Bag", price: 45, rating: 4, reviewsCount: 18, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_14.png" }
                                                    ].map((topProd) => (
                                                        <li key={topProd.id} className="d-flex align-items-center gap-3 mb-3 border-bottom pb-2">
                                                            <Link to={`/shop/product/${getSlug(topProd.name)}`} className="img" style={{ width: '50px', flexShrink: 0 }}>
                                                                <img src={topProd.img} alt={topProd.name} className="img-fluid rounded" />
                                                            </Link>
                                                            <div className="text">
                                                                <Link className="title text-decoration-none text-dark d-block mb-1" to={`/shop/product/${getSlug(topProd.name)}`} style={{ fontSize: '13px', fontWeight: 500, lineHeight: 1.3 }}>
                                                                    {topProd.name}
                                                                </Link>
                                                                <p className="rating d-flex align-items-center gap-1 mb-1">
                                                                    {[...Array(5)].map((_, i) => (
                                                                        <Star key={i} size={11} fill={i < topProd.rating ? '#f59e0b' : 'none'} color={i < topProd.rating ? '#f59e0b' : '#ccc'} />
                                                                    ))}
                                                                    <span className="text-muted" style={{ fontSize: '11px' }}>({topProd.reviewsCount})</span>
                                                                </p>
                                                                <p className="price mb-0" style={{ fontSize: '14px', fontWeight: 600, color: '#ff3366' }}>
                                                                    {formatPrice(topProd.price)}
                                                                </p>
                                                            </div>
                                                        </li>
                                                    ))}
                                                </ul>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                {/* Catalog List Area */}
                                <div className="col-xxl-9 col-lg-8 col-xl-9">
                                    <div className="product_page_top bg-white p-3 border rounded-3 mb-4">
                                        <div className="row align-items-center">
                                            <div className="col-12 col-md-6 d-flex align-items-center justify-content-between justify-content-md-start gap-4">
                                                <div className="d-flex align-items-center border rounded p-1">
                                                    <button 
                                                        className={`view-toggle-btn ${viewMode === 'grid' ? 'active' : ''}`}
                                                        onClick={() => setViewMode('grid')}
                                                        title="Grid View"
                                                    >
                                                        <Grid size={18} />
                                                    </button>
                                                    <button 
                                                        className={`view-toggle-btn ${viewMode === 'list' ? 'active' : ''}`}
                                                        onClick={() => setViewMode('list')}
                                                        title="List View"
                                                    >
                                                        <List size={18} />
                                                    </button>
                                                </div>
                                                <p className="mb-0 text-muted" style={{ fontSize: '14px' }}>
                                                    Showing {Math.min(startIndex + 1, totalItems)}–{Math.min(startIndex + itemsPerPage, totalItems)} of {totalItems} results
                                                </p>
                                            </div>
                                            <div className="col-12 col-md-6 mt-3 mt-md-0 d-flex justify-content-start justify-content-md-end gap-2">
                                                <select 
                                                    className="form-select border-0 bg-light py-2 px-3" 
                                                    style={{ width: '160px', fontSize: '14px', borderRadius: '8px' }}
                                                    value={sortBy}
                                                    onChange={e => setSortBy(e.target.value)}
                                                >
                                                    <option value="default">Default Sorting</option>
                                                    <option value="low-high">Price: Low to High</option>
                                                    <option value="high-low">Price: High to Low</option>
                                                    <option value="newest">New Added</option>
                                                    <option value="on-sale">On Sale</option>
                                                </select>
                                                <select 
                                                    className="form-select border-0 bg-light py-2 px-3" 
                                                    style={{ width: '120px', fontSize: '14px', borderRadius: '8px' }}
                                                    value={itemsPerPage}
                                                    onChange={e => setItemsPerPage(Number(e.target.value))}
                                                >
                                                    <option value="12">Show: 12</option>
                                                    <option value="16">Show: 16</option>
                                                    <option value="20">Show: 20</option>
                                                    <option value="24">Show: 24</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Products Rendering Pane */}
                                    {paginatedProducts.length === 0 ? (
                                        <div className="text-center py-5 border rounded bg-white">
                                            <p className="text-muted mb-0">No products found matching the selected filters.</p>
                                            <button 
                                                className="common_btn mt-3" 
                                                onClick={() => {
                                                    setSelectedCategory(null);
                                                    setPriceRange([0, 150]);
                                                    setStatusFilter({ onSale: false, inStock: false });
                                                    setSelectedColors([]);
                                                    setSelectedRatings([]);
                                                    setSortBy('default');
                                                }}
                                            >
                                                Reset Filters
                                            </button>
                                        </div>
                                    ) : viewMode === 'grid' ? (
                                        <div className="row">
                                            {paginatedProducts.map(product => (
                                                <div key={product.id} className="col-xxl-3 col-xl-3 col-6 col-md-4 col-sm-6 mb-4">
                                                    <ProductCard {...product} />
                                                </div>
                                            ))}
                                        </div>
                                    ) : (
                                        <div className="row">
                                            {paginatedProducts.map(product => (
                                                <div key={product.id} className="col-12 col-xxl-10 mb-4">
                                                    <div className="product_list_item product_item_2 product_item">
                                                        <div className="row align-items-center">
                                                            <div className="col-md-5 col-sm-6 col-xxl-4">
                                                                <div className="product_img">
                                                                    <Link to={`/shop/product/${getSlug(product.name)}`} className="d-block">
                                                                        <img src={product.img} alt={product.name} className="img-fluid w-100" />
                                                                    </Link>
                                                                    <ul className="discount_list">
                                                                        {product.discount && <li className="discount">-{product.discount}%</li>}
                                                                        {product.isNew && <li className="new">new</li>}
                                                                    </ul>
                                                                    <ul className="btn_list">
                                                                        <li>
                                                                            <a href="#">
                                                                                <img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/compare_icon_white.svg'} alt="Compare" className="img-fluid" />
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a href="#">
                                                                                <img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/love_icon_white.svg'} alt="Love" className="img-fluid" />
                                                                            </a>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <div className="col-md-7 col-sm-6 col-xxl-8">
                                                                <div className="product_text">
                                                                    <Link className="title" to={`/shop/product/${getSlug(product.name)}`}>
                                                                        {product.name}
                                                                    </Link>
                                                                    <p className="price">
                                                                        {formatPrice(product.price)}
                                                                        {product.oldPrice && <del className="ms-2">{formatPrice(product.oldPrice)}</del>}
                                                                    </p>
                                                                    <ul className="color" style={{ padding: 0 }}>
                                                                        {product.colors.map((color, idx) => (
                                                                            <li key={idx} className={idx === 0 ? 'active' : ''} style={{ background: color }}></li>
                                                                        ))}
                                                                    </ul>
                                                                    <p className="short_description">
                                                                        High-quality custom fabric styled for standard fits and modern elegance. Durable stitching with soft elements ensuring maximum comfort and lifestyle utility.
                                                                    </p>
                                                                    <Link className="common_btn mt-3" to={`/shop/product/${getSlug(product.name)}`}>
                                                                        add to cart <ArrowRight size={16} className="ms-1" style={{ display: 'inline-block' }} />
                                                                    </Link>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    )}

                                    {/* Pagination Controls */}
                                    {totalPages > 1 && (
                                        <div className="pagination_area mt-4 d-flex justify-content-center">
                                            <ul className="pagination d-flex gap-2 list-unstyled">
                                                {currentPage > 1 && (
                                                    <li>
                                                        <a href="#" onClick={(e) => { e.preventDefault(); setCurrentPage(prev => prev - 1); }}>
                                                            <Minus size={14} />
                                                        </a>
                                                    </li>
                                                )}
                                                {[...Array(totalPages)].map((_, i) => (
                                                    <li key={i}>
                                                        <a 
                                                            href="#" 
                                                            className={currentPage === i + 1 ? 'active' : ''}
                                                            onClick={(e) => { e.preventDefault(); setCurrentPage(i + 1); }}
                                                        >
                                                            {i + 1}
                                                        </a>
                                                    </li>
                                                ))}
                                                {currentPage < totalPages && (
                                                    <li>
                                                        <a href="#" onClick={(e) => { e.preventDefault(); setCurrentPage(prev => prev + 1); }}>
                                                            <Plus size={14} />
                                                        </a>
                                                    </li>
                                                )}
                                            </ul>
                                        </div>
                                    )}

                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            </div>
        </ShopLayout>
    );
}





