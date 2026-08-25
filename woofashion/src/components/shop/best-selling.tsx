import { Link } from 'react-router-dom';
import { ArrowRight, ArrowUpRight } from 'lucide-react';
import { formatPrice } from '@/lib/currency';

export default function BestSelling({ products = [] }: { products?: any[] }) {
    const localProducts = [
        { id: 15, name: "Men's trendy casual shoes", price: 89.00, oldPrice: 120.00, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/best_sell_pro_img_1.jpg" },
        { id: 16, name: "Kid's Western Party Bag", price: 35.00, oldPrice: null, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/best_sell_pro_img_2.jpg" },
        { id: 7, name: "Denim 2 Quarter Pant", price: 40.00, oldPrice: 45.00, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/best_sell_pro_img_3.jpg" }
    ];

    const displayProducts = (products.length > 0 ? products : localProducts).slice(0, 3);

    const getSlug = (str: string) => {
        return str
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)+/g, '');
    };

    return (
        <section className="best_selling_product_2 mt_95">
            <div className="container">
                <div className="row">
                    <div className="col-xl-6 col-sm-9">
                        <div className="section_heading_2 section_heading">
                            <h3>Our <span>Best</span> Selling Products</h3>
                        </div>
                    </div>
                    <div className="col-xl-6 col-sm-3">
                        <div className="view_all_btn_area">
                            <Link className="view_all_btn" to="/shop">View all</Link>
                        </div>
                    </div>
                </div>
                <div className="row mt_15">
                    <div className="col-xl-7">
                        <div className="row">
                            {displayProducts.map((product) => {
                                const imageSrc = product.img || product.image;
                                const formattedPrice = formatPrice(product.price);
                                const formattedOldPrice = product.oldPrice ? formatPrice(product.oldPrice) : null;

                                return (
                                    <div key={product.id} className="col-xl-4 col-sm-6 col-md-4 wow fadeInUp">
                                        <div className="best_selling_product_item" style={{ height: '420px', background: '#f8fafc', padding: '16px', borderRadius: '16px', display: 'flex', flexDirection: 'column', justifyContent: 'space-between' }}>
                                            <Link to={`/shop/product/${getSlug(product.name)}`} className="d-flex align-items-center justify-content-center" style={{ height: '210px', width: '100%', overflow: 'hidden' }}>
                                                <img src={imageSrc} alt={product.name} style={{ maxHeight: '200px', maxWidth: '100%', objectFit: 'contain', transition: 'transform 0.3s ease' }} />
                                            </Link>
                                            <div className="text" style={{ position: 'static', padding: '10px 0 0 0', width: '100%' }}>
                                                <Link className="title" to={`/shop/product/${getSlug(product.name)}`} style={{ fontSize: '15px', fontWeight: 600, color: '#0f172a', display: '-webkit-box', WebkitLineClamp: 2, WebkitBoxOrient: 'vertical', overflow: 'hidden', minHeight: '40px', lineHeight: '1.3' }}>
                                                    {product.name}
                                                </Link>
                                                <p className="price" style={{ margin: '8px 0', fontSize: '20px', fontWeight: 'bold' }}>
                                                    {formattedPrice} {formattedOldPrice && <del style={{ fontSize: '14px', marginLeft: '6px', color: '#94a3b8' }}>{formattedOldPrice}</del>}
                                                </p>
                                                <Link 
                                                    className="buy_btn" 
                                                    to={`/shop/product/${getSlug(product.name)}`}
                                                    style={{ 
                                                        display: 'inline-flex', 
                                                        alignItems: 'center', 
                                                        whiteSpace: 'nowrap', 
                                                        gap: '4px', 
                                                        borderBottom: '1.5px solid #ffa500', 
                                                        width: 'fit-content',
                                                        paddingBottom: '2px',
                                                        color: '#0f172a',
                                                        fontWeight: 500,
                                                        textDecoration: 'none',
                                                        marginTop: '10px'
                                                    }}
                                                >
                                                    <span>Buy Now</span>
                                                    <ArrowUpRight size={15} style={{ flexShrink: 0 }} />
                                                </Link>
                                            </div>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    </div>
                    <div className="col-xl-5 wow fadeInRight">
                        <div className="best_selling_product_item_large">
                            <img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/best_sell_pro_img_4.jpg'} alt="best sell" className="img-fluid w-100" />
                            <div className="text" style={{ width: '55%' }}>
                                <Link className="title" to="/shop">Best Sales Discount And Offers</Link>
                                <p className="price">{formatPrice(89)} <del>{formatPrice(120)}</del></p>
                                <Link className="common_btn" to="/shop">
                                    Buy Now <ArrowRight size={14} className="ms-1" color="#ffffff" />
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}




