import { Link } from 'react-router-dom';

export default function BrandMarquee() {
    const brands = [
        { img: window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/brand1.png' },
        { img: window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/brand2.png' },
        { img: window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/brand3.png' },
        { img: window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/brand4.png' },
        { img: window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/brand5.png' },
        { img: window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/brand6.png' },
        { img: window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/brand7.png' },
        { img: window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/brand8.png' },
        { img: window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/brand9.png' },
        { img: window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/brand10.png' }
    ];

    // Double the brands list to create a seamless infinite loop
    const doubledBrands = [...brands, ...brands];

    return (
        <section className="brand_2 mt_85">
            {/* Inject safe, high-performance CSS keyframe animations for marquee */}
            <style>{`
                @keyframes marquee-scroll {
                    0% { transform: translateX(0); }
                    100% { transform: translateX(-50%); }
                }
                .marquee-container {
                    overflow: hidden;
                    width: 100%;
                    display: flex;
                }
                .marquee-list {
                    display: flex !important;
                    flex-wrap: nowrap !important;
                    width: max-content;
                    animation: marquee-scroll 25s linear infinite;
                    padding: 0;
                    margin: 0;
                    list-style: none;
                }
                .marquee-list li {
                    flex-shrink: 0;
                    width: 160px !important;
                    margin-right: 20px;
                }
                .marquee-list:hover {
                    animation-play-state: paused;
                }
            `}</style>

            <div className="container">
                <div className="row">
                    <div className="col-xl-6 col-sm-9">
                        <div className="section_heading_2 section_heading">
                            <h3>Our Top <span>Brands</span></h3>
                        </div>
                    </div>
                    <div className="col-xl-6 col-sm-3">
                        <div className="view_all_btn_area">
                            <Link className="view_all_btn" to="/brand">View all</Link>
                        </div>
                    </div>
                </div>
                <div className="row mt_40">
                    <div className="col-12">
                        <div className="marquee-container">
                            <ul className="marquee-list">
                                {doubledBrands.map((brand, idx) => (
                                    <li key={idx} className="wow fadeInUp">
                                        <Link to="/shop">
                                            <img src={brand.img} alt="Brand" className="img-fluid" />
                                        </Link>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}


