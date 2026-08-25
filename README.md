# 🛍️ Woocom — Next-Gen WooCommerce Themes & SPA Ecosystem

An advanced WooCommerce e-commerce solution offering both a high-performance **Classic WordPress Theme** and a modern **React SPA Headless/Hybrid Theme**.

---

## 📁 Repository Structure

`	ext
woocom/
├── woocom/                # Classic WooCommerce WordPress Theme
│   ├── assets/            # CSS, JavaScript & Assets
│   ├── inc/               # Theme core functions, settings & API handlers
│   ├── woocommerce/       # Custom WooCommerce template overrides
│   ├── functions.php      # WordPress Theme Setup & Hooks
│   ├── header.php         # Header Template
│   ├── footer.php         # Footer Template
│   └── style.css          # Theme metadata and stylesheet
│
└── woofashion/            # React 19 + Vite SPA Theme for WooCommerce
    ├── dist/              # Compiled production build assets
    ├── src/               # React Source Code (Components, Pages, Hooks, Lib)
    │   ├── components/    # Reusable UI & Shop components
    │   ├── layouts/       # Shop & App Layouts
    │   ├── pages/         # Shop, Checkout, Auth, and Account views
    │   └── services/      # REST API & WooCommerce integration
    ├── inc/               # WordPress PHP bridge & REST endpoints
    ├── functions.php      # Vite asset loader & WP enqueue setup
    ├── package.json       # Dependencies & NPM scripts
    └── vite.config.ts     # Vite configuration & React plugin
`

---

## ✨ Features & Packages

### 1. 👗 woofashion — React SPA Theme
- **Framework**: Built with **React 19**, **TypeScript**, and **Vite**.
- **Styling**: **Tailwind CSS v4** & **Radix UI** primitives for high accessibility and performance.
- **Dynamic Storefront**:
  - Hero Slider & Category Carousel
  - Real-time Product Filter & Instant Search
  - Flash Sales & Special Offer countdowns
  - Interactive Mini Cart & Seamless Checkout
  - User Authentication, Profile & 2FA Management
- **WordPress Integration**: Seamlessly runs as a WordPress theme or headless frontend.

### 2. 🛒 woocom — Classic WooCommerce Theme (Woocom X Shantobazar)
- Optimized specifically for e-commerce, grocery, and fashion shops.
- Full custom WooCommerce templates override (rchive-product, single-product, checkout, mini-cart).
- Fast page loads, mobile-responsive layout, and built-in Bengali typography support (Noto Serif Bengali).
- Custom theme options panel in WordPress admin.

---

## 🚀 Getting Started

### Setting Up woofashion (Development & Build)

1. Navigate to the woofashion directory:
   `ash
   cd woofashion
   `

2. Install dependencies:
   `ash
   npm install
   `

3. Start development server (with HMR):
   `ash
   npm run dev
   `

4. Build for production:
   `ash
   npm run build
   `
   > The production build outputs directly to woofashion/dist which is automatically enqueued by woofashion/functions.php.

---

## ⚙️ WordPress Installation

1. Copy either woocom or woofashion folder to your WordPress themes directory:
   `	ext
   wp-content/themes/
   `
2. In your WordPress Admin Dashboard, navigate to **Appearance > Themes**.
3. Activate your desired theme (**Woocom** or **WooFashion SPA**).
4. Ensure **WooCommerce** plugin is installed and activated.

---

## 🛠️ Tech Stack

- **Frontend**: React 19, TypeScript, Tailwind CSS, Vite, Lucide Icons, Radix UI
- **Backend / CMS**: WordPress, WooCommerce REST API, PHP 8+
- **Styling & Effects**: Tailwind CSS, PostCSS, Animate.css

---

## 📄 License & Credits

Developed and maintained by **[Ecare Solution](https://ecare.com.bd)**.  
Licensed under GNU General Public License v2 or later.
