import { useEffect, useRef, useState } from 'react';
import { Link } from 'react-router-dom';
import { ShoppingBag, RefreshCw, Heart, ArrowRight } from 'lucide-react';
import { formatPrice } from '@/lib/currency';

export default function FavouriteProducts({ products = [] }: { products?: any[] }) {
    const sliderRef = useRef<HTMLDivElement>(null);
    const [mountKey, setMountKey] = useState(0);

    const localProducts = [
        { id: 22, name: "cherry fabric western Bag", price: 46.00, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_22.png" },
        { id: 24, name: "women's long full Shoes", price: 65.00, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_24.png" },
        { id: 25, name: "Men's premium check Shoes", price: 32.00, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_25.png" },
        { id: 26, name: "Women's designer Shoes", price: 28.00, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_26.png" }
    ];

    const displayProducts = products.length > 0 ? products : localProducts;

    useEffect(() => {
        setMountKey(prev => prev + 1);
    }, []);

    useEffect(() => {
        if (mountKey === 0) return;

        // @ts-ignore
        const $ = window.$;
        let timer: any;

        if ($ && sliderRef.current) {
            timer = setTimeout(() => {
                try {
                    // @ts-ignore
                    $(sliderRef.current).slick({
                        slidesToShow: 4,
                        slidesToScroll: 1,
                        autoplay: true,
                        autoplaySpeed: 2500,
                        dots: false,
                        arrows: false,
                        responsive: [
                            {
                                breakpoint: 1200,
                                settings: {
                                    slidesToShow: 3,
                                }
                            },
                            {
                                breakpoint: 992,
                                settings: {
                                    slidesToShow: 2,
                                }
                            },
                            {
                                breakpoint: 576,
                                settings: {
                                    slidesToShow: 1,
                                }
                            }
                        ]
                    });
                } catch (err) {
                    console.error("Error initializing favourite_product_2_slider:", err);
                }
            }, 100);
        }

        return () => {
            if (timer) clearTimeout(timer);
            try {
                // @ts-ignore
                if ($ && sliderRef.current && $(sliderRef.current).hasClass('slick-initialized')) {
                    // @ts-ignore
                    $(sliderRef.current).slick('unslick');
                }
            } catch (e) {}
        };
    }, [mountKey]);

    const getSlug = (str: string) => {
        return str
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)+/g, '');
    };

    return (
        <section className="favourite_product_2 mt_100">
            <div className="container">
                <div className="row">
                    <div className="col-xl-3 col-lg-4 wow fadeInLeft">
                        <div className="bundle_product_banner">
                            <img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/favourite_pro_2_banner_img.png'} alt="bundle" className="img-fluid" />
                            <div className="text">
                                <h4>This Spring On Apple <span>Up To 50K Off</span></h4>
                                <p>Limited Time Offer</p>
                                <Link className="common_btn" to="/shop">
                                    shop now <ArrowRight size={14} className="ms-1" />
                                </Link>
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-9 col-lg-8">
                        <div className="row">
                            <div className="col-xl-8">
                                <div className="section_heading_2 section_heading">
                                    <h3>Our <span>Favorite</span> Style Product</h3>
                                </div>
                            </div>
                        </div>
                        <div key={mountKey} ref={sliderRef} className="row mt_40 favourite_product_2_slider">
                            {displayProducts.map((product, idx) => {
                                const imageSrc = product.img || product.image;
                                const formattedPrice = formatPrice(product.price);

                                return (
                                    <div key={idx} className="col-xl-3">
                                        <div className="product_item_2 product_item">
                                            <div className="product_img">
                                                <Link to={`/shop/product/${getSlug(product.name)}`} className="d-block">
                                                    <img src={imageSrc} alt={product.name} className="img-fluid w-100" />
                                                </Link>
                                                <ul className="discount_list">
                                                    <li className="new">new</li>
                                                </ul>
                                                <ul className="btn_list">
                                                    <li>
                                                        <a href="#" onClick={(e) => e.preventDefault()}>
                                                            <RefreshCw size={14} color="#fff" />
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#" onClick={(e) => e.preventDefault()}>
                                                            <Heart size={14} color="#fff" />
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#" onClick={(e) => e.preventDefault()}>
                                                            <ShoppingBag size={14} color="#fff" />
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div className="product_text">
                                                <Link className="title" to={`/shop/product/${getSlug(product.name)}`}>{product.name}</Link>
                                                <p className="price">{formattedPrice}</p>
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




