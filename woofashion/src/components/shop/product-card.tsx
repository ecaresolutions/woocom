import { Link } from 'react-router-dom';
import { Star, StarHalf } from 'lucide-react';
import { formatPrice } from '@/lib/currency';

export interface ProductCardProps {
    id: number;
    name: string;
    slug?: string;
    price: number;
    oldPrice?: number;
    discount?: number;
    isNew?: boolean;
    rating: number;
    reviewsCount: number;
    img: string;
    colors?: string[] | Array<{ name: string; hex: string }>;
}

export default function ProductCard({
    id,
    name,
    slug,
    price,
    oldPrice,
    discount,
    isNew,
    rating,
    reviewsCount,
    img,
    colors = ['#DB4437', '#638C34', '#1C58F2', '#ffa500']
}: ProductCardProps) {
    const productSlug = slug || name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
    const colorHexList = colors.map(c => typeof c === 'string' ? c : c.hex);

    
    const renderStars = () => {
        const stars = [];
        const fullStars = Math.floor(rating);
        const hasHalf = rating % 1 !== 0;

        for (let i = 1; i <= 5; i++) {
            if (i <= fullStars) {
                stars.push(<Star key={i} size={12} fill="#f59e0b" color="#f59e0b" style={{ display: 'inline-block', marginRight: '2px' }} />);
            } else if (i === fullStars + 1 && hasHalf) {
                stars.push(<StarHalf key={i} size={12} fill="#f59e0b" color="#f59e0b" style={{ display: 'inline-block', marginRight: '2px' }} />);
            } else {
                stars.push(<Star key={i} size={12} fill="none" color="#ccc" style={{ display: 'inline-block', marginRight: '2px' }} />);
            }
        }
        return stars;
    };

    const getSlug = (str: string) => {
        return str
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)+/g, '');
    };

    return (
        <div className="product_item_2 product_item">
            <div className="product_img">
                <Link to={`/shop/product/${productSlug}`} className="d-block">
                    <img src={img} alt={name} className="img-fluid w-100" />
                </Link>
                <ul className="discount_list">
                    {discount && <li className="discount"> <b>-</b> {discount}%</li>}
                    {isNew && <li className="new"> new</li>}
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
                    <li>
                        <a href="#">
                            <img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/cart_icon_white.svg'} alt="Cart" className="img-fluid" />
                        </a>
                    </li>
                </ul>
            </div>
            <div className="product_text">
                <Link className="title" to={`/shop/product/${productSlug}`}>
                    {name}
                </Link>
                <p className="price" style={{ margin: 0, padding: 0 }}>
                    {formatPrice(price)} 
                    {oldPrice && <del className="ms-1">{formatPrice(oldPrice)}</del>}
                </p>
                <ul className="color" style={{ marginTop: '5px', padding: 0, marginBottom: 0 }}>
                    {colorHexList.map((color, idx) => (
                        <li 
                            key={idx} 
                            className={idx === 0 ? 'active' : ''} 
                            style={{ background: color }}
                        ></li>
                    ))}
                </ul>
            </div>
        </div>
    );
}



