import { Link, useNavigate } from 'react-router-dom';
import { useState, useEffect } from 'react';
import { Shirt, Menu, Search, PhoneCall, ChevronDown, ChevronRight, ArrowRight, User, Settings, LogOut, X, Truck, ShoppingBag } from 'lucide-react';
import { formatPrice } from '@/lib/currency';
import { useThemeSettings, getThemeSettings } from '@/lib/theme-settings';

interface CartItem {
    id: number;
    name: string;
    price: number;
    img: string;
    quantity: number;
    color: string;
    size: string;
}

export default function Header({ isLoaded = true }: { isLoaded?: boolean }) {
    const navigate = useNavigate();
    const settings = useThemeSettings();
    const auth = { user: null }; // Mock auth for now
    const [showCategory, setShowCategory] = useState(false);
    const [showUserMenu, setShowUserMenu] = useState(false);
    const [cartItems, setCartItems] = useState<CartItem[]>([]);

    const loadCart = () => {
        const items = localStorage.getItem('cart');
        if (items) {
            try {
                setCartItems(JSON.parse(items));
            } catch (e) {
                setCartItems([]);
            }
        } else {
            setCartItems([]);
        }
    };

    const removeFromCart = (index: number) => {
        const newCart = [...cartItems];
        newCart.splice(index, 1);
        localStorage.setItem('cart', JSON.stringify(newCart));
        setCartItems(newCart);
        window.dispatchEvent(new Event('cart-updated'));
    };

    const subTotal = cartItems.reduce((acc, item) => acc + (item.price * item.quantity), 0);

    useEffect(() => {
        loadCart();
        window.addEventListener('cart-updated', loadCart);
        return () => {
            window.removeEventListener('cart-updated', loadCart);
        };
    }, []);

    useEffect(() => {
        // @ts-ignore
        const $ = window.$;
        if ($) {
            // @ts-ignore
            $('.select_2').select2();
            // @ts-ignore
            $('.select_js').niceSelect();
        }
    }, []);

    return (
        <>
            {/*=========================
                HEADER START
            ==========================*/}
            <header className="header_2 d-flex align-items-center" style={{ position: 'relative', display: 'flex', alignItems: 'center' }}>
                {/* SKELETON OVERLAY FOR HEADER TOP BAR */}
                {!isLoaded && (
                    <div style={{
                        position: 'absolute',
                        top: 0,
                        left: 0,
                        right: 0,
                        bottom: 0,
                        background: '#fff',
                        zIndex: 999,
                        display: 'flex',
                        alignItems: 'center',
                        pointerEvents: 'none',
                        borderBottom: '1px solid #f1f5f9'
                    }}>
                        <div className="container">
                            <div className="row align-items-center" style={{ height: '80px' }}>
                                <div className="col-lg-2 col-4">
                                    <div className="skeleton-box" style={{ height: '32px', width: '130px' }}></div>
                                </div>
                                <div className="col-xxl-6 col-xl-5 col-lg-5 d-none d-lg-block">
                                    <div className="skeleton-box" style={{ height: '45px', width: '100%', borderRadius: '30px' }}></div>
                                </div>
                                <div className="col-xxl-4 col-xl-5 col-lg-5 col-8 d-flex justify-content-end gap-3 align-items-center">
                                    <div className="skeleton-box d-none d-sm-block" style={{ height: '35px', width: '120px' }}></div>
                                    <div className="skeleton-box" style={{ height: '35px', width: '80px' }}></div>
                                    <div className="skeleton-box" style={{ height: '35px', width: '80px' }}></div>
                                </div>
                            </div>
                        </div>
                    </div>
                )}

                <div className="container">
                    <div className="row align-items-center">
                        <div className="col-xxl-3 col-xl-3 col-lg-3 col-6">
                            <div className="header_logo_area d-flex align-items-center justify-content-between w-100" style={{ minHeight: '60px' }}>
                                <Link to="/" className="header_logo d-flex align-items-center" style={{ textDecoration: 'none' }}>
                                    {settings.general?.logo_url ? (
                                        <img 
                                            src={settings.general?.logo_url} 
                                            alt={settings.general?.brand_name || 'WoocomFashion'} 
                                            className="header_store_logo"
                                            style={{ 
                                                height: settings.general?.logo_height ? `${settings.general.logo_height}px` : '62px', 
                                                maxHeight: '90px', 
                                                maxWidth: '280px', 
                                                width: 'auto',
                                                objectFit: 'contain', 
                                                display: 'block' 
                                            }} 
                                        />
                                    ) : (
                                        <div style={{ fontSize: '24px', fontWeight: '800', color: '#0f172a', letterSpacing: '0.5px', display: 'flex', alignItems: 'center', gap: '10px' }}>
                                            <span style={{ backgroundColor: settings.general?.primary_color || '#f59e0b', color: '#ffffff', borderRadius: '50%', width: '40px', height: '40px', display: 'inline-flex', alignItems: 'center', justifyContent: 'center', fontSize: '18px', flexShrink: 0 }}>
                                                <i style={{ display: 'inline-flex', alignItems: 'center', justifyContent: 'center' }}><Shirt size={20} /></i>
                                            </span>
                                            {settings.general?.brand_name || 'WoocomFashion'}
                                        </div>
                                    )}
                                </Link>
                                <div className="mobile_menu_icon d-block d-lg-none" data-bs-toggle="offcanvas"
                                    data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptions"
                                    style={{ position: 'relative', top: 'auto', right: 'auto', margin: '0', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                                    <span className="mobile_menu_icon"><i style={{ display: 'inline-flex', alignItems: 'center', justifyContent: 'center' }}><Menu className="menu_icon_bar" size={20} /></i></span>
                                </div>
                            </div>
                        </div>
                        <div className="col-xxl-5 col-xl-5 col-lg-5 d-none d-lg-block">
                            <form action="#">
                                <select className="select_2">
                                    <option>All Categories</option>
                                    <option>Fashion</option>
                                    <option>Elentronics</option>
                                    <option>Fashion & Beauty</option>
                                    <option>Jewelry</option>
                                    <option>Grocery</option>
                                </select>
                                <div className="input">
                                    <input type="text" placeholder="Search your product..." />
                                    <button type="submit" style={{ display: 'inline-flex', alignItems: 'center', justifyContent: 'center' }}><i style={{ display: 'inline-flex', alignItems: 'center', justifyContent: 'center', width: '100%', height: '100%' }}><Search size={16} /></i></button>
                                </div>
                            </form>
                        </div>
                        <div className="col-xxl-4 col-xl-4 col-lg-4 col-6 d-flex justify-content-end align-items-center">
                            <div className="header_support_user d-flex flex-wrap align-items-center justify-content-end gap-3" style={{ justifyContent: 'flex-end', marginLeft: 'auto', marginRight: 0 }}>
                                <div className="header_support me-2">
                                    <span className="icon" style={{ display: 'inline-flex', alignItems: 'center', justifyContent: 'center' }}>
                                        <i style={{ display: 'inline-flex', alignItems: 'center', justifyContent: 'center', width: '100%', height: '100%' }}><PhoneCall size={16} /></i>
                                    </span>
                                    <h3>
                                        Hotline:
                                        <a href={`tel:${settings.header?.hotline_phone || '+880 9612-888999'}`}>
                                            <span>{settings.header?.hotline_phone || '+880 9612-888999'}</span>
                                        </a>
                                    </h3>
                                </div>
                                {settings.header?.enable_track_order !== 'no' && (
                                    <div className="track_order_area d-flex align-items-center ps-4" style={{ borderLeft: '1px solid #e2e8f0', height: '40px', lineHeight: 'normal' }}>
                                        <Link to="/track-order" className="header_track_btn">
                                            <Truck size={14} />
                                            <span>Track Order</span>
                                        </Link>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            {/*=========================
                HEADER END
            ==========================*/}


            {/*=========================
                MENU 2 START
            ==========================*/}
            <nav className="main_menu_2 main_menu d-none d-lg-block" style={{ position: 'relative' }}>
                {/* SKELETON OVERLAY FOR MAIN MENU BAR */}
                {!isLoaded && (
                    <div style={{
                        position: 'absolute',
                        top: 0,
                        left: 0,
                        right: 0,
                        bottom: 0,
                        background: '#fff',
                        zIndex: 999,
                        display: 'flex',
                        alignItems: 'center',
                        pointerEvents: 'none',
                        borderBottom: '1px solid #f1f5f9'
                    }}>
                        <div className="container">
                            <div className="d-flex align-items-center justify-content-between" style={{ height: '50px' }}>
                                <div className="skeleton-box" style={{ height: '35px', width: '200px', borderRadius: '4px 4px 0 0' }}></div>
                                <div className="d-flex gap-4">
                                    {[...Array(5)].map((_, i) => (
                                        <div key={i} className="skeleton-box" style={{ height: '15px', width: '60px' }}></div>
                                    ))}
                                </div>
                                <div className="skeleton-box" style={{ height: '30px', width: '150px' }}></div>
                            </div>
                        </div>
                    </div>
                )}

                <div className="container">
                    <div className="row">
                        <div className="col-12 d-flex flex-wrap">
                            <div className="main_menu_area">
                                <div className={`menu_category_area ${showCategory ? 'show_category' : ''}`}>
                                    <div
                                        className={`menu_category_bar ${showCategory ? 'active' : ''}`}
                                        onClick={() => setShowCategory(!showCategory)}
                                        style={{ cursor: 'pointer' }}
                                    >
                                        <p>
                                            <span>
                                                <img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/bar_icon_white.svg'} alt="category icon" />
                                            </span>
                                            Browse Categories
                                        </p>
                                        <i style={{ display: 'inline-flex', alignItems: 'center' }}><ChevronDown size={14} className="ms-1" /></i>
                                    </div>
                                    <ul className="menu_cat_item">
                                        <li>
                                            <Link to="/shop?category=men-s-fashion">
                                                <span>
                                                    <img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/category_list_icon_1.png'} alt="category" />
                                                </span>
                                                Men’s Fashion
                                                <ChevronRight size={14} style={{ position: 'absolute', right: '20px', top: '50%', transform: 'translateY(-50%)', opacity: 0.8 }} />
                                            </Link>
                                            <ul className="menu_cat_droapdown">
                                                <li><Link to="/shop" className="d-flex align-items-center">shirts <i style={{ display: 'inline-flex', alignItems: 'center', width: '100%' }}><ChevronRight size={12} className="ms-auto" /></i></Link>
                                                    <ul className="sub_category">
                                                        <li><Link to="/shop">Casual Shirts</Link> </li>
                                                        <li><Link to="/shop">Formal Shirts</Link></li>
                                                        <li><Link to="/shop">Denim Shirts</Link></li>
                                                    </ul>
                                                </li>
                                                <li><Link to="/shop" className="d-flex align-items-center">pant <i style={{ display: 'inline-flex', alignItems: 'center', width: '100%' }}><ChevronRight size={12} className="ms-auto" /></i></Link>
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
                                            <Link to="/shop?category=western-wear">
                                                <span>
                                                    <img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/category_list_icon_2.png'} alt="category" />
                                                </span>
                                                Women’s Fashion
                                                <ChevronRight size={14} style={{ position: 'absolute', right: '20px', top: '50%', transform: 'translateY(-50%)', opacity: 0.8 }} />
                                            </Link>
                                            <ul className="menu_cat_droapdown">
                                                <li><Link to="/shop">sharee</Link></li>
                                                <li><Link to="/shop" className="d-flex align-items-center">Shirts <i style={{ display: 'inline-flex', alignItems: 'center', width: '100%' }}><ChevronRight size={12} className="ms-auto" /></i></Link>
                                                    <ul className="sub_category">
                                                        <li><Link to="/shop">Full Sleeves Printed</Link> </li>
                                                        <li><Link to="/shop">Full Sleeves Solid</Link></li>
                                                        <li><Link to="/shop">Half Sleeves Solid</Link></li>
                                                    </ul>
                                                </li>
                                                <li><Link to="/shop" className="d-flex align-items-center">T-Shirts <i style={{ display: 'inline-flex', alignItems: 'center', width: '100%' }}><ChevronRight size={12} className="ms-auto" /></i></Link>
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
                                            <Link to="/shop?category=western-wear">
                                                <span>
                                                    <img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/category_list_icon_3.png'} alt="category" />
                                                </span>
                                                KId’s Fashion
                                                <ChevronRight size={14} style={{ position: 'absolute', right: '20px', top: '50%', transform: 'translateY(-50%)', opacity: 0.8 }} />
                                            </Link>
                                            <ul className="menu_cat_droapdown">
                                                <li><Link to="/shop">Boys’ Fashion</Link></li>
                                                <li><Link to="/shop">Girls’ Fashion</Link></li>
                                                <li><Link to="/shop" className="d-flex align-items-center">Newborn Essentials <i style={{ display: 'inline-flex', alignItems: 'center', width: '100%' }}><ChevronRight size={12} className="ms-auto" /></i></Link>
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
                                            <Link to="/shop?category=men-s-fashion">
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
                                            <Link to="/shop?category=western-wear">
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
                                            <Link to="/shop?category=sport-wear">
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
                                            <Link to="/shop?category=sport-wear">
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
                                            <Link to="/shop?category=fashion-jewellery">
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
                                            <Link to="/shop?category=beauty-care">
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
                                            <Link to="/category" className="d-flex align-items-center">View All Categories <i style={{ display: 'inline-flex', alignItems: 'center' }}><ArrowRight size={14} className="ms-1" /></i></Link>
                                        </li>
                                    </ul>
                                </div>
                                <ul className="menu_item">
                                    <li>
                                        <Link className="active" to="/">Home</Link>
                                    </li>
                                    <li>
                                        <Link to="/shop">shop</Link>
                                    </li>
                                    <li>
                                        <Link to="/about">About Us</Link>
                                    </li>
                                    <li>
                                        <Link to="/blog">blog</Link>
                                    </li>
                                    <li>
                                        <Link to="/contact">contact</Link>
                                    </li>
                                </ul>
                                <ul className="menu_icon">
                                    <li>
                                        <a className="header_cart_link" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight" style={{ cursor: 'pointer', display: 'inline-flex', alignItems: 'center', justifyContent: 'center' }}>
                                            <b style={{ display: 'inline-flex', alignItems: 'center', justifyContent: 'center' }}>
                                                <ShoppingBag size={27} strokeWidth={2} style={{ color: 'var(--colorBlack)' }} />
                                            </b>
                                            <span>{cartItems.reduce((acc, item) => acc + item.quantity, 0)}</span>
                                        </a>
                                    </li>
                                    <li className="position-relative" onMouseEnter={() => setShowUserMenu(true)} onMouseLeave={() => setShowUserMenu(false)}>
                                        {auth?.user ? (
                                            <>
                                                <Link className="user d-flex align-items-center text-dark text-decoration-none" to="/dashboard">
                                                    <b>
                                                        <img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/user_icon_black.svg'} alt="User" className="img-fluid" />
                                                    </b>
                                                    <h5> {auth.user.name}</h5>
                                                </Link>
                                                {showUserMenu && (
                                                    <ul className="user_dropdown d-block position-absolute bg-white shadow-sm py-2 px-3 rounded-2" style={{ zIndex: 1000, top: '100%', right: 0, minWidth: '160px', listStyle: 'none' }}>
                                                        <li className="my-2">
                                                            <Link to="/dashboard" className="text-decoration-none text-dark d-flex align-items-center">
                                                                <i style={{ display: 'inline-flex', alignItems: 'center' }}><User size={14} className="me-2" /></i> Dashboard
                                                            </Link>
                                                        </li>
                                                        <li className="my-2">
                                                            <Link to="/settings" className="text-decoration-none text-dark d-flex align-items-center">
                                                                <i style={{ display: 'inline-flex', alignItems: 'center' }}><Settings size={14} className="me-2" /></i> Settings
                                                            </Link>
                                                        </li>
                                                        <li className="border-top my-2 pt-2">
                                                            <Link to="/logout" method="post" as="button" className="btn btn-sm btn-link text-danger text-decoration-none p-0 d-flex align-items-center">
                                                                <i style={{ display: 'inline-flex', alignItems: 'center' }}><LogOut size={14} className="me-2" /></i> Logout
                                                            </Link>
                                                        </li>
                                                    </ul>
                                                )}
                                            </>
                                        ) : (
                                            <Link className="user d-flex align-items-center text-dark text-decoration-none" to="/login">
                                                <b>
                                                    <img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/user_icon_black.svg'} alt="User" className="img-fluid" />
                                                </b>
                                                <h5> Login</h5>
                                            </Link>
                                        )}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            {/*=========================
                CART DRAWER
            ==========================*/}
            <div className="mini_cart">
                <div className="offcanvas offcanvas-end" tabIndex={-1} id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
                    <div className="offcanvas-header border-bottom">
                        <h5 className="offcanvas-title" id="offcanvasRightLabel"> my cart <span>({cartItems.reduce((acc, item) => acc + item.quantity, 0)})</span></h5>
                        <button type="button" className="btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
                            <i style={{ display: 'inline-flex', alignItems: 'center', justifyContent: 'center' }}><X size={16} /></i>
                        </button>
                    </div>
                    <div className="offcanvas-body">
                        {cartItems.length === 0 ? (
                            <div className="text-center py-5">
                                <p className="text-muted mb-0">Your cart is empty.</p>
                            </div>
                        ) : (
                            <>
                                <ul className="list-unstyled p-0 m-0">
                                    {cartItems.map((item, index) => {
                                        const getSlug = (str: string) => {
                                            return str
                                                .toLowerCase()
                                                .replace(/[^a-z0-9]+/g, '-')
                                                .replace(/(^-|-$)+/g, '');
                                        };
                                        return (
                                            <li key={index} className="d-flex align-items-start my-3 pb-3 border-bottom">
                                                <Link to={`/shop/product/${getSlug(item.name)}`} className="cart_img mr-3" style={{ width: '70px' }}>
                                                    <img src={item.img} alt={item.name} className="img-fluid w-100" />
                                                </Link>
                                                <div className="cart_text flex-grow-1">
                                                    <Link className="cart_title text-dark font-weight-bold text-decoration-none d-block mb-1" to={`/shop/product/${getSlug(item.name)}`}>{item.name}</Link>
                                                    <p className="m-0">{formatPrice(item.price)} x {item.quantity}</p>
                                                    <small className="text-muted d-block"><b>Color:</b> {item.color} | <b>Size:</b> {item.size}</small>
                                                </div>
                                                <a 
                                                    className="del_icon text-muted" 
                                                    href="#" 
                                                    onClick={(e) => { e.preventDefault(); removeFromCart(index); }}
                                                    style={{ cursor: 'pointer' }}
                                                >
                                                    <i style={{ display: 'inline-flex', alignItems: 'center', justifyContent: 'center' }}><X size={14} /></i>
                                                </a>
                                            </li>
                                        );
                                    })}
                                </ul>
                                <div className="mt-4">
                                    <h5 className="d-flex justify-content-between mb-3">sub total: <span className="font-weight-bold">{formatPrice(subTotal)}</span></h5>
                                    <div className="minicart_btn_area">
                                        <button 
                                            onClick={() => {
                                                const closeBtn = document.querySelector('.offcanvas.show .btn-close, .mini_cart .btn-close') as HTMLElement;
                                                if (closeBtn) {
                                                    closeBtn.click();
                                                }
                                                navigate('/checkout');
                                            }}
                                            className="btn btn-dark w-100 rounded-0 py-2 text-uppercase font-weight-bold"
                                        >
                                            Checkout
                                        </button>
                                    </div>
                                </div>
                            </>
                        )}
                    </div>
                </div>
            </div>

            {/*=========================
                MOBILE MENU DRAWER
            ==========================*/}
            <div className="mobile_menu_area">
                <div className="offcanvas offcanvas-start" data-bs-scroll="true" tabIndex={-1} id="offcanvasWithBothOptions" style={{ width: '300px' }}>
                    <div className="offcanvas-header border-bottom justify-content-between align-items-center">
                        <Link to="/" className="header_logo text-decoration-none text-dark fw-bold d-flex align-items-center gap-2" style={{ fontSize: '18px' }}
                            onClick={() => {
                                const closeBtn = document.querySelector('.mobile_menu_area .btn-close') as HTMLElement;
                                if (closeBtn) closeBtn.click();
                            }}
                        >
                            {settings.general?.logo_url ? (
                                <img 
                                    src={settings.general?.logo_url} 
                                    alt={settings.general?.brand_name || 'WoocomFashion'} 
                                    style={{ 
                                        height: settings.general?.logo_height ? `${Math.min(Number(settings.general.logo_height), 44)}px` : '40px',
                                        maxHeight: '44px', 
                                        maxWidth: '180px', 
                                        objectFit: 'contain' 
                                    }} 
                                />
                            ) : (
                                <>
                                    <span style={{ backgroundColor: settings.general?.primary_color || '#f59e0b', color: '#ffffff', borderRadius: '50%', width: '30px', height: '30px', display: 'inline-flex', alignItems: 'center', justifyContent: 'center', fontSize: '14px' }}>
                                        <Shirt size={15} />
                                    </span>
                                    {settings.general?.brand_name || 'WoocomFashion'}
                                </>
                            )}
                        </Link>
                        <button type="button" className="btn-close" data-bs-dismiss="offcanvas" aria-label="Close" style={{ display: 'inline-flex', alignItems: 'center', justifyContent: 'center', padding: '6px' }}>
                            <X size={18} />
                        </button>
                    </div>
                    <div className="offcanvas-body p-3">
                        {/* Search Bar */}
                        <form className="mb-4" onSubmit={(e) => {
                            e.preventDefault();
                            const val = (e.currentTarget.elements.namedItem('search') as HTMLInputElement).value;
                            const closeBtn = document.querySelector('.mobile_menu_area .btn-close') as HTMLElement;
                            if (closeBtn) closeBtn.click();
                            window.location.href = window.wpData?.homeUrl + `/shop?search=${encodeURIComponent(val)}`;
                        }}>
                            <div className="input-group">
                                <input type="text" name="search" className="form-control" placeholder="Search product..." style={{ fontSize: '14px' }} />
                                <button type="submit" className="btn btn-warning text-white" style={{ background: '#f59e0b', border: 'none' }}>
                                    <Search size={14} />
                                </button>
                            </div>
                        </form>

                        {/* Navigation Links */}
                        <div className="mb-4">
                            <h6 className="text-uppercase text-muted fw-bold mb-3" style={{ fontSize: '12px', letterSpacing: '1px' }}>Quick Navigation</h6>
                            <ul className="list-unstyled d-flex flex-column gap-2" style={{ fontSize: '15px' }}>
                                <li>
                                    <Link to="/" className="text-decoration-none text-dark d-block py-2 border-bottom"
                                        onClick={() => {
                                            const closeBtn = document.querySelector('.mobile_menu_area .btn-close') as HTMLElement;
                                            if (closeBtn) closeBtn.click();
                                        }}
                                    >
                                        Home
                                    </Link>
                                </li>
                                <li>
                                    <Link to="/shop" className="text-decoration-none text-dark d-block py-2 border-bottom"
                                        onClick={() => {
                                            const closeBtn = document.querySelector('.mobile_menu_area .btn-close') as HTMLElement;
                                            if (closeBtn) closeBtn.click();
                                        }}
                                    >
                                        Shop Catalog
                                    </Link>
                                </li>
                                <li>
                                    <Link to="/track-order" className="text-decoration-none text-dark d-block py-2 border-bottom fw-semibold text-warning"
                                        onClick={() => {
                                            const closeBtn = document.querySelector('.mobile_menu_area .btn-close') as HTMLElement;
                                            if (closeBtn) closeBtn.click();
                                        }}
                                    >
                                        Track Order
                                    </Link>
                                </li>
                            </ul>
                        </div>

                        {/* Browse Categories */}
                        <div>
                            <h6 className="text-uppercase text-muted fw-bold mb-3" style={{ fontSize: '12px', letterSpacing: '1px' }}>Categories</h6>
                            <ul className="list-unstyled d-flex flex-column gap-2" style={{ fontSize: '14.5px' }}>
                                <li>
                                    <Link to="/shop?category=men-s-fashion" className="text-decoration-none text-dark d-flex align-items-center gap-2 py-1"
                                        onClick={() => {
                                            const closeBtn = document.querySelector('.mobile_menu_area .btn-close') as HTMLElement;
                                            if (closeBtn) closeBtn.click();
                                        }}
                                    >
                                        <ChevronRight size={14} className="text-muted" /> Men's Fashion
                                    </Link>
                                </li>
                                <li>
                                    <Link to="/shop?category=women-s-fashion" className="text-decoration-none text-dark d-flex align-items-center gap-2 py-1"
                                        onClick={() => {
                                            const closeBtn = document.querySelector('.mobile_menu_area .btn-close') as HTMLElement;
                                            if (closeBtn) closeBtn.click();
                                        }}
                                    >
                                        <ChevronRight size={14} className="text-muted" /> Women's Fashion
                                    </Link>
                                </li>
                                <li>
                                    <Link to="/shop?category=kids-fashion" className="text-decoration-none text-dark d-flex align-items-center gap-2 py-1"
                                        onClick={() => {
                                            const closeBtn = document.querySelector('.mobile_menu_area .btn-close') as HTMLElement;
                                            if (closeBtn) closeBtn.click();
                                        }}
                                    >
                                        <ChevronRight size={14} className="text-muted" /> Kids Fashion
                                    </Link>
                                </li>
                                <li>
                                    <Link to="/shop?category=western-wear" className="text-decoration-none text-dark d-flex align-items-center gap-2 py-1"
                                        onClick={() => {
                                            const closeBtn = document.querySelector('.mobile_menu_area .btn-close') as HTMLElement;
                                            if (closeBtn) closeBtn.click();
                                        }}
                                    >
                                        <ChevronRight size={14} className="text-muted" /> Western Wear
                                    </Link>
                                </li>
                                <li>
                                    <Link to="/shop?category=beauty-care" className="text-decoration-none text-dark d-flex align-items-center gap-2 py-1"
                                        onClick={() => {
                                            const closeBtn = document.querySelector('.mobile_menu_area .btn-close') as HTMLElement;
                                            if (closeBtn) closeBtn.click();
                                        }}
                                    >
                                        <ChevronRight size={14} className="text-muted" /> Beauty Care
                                    </Link>
                                </li>
                                <li>
                                    <Link to="/shop?category=fashion-jewellery" className="text-decoration-none text-dark d-flex align-items-center gap-2 py-1"
                                        onClick={() => {
                                            const closeBtn = document.querySelector('.mobile_menu_area .btn-close') as HTMLElement;
                                            if (closeBtn) closeBtn.click();
                                        }}
                                    >
                                        <ChevronRight size={14} className="text-muted" /> Fashion Jewellery
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}



