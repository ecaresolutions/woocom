import { Link } from 'react-router-dom';
import { ArrowRight, Star, StarHalf } from 'lucide-react';
import { formatPrice } from '@/lib/currency';

export default function SpecialProducts({ products = [] }: { products?: any[] }) {
    const localProducts = [
        { id: 30, name: "Men's Premium Formal Pant", price: 29.00, oldPrice: 32.00, discount: 9, rating: 4.5, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_30.png" },
        { id: 1, name: "Full Sleeve Hoodie Jacket", price: 20.00, oldPrice: 22.00, discount: 9, rating: 4.5, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_1.png" },
        { id: 2, name: "Kids Cotton Combo Set", price: 16.00, oldPrice: 18.00, discount: 11, rating: 4.5, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_2.png" },
        { id: 27, name: "Men's Trendy Formal Shoes", price: 10.00, oldPrice: 12.00, discount: 17, rating: 4.5, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_27.png" },
        { id: 28, name: "Men's T-Shirt Combo Set", price: 17.00, oldPrice: 20.00, discount: 15, rating: 4.5, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_28.png" },
        { id: 29, name: "Women's T-Shirt Combo", price: 13.00, oldPrice: 15.00, discount: 13, rating: 4.5, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_29.png" },
        { id: 11, name: "Classic Shoulder Bag", price: 40.00, oldPrice: 48.00, discount: 16, rating: 4.5, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_11.png" },
        { id: 12, name: "Premium Leather Hand Bag", price: 120.00, oldPrice: 140.00, discount: 14, rating: 4.8, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_12.png" },
        { id: 6, name: "Women's Designer Tops", price: 55.00, oldPrice: 65.00, discount: 15, rating: 4.2, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_6.png" },
        { id: 8, name: "Kids Summer Cotton Combo", price: 38.00, oldPrice: 45.00, discount: 16, rating: 4.4, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_8.png" },
        { id: 4, name: "Comfortable Sports Sneakers", price: 75.00, oldPrice: 85.00, discount: 12, rating: 4.7, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_4.png" },
        { id: 13, name: "Sharee Petticoat For Women", price: 28.00, oldPrice: 35.00, discount: 20, rating: 4.2, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_13.png" }
    ];

    const displayProducts = (products.length >= 12 ? products : [...products, ...localProducts]).slice(0, 12);

    const getSlug = (str: string) => {
        return str
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)+/g, '');
    };

    const renderStars = (rating: number = 4.5) => {
        const stars = [];
        const fullStars = Math.floor(rating);
        const hasHalf = rating % 1 !== 0;

        for (let i = 1; i <= 5; i++) {
            if (i <= fullStars) {
                stars.push(<Star key={i} size={11} fill="#f59e0b" color="#f59e0b" style={{ display: 'inline-block', marginRight: '1px' }} />);
            } else if (i === fullStars + 1 && hasHalf) {
                stars.push(<StarHalf key={i} size={11} fill="#f59e0b" color="#f59e0b" style={{ display: 'inline-block', marginRight: '1px' }} />);
            } else {
                stars.push(<Star key={i} size={11} fill="none" color="#cbd5e1" style={{ display: 'inline-block', marginRight: '1px' }} />);
            }
        }
        return stars;
    };

    return (
        <section className="special_product_2 pt_85">
            <style>{`
                .special_product_item_custom {
                    background: #ffffff;
                    border: 1px solid #eef2f6;
                    border-radius: 10px;
                    padding: 10px 14px;
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    height: 100%;
                    margin: 0 !important;
                    transition: all 0.25s ease;
                    position: relative;
                }
                .special_product_item_custom:hover {
                    border-color: #f59e0b;
                    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
                    transform: translateY(-2px);
                }
                .special_product_item_custom .product_thumb {
                    width: 76px;
                    height: 76px;
                    flex-shrink: 0;
                    border-radius: 8px;
                    overflow: hidden;
                    background: #f8fafc;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    position: relative;
                }
                .special_product_item_custom .product_thumb img {
                    max-height: 70px;
                    max-width: 70px;
                    object-fit: contain;
                    transition: transform 0.3s ease;
                }
                .special_product_item_custom:hover .product_thumb img {
                    transform: scale(1.06);
                }
                .special_discount_badge {
                    position: absolute;
                    top: -6px;
                    right: 8px;
                    background: #c23321;
                    color: #ffffff;
                    font-size: 10px;
                    font-weight: 700;
                    padding: 2px 7px;
                    border-radius: 4px;
                    letter-spacing: 0.3px;
                }
            `}</style>
            <div className="container">
                <div className="row">
                    <div className="col-xl-6 col-sm-9">
                        <div className="section_heading_2 section_heading">
                            <h3>Our <span>Spatial</span> Brand Products</h3>
                        </div>
                    </div>
                    <div className="col-xl-6 col-sm-3">
                        <div className="view_all_btn_area">
                            <Link className="view_all_btn" to="/shop">View all</Link>
                        </div>
                    </div>
                </div>

                <div className="row pt_15 align-items-stretch">
                    {/* Left Banner */}
                    <div className="col-xl-4 col-lg-5 wow fadeInLeft mb-4 mb-lg-0 d-flex">
                        <div 
                            className="special_product_banner w-100" 
                            style={{ 
                                margin: 0, 
                                height: '100%', 
                                minHeight: '520px',
                                borderRadius: '12px',
                                overflow: 'hidden',
                                position: 'relative'
                            }}
                        >
                            <img 
                                src={(window.wpData?.homeUrl || '') + '/wp-content/themes/woofashion-spa/public/zenis/images/home2_special_banner.jpg'} 
                                alt="special product" 
                                className="img-fluid w-100 h-100" 
                                style={{ objectFit: 'cover' }}
                            />
                            <div className="text" style={{ padding: '38px 28px' }}>
                                <h3 style={{ fontSize: '26px', lineHeight: 1.25 }}>make your fashion look more changing</h3>
                                <p style={{ fontSize: '14px', marginBottom: '20px' }}>Get 50% Off on Selected Clothing Items</p>
                                <Link className="common_btn" to="/shop" style={{ padding: '8px 22px', fontSize: '13px' }}>
                                    shop now <ArrowRight size={14} className="ms-1" style={{ display: 'inline-block' }} />
                                </Link>
                            </div>
                        </div>
                    </div>

                    {/* Right Side 12 Products (6 rows x 2 columns) with clean tight spacing */}
                    <div className="col-xl-8 col-lg-7 d-flex flex-column justify-content-between">
                        <div className="row g-2 g-md-3">
                            {displayProducts.map((prod) => {
                                const productSlug = prod.slug || getSlug(prod.name);
                                const imageSrc = prod.img || prod.image || ((window.wpData?.homeUrl || '') + '/wp-content/themes/woofashion-spa/public/zenis/images/best_sell_pro_img_1.jpg');
                                return (
                                    <div key={prod.id} className="col-md-6 col-sm-6 wow fadeInUp">
                                        <div className="special_product_item_custom">
                                            {prod.discount && (
                                                <span className="special_discount_badge">
                                                    Save {prod.discount}%
                                                </span>
                                            )}
                                            <Link to={`/shop/product/${productSlug}`} className="product_thumb">
                                                <img src={imageSrc} alt={prod.name} />
                                            </Link>
                                            <div className="product_info flex-grow-1" style={{ minWidth: 0 }}>
                                                <Link 
                                                    to={`/shop/product/${productSlug}`} 
                                                    className="text-dark fw-bold text-decoration-none d-block mb-1" 
                                                    style={{ 
                                                        fontSize: '13.5px', 
                                                        lineHeight: '1.3',
                                                        display: '-webkit-box',
                                                        WebkitLineClamp: 1,
                                                        WebkitBoxOrient: 'vertical',
                                                        overflow: 'hidden'
                                                    }}
                                                >
                                                    {prod.name}
                                                </Link>
                                                <div className="d-flex align-items-center mb-1">
                                                    {renderStars(prod.rating || 4.5)}
                                                </div>
                                                <p className="m-0 fw-bold" style={{ fontSize: '14px', color: '#ff3366' }}>
                                                    {formatPrice(prod.price)}
                                                    {prod.oldPrice && (
                                                        <del className="ms-2 text-muted fw-normal" style={{ fontSize: '12px' }}>
                                                            {formatPrice(prod.oldPrice)}
                                                        </del>
                                                    )}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}