import { useEffect, useRef, useState } from 'react';
import { Link } from 'react-router-dom';
import ProductCard from './product-card';

export default function FlashSale({ products = [] }: { products?: any[] }) {
    const sliderRef = useRef<HTMLDivElement>(null);
    const countdownRef = useRef<HTMLDivElement>(null);

    const localProducts = [
        {
            id: 1,
            name: "Full Sleeve Hoodie Jacket",
            price: 40.00,
            oldPrice: 48.00,
            discount: 75,
            isNew: true,
            rating: 4,
            reviewsCount: 20,
            img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_1.png"
        },
        {
            id: 2,
            name: "Kids cotton Combo Set",
            price: 16.00,
            oldPrice: 18.00,
            discount: 11,
            isNew: false,
            rating: 5,
            reviewsCount: 17,
            img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_2.png"
        },
        {
            id: 3,
            name: "Women's Western Party Dress",
            price: 50.00,
            oldPrice: 40.00,
            discount: 15,
            isNew: false,
            rating: 4,
            reviewsCount: 22,
            img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_3.png"
        },
        {
            id: 4,
            name: "Comfortable Sports Sneakers",
            price: 75.00,
            oldPrice: 85.00,
            discount: 12,
            isNew: true,
            rating: 3.5,
            reviewsCount: 58,
            img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_4.png"
        },
        {
            id: 5,
            name: "Kid's Western Party Dress",
            price: 49.00,
            oldPrice: 39.00,
            discount: 49,
            isNew: false,
            rating: 3.5,
            reviewsCount: 44,
            img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_5.png"
        },
        {
            id: 6,
            name: "Women's Designer Tops",
            price: 55.00,
            oldPrice: null,
            discount: null,
            isNew: true,
            rating: 4.5,
            reviewsCount: 19,
            img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_6.png"
        }
    ];

    const displayProducts = products.length > 0 ? products : localProducts;
    const sliderKey = displayProducts.map(p => p.id).join('-');

    const [timeLeft, setTimeLeft] = useState({
        days: 14,
        hours: 2,
        minutes: 22,
        seconds: 16
    });

    useEffect(() => {
        // Target date 15 days in future
        const target = new Date();
        target.setDate(target.getDate() + 15);
        target.setHours(23, 59, 59, 999);

        const updateCountdown = () => {
            const now = new Date().getTime();
            const diff = target.getTime() - now;

            if (diff <= 0) {
                setTimeLeft({ days: 0, hours: 0, minutes: 0, seconds: 0 });
                return;
            }

            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

            setTimeLeft({ days, hours, minutes, seconds });
        };

        updateCountdown();
        const interval = setInterval(updateCountdown, 1000);

        return () => clearInterval(interval);
    }, []);

    useEffect(() => {
        // @ts-ignore
        const $ = window.$;
        let timer: any;

        // Initialize Slick Slider
        if ($ && sliderRef.current) {
            timer = setTimeout(() => {
                try {
                    // @ts-ignore
                    if ($(sliderRef.current).hasClass('slick-initialized')) {
                        // @ts-ignore
                        $(sliderRef.current).slick('unslick');
                    }
                    // @ts-ignore
                    $(sliderRef.current).slick({
                        slidesToShow: 5,
                        slidesToScroll: 1,
                        autoplay: true,
                        autoplaySpeed: 2500,
                        dots: false,
                        arrows: true,
                        nextArrow: '<i class="nextArrow" style="font-style: normal; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></i>',
                        prevArrow: '<i class="prevArrow" style="font-style: normal; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg></i>',
                        responsive: [
                            { breakpoint: 1600, settings: { slidesToShow: 4 } },
                            { breakpoint: 1400, settings: { slidesToShow: 4 } },
                            { breakpoint: 1200, settings: { slidesToShow: 3 } },
                            { breakpoint: 992, settings: { slidesToShow: 3 } },
                            { breakpoint: 768, settings: { slidesToShow: 2, arrows: false } },
                            { breakpoint: 576, settings: { slidesToShow: 2 } }
                        ]
                    });
                } catch (err) {
                    console.error("Error initializing slick on flash sale slider:", err);
                }
            }, 100);
        }

        return () => {
            clearTimeout(timer);
        };
    }, [sliderKey]);


    return (
        <section className="flash_sell_2 flash_sell mt_95">
            <div className="container">
                <div className="row align-items-center">
                    <div className="col-xxl-6 col-md-3 col-xl-4">
                        <div className="section_heading_2 section_heading">
                            <h3><span>Flash</span> Sell</h3>
                        </div>
                    </div>
                    <div className="col-xxl-6 col-md-9 col-xl-8">
                        <div className="d-flex flex-wrap justify-content-end align-items-center gap-3">
                            <div className="simply-countdown simply-countdown-one">
                                <div className="simply-section simply-days-section">
                                    <div>
                                        <span className="simply-amount">{timeLeft.days}</span>
                                        <span className="simply-word">Days</span>
                                    </div>
                                </div>
                                <div className="simply-section simply-hours-section">
                                    <div>
                                        <span className="simply-amount">{timeLeft.hours}</span>
                                        <span className="simply-word">Hours</span>
                                    </div>
                                </div>
                                <div className="simply-section simply-minutes-section">
                                    <div>
                                        <span className="simply-amount">{timeLeft.minutes}</span>
                                        <span className="simply-word">Minutes</span>
                                    </div>
                                </div>
                                <div className="simply-section simply-seconds-section">
                                    <div>
                                        <span className="simply-amount">{timeLeft.seconds}</span>
                                        <span className="simply-word">Seconds</span>
                                    </div>
                                </div>
                            </div>
                            <div className="view_all_btn_area">
                                <Link className="view_all_btn" to="/flash-deals">View all</Link>
                            </div>
                        </div>
                    </div>
                </div>
                <div key={sliderKey} ref={sliderRef} className="row mt_25 flash_sell_2_slider">
                    {displayProducts.map((prod) => (
                        <div key={prod.id} className="col-xl-1-5 wow fadeInUp">
                            <ProductCard {...prod} />
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}


