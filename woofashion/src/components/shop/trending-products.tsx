import { useEffect, useRef, useState } from 'react';
import ProductCard from './product-card';

export default function TrendingProducts({ products = [] }: { products?: any[] }) {
    const tabsRef = useRef<HTMLDivElement>(null);
    const [mountKey, setMountKey] = useState(0);

    useEffect(() => {
        // Trigger a re-mount on client to clear old tab nodes
        setMountKey(prev => prev + 1);
    }, []);

    useEffect(() => {
        if (mountKey === 0) return;

        // @ts-ignore
        const $ = window.$;
        let timer: any;

        if ($ && tabsRef.current) {
            timer = setTimeout(() => {
                try {
                    // @ts-ignore
                    $(tabsRef.current).pwstabs({
                        effect: 'slidedown',
                        defaultTab: 1,
                    });
                } catch (err) {
                    console.error("Error initializing pwstabs:", err);
                }
            }, 50);
        }

        return () => {
            clearTimeout(timer);
        };
    }, [mountKey]);

    const localWestern = [
        { id: 7, name: "Denim 2 Quarter Pant", price: 40.00, isNew: true, rating: 3.5, reviewsCount: 20, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_7.png" },
        { id: 9, name: "Men's Denim combo set", price: 47.00, oldPrice: 50.00, discount: 45, rating: 4, reviewsCount: 17, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_9.png" },
        { id: 10, name: "Women's Elegant Party Dress", price: 43.00, rating: 5, reviewsCount: 22, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_10.png" },
        { id: 5, name: "Kid's Western Party Dress", price: 49.00, oldPrice: 39.00, discount: 49, rating: 3.5, reviewsCount: 44, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_5.png" },
        { id: 13, name: "Sharee Petticoat For Women", price: 28.00, oldPrice: 35.00, discount: 20, rating: 3.7, reviewsCount: 9, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_13.png" },
        { id: 1, name: "Full Sleeve Hoodie Jacket", price: 20.00, rating: 4, reviewsCount: 20, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_1.png" },
        { id: 2, name: "Kids cotton Combo Set", price: 16.00, rating: 4.5, reviewsCount: 15, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_2.png" },
        { id: 3, name: "Women's Western Party Dress", price: 50.00, rating: 5, reviewsCount: 22, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_3.png" },
        { id: 6, name: "Women's Designer Tops", price: 55.00, rating: 4.2, reviewsCount: 19, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_6.png" },
        { id: 8, name: "Kids Summer Cotton Combo", price: 38.00, rating: 4.4, reviewsCount: 23, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_8.png" }
    ];

    const localTops = [
        { id: 1, name: "Full Sleeve Hoodie Jacket", price: 20.00, oldPrice: 22.00, discount: 9, isNew: true, rating: 4.0, reviewsCount: 20, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_1.png" },
        { id: 2, name: "Kids cotton Combo Set", price: 16.00, oldPrice: 18.00, discount: 11, rating: 4.5, reviewsCount: 15, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_2.png" },
        { id: 3, name: "Women's Western Party Dress", price: 50.00, oldPrice: 60.00, discount: 16, rating: 5.0, reviewsCount: 22, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_3.png" },
        { id: 6, name: "Women's Designer Tops", price: 55.00, rating: 4.2, reviewsCount: 19, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_6.png" },
        { id: 8, name: "Kids Summer Cotton Combo", price: 38.00, rating: 4.4, reviewsCount: 23, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_8.png" },
        { id: 7, name: "Denim 2 Quarter Pant", price: 40.00, rating: 3.5, reviewsCount: 20, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_7.png" },
        { id: 9, name: "Men's Denim combo set", price: 47.00, rating: 4, reviewsCount: 17, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_9.png" },
        { id: 10, name: "Women's Elegant Party Dress", price: 43.00, rating: 5, reviewsCount: 22, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_10.png" },
        { id: 5, name: "Kid's Western Party Dress", price: 49.00, rating: 3.5, reviewsCount: 44, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_5.png" },
        { id: 13, name: "Sharee Petticoat For Women", price: 28.00, rating: 3.7, reviewsCount: 9, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_13.png" }
    ];

    const localBags = [
        { id: 11, name: "Classic Shoulder Bag", price: 40.00, oldPrice: 48.00, discount: 16, isNew: true, rating: 3.5, reviewsCount: 20, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_11.png" },
        { id: 12, name: "Premium Hand Bag", price: 120.00, oldPrice: 140.00, discount: 14, rating: 4.0, reviewsCount: 17, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_12.png" },
        { id: 14, name: "Kids Cotton Combo Bag", price: 45.00, rating: 4.5, reviewsCount: 18, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_14.png" },
        { id: 16, name: "Kid's Western Party Bag", price: 35.00, rating: 3.5, reviewsCount: 14, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_16.png" },
        { id: 1, name: "Full Sleeve Hoodie Jacket", price: 20.00, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_1.png", rating: 4, reviewsCount: 20 },
        { id: 2, name: "Kids cotton Combo Set", price: 16.00, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_2.png", rating: 4.5, reviewsCount: 15 },
        { id: 3, name: "Women's Western Party Dress", price: 50.00, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_3.png", rating: 5, reviewsCount: 22 },
        { id: 5, name: "Kid's Western Party Dress", price: 49.00, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_5.png", rating: 3.5, reviewsCount: 44 },
        { id: 6, name: "Women's Designer Tops", price: 55.00, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_6.png", rating: 4.2, reviewsCount: 19 },
        { id: 7, name: "Denim 2 Quarter Pant", price: 40.00, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_7.png", rating: 3.5, reviewsCount: 20 }
    ];

    const localShoes = [
        { id: 21, name: "Kid's Western Shoes", price: 40.00, oldPrice: 48.00, discount: 16, isNew: true, rating: 3.5, reviewsCount: 20, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_21.png" },
        { id: 22, name: "Denim casual Shoes for men", price: 120.00, oldPrice: 135.00, discount: 11, rating: 4.0, reviewsCount: 17, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_22.png" },
        { id: 23, name: "Kid's dresses shoes for summer", price: 70.00, rating: 4.5, reviewsCount: 22, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_23.png" },
        { id: 24, name: "women's long full Shoes", price: 65.00, rating: 4.6, reviewsCount: 30, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_24.png" },
        { id: 25, name: "Men's premium check Shoes", price: 32.00, rating: 4.1, reviewsCount: 18, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_25.png" },
        { id: 26, name: "Women's designer Shoes", price: 28.00, rating: 4.0, reviewsCount: 15, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_26.png" },
        { id: 1, name: "Full Sleeve Hoodie Jacket", price: 20.00, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_1.png", rating: 4, reviewsCount: 20 },
        { id: 2, name: "Kids cotton Combo Set", price: 16.00, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_2.png", rating: 4.5, reviewsCount: 15 },
        { id: 3, name: "Women's Western Party Dress", price: 50.00, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_3.png", rating: 5, reviewsCount: 22 },
        { id: 5, name: "Kid's Western Party Dress", price: 49.00, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_5.png", rating: 3.5, reviewsCount: 44 }
    ];

    // Helper to get or pad products to at least 10 items
    const getProductsForTab = (categoryNames: string[], offset = 0) => {
        if (products.length === 0) return [];
        
        let filtered = products.filter(p => 
            categoryNames.some(cat => p.category.toLowerCase() === cat.toLowerCase())
        );
        
        if (filtered.length < 10 + offset) {
            const ids = new Set(filtered.map(p => p.id));
            for (const p of products) {
                if (!ids.has(p.id)) {
                    filtered.push(p);
                    ids.add(p.id);
                }
                if (filtered.length >= 10 + offset) break;
            }
        }
        
        return filtered.slice(offset, 10 + offset);
    };

    const westernProducts = products.length > 0
        ? getProductsForTab(['western wear', "men's fashion"], 0)
        : localWestern;

    const topsProducts = products.length > 0
        ? getProductsForTab(['western wear', 'sport wear'], 2)
        : localTops;

    const bagsProducts = products.length > 0
        ? getProductsForTab(['beauty care'], 0)
        : localBags;

    const shoesProducts = products.length > 0
        ? getProductsForTab(['fashion jewellery', 'sport wear'], 0)
        : localShoes;

    return (
        <section className="trending_product_2 mt_90">
            <div className="container">
                <div className="row">
                    <div className="col-xl-6">
                        <div className="section_heading_2 section_heading mb_15">
                            <h3><span>Trending</span> Products</h3>
                        </div>
                    </div>
                </div>
                <div className="row wow fadeInUp">
                    <div className="col-12">
                        <div key={mountKey} ref={tabsRef} className="product_tabs">
                            <div data-pws-tab="tab111" data-pws-tab-name="western">
                                <div className="row">
                                    {westernProducts.map((prod) => (
                                        <div key={prod.id} className="col-xl-1-5-static col-6 col-md-4 col-sm-6 mb-4">
                                            <ProductCard {...prod} />
                                        </div>
                                    ))}
                                </div>
                            </div>
                            <div data-pws-tab="tab222" data-pws-tab-name="tops">
                                <div className="row">
                                    {topsProducts.map((prod) => (
                                        <div key={prod.id} className="col-xl-1-5-static col-6 col-md-4 col-sm-6 mb-4">
                                            <ProductCard {...prod} />
                                        </div>
                                    ))}
                                </div>
                            </div>
                            <div data-pws-tab="tab333" data-pws-tab-name="bags">
                                <div className="row">
                                    {bagsProducts.map((prod) => (
                                        <div key={prod.id} className="col-xl-1-5-static col-6 col-md-4 col-sm-6 mb-4">
                                            <ProductCard {...prod} />
                                        </div>
                                    ))}
                                </div>
                            </div>
                            <div data-pws-tab="tab444" data-pws-tab-name="shoes">
                                <div className="row">
                                    {shoesProducts.map((prod) => (
                                        <div key={prod.id} className="col-xl-1-5-static col-6 col-md-4 col-sm-6 mb-4">
                                            <ProductCard {...prod} />
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}

