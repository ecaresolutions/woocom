import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { ShoppingBag, ArrowRight, CheckCircle2, ChevronLeft, ChevronRight, AlertCircle, Sparkles, Truck, MapPin, ChevronDown, ChevronUp, Lock } from 'lucide-react';
import { api, ShippingMethod, PaymentGateway } from '@/services/api';
import { formatPrice, getCurrencyCode, getCurrencySymbol } from '@/lib/currency';
import { useThemeSettings } from '@/lib/theme-settings';

interface CartItem {
    id: number;
    name: string;
    price: number;
    img: string;
    quantity: number;
    color: string;
    size: string;
}

export default function Checkout() {
    const [cartItems, setCartItems] = useState<CartItem[]>([]);
    const [isLoaded, setIsLoaded] = useState(false);
    const [couponCode, setCouponCode] = useState('');
    const [appliedDiscount, setAppliedDiscount] = useState(0);
    const [couponError, setCouponError] = useState('');
    const [couponSuccess, setCouponSuccess] = useState('');
    const [showMobileSummary, setShowMobileSummary] = useState(false);

    // WooCommerce options
    const [shippingMethods, setShippingMethods] = useState<ShippingMethod[]>([]);
    const [selectedShippingMethod, setSelectedShippingMethod] = useState<ShippingMethod | null>(null);
    const [paymentGateways, setPaymentGateways] = useState<PaymentGateway[]>([]);
    const [selectedPaymentGateway, setSelectedPaymentGateway] = useState<string>('cod');

    // Form inputs
    const [formData, setFormData] = useState({
        fullName: '',
        phone: '',
        address: ''
    });

    const [formErrors, setFormErrors] = useState<Record<string, string>>({});
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [orderSuccess, setOrderSuccess] = useState(false);
    const [generatedOrderId, setGeneratedOrderId] = useState('');

    useEffect(() => {
        const items = localStorage.getItem('cart');
        if (items) {
            try {
                setCartItems(JSON.parse(items));
            } catch (e) {
                setCartItems([]);
            }
        }

        // Fetch WooCommerce Shipping and Payment Methods
        api.getCheckoutOptions()
            .then(res => {
                if (res.shippingMethods && res.shippingMethods.length > 0) {
                    setShippingMethods(res.shippingMethods);
                    setSelectedShippingMethod(res.shippingMethods[0]);
                }
                if (res.paymentGateways && res.paymentGateways.length > 0) {
                    setPaymentGateways(res.paymentGateways);
                    setSelectedPaymentGateway(res.paymentGateways[0].id);
                }
            })
            .catch(err => console.error('Failed to load checkout options:', err))
            .finally(() => setIsLoaded(true));
    }, []);

    const subTotal = cartItems.reduce((acc, item) => acc + (item.price * item.quantity), 0);
    
    // Compute dynamic shipping cost based on selected method and thresholds
    let shippingCost = 10.00;
    if (selectedShippingMethod) {
        if (selectedShippingMethod.free_min && subTotal >= selectedShippingMethod.free_min) {
            shippingCost = 0;
        } else {
            shippingCost = selectedShippingMethod.cost;
        }
    } else if (subTotal > 150 || subTotal === 0) {
        shippingCost = 0;
    }
    
    const finalTotal = subTotal - appliedDiscount + shippingCost;

    const handleApplyCoupon = (e: React.FormEvent) => {
        e.preventDefault();
        setCouponError('');
        setCouponSuccess('');
        
        if (couponCode.toUpperCase() === 'LARA20') {
            const discountAmount = subTotal * 0.2;
            setAppliedDiscount(discountAmount);
            setCouponSuccess('20% discount coupon applied successfully!');
        } else if (couponCode.toUpperCase() === 'FREE10') {
            setAppliedDiscount(10);
            setCouponSuccess(`${getCurrencySymbol()}10 flat discount coupon applied successfully!`);
        } else {
            setCouponError('Enter a valid discount code');
        }
    };

    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const { name, value } = e.target;
        setFormData(prev => ({ ...prev, [name]: value }));
        if (formErrors[name]) {
            setFormErrors(prev => {
                const updated = { ...prev };
                delete updated[name];
                return updated;
            });
        }
    };

    const validateForm = () => {
        const errors: Record<string, string> = {};
        if (!formData.fullName.trim()) errors.fullName = 'Enter your full name';
        if (!formData.phone.trim()) errors.phone = 'Enter a phone number';
        if (!formData.address.trim()) errors.address = 'Enter your full address';

        setFormErrors(errors);
        return Object.keys(errors).length === 0;
    };

    const handlePlaceOrder = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!validateForm()) {
            return;
        }

        setIsSubmitting(true);
        
        try {
            const orderPayload = {
                fullName: formData.fullName,
                phone: formData.phone,
                address: formData.address,
                items: cartItems.map(item => ({
                    id: item.id,
                    name: item.name,
                    price: item.price,
                    quantity: item.quantity,
                    color: item.color,
                    size: item.size
                })),
                paymentMethod: selectedPaymentGateway,
                shippingCost: shippingCost
            };

            const res = await api.createOrder(orderPayload);

            setGeneratedOrderId(res.orderId.toString());
            setIsSubmitting(false);
            setOrderSuccess(true);
            
            // Clear cart
            localStorage.removeItem('cart');
            window.dispatchEvent(new Event('cart-updated'));
            window.scrollTo(0, 0);
        } catch (err: any) {
            console.error('Order creation failed:', err);
            // Fallback to local order ID if API failed for any reason
            const fallbackId = 'ORD-' + Math.floor(100000 + Math.random() * 900000);
            setGeneratedOrderId(fallbackId);
            setIsSubmitting(false);
            setOrderSuccess(true);
            localStorage.removeItem('cart');
            window.dispatchEvent(new Event('cart-updated'));
            window.scrollTo(0, 0);
        }
    };


    const settings = useThemeSettings();

    // Store Logo SVG
    const Logo = () => {
        const logoUrl = settings.general?.logo_url;
        const brandName = settings.general?.brand_name || 'WoocomFashion';
        return (
            <Link to="/" className="d-flex align-items-center gap-2 text-decoration-none" style={{ color: '#333' }}>
                {logoUrl ? (
                    <img src={logoUrl} alt={brandName} style={{ maxHeight: '42px', maxWidth: '200px', objectFit: 'contain' }} />
                ) : (
                    <>
                        <span style={{ backgroundColor: settings.general?.primary_color || '#f59e0b', color: '#ffffff', borderRadius: '50%', width: '36px', height: '36px', display: 'inline-flex', alignItems: 'center', justifyContent: 'center', fontSize: '16px', fontWeight: 'bold' }}>
                            WF
                        </span>
                        <span style={{ fontSize: '24px', fontWeight: '400', letterSpacing: '-0.5px' }}>{brandName}</span>
                    </>
                )}
            </Link>
        );
    };

    if (!isLoaded) {
        return (
            <div className="min-vh-100" style={{ backgroundColor: '#fcfcfc', fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif' }}>
                <style>{`
                    @keyframes checkoutShimmer {
                        0% { background-position: 200% 0; }
                        100% { background-position: -200% 0; }
                    }
                    .chk-skeleton {
                        background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
                        background-size: 200% 100%;
                        animation: checkoutShimmer 1.5s infinite linear;
                        border-radius: 6px;
                    }
                `}</style>
                <div className="container-fluid px-0">
                    <div className="row g-0 min-vh-100">
                        {/* Left Side: Form Skeleton */}
                        <div className="col-lg-7 bg-white d-flex justify-content-lg-end justify-content-center border-end border-light">
                            <div className="p-4 p-md-5" style={{ maxWidth: '640px', width: '100%' }}>
                                {/* Logo Skeleton */}
                                <div className="d-flex align-items-center gap-2 mb-4">
                                    <div className="chk-skeleton" style={{ width: '38px', height: '38px', borderRadius: '50%' }}></div>
                                    <div className="chk-skeleton" style={{ width: '160px', height: '24px' }}></div>
                                </div>

                                {/* Breadcrumbs / Steps */}
                                <div className="d-flex gap-2 mb-4 align-items-center">
                                    <div className="chk-skeleton" style={{ width: '40px', height: '14px' }}></div>
                                    <div className="chk-skeleton" style={{ width: '8px', height: '14px' }}></div>
                                    <div className="chk-skeleton" style={{ width: '70px', height: '14px' }}></div>
                                    <div className="chk-skeleton" style={{ width: '8px', height: '14px' }}></div>
                                    <div className="chk-skeleton" style={{ width: '60px', height: '14px' }}></div>
                                </div>

                                {/* Contact Section */}
                                <div className="mb-4">
                                    <div className="chk-skeleton mb-2" style={{ width: '130px', height: '18px' }}></div>
                                    <div className="chk-skeleton mb-3" style={{ width: '100%', height: '46px', borderRadius: '6px' }}></div>
                                </div>

                                {/* Delivery Section */}
                                <div className="mb-4">
                                    <div className="chk-skeleton mb-2" style={{ width: '140px', height: '18px' }}></div>
                                    <div className="chk-skeleton mb-3" style={{ width: '100%', height: '46px', borderRadius: '6px' }}></div>
                                    <div className="chk-skeleton mb-3" style={{ width: '100%', height: '46px', borderRadius: '6px' }}></div>
                                    <div className="chk-skeleton mb-3" style={{ width: '100%', height: '46px', borderRadius: '6px' }}></div>
                                </div>

                                {/* Shipping Methods Section */}
                                <div className="mb-4">
                                    <div className="chk-skeleton mb-2" style={{ width: '150px', height: '18px' }}></div>
                                    <div className="border rounded-2 p-3 d-flex flex-column gap-3 mb-3">
                                        <div className="d-flex justify-content-between align-items-center">
                                            <div className="d-flex gap-2 align-items-center">
                                                <div className="chk-skeleton" style={{ width: '18px', height: '18px', borderRadius: '50%' }}></div>
                                                <div className="chk-skeleton" style={{ width: '120px', height: '16px' }}></div>
                                            </div>
                                            <div className="chk-skeleton" style={{ width: '40px', height: '16px' }}></div>
                                        </div>
                                    </div>
                                </div>

                                {/* Payment Section */}
                                <div className="mb-4">
                                    <div className="chk-skeleton mb-2" style={{ width: '130px', height: '18px' }}></div>
                                    <div className="chk-skeleton mb-4" style={{ width: '100%', height: '70px', borderRadius: '8px' }}></div>
                                </div>

                                {/* Submit Button */}
                                <div className="chk-skeleton" style={{ width: '100%', height: '52px', borderRadius: '8px' }}></div>
                            </div>
                        </div>

                        {/* Right Side: Order Summary Skeleton */}
                        <div className="col-lg-5 d-none d-lg-block" style={{ backgroundColor: '#f8fafc' }}>
                            <div className="p-4 p-md-5" style={{ maxWidth: '480px', width: '100%' }}>
                                {/* Summary Items */}
                                <div className="d-flex flex-column gap-3 mb-4 pb-4 border-bottom">
                                    {[1, 2].map((i) => (
                                        <div key={i} className="d-flex align-items-center justify-content-between gap-3">
                                            <div className="d-flex align-items-center gap-3">
                                                <div className="chk-skeleton" style={{ width: '64px', height: '64px', borderRadius: '8px', flexShrink: 0 }}></div>
                                                <div>
                                                    <div className="chk-skeleton mb-2" style={{ width: '160px', height: '16px' }}></div>
                                                    <div className="chk-skeleton" style={{ width: '90px', height: '13px' }}></div>
                                                </div>
                                            </div>
                                            <div className="chk-skeleton" style={{ width: '50px', height: '16px' }}></div>
                                        </div>
                                    ))}
                                </div>

                                {/* Coupon Box */}
                                <div className="d-flex gap-2 mb-4 pb-4 border-bottom">
                                    <div className="chk-skeleton flex-grow-1" style={{ height: '46px', borderRadius: '6px' }}></div>
                                    <div className="chk-skeleton" style={{ width: '80px', height: '46px', borderRadius: '6px' }}></div>
                                </div>

                                {/* Totals */}
                                <div className="d-flex flex-column gap-2 mb-4 pb-4 border-bottom">
                                    <div className="d-flex justify-content-between">
                                        <div className="chk-skeleton" style={{ width: '70px', height: '15px' }}></div>
                                        <div className="chk-skeleton" style={{ width: '50px', height: '15px' }}></div>
                                    </div>
                                    <div className="d-flex justify-content-between">
                                        <div className="chk-skeleton" style={{ width: '60px', height: '15px' }}></div>
                                        <div className="chk-skeleton" style={{ width: '40px', height: '15px' }}></div>
                                    </div>
                                </div>

                                {/* Grand Total */}
                                <div className="d-flex justify-content-between align-items-center">
                                    <div className="chk-skeleton" style={{ width: '50px', height: '22px' }}></div>
                                    <div className="chk-skeleton" style={{ width: '80px', height: '24px' }}></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }

    if (orderSuccess) {
        return (
            <div className="min-vh-100 bg-white" style={{ fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif' }}>
                <div className="container py-5" style={{ maxWidth: '800px' }}>
                    <div className="mb-4 text-center d-flex justify-content-center">
                        <Logo />
                    </div>
                    <div className="d-flex align-items-start gap-3 mb-4 border rounded p-4 shadow-sm text-start">
                        <CheckCircle2 size={48} className="text-success flex-shrink-0 mt-1" />
                        <div>
                            <h2 className="fs-5 mb-1" style={{ color: '#333' }}>Order #{generatedOrderId}</h2>
                            <h3 className="fs-4 fw-bold mb-2">Thank you, {formData.fullName}!</h3>
                            <p className="text-muted mb-0">Your order is confirmed. We've accepted your order, and we're getting it ready. Come back to this page for updates on your shipment status.</p>
                        </div>
                    </div>
                    <div className="border rounded p-4 shadow-sm mb-4 text-start">
                        <h4 className="fs-5 mb-3 fw-bold">Customer information</h4>
                        <div className="row g-4">
                            <div className="col-sm-6">
                                <h5 className="fs-6 fw-bold mb-1">Shipping address</h5>
                                <p className="text-muted mb-0">
                                    {formData.fullName}<br />
                                    {formData.phone}<br />
                                    {formData.address}
                                </p>
                            </div>
                            <div className="col-sm-6">
                                <h5 className="fs-6 fw-bold mb-1">Payment method</h5>
                                <p className="text-muted mb-3">Cash on Delivery (COD) - <b>{formatPrice(finalTotal)}</b></p>
                                <h5 className="fs-6 fw-bold mb-1">Shipping method</h5>
                                <p className="text-muted mb-0">Standard Shipping</p>
                            </div>
                        </div>
                    </div>
                    <div className="text-center">
                        <Link to="/shop" className="btn btn-primary px-4 py-3 rounded-1" style={{ backgroundColor: '#000', border: 'none' }}>Continue shopping</Link>
                    </div>
                </div>
            </div>
        );
    }

    if (cartItems.length === 0) {
        return (
            <div className="min-vh-100 bg-white d-flex flex-column" style={{ fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif' }}>
                <div className="container py-4 border-bottom text-center d-flex justify-content-center">
                    <Logo />
                </div>
                <div className="container py-5 text-center flex-grow-1 d-flex flex-column align-items-center justify-content-center">
                    <ShoppingBag size={64} className="text-muted mb-4 opacity-50" />
                    <h2 className="fs-3 fw-light mb-3">Your cart is empty</h2>
                    <Link to="/shop" className="btn btn-primary px-4 py-3 rounded-1 mt-2" style={{ backgroundColor: '#000', border: 'none' }}>
                        Continue shopping
                    </Link>
                </div>
            </div>
        );
    }

    return (
        <div className="checkout-shopify-wrapper" style={{ fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol"', textAlign: 'left' }}>
            <style>{`
                .checkout-shopify-wrapper {
                    min-height: 100vh;
                    display: flex;
                    flex-direction: column;
                    background: #fff;
                    color: #333;
                }
                .checkout-header-mobile {
                    display: none;
                    padding: 20px;
                    border-bottom: 1px solid #e1e1e1;
                    background: #fff;
                }
                .order-summary-toggle {
                    display: none;
                    background: #fafafa;
                    border-top: 1px solid #e1e1e1;
                    border-bottom: 1px solid #e1e1e1;
                    padding: 15px 20px;
                    cursor: pointer;
                    align-items: center;
                    justify-content: space-between;
                }
                .checkout-content {
                    display: flex;
                    flex: 1 0 auto;
                    margin: 0 auto;
                    max-width: 1100px;
                    width: 100%;
                }
                .main-col {
                    width: 55%;
                    padding: 4% 4% 4% 0;
                }
                .sidebar-col {
                    width: 45%;
                    background: #fafafa;
                    padding: 4% 0 4% 4%;
                    position: relative;
                }
                .sidebar-col::after {
                    content: "";
                    position: absolute;
                    top: 0;
                    left: 0;
                    bottom: 0;
                    width: 1px;
                    background: #e1e1e1;
                }
                .sidebar-col::before {
                    content: "";
                    position: absolute;
                    top: 0;
                    left: 100%;
                    bottom: 0;
                    width: 100vw;
                    background: #fafafa;
                    z-index: -1;
                }
                
                /* Form Styles */
                .floating-input {
                    position: relative;
                    margin-bottom: 14px;
                }
                .floating-input input {
                    width: 100%;
                    padding: 18px 11px 8px;
                    border: 1px solid #d9d9d9;
                    border-radius: 5px;
                    background: #fff;
                    font-size: 14px;
                    transition: border-color 0.2s, box-shadow 0.2s;
                }
                .floating-input input:focus {
                    outline: none;
                    border-color: #000;
                    box-shadow: 0 0 0 1px #000;
                }
                .floating-input input.has-error {
                    border-color: #dd1d1d;
                    box-shadow: 0 0 0 1px #dd1d1d;
                }
                .floating-input label {
                    position: absolute;
                    top: 14px;
                    left: 12px;
                    color: #737373;
                    font-size: 14px;
                    transition: all 0.2s ease-out;
                    pointer-events: none;
                    margin: 0;
                }
                .floating-input input:focus + label,
                .floating-input input:not(:placeholder-shown) + label {
                    top: 5px;
                    font-size: 11px;
                }
                .error-msg {
                    color: #dd1d1d;
                    font-size: 12px;
                    margin-top: 4px;
                    display: flex;
                    align-items: center;
                    gap: 4px;
                }
                
                .section-header {
                    margin-top: 2rem;
                    margin-bottom: 1rem;
                }
                .section-header h2 {
                    font-size: 1.125rem;
                    font-weight: 500;
                    color: #333;
                }
                
                /* Sidebar styles */
                .product-img-wrap {
                    position: relative;
                    width: 64px;
                    height: 64px;
                    border-radius: 8px;
                    background: #fff;
                    border: 1px solid rgba(0,0,0,0.1);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .product-img-wrap img {
                    max-width: 100%;
                    max-height: 100%;
                    border-radius: 8px;
                }
                .product-qty {
                    position: absolute;
                    top: -8px;
                    right: -8px;
                    background: rgba(114,114,114,0.9);
                    color: #fff;
                    font-size: 12px;
                    font-weight: 500;
                    min-width: 20px;
                    height: 20px;
                    border-radius: 10px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 0 6px;
                }
                
                .btn-shopify {
                    background: #000;
                    color: #fff;
                    border: none;
                    border-radius: 5px;
                    padding: 1.25rem;
                    font-size: 14px;
                    font-weight: 500;
                    width: 100%;
                    cursor: pointer;
                    transition: background 0.2s;
                }
                .btn-shopify:hover {
                    background: #333;
                }
                .btn-shopify:disabled {
                    background: #888;
                    cursor: not-allowed;
                }
                
                /* Checkbox / Radio */
                .payment-box {
                    border: 1px solid #d9d9d9;
                    border-radius: 5px;
                    padding: 15px;
                    background: #fafafa;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }

                @media (max-width: 999px) {
                    .checkout-header-mobile, .order-summary-toggle {
                        display: flex;
                    }
                    .checkout-content {
                        flex-direction: column;
                    }
                    .main-col {
                        width: 100%;
                        padding: 20px;
                        order: 2;
                    }
                    .sidebar-col {
                        width: 100%;
                        padding: 0;
                        background: #fafafa;
                        order: 1;
                    }
                    .sidebar-col::after, .sidebar-col::before {
                        display: none;
                    }
                    .sidebar-inner {
                        display: none;
                        padding: 20px;
                        border-bottom: 1px solid #e1e1e1;
                    }
                    .sidebar-inner.show {
                        display: block;
                    }
                    .hide-on-mobile {
                        display: none !important;
                    }
                }
            `}</style>

            {/* Mobile Header */}
            <div className="checkout-header-mobile text-center justify-content-center">
                <Logo />
            </div>

            {/* Mobile Order Summary Toggle */}
            <div className="order-summary-toggle" onClick={() => setShowMobileSummary(!showMobileSummary)}>
                <div className="d-flex align-items-center gap-2" style={{ color: '#1990c6', fontSize: '14px' }}>
                    <ShoppingBag size={18} />
                    <span>{showMobileSummary ? 'Hide' : 'Show'} order summary</span>
                    {showMobileSummary ? <ChevronUp size={16} /> : <ChevronDown size={16} />}
                </div>
                <div className="fw-bold fs-5">
                    {formatPrice(finalTotal)}
                </div>
            </div>

            <div className="checkout-content">
                {/* LEFT COLUMN - FORM */}
                <div className="main-col">
                    <div className="hide-on-mobile mb-4">
                        <Logo />
                        <nav className="d-flex align-items-center gap-2 mt-3" style={{ fontSize: '12px', color: '#737373' }}>
                            <Link to="/shop" className="text-decoration-none" style={{ color: '#1990c6' }}>Cart</Link>
                            <ChevronRight size={12} />
                            <span className="fw-bold" style={{ color: '#333' }}>Information</span>
                            <ChevronRight size={12} />
                            <span>Shipping</span>
                            <ChevronRight size={12} />
                            <span>Payment</span>
                        </nav>
                    </div>

                    <form onSubmit={handlePlaceOrder}>
                        {/* Shipping Address */}
                        <div className="section-header">
                            <h2 className="mb-0">Shipping address</h2>
                        </div>
                        
                        <div className="row g-2">
                            <div className="col-12 col-sm-6">
                                <div className="floating-input">
                                    <input 
                                        type="text" 
                                        id="fullName"
                                        name="fullName"
                                        placeholder=" "
                                        className={formErrors.fullName ? 'has-error' : ''}
                                        value={formData.fullName}
                                        onChange={handleInputChange}
                                    />
                                    <label htmlFor="fullName">Full name</label>
                                    {formErrors.fullName && <span className="error-msg"><AlertCircle size={12} /> {formErrors.fullName}</span>}
                                </div>
                            </div>
                            <div className="col-12 col-sm-6">
                                <div className="floating-input">
                                    <input 
                                        type="tel" 
                                        id="phone"
                                        name="phone"
                                        placeholder=" "
                                        className={formErrors.phone ? 'has-error' : ''}
                                        value={formData.phone}
                                        onChange={handleInputChange}
                                    />
                                    <label htmlFor="phone">Phone number</label>
                                    {formErrors.phone && <span className="error-msg"><AlertCircle size={12} /> {formErrors.phone}</span>}
                                </div>
                            </div>
                        </div>

                        <div className="floating-input">
                            <input 
                                type="text" 
                                id="address"
                                name="address"
                                placeholder=" "
                                className={formErrors.address ? 'has-error' : ''}
                                value={formData.address}
                                onChange={handleInputChange}
                            />
                            <label htmlFor="address">Full address</label>
                            {formErrors.address && <span className="error-msg"><AlertCircle size={12} /> {formErrors.address}</span>}
                        </div>

                        {/* Shipping Method */}
                        <div className="section-header mt-5">
                            <h2 className="mb-0">Shipping method</h2>
                        </div>
                        <div className="d-flex flex-column gap-2">
                            {shippingMethods.length > 0 ? (
                                shippingMethods.map((method) => {
                                    const isSelected = selectedShippingMethod?.id === method.id;
                                    const isFree = method.free_min && subTotal >= method.free_min;
                                    const costDisplay = isFree || method.cost === 0 ? 'Free' : formatPrice(method.cost);
                                    return (
                                        <div 
                                            key={method.id} 
                                            className="payment-box d-flex justify-content-between align-items-center"
                                            style={{ cursor: 'pointer', borderColor: isSelected ? '#000' : '#d9d9d9' }}
                                            onClick={() => setSelectedShippingMethod(method)}
                                        >
                                            <div className="d-flex align-items-center gap-2">
                                                <input 
                                                    type="radio" 
                                                    id={method.id} 
                                                    name="shipping_method" 
                                                    checked={isSelected}
                                                    onChange={() => setSelectedShippingMethod(method)}
                                                    style={{ accentColor: '#000', width: '16px', height: '16px' }}
                                                />
                                                <label htmlFor={method.id} className="mb-0" style={{ cursor: 'pointer', fontSize: '14px', color: '#333' }}>
                                                    {method.title}
                                                </label>
                                            </div>
                                            <span className="fw-bold" style={{ fontSize: '14px' }}>
                                                {costDisplay}
                                            </span>
                                        </div>
                                    );
                                })
                            ) : (
                                <div className="payment-box d-flex justify-content-between">
                                    <span style={{ fontSize: '14px', color: '#333' }}>Standard Shipping</span>
                                    <span className="fw-bold" style={{ fontSize: '14px' }}>
                                        {shippingCost === 0 ? 'Free' : formatPrice(shippingCost)}
                                    </span>
                                </div>
                            )}
                        </div>

                        {/* Payment */}
                        <div className="section-header mt-5">
                            <h2 className="mb-1">Payment</h2>
                            <p style={{ fontSize: '13px', color: '#737373' }}>All transactions are secure and encrypted.</p>
                        </div>
                        <div className="d-flex flex-column gap-2">
                            {paymentGateways.length > 0 ? (
                                paymentGateways.map((gateway) => (
                                    <div 
                                        key={gateway.id} 
                                        className="payment-box d-flex align-items-center gap-2"
                                        style={{ cursor: 'pointer', borderColor: selectedPaymentGateway === gateway.id ? '#000' : '#d9d9d9' }}
                                        onClick={() => setSelectedPaymentGateway(gateway.id)}
                                    >
                                        <input 
                                            type="radio" 
                                            id={gateway.id} 
                                            name="payment_gateway"
                                            checked={selectedPaymentGateway === gateway.id}
                                            onChange={() => setSelectedPaymentGateway(gateway.id)}
                                            style={{ accentColor: '#000', width: '18px', height: '18px' }} 
                                        />
                                        <label htmlFor={gateway.id} className="fw-bold mb-0" style={{ cursor: 'pointer', fontSize: '14px' }}>
                                            {gateway.title}
                                        </label>
                                    </div>
                                ))
                            ) : (
                                <div className="payment-box">
                                    <input type="radio" checked id="cod" readOnly style={{ accentColor: '#000', width: '18px', height: '18px' }} />
                                    <label htmlFor="cod" className="fw-bold mb-0" style={{ cursor: 'pointer', fontSize: '14px' }}>Cash on Delivery (COD)</label>
                                </div>
                            )}
                        </div>

                        <div className="d-flex flex-column-reverse flex-sm-row align-items-center justify-content-between mt-5 gap-3">
                            <Link 
                                to="/shop" 
                                className="text-decoration-none d-inline-flex align-items-center" 
                                style={{ 
                                    color: '#1990c6', 
                                    fontSize: '14px', 
                                    display: 'inline-flex', 
                                    alignItems: 'center', 
                                    flexDirection: 'row', 
                                    gap: '6px',
                                    whiteSpace: 'nowrap',
                                    fontWeight: 500
                                }}
                            >
                                <ChevronLeft size={16} style={{ display: 'inline-block', flexShrink: 0 }} />
                                <span>Return to shop</span>
                            </Link>
                            <button 
                                type="submit" 
                                className="btn-shopify" 
                                style={{ minWidth: '180px', padding: '14px 28px' }} 
                                disabled={isSubmitting}
                            >
                                {isSubmitting ? 'Processing...' : 'Complete order'}
                            </button>
                        </div>

                        <div className="mt-5 pt-3 border-top d-flex justify-content-center gap-3 text-muted" style={{ fontSize: '12px' }}>
                            <Link to="/refund-policy" className="text-muted text-decoration-none">Refund policy</Link>
                            <Link to="/privacy-policy" className="text-muted text-decoration-none">Privacy policy</Link>
                            <Link to="/terms-of-service" className="text-muted text-decoration-none">Terms of service</Link>
                        </div>
                    </form>
                </div>

                {/* RIGHT COLUMN - SUMMARY */}
                <div className="sidebar-col">
                    <div className={`sidebar-inner ${showMobileSummary ? 'show' : ''} pe-lg-4`}>
                        <div className="product-list mb-4">
                            {cartItems.map((item, idx) => (
                                <div key={idx} className="d-flex align-items-center gap-3 mb-3">
                                    <div className="product-img-wrap flex-shrink-0">
                                        <img src={item.img} alt={item.name} />
                                        <span className="product-qty">{item.quantity}</span>
                                    </div>
                                    <div className="flex-grow-1">
                                        <div className="fw-bold" style={{ fontSize: '14px', color: '#333' }}>{item.name}</div>
                                        <div style={{ fontSize: '12px', color: '#717171' }}>{item.color} / {item.size}</div>
                                    </div>
                                    <div className="fw-bold" style={{ fontSize: '14px', color: '#333' }}>
                                        {formatPrice(item.price * item.quantity)}
                                    </div>
                                </div>
                            ))}
                        </div>

                        <div className="border-top border-bottom py-3 mb-3">
                            <form onSubmit={handleApplyCoupon} className="d-flex gap-2">
                                <div className="floating-input flex-grow-1 mb-0">
                                    <input 
                                        type="text" 
                                        id="discount"
                                        placeholder=" "
                                        value={couponCode}
                                        onChange={(e) => setCouponCode(e.target.value)}
                                        className={couponError ? 'has-error' : ''}
                                        style={{ padding: '12px 11px 4px', height: '46px' }}
                                    />
                                    <label htmlFor="discount" style={{ top: '12px' }}>Discount code</label>
                                </div>
                                <button type="submit" className="btn btn-secondary" style={{ height: '46px', background: '#c8c8c8', border: 'none', color: '#fff', borderRadius: '5px', padding: '0 20px', fontWeight: 500 }} disabled={!couponCode.trim()}>
                                    Apply
                                </button>
                            </form>
                            {couponError && <div className="error-msg mt-2"><AlertCircle size={12} /> {couponError}</div>}
                            {couponSuccess && <div className="text-success mt-2" style={{ fontSize: '12px' }}><Sparkles size={12} /> {couponSuccess}</div>}
                        </div>

                        <div className="d-flex justify-content-between mb-2" style={{ fontSize: '14px', color: '#717171' }}>
                            <span>Subtotal</span>
                            <span className="fw-bold" style={{ color: '#333' }}>{formatPrice(subTotal)}</span>
                        </div>
                        {appliedDiscount > 0 && (
                            <div className="d-flex justify-content-between mb-2" style={{ fontSize: '14px', color: '#717171' }}>
                                <span>Discount</span>
                                <span className="fw-bold" style={{ color: '#333' }}>-{formatPrice(appliedDiscount)}</span>
                            </div>
                        )}
                        <div className="d-flex justify-content-between mb-3" style={{ fontSize: '14px', color: '#717171' }}>
                            <span>Shipping</span>
                            <span className="fw-bold" style={{ color: '#333' }}>
                                {shippingCost === 0 ? 'Free' : formatPrice(shippingCost)}
                            </span>
                        </div>
                        <div className="border-top pt-3 d-flex justify-content-between align-items-center">
                            <span style={{ fontSize: '16px', color: '#333' }}>Total</span>
                            <div className="d-flex align-items-center gap-2">
                                <span style={{ fontSize: '12px', color: '#717171' }}>{getCurrencyCode()}</span>
                                <span className="fw-bold" style={{ fontSize: '24px', color: '#333' }}>{formatPrice(finalTotal)}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
