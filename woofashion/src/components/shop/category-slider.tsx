import { useEffect, useRef, useState } from 'react';
import { Link } from 'react-router-dom';

export default function CategorySlider() {
    const sliderRef = useRef<HTMLDivElement>(null);
    const [mountKey, setMountKey] = useState(0);

    useEffect(() => {
        // @ts-ignore
        const $ = window.$;
        let timer: any;

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
                        slidesToShow: 8,
                        slidesToScroll: 1,
                        autoplay: true,
                        autoplaySpeed: 2500,
                        dots: false,
                        arrows: true,
                        nextArrow: '<i class="nextArrow" style="font-style: normal; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></i>',
                        prevArrow: '<i class="prevArrow" style="font-style: normal; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg></i>',
                        responsive: [
                            { breakpoint: 1600, settings: { slidesToShow: 7 } },
                            { breakpoint: 1400, settings: { slidesToShow: 6 } },
                            { breakpoint: 1200, settings: { slidesToShow: 5 } },
                            { breakpoint: 992, settings: { slidesToShow: 4 } },
                            { breakpoint: 768, settings: { slidesToShow: 3 } },
                            { breakpoint: 576, settings: { slidesToShow: 2 } }
                        ]
                    });
                } catch (err) {
                    console.error("Error initializing slick on category slider:", err);
                }
            }, 100);
        }

        return () => {
            clearTimeout(timer);
        };
    }, []);

    const categories = [
        { name: "Men's Fashion", img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/category_img_2.png" },
        { name: "women's Fashion", img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/category_img_3.png" },
        { name: "kids fashion", img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/category_img_1.png" },
        { name: "kids fashion", img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/category_img_4.png" },
        { name: "kids fashion", img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/category_img_5.png" },
        { name: "kids fashion", img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/category_img_6.png" },
        { name: "kids fashion", img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/category_img_7.png" },
        { name: "Men's Fashion", img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/category_img_2.png" },
        { name: "women's Fashion", img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/category_img_3.png" },
        { name: "kids fashion", img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/category_img_1.png" }
    ];

    return (
        <section className="category category_2 mt_55">
            <div className="container">
                <div key={mountKey} ref={sliderRef} className="row category_2_slider">
                    {categories.map((cat, idx) => {
                        const getCategorySlug = (name: string) => {
                            const n = name.toLowerCase();
                            if (n.includes('women')) return 'western-wear';
                            if (n.includes('kids') || n.includes('kid')) return 'western-wear';
                            return n.replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
                        };
                        return (
                            <div key={idx} className="col-2 wow fadeInUp">
                                <Link to={`/shop?category=${getCategorySlug(cat.name)}`} className="category_item">
                                    <div className="img">
                                        <img src={cat.img} alt={cat.name} className="img-fluid w-100" />
                                    </div>
                                    <h3>{cat.name}</h3>
                                </Link>
                            </div>
                        );
                    })}
                </div>
            </div>
        </section>
    );
}


