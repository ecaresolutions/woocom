import { Link } from 'react-router-dom';
import { Shirt, Facebook, Twitter, Linkedin, Instagram } from 'lucide-react';
import { useThemeSettings } from '@/lib/theme-settings';

export default function Footer() {
    const settings = useThemeSettings();
    const general = settings.general || {};
    const footer = settings.footer || {};

    return (
        <>
            {/*=========================
                FOOTER 2 START
            ==========================*/}
            <footer className="footer_2 pt_100" style={{ background: 'url(' + window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/footer_2_bg_2.jpg)' }}>
                <div className="container">
                    <div className="row justify-content-between">
                        <div className="col-xl-3 col-md-6 col-lg-3 wow fadeInUp" data-wow-delay=".7s">
                            <div className="footer_2_logo_area">
                                <Link className="footer_logo d-flex align-items-center" to="/" style={{ textDecoration: 'none' }}>
                                    {(general.footer_logo_url || general.logo_url) ? (
                                        <img 
                                            src={general.footer_logo_url || general.logo_url} 
                                            alt={general.brand_name || 'WoocomFashion'} 
                                            className="footer_store_logo"
                                            style={{ 
                                                height: general.logo_height ? `${general.logo_height}px` : '62px', 
                                                maxHeight: '90px', 
                                                maxWidth: '280px', 
                                                width: 'auto',
                                                objectFit: 'contain', 
                                                display: 'block' 
                                            }} 
                                        />
                                    ) : (
                                        <div style={{ fontSize: '24px', fontWeight: '800', color: '#ffffff', letterSpacing: '0.5px', display: 'flex', alignItems: 'center', gap: '10px' }}>
                                            <span style={{ backgroundColor: general.primary_color || '#f59e0b', color: '#ffffff', borderRadius: '50%', width: '40px', height: '40px', display: 'inline-flex', alignItems: 'center', justifyContent: 'center', fontSize: '18px', flexShrink: 0 }}>
                                                <Shirt size={20} />
                                            </span>
                                            {general.brand_name || 'WoocomFashion'}
                                        </div>
                                    )}
                                </Link>
                                <p className="mt-3">
                                    {footer.about_bio || 'WoocomFashion is your premium destination for the latest fashion trends. Discover curated collections crafted for comfort and style.'}
                                </p>
                                <ul>
                                    <li><span>Follow :</span></li>
                                    {footer.facebook && <li><a href={footer.facebook} target="_blank" rel="noreferrer" style={{ display: 'inline-flex', alignItems: 'center', justifyContent: 'center' }}><Facebook size={16} /></a></li>}
                                    {footer.twitter && <li><a href={footer.twitter} target="_blank" rel="noreferrer" style={{ display: 'inline-flex', alignItems: 'center', justifyContent: 'center' }}><Twitter size={16} /></a></li>}
                                    {footer.instagram && <li><a href={footer.instagram} target="_blank" rel="noreferrer" style={{ display: 'inline-flex', alignItems: 'center', justifyContent: 'center' }}><Instagram size={16} /></a></li>}
                                    {footer.linkedin && <li><a href={footer.linkedin} target="_blank" rel="noreferrer" style={{ display: 'inline-flex', alignItems: 'center', justifyContent: 'center' }}><Linkedin size={16} /></a></li>}
                                </ul>
                            </div>
                        </div>
                        <div className="col-xl-2 col-sm-6 col-md-4 col-lg-2 wow fadeInUp" data-wow-delay="1s">
                            <div className="footer_link">
                                <h3>Company</h3>
                                <ul>
                                    <li><Link to="/about">About us</Link></li>
                                    <li><Link to="/contact">Contact Us</Link></li>
                                    <li><a href="#">Affiliate</a></li>
                                    <li><a href="#">Career</a></li>
                                    <li><Link to="/blog">Latest News</Link></li>
                                </ul>
                            </div>
                        </div>
                        <div className="col-xl-2 col-sm-6 col-md-4 col-lg-2 wow fadeInUp" data-wow-delay="1.3s">
                            <div className="footer_link">
                                <h3>Category</h3>
                                <ul>
                                    <li><Link to="/shop">Men’s Fashion</Link></li>
                                    <li><Link to="/shop">Denim Collection</Link></li>
                                    <li><Link to="/shop">Western Wear</Link></li>
                                    <li><Link to="/shop">Sport Wear</Link></li>
                                    <li><Link to="/shop">Fashion Jewellery</Link></li>
                                </ul>
                            </div>
                        </div>
                        <div className="col-xl-2 col-sm-6 col-md-4 col-lg-2 wow fadeInUp" data-wow-delay="1.6s">
                            <div className="footer_link">
                                <h3>Quick Links</h3>
                                <ul>
                                    <li><Link to="/privacy-policy">Privacy Policy</Link></li>
                                    <li><Link to="/terms-and-conditions">Terms and Condition</Link></li>
                                    <li><Link to="/return-policy">Return Policy</Link></li>
                                    <li><Link to="/faq">FAQ's</Link></li>
                                    <li><Link to="/register">Become a Vendor</Link></li>
                                </ul>
                            </div>
                        </div>
                        <div className="col-xl-3 col-sm-6 col-md-4 col-lg-3 wow fadeInUp" data-wow-delay="1.9s">
                            <div className="footer_link footer_logo_area">
                                <h3>Contact Us</h3>
                                <p>Get in touch with our customer service team. We are here to help you 24/7.</p>
                                <span>
                                    <b><img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/location_icon_white.png'} alt="Map" className="img-fluid" /></b>
                                    {footer.contact_address || '37 W 24th St, New York, NY'}
                                </span>
                                <span>
                                    <b><img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/phone_icon_white.png'} alt="Call" className="img-fluid" /></b>
                                    <a href={`tel:${footer.contact_phone || '+123 324 5879 39'}`}>{footer.contact_phone || '+123 324 5879 39'}</a>
                                </span>
                                <span>
                                    <b><img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/mail_icon_white.png'} alt="Mail" className="img-fluid" /></b>
                                    <a href={`mailto:${footer.contact_email || 'info@WoocomFashion.com'}`}>{footer.contact_email || 'info@WoocomFashion.com'}</a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-12">
                            <div className="footer_copyright mt_75">
                                <p>{footer.copyright_text || 'Copyright @ WoocomFashion 2026. All right reserved.'}</p>
                                <ul className="payment">
                                    <li>Payment by :</li>
                                    <li>
                                        <img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/footer_payment_icon_1.jpg'} alt="payment" className="img-fluid w-100" />
                                    </li>
                                    <li>
                                        <img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/footer_payment_icon_2.jpg'} alt="payment" className="img-fluid w-100" />
                                    </li>
                                    <li>
                                        <img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/footer_payment_icon_3.jpg'} alt="payment" className="img-fluid w-100" />
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
            {/*=========================
                FOOTER 2 END
            ==========================*/}
        </>
    );
}




