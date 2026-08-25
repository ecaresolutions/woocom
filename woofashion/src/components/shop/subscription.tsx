export default function Subscription() {
    return (
        <section className="subscription_2 mt_50 xs_mt_60" style={{ backgroundImage: 'url(' + window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/subscribe_2_bg.jpg)' }}>
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-xxl-6 col-lg-8 wow fadeInUp">
                        <div className="subscription_2_text">
                            <h2>Get Upto <span>70% </span> Off Discount Coupon</h2>
                            <p>by Subscribe our Newsletter</p>
                            <form action="#">
                                <input type="text" placeholder="Your email" required />
                                <button type="submit" className="common_btn">Subscribe</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}

