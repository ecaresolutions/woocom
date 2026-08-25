import { Routes, Route } from 'react-router-dom';
import { Toaster } from 'sonner';

import Home from './pages/welcome';
import ShopIndex from './pages/shop/index';
import ShopShow from './pages/shop/show';
import Checkout from './pages/shop/checkout';
import TrackOrder from './pages/shop/track-order';

import NotFound from './pages/not-found';

export default function App() {
  return (
    <>
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/shop" element={<ShopIndex />} />
        <Route path="/category" element={<ShopIndex />} />
        <Route path="/category/:categorySlug" element={<ShopIndex />} />
        <Route path="/product-category/:categorySlug" element={<ShopIndex />} />
        <Route path="/shop/category/:categorySlug" element={<ShopIndex />} />
        <Route path="/shop/product/:slug" element={<ShopShow />} />
        <Route path="/product/:slug" element={<ShopShow />} />
        <Route path="/checkout" element={<Checkout />} />
        <Route path="/track-order" element={<TrackOrder />} />
        <Route path="*" element={<NotFound />} />
      </Routes>
      <Toaster />
    </>
  );
}
