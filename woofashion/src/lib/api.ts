import axios from 'axios';

// The global wpData is injected via functions.php
declare global {
  interface Window {
    wpData: {
      apiUrl: string;
      nonce: string;
      homeUrl: string;
    };
  }
}

const api = axios.create({
  baseURL: window.wpData?.apiUrl || '/wp-json',
  headers: {
    'X-WP-Nonce': window.wpData?.nonce || '',
    'Content-Type': 'application/json',
  },
});

export const getProducts = async () => {
  // WooCommerce REST API endpoint for products
  const response = await api.get('/wc/v3/products');
  return response.data;
};

// You can add more endpoints like addToCart, getCart, checkout etc.
// export const addToCart = async (productId, quantity) => ...

export default api;
