import { useEffect, useRef, useState } from 'react';
import { Link } from 'react-router-dom';
import { ChevronRight, ArrowRight } from 'lucide-react';

export default function HeroSlider() {
    const sliderRef = useRef<HTMLDivElement>(null);
    const [mountKey, setMountKey] = useState(0);

    useEffect(() => {
        // Trigger a re-mount on the client side to bypass cached jQuery nodes
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
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        autoplay: true,
                        autoplaySpeed: 3000,
                        dots: true,
                        arrows: false,
                        fade: true,
                    });
                } catch (err) {
                    console.error("Error initializing slick on hero slider:", err);
                }
            }, 50);
        }

        return () => {
            clearTimeout(timer);
            // @ts-ignore
            if ($ && sliderRef.current) {
                try {
                    // @ts-ignore
                    if ($(sliderRef.current).hasClass('slick-initialized')) {
                        // @ts-ignore
                        $(sliderRef.current).slick('unslick');
                    }
                } catch (e) {
                    console.warn("Caught error during hero slider slick destruction:", e);
                }
            }
        };
    }, [mountKey]);

    return (
        <section className="banner_2">
            <div className="container">
                <div className="row">
                    <div className="col-xl-2 d-none d-xxl-block">
                        <ul className="menu_cat_item">
                            <li>
                                <Link to="/shop">
                                    <span>
                                        <img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/category_list_icon_1.png'} alt="category" />
                                    </span>
                                    Men’s Fashion
                                    <ChevronRight size={14} style={{ position: 'absolute', right: '20px', top: '50%', transform: 'translateY(-50%)', opacity: 0.8 }} />
                                </Link>
                                <ul className="menu_cat_droapdown">
                                    <li><Link to="/shop" className="d-flex align-items-center">shirts <ChevronRight size={12} className="ms-auto" /></Link>
                                        <ul className="sub_category">
                                            <li><Link to="/shop">Casual Shirts</Link> </li>
                                            <li><Link to="/shop">Formal Shirts</Link></li>
                                            <li><Link to="/shop">Denim Shirts</Link></li>
                                        </ul>
                                    </li>
                                    <li><Link to="/shop" className="d-flex align-items-center">pant <ChevronRight size={12} className="ms-auto" /></Link>
                                        <ul className="sub_category">
                                            <li><Link to="/shop">Casual Pants</Link></li>
                                            <li><Link to="/shop">Formal Trousers</Link> </li>
                                            <li><Link to="/shop">Jeans & Denim</Link></li>
                                        </ul>
                                    </li>
                                    <li><Link to="/shop">Casual Wear</Link></li>
                                    <li><Link to="/shop">Formal Attire</Link></li>
                                </ul>
                            </li>
                            <li>
                                <Link to="/shop">
                                    <span>
                                        <img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/category_list_icon_2.png'} alt="category" />
                                    </span>
                                    Women's Fashion
                                    <ChevronRight size={14} style={{ position: 'absolute', right: '20px', top: '50%', transform: 'translateY(-50%)', opacity: 0.8 }} />
                                </Link>
                                <ul className="menu_cat_droapdown">
                                    <li><Link to="/shop">sharee</Link></li>
                                    <li><Link to="/shop" className="d-flex align-items-center">Shirts <ChevronRight size={12} className="ms-auto" /></Link>
                                        <ul className="sub_category">
                                            <li><Link to="/shop">Full Sleeves Printed</Link> </li>
                                            <li><Link to="/shop">Full Sleeves Solid</Link></li>
                                            <li><Link to="/shop">Half Sleeves Solid</Link></li>
                                        </ul>
                                    </li>
                                    <li><Link to="/shop" className="d-flex align-items-center">T-Shirts <ChevronRight size={12} className="ms-auto" /></Link>
                                        <ul className="sub_category">
                                            <li><Link to="/shop">Crew Neck</Link></li>
                                            <li><Link to="/shop">V Neck</Link> </li>
                                            <li><Link to="/shop">Henley Neck</Link></li>
                                        </ul>
                                    </li>
                                    <li><Link to="/shop">Nightie Set</Link></li>
                                    <li><Link to="/shop">3-Piece</Link></li>
                                    <li><Link to="/shop">leggings</Link></li>
                                </ul>
                            </li>
                            <li>
                                <Link to="/shop">
                                    <span>
                                        <img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/category_list_icon_3.png'} alt="category" />
                                    </span>
                                    KId's Fashion
                                    <ChevronRight size={14} style={{ position: 'absolute', right: '20px', top: '50%', transform: 'translateY(-50%)', opacity: 0.8 }} />
                                </Link>
                                <ul className="menu_cat_droapdown">
                                    <li><Link to="/shop">Boys’ Fashion</Link></li>
                                    <li><Link to="/shop">Girls’ Fashion</Link></li>
                                    <li><Link to="/shop" className="d-flex align-items-center">Newborn Essentials <ChevronRight size={12} className="ms-auto" /></Link>
                                        <ul className="sub_category">
                                            <li><Link to="/shop">Sleepwear</Link></li>
                                            <li><Link to="/shop">Loungewear</Link></li>
                                        </ul>
                                    </li>
                                    <li><Link to="/shop">Party & Occasion Wear</Link></li>
                                    <li><Link to="/shop">Winter Warmers</Link></li>
                                    <li><Link to="/shop">Summer Coolers</Link></li>
                                </ul>
                            </li>
                            <li>
                                <Link to="/shop">
                                    <span>
                                        <img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/category_list_icon_4.png'} alt="category" />
                                    </span>
                                    denim Collection
                                    <ChevronRight size={14} style={{ position: 'absolute', right: '20px', top: '50%', transform: 'translateY(-50%)', opacity: 0.8 }} />
                                </Link>
                                <ul className="menu_cat_droapdown">
                                    <li><Link to="/shop">Denim Essentials</Link></li>
                                    <li><Link to="/shop">Jeans & Bottoms</Link></li>
                                    <li><Link to="/shop">Denim Jackets</Link></li>
                                    <li><Link to="/shop">Outerwear</Link></li>
                                    <li><Link to="/shop">Denim Shirts & Tops</Link></li>
                                    <li><Link to="/shop">Denim Shorts</Link></li>
                                </ul>
                            </li>
                            <li>
                                <Link to="/shop">
                                    <span>
                                        <img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/category_list_icon_5.png'} alt="category" />
                                    </span>
                                    western wear
                                    <ChevronRight size={14} style={{ position: 'absolute', right: '20px', top: '50%', transform: 'translateY(-50%)', opacity: 0.8 }} />
                                </Link>
                                <ul className="menu_cat_droapdown">
                                    <li><Link to="/shop">Dresses & Jumpsuits</Link></li>
                                    <li><Link to="/shop">Tops & Blouses</Link></li>
                                    <li><Link to="/shop">T-Shirts & Tank Tops</Link></li>
                                    <li><Link to="/shop">Jeans & Denim</Link></li>
                                    <li><Link to="/shop">Trousers & Pants</Link></li>
                                    <li><Link to="/shop">Skirts & Shorts</Link></li>
                                    <li><Link to="/shop">Blazers & Jackets</Link></li>
                                </ul>
                            </li>
                            <li>
                                <Link to="/shop">
                                    <span>
                                        <img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/category_list_icon_6.png'} alt="category" />
                                    </span>
                                    sport wear
                                    <ChevronRight size={14} style={{ position: 'absolute', right: '20px', top: '50%', transform: 'translateY(-50%)', opacity: 0.8 }} />
                                </Link>
                                <ul className="menu_cat_droapdown">
                                    <li><Link to="/shop">Men’s Activewear</Link></li>
                                    <li><Link to="/shop">Women’s Activewear</Link></li>
                                    <li><Link to="/shop">Gym & Training Gear</Link></li>
                                    <li><Link to="/shop">Running Apparel</Link></li>
                                    <li><Link to="/shop">Yoga & Athleisure</Link></li>
                                </ul>
                            </li>
                            <li>
                                <Link to="/shop">
                                    <span>
                                        <img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/category_list_icon_7.png'} alt="category" />
                                    </span>
                                    footwear
                                    <ChevronRight size={14} style={{ position: 'absolute', right: '20px', top: '50%', transform: 'translateY(-50%)', opacity: 0.8 }} />
                                </Link>
                                <ul className="menu_cat_droapdown">
                                    <li><Link to="/shop">Men’s Footwear</Link></li>
                                    <li><Link to="/shop">Women’s Footwear</Link></li>
                                    <li><Link to="/shop">Casual Shoes</Link></li>
                                    <li><Link to="/shop">Formal Shoes</Link></li>
                                    <li><Link to="/shop">Boots & Winter Wear</Link></li>
                                    <li><Link to="/shop">Sandals & Slippers</Link></li>
                                </ul>
                            </li>
                            <li>
                                <Link to="/shop">
                                    <span>
                                        <img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/category_list_icon_8.png'} alt="category" />
                                    </span>
                                    fashion jewellery
                                    <ChevronRight size={14} style={{ position: 'absolute', right: '20px', top: '50%', transform: 'translateY(-50%)', opacity: 0.8 }} />
                                </Link>
                                <ul className="menu_cat_droapdown">
                                    <li><Link to="/shop">Necklaces & Pendants</Link></li>
                                    <li><Link to="/shop">Earrings & Studs</Link></li>
                                    <li><Link to="/shop">Bracelets & Bangles</Link></li>
                                    <li><Link to="/shop">Rings & Finger Jewelry</Link></li>
                                    <li><Link to="/shop">Brooches & Pins</Link></li>
                                    <li><Link to="/shop">Hair Accessories</Link></li>
                                </ul>
                            </li>
                            <li>
                                <Link to="/shop">
                                    <span>
                                        <img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/category_list_icon_2.png'} alt="category" />
                                    </span>
                                    Beauty & Cosmetics
                                    <ChevronRight size={14} style={{ position: 'absolute', right: '20px', top: '50%', transform: 'translateY(-50%)', opacity: 0.8 }} />
                                </Link>
                                <ul className="menu_cat_droapdown">
                                    <li><Link to="/shop">Necklaces & Pendants</Link></li>
                                    <li><Link to="/shop">Earrings & Studs</Link></li>
                                    <li><Link to="/shop">Bracelets & Bangles</Link></li>
                                    <li><Link to="/shop">Rings & Finger Jewelry</Link></li>
                                    <li><Link to="/shop">Brooches & Pins</Link></li>
                                    <li><Link to="/shop">Hair Accessories</Link></li>
                                </ul>
                            </li>
                            <li className="all_category">
                                <Link to="/category" className="d-flex align-items-center">View All Categories <ArrowRight size={14} className="ms-1" /></Link>
                            </li>
                        </ul>
                    </div>
                    <div className="col-xxl-7 col-lg-8">
                        <div className="banner_content">
                            <div key={mountKey} ref={sliderRef} className="row banner_2_slider">
                                <div className="col-xl-12">
                                    <div className="banner_slider_2 wow fadeInUp" style={{ backgroundImage: 'url(' + window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/slider_1.jpg)' }}>
                                        <div className="banner_slider_2_text">
                                            <h3>New arrivals of 2026</h3>
                                            <h1>Where Fashion Meets Individuality</h1>
                                            <Link className="common_btn d-inline-flex align-items-center gap-2" to="/shop">shop now <ArrowRight size={16} /></Link>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-xl-12">
                                    <div className="banner_slider_2 wow fadeInUp" style={{ backgroundImage: 'url(' + window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/slider_2.jpg)' }}>
                                        <div className="banner_slider_2_text">
                                            <h3>Trending of this month</h3>
                                            <h1>make your fashion look more changing</h1>
                                            <Link className="common_btn d-inline-flex align-items-center gap-2" to="/shop">shop now <ArrowRight size={16} /></Link>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-xl-12">
                                    <div className="banner_slider_2 wow fadeInUp" style={{ backgroundImage: 'url(' + window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/slider_3.jpg)' }}>
                                        <div className="banner_slider_2_text">
                                            <h3>Best selling of 2026</h3>
                                            <h1>Discover ypur Best fitting Clothes</h1>
                                            <Link className="common_btn d-inline-flex align-items-center gap-2" to="/shop">shop now <ArrowRight size={16} /></Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="col-xxl-3 col-lg-4 col-sm-12 col-md-12">
                        <div className="row">
                            <div className="col-xl-12">
                                <div className="banner_2_add wow fadeInUp" style={{ backgroundImage: 'url(' + window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/banner_3_add_bg_1.jpg)' }}>
                                    <div className="text">
                                        <h4>Summer Offer</h4>
                                        <h2>Make Your Fashion Story Unique Every Day</h2>
                                        <Link className="common_btn d-inline-flex align-items-center gap-2" to="/shop">shop now <ArrowRight size={16} /></Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}




