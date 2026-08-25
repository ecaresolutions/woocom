import { Link } from 'react-router-dom';
import ProductCard from './product-card';

export default function NewArrivals({ products = [] }: { products?: any[] }) {
    const localProducts = [
        { id: 18, name: "Full Sleeve Hoodie Jacket", price: 88.00, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_18.png", colors: ["#DB4437", "#638C34", "#1C58F2", "#ffa500"], isNew: true, rating: 4.5, reviewsCount: 12 },
        { id: 19, name: "Men's premium formal shirt", price: 46.00, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_19.png", colors: ["#DB4437", "#638C34", "#ffa500"], isNew: true, rating: 4.5, reviewsCount: 15 },
        { id: 20, name: "cherry fabric western tops", price: 46.00, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_20.png", colors: ["#DB4437", "#638C34", "#1C58F2", "#ffa500"], isNew: true, rating: 4.5, reviewsCount: 10 },
        { id: 4, name: "Comfortable Sports Sneakers", price: 75.00, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_4.png", colors: ["#DB4437", "#638C34"], isNew: true, rating: 4.5, reviewsCount: 18 },
        { id: 23, name: "Kid's dresses for summer", price: 70.00, img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/product_23.png", colors: ["#DB4437", "#638C34", "#1C58F2", "#ffa500"], isNew: true, rating: 4.5, reviewsCount: 11 }
    ];

    const displayProducts = products.length > 0 ? products : localProducts;

    return (
        <section className="new_arrival_2 mt_95">
            <div className="container">
                <div className="row">
                    <div className="col-xl-6 col-sm-9">
                        <div className="section_heading_2 section_heading">
                            <h3>Our <span>New</span> arrival Products</h3>
                        </div>
                    </div>
                    <div className="col-xl-6 col-sm-3">
                        <div className="view_all_btn_area">
                            <Link className="view_all_btn" to="/shop">View all</Link>
                        </div>
                    </div>
                </div>
                <div className="row mt_15 justify-content-center">
                    {displayProducts.map((product) => (
                        <div key={product.id} className="col-xl-1-5-static col-6 col-md-4 col-sm-6 mb-4 wow fadeInUp">
                            <ProductCard {...product} />
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}


