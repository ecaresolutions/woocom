import { Link } from 'react-router-dom';
import { ArrowRight, MessageSquare } from 'lucide-react';

export default function BlogSection() {
    const blogs = [
        {
            id: 1,
            author: "Adnan Alvi",
            date: "12 Mar 2025",
            title: "How to Plop Hair for Bouncy, Beautiful Curls",
            img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/blog_img_1.png",
            comments: 15
        },
        {
            id: 2,
            author: "Hasib Sing",
            date: "20 Apr 2025",
            title: "Fast fashion: How clothes are linked to climate change",
            img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/blog_img_2.png",
            comments: 42
        },
        {
            id: 3,
            author: "Smith Jhon",
            date: "07 Mar 2025",
            title: "Which foundation formula is right for your skin?",
            img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/blog_img_3.png",
            comments: 36
        },
        {
            id: 4,
            author: "Jhon Deo",
            date: "24 Apr 2025",
            title: "How To Choose The Right Sofa for your home",
            img: window.wpData?.homeUrl + "/wp-content/themes/woofashion-spa/public/zenis/images/blog_img_4.png",
            comments: 15
        }
    ];

    return (
        <section className="blog_2 pt_95">
            <div className="container">
                <div className="row">
                    <div className="col-xl-6 col-sm-9">
                        <div className="section_heading_2 section_heading">
                            <h3>Our <span>News</span> & Articles</h3>
                        </div>
                    </div>
                    <div className="col-xl-6 col-sm-3">
                        <div className="view_all_btn_area">
                            <Link className="view_all_btn" to="/blog">View all</Link>
                        </div>
                    </div>
                </div>
                <div className="row mt_15">
                    {blogs.map((blog) => (
                        <div key={blog.id} className="col-lg-4 col-xxl-3 col-md-6 wow fadeInUp">
                            <div className="blog_item">
                                <Link to={`/blog/${blog.id}`} className="blog_img">
                                    <img src={blog.img} alt="blog" className="img-fluid w-100" />
                                </Link>
                                <div className="blog_text">
                                    <ul className="top">
                                        <li>
                                            <span>
                                                <img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/user_icon_black.svg'} alt="user" className="img-fluid w-100" />
                                            </span>
                                            {blog.author}
                                        </li>
                                        <li>
                                            <span>
                                                <img src={window.wpData?.homeUrl + '/wp-content/themes/woofashion-spa/public/zenis/images/calender.png'} alt="Message" className="img-fluid w-100" />
                                            </span>
                                            {blog.date}
                                        </li>
                                    </ul>
                                    <Link className="title" to={`/blog/${blog.id}`}>
                                        {blog.title}
                                    </Link>
                                    <ul className="bottom">
                                        <li>
                                            <Link to={`/blog/${blog.id}`} className="d-inline-flex align-items-center">
                                                read more <ArrowRight size={14} className="ms-1" />
                                            </Link>
                                        </li>
                                        <li>
                                            <span className="d-inline-flex align-items-center"><MessageSquare size={14} className="me-1" /> {blog.comments} Comments</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}




