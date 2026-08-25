import { Link } from 'react-router-dom';
import ShopLayout from '@/layouts/shop-layout';

export default function NotFound() {
    return (
        <ShopLayout isLoaded={true}>
            <div className="container py-5 text-center mt-5 mb-5" style={{ minHeight: '50vh', display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center' }}>
                <h1 className="display-1 fw-bold text-muted mb-3">404</h1>
                <h2 className="h3 mb-4">Page Under Construction / Not Found</h2>
                <p className="text-muted mb-4">
                    The page you are looking for has not been migrated to the React SPA yet, or it does not exist.
                </p>
                <Link to="/" className="common_btn text-white text-decoration-none" style={{ display: 'inline-block', padding: '10px 25px', borderRadius: '5px' }}>
                    Go Back Home
                </Link>
            </div>
        </ShopLayout>
    );
}
