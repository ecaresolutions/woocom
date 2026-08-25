import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import ShopLayout from '@/layouts/shop-layout';
import { Search, MapPin, Package, Truck, CheckCircle2, ClipboardList, AlertCircle, Calendar, ShieldCheck, User } from 'lucide-react';
import { api } from '@/services/api';

export default function TrackOrder() {
    const [orderId, setOrderId] = useState('');
    const [contact, setContact] = useState('');
    const [error, setError] = useState('');
    const [orderIdError, setOrderIdError] = useState('');
    const [contactError, setContactError] = useState('');
    const [searched, setSearched] = useState(false);
    const [trackingData, setTrackingData] = useState<any>(null);

    const performTrack = async (idToTrack: string, contactInfo: string) => {
        const cleanId = idToTrack.trim().replace(/[^0-9]/g, '');
        
        try {
            const data = await api.trackOrder(cleanId || idToTrack.trim(), contactInfo);
            
            const dateObj = new Date(data.date);
            const estDelivery = new Date(dateObj);
            estDelivery.setDate(estDelivery.getDate() + 3);

            const isProcessing = data.statusCode === 'processing';
            const isCompleted = data.statusCode === 'completed';

            setTrackingData({
                id: `#${data.orderId}`,
                status: data.status,
                statusText: `Order is currently in ${data.status} status. Customer: ${data.customer.name}`,
                datePlaced: data.date,
                estimatedDelivery: estDelivery.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }),
                shippingMethod: 'Standard Shipping',
                destination: data.customer.address || 'Dhaka, Bangladesh',
                steps: [
                    { title: 'Order Placed', desc: 'We have received your order in WooCommerce.', time: dateObj.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }), date: dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }), completed: true },
                    { title: 'Order Processing', desc: 'Order is confirmed and being prepared.', time: isProcessing || isCompleted ? 'Verified' : '--:--', date: isProcessing || isCompleted ? 'Confirmed' : 'Pending', completed: isProcessing || isCompleted, active: isProcessing },
                    { title: 'Shipped (In Transit)', desc: 'Handed over to delivery courier.', time: isCompleted ? 'Shipped' : '--:--', date: isCompleted ? 'In Transit' : 'Pending', completed: isCompleted },
                    { title: 'Delivered', desc: 'Package signed and delivered.', time: isCompleted ? 'Delivered' : '--:--', date: 'Estimated ' + estDelivery.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }), completed: isCompleted }
                ]
            });
            setSearched(true);
        } catch (err: any) {
            setError(err.message || 'No order found with this Order ID.');
            setTrackingData(null);
            setSearched(false);
        }
    };

    useEffect(() => {
        const params = new URLSearchParams(window.location.search);
        const qOrderId = params.get('orderId');
        if (qOrderId) {
            setOrderId(qOrderId);
            setContact('Customer');
            performTrack(qOrderId, 'Customer');
        }
    }, []);

    const handleTrack = (e: React.FormEvent) => {
        e.preventDefault();
        setError('');
        setOrderIdError('');
        setContactError('');

        let hasError = false;
        if (!orderId.trim()) {
            setOrderIdError('Please enter a valid Order ID.');
            hasError = true;
        }

        if (!contact.trim()) {
            setContactError('Please enter your Email or Phone Number.');
            hasError = true;
        }

        if (hasError) {
            return;
        }

        performTrack(orderId, contact);
    };


    return (
        <ShopLayout>

            <style>{`
                .track_container {
                    background: #ffffff;
                    border-radius: 16px;
                    border: 1px solid #e2e8f0;
                    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
                    padding: 30px;
                }
                .form_input {
                    position: relative;
                    margin-bottom: 20px;
                }
                .form_input label {
                    display: block;
                    font-size: 13px;
                    font-weight: 600;
                    color: #475569;
                    margin-bottom: 8px;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }
                .form_input input {
                    width: 100%;
                    padding: 12px 16px;
                    border: 1px solid #cbd5e1;
                    border-radius: 8px;
                    font-size: 15px;
                    color: #1e293b;
                    outline: none;
                    transition: all 0.2s ease-in-out;
                }
                .form_input input:focus {
                    border-color: #f59e0b;
                    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15);
                }
                .btn_track {
                    background: #f59e0b;
                    color: #fff;
                    font-weight: 600;
                    font-size: 16px;
                    padding: 13px 25px;
                    border: none;
                    border-radius: 8px;
                    width: 100%;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    gap: 8px;
                    cursor: pointer;
                    transition: all 0.2s ease-in-out;
                }
                .btn_track:hover {
                    background: #d97706;
                }
                .stepper_list {
                    position: relative;
                    padding-left: 35px;
                    margin-top: 30px;
                }
                .stepper_list::before {
                    content: '';
                    position: absolute;
                    left: 11px;
                    top: 10px;
                    bottom: 10px;
                    width: 2px;
                    background: #e2e8f0;
                }
                .stepper_item {
                    position: relative;
                    margin-bottom: 35px;
                }
                .stepper_item:last-child {
                    margin-bottom: 0;
                }
                .stepper_dot {
                    position: absolute;
                    left: -35px;
                    top: 3px;
                    width: 24px;
                    height: 24px;
                    border-radius: 50%;
                    background: #fff;
                    border: 2px solid #cbd5e1;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 1;
                    transition: all 0.3s ease;
                }
                .stepper_item.completed .stepper_dot {
                    background: #10b981;
                    border-color: #10b981;
                    color: #fff;
                }
                .stepper_item.active .stepper_dot {
                    border-color: #f59e0b;
                    background: #fff;
                    box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.2);
                }
                .stepper_content {
                    padding-left: 10px;
                }
                .stepper_title {
                    font-weight: 700;
                    color: #1e293b;
                    font-size: 15px;
                    margin-bottom: 3px;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                }
                .stepper_date {
                    font-size: 12px;
                    color: #64748b;
                    font-weight: 500;
                }
                .stepper_desc {
                    font-size: 13.5px;
                    color: #64748b;
                    margin-bottom: 0;
                }
                .pulsing_indicator {
                    width: 8px;
                    height: 8px;
                    border-radius: 50%;
                    background: #f59e0b;
                    animation: pulse 1.5s infinite ease-in-out;
                }
                @keyframes pulse {
                    0% { transform: scale(0.9); opacity: 0.6; }
                    50% { transform: scale(1.3); opacity: 1; }
                    100% { transform: scale(0.9); opacity: 0.6; }
                }
            `}</style>

            <div className="track_order_area py-5 bg-light" style={{ minHeight: '80vh' }}>
                <div className="container">
                    {/* BREADCRUMB */}
                    <div className="row mb-4">
                        <div className="col-12 text-start">
                            <h2 style={{ fontSize: '32px', fontWeight: 700, color: '#1e293b', marginBottom: '4px' }}>
                                Track Order
                            </h2>
                            <nav style={{ fontSize: '14px', color: '#64748b' }}>
                                <Link to="/" className="text-decoration-none" style={{ color: '#64748b' }}>Home</Link>
                                <span className="mx-2">/</span>
                                <Link to="/shop" className="text-decoration-none" style={{ color: '#64748b' }}>Shop</Link>
                                <span className="mx-2">/</span>
                                <span style={{ color: '#1e293b', fontWeight: 600 }}>Track Order</span>
                            </nav>
                        </div>
                    </div>

                    <div className="row">
                        {/* SEARCH PANEL */}
                        <div className="col-lg-5 mb-4 mb-lg-0">
                            <div className="track_container">
                                <h3 className="h5 fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                                    <ClipboardList size={20} className="text-warning" />
                                    Track Your Order
                                </h3>
                                <p className="text-muted small mb-4">
                                    Type your Order ID (provided in your order confirmation message or page) and Email/Phone Number to see current shipment progress.
                                </p>

                                <form onSubmit={handleTrack}>
                                    <div className="form_input">
                                        <label>Order ID <span className="text-danger">*</span></label>
                                        <input
                                            type="text"
                                            value={orderId}
                                            onChange={(e) => {
                                                setOrderId(e.target.value);
                                                if (orderIdError) setOrderIdError('');
                                            }}
                                            placeholder="e.g. LARA-104952"
                                            style={orderIdError ? { borderColor: '#ef4444', boxShadow: '0 0 0 3px rgba(239, 68, 68, 0.15)' } : {}}
                                        />
                                        {orderIdError && (
                                            <span style={{ display: 'inline-flex', alignItems: 'center', gap: '6px', color: '#ef4444', fontSize: '13px', fontWeight: 500, marginTop: '6px', width: '100%', justifyContent: 'flex-start', textAlign: 'left' }}>
                                                <AlertCircle size={15} style={{ flexShrink: 0 }} />
                                                <span>{orderIdError}</span>
                                            </span>
                                        )}
                                    </div>

                                    <div className="form_input">
                                        <label>Email or Phone <span className="text-danger">*</span></label>
                                        <input
                                            type="text"
                                            value={contact}
                                            onChange={(e) => {
                                                setContact(e.target.value);
                                                if (contactError) setContactError('');
                                            }}
                                            placeholder="e.g. john@example.com or 017XXXXXXXX"
                                            style={contactError ? { borderColor: '#ef4444', boxShadow: '0 0 0 3px rgba(239, 68, 68, 0.15)' } : {}}
                                        />
                                        {contactError && (
                                            <span style={{ display: 'inline-flex', alignItems: 'center', gap: '6px', color: '#ef4444', fontSize: '13px', fontWeight: 500, marginTop: '6px', width: '100%', justifyContent: 'flex-start', textAlign: 'left' }}>
                                                <AlertCircle size={15} style={{ flexShrink: 0 }} />
                                                <span>{contactError}</span>
                                            </span>
                                        )}
                                    </div>

                                    {error && (
                                        <div className="alert alert-danger d-flex align-items-center gap-2 p-2 mb-3" style={{ fontSize: '14px', borderRadius: '8px' }}>
                                            <AlertCircle size={16} />
                                            <span>{error}</span>
                                        </div>
                                    )}

                                    <button type="submit" className="btn_track mt-2">
                                        <Search size={18} />
                                        Track Progress
                                    </button>
                                </form>
                            </div>

                            <div className="bg-white p-4 border rounded-3 mt-4 text-start">
                                <h4 className="h6 fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                                    <ShieldCheck size={18} className="text-success" />
                                    Need assistance?
                                </h4>
                                <p className="text-muted small mb-0">
                                    If you cannot find your order or have questions about delivery times, please contact our support team at <strong>support@woocomfashion.com</strong> or call <strong>+880 9612-XXXXXX</strong>.
                                </p>
                            </div>
                        </div>

                        {/* RESULTS PANEL */}
                        <div className="col-lg-7">
                            {searched && trackingData ? (
                                <div className="track_container animated fadeIn">
                                    <div className="border-bottom pb-3 mb-4">
                                        <div className="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                            <h4 className="fw-bold text-dark mb-0">
                                                Order ID: <span className="text-primary">{trackingData.id}</span>
                                            </h4>
                                            <span className="badge bg-warning text-dark px-3 py-2 fw-semibold" style={{ borderRadius: '20px' }}>
                                                {trackingData.status}
                                            </span>
                                        </div>
                                        <p className="text-muted small mb-0">{trackingData.statusText}</p>
                                    </div>

                                    <div className="row g-3 mb-4 bg-light p-3 rounded-3 border">
                                        <div className="col-sm-6">
                                            <span className="text-muted d-block small">Order Date:</span>
                                            <strong className="text-dark d-flex align-items-center gap-1 mt-1 small">
                                                <Calendar size={14} className="text-muted" />
                                                {trackingData.datePlaced}
                                            </strong>
                                        </div>
                                        <div className="col-sm-6">
                                            <span className="text-muted d-block small">Estimated Delivery:</span>
                                            <strong className="text-dark d-flex align-items-center gap-1 mt-1 small">
                                                <Truck size={14} className="text-muted" />
                                                {trackingData.estimatedDelivery}
                                            </strong>
                                        </div>
                                        <div className="col-sm-6">
                                            <span className="text-muted d-block small">Shipping Method:</span>
                                            <strong className="text-dark d-flex align-items-center gap-1 mt-1 small">
                                                <Package size={14} className="text-muted" />
                                                {trackingData.shippingMethod}
                                            </strong>
                                        </div>
                                        <div className="col-sm-6">
                                            <span className="text-muted d-block small">Destination:</span>
                                            <strong className="text-dark d-flex align-items-center gap-1 mt-1 small">
                                                <MapPin size={14} className="text-muted" />
                                                {trackingData.destination}
                                            </strong>
                                        </div>
                                    </div>

                                    <h5 className="fw-bold text-dark mb-3">Shipment Progress Timeline</h5>
                                    
                                    <div className="stepper_list">
                                        {trackingData.steps.map((step: any, idx: number) => (
                                            <div key={idx} className={`stepper_item ${step.completed ? 'completed' : ''} ${step.active ? 'active' : ''}`}>
                                                <div className="stepper_dot">
                                                    {step.completed ? (
                                                        <CheckCircle2 size={14} />
                                                    ) : step.active ? (
                                                        <div className="pulsing_indicator" />
                                                    ) : (
                                                        <span style={{ fontSize: '10px', color: '#cbd5e1', fontWeight: 'bold' }}>{idx + 1}</span>
                                                    )}
                                                </div>
                                                <div className="stepper_content">
                                                    <div className="stepper_title">
                                                        <span>{step.title}</span>
                                                        <span className="stepper_date">{step.date} • {step.time}</span>
                                                    </div>
                                                    <p className="stepper_desc">{step.desc}</p>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            ) : (
                                <div className="track_container d-flex flex-column align-items-center justify-content-center py-5 text-center border-dashed" style={{ border: '2px dashed #cbd5e1', background: '#f8fafc', height: '100%' }}>
                                    <Package size={64} className="text-muted mb-3" style={{ opacity: 0.5 }} />
                                    <h4 className="fw-bold text-dark mb-2">No Order Tracked Yet</h4>
                                    <p className="text-muted small max-w-sm mb-0">
                                        Enter your tracking details in the left panel to search and see real-time delivery status updates.
                                    </p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </ShopLayout>
    );
}


