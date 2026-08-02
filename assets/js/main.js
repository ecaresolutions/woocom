function initWoocomTheme() {
    // Constants
    // Settings from WordPress
    const settings = window.woocom_settings || {
        cart_promo_enabled: false,
        cart_promo_title: 'Get a Free Gift!',
        cart_promo_min_amount: 3000
    };
    const PROMO_THRESHOLD = settings.cart_promo_min_amount;
    
    // Selectors
    const cartDrawer = document.getElementById('cart-drawer');
    const cartOverlay = document.getElementById('cart-drawer-overlay');
    const cartCloseBtn = document.getElementById('cart-drawer-close');
    const stickyWidget = document.getElementById('cart-drawer-open-sticky');
    const desktopCartBtn = document.getElementById('cart-drawer-open-desktop');
    const mobileCartBtn = document.getElementById('cart-drawer-open-mobile');
    const bottomCartBtn = document.getElementById('cart-drawer-open-bottom');
    const bottomMenuBtn = document.getElementById('mobile-bottom-menu-open');
    const bottomSearchBtn = document.getElementById('mobile-bottom-search-open');

    // Mobile Menu Selectors (Corrected based on header.php)
    const mobileDrawer = document.getElementById('mobile-drawer');
    const mobileDrawerOverlay = document.getElementById('mobile-drawer-overlay');
    const mobileDrawerContent = document.getElementById('mobile-drawer-content');
    const mobileMenuOpenBtn = document.getElementById('mobile-menu-open');
    const mobileMenuCloseBtn = document.getElementById('mobile-menu-close');

    // Search Overlay Selectors
    const searchOverlay = document.getElementById('mobile-search-overlay');
    const searchContainer = document.getElementById('mobile-search-container');
    const searchCloseBtn = document.getElementById('mobile-search-close');
    const searchInput = document.getElementById('mobile-search-input');

    // Mobile Menu Event Listeners
    if (mobileMenuOpenBtn) {
        mobileMenuOpenBtn.addEventListener('click', () => {
            if (!mobileDrawer || !mobileDrawerOverlay || !mobileDrawerContent) {
                console.error('Mobile drawer elements not found');
                return;
            }
            mobileDrawer.classList.remove('invisible');
            setTimeout(() => {
                mobileDrawerOverlay.style.opacity = '1';
                mobileDrawerContent.style.transform = 'translateX(0)';
            }, 10);
        });
    }

    if (mobileMenuCloseBtn) {
        mobileMenuCloseBtn.addEventListener('click', closeMobileMenu);
    }
    
    if (mobileDrawerOverlay) {
        mobileDrawerOverlay.addEventListener('click', closeMobileMenu);
    }

    function closeMobileMenu() {
        if (!mobileDrawer || !mobileDrawerOverlay || !mobileDrawerContent) return;
        mobileDrawerOverlay.style.opacity = '0';
        mobileDrawerContent.style.transform = 'translateX(-100%)';
        setTimeout(() => {
            mobileDrawer.classList.add('invisible');
        }, 300);
    }
    
    // Cart State
    console.log('woocom: cartDrawer =', cartDrawer, 'cartOverlay =', cartOverlay);

    let cartState = {
        items: [],
        total: 0
    };

    // Open Drawer function
    function openCartDrawer() {
        console.log('woocom: openCartDrawer called');
        if (window.woocom_suppress_drawer) {
            window.woocom_suppress_drawer = false;
            console.log('woocom: openCartDrawer suppressed by flag');
            return;
        }
        if (!cartDrawer || !cartOverlay) {
            console.warn('woocom: openCartDrawer aborted, missing drawer or overlay element');
            return;
        }
        cartOverlay.style.display = 'block';
        cartDrawer.classList.remove('translate-x-full');
        setTimeout(() => {
            cartOverlay.style.opacity = '1';
            cartDrawer.style.transform = 'translateX(0)';
            console.log('woocom: openCartDrawer styles applied');
        }, 10);
    }

    // Close Drawer function
    function closeCartDrawer() {
        console.log('woocom: closeCartDrawer called');
        if (!cartDrawer || !cartOverlay) return;
        cartOverlay.style.opacity = '0';
        cartDrawer.style.transform = 'translateX(100%)';
        setTimeout(() => {
            cartOverlay.style.display = 'none';
            cartDrawer.classList.add('translate-x-full');
            console.log('woocom: closeCartDrawer completed');
        }, 300);
    }

    // Event Listeners
    if (cartCloseBtn) cartCloseBtn.addEventListener('click', closeCartDrawer);
    if (cartOverlay) cartOverlay.addEventListener('click', closeCartDrawer);
    
    [stickyWidget, desktopCartBtn, mobileCartBtn, bottomCartBtn].forEach(btn => {
        if (btn) btn.addEventListener('click', (e) => {
            e.preventDefault();
            openCartDrawer();
        });
    });

    // Mobile Bottom Menu Trigger
    if (bottomMenuBtn && mobileMenuOpenBtn) {
        bottomMenuBtn.addEventListener('click', () => {
            mobileMenuOpenBtn.click();
        });
    }

    // Mobile Bottom Search Trigger
    if (bottomSearchBtn) {
        bottomSearchBtn.addEventListener('click', openSearchOverlay);
    }

    if (searchCloseBtn) searchCloseBtn.addEventListener('click', closeSearchOverlay);
    if (searchOverlay) searchOverlay.addEventListener('click', closeSearchOverlay);

    function openSearchOverlay() {
        if (!searchOverlay || !searchContainer) return;
        searchOverlay.classList.remove('invisible');
        setTimeout(() => {
            searchOverlay.style.opacity = '1';
            searchContainer.style.transform = 'translateY(0)';
            if (searchInput) searchInput.focus();
        }, 10);
    }

    function closeSearchOverlay() {
        if (!searchOverlay || !searchContainer) return;
        searchOverlay.style.opacity = '0';
        searchContainer.style.transform = 'translateY(-100%)';
        setTimeout(() => {
            searchOverlay.classList.add('invisible');
        }, 300);
    }



    // Cart Rendering Functions
    function cartFormatPrice(num) {
        const decimals = Number(window.woocom_ajax && woocom_ajax.price_decimals);
        const fractionDigits = Number.isInteger(decimals) && decimals >= 0 ? decimals : 2;
        const decimalSeparator = (window.woocom_ajax && woocom_ajax.price_decimal_separator) || '.';
        const thousandSeparator = (window.woocom_ajax && woocom_ajax.price_thousand_separator) || ',';
        const fixed = (Number(num) || 0).toFixed(fractionDigits);
        const parts = fixed.split('.');

        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandSeparator);

        return fractionDigits > 0 ? parts.join(decimalSeparator) : parts[0];
    }

    function cartRenderItems() {
        const list = document.getElementById('cart-items-list');
        const emptyState = document.getElementById('cart-empty-state');
        if (!list) return;

        list.innerHTML = '';

        const crossSells = document.getElementById('cart-drawer-cross-sells-container');
        const footer = document.getElementById('cart-drawer-footer');

        if (cartState.items.length === 0) {
            if (emptyState) { emptyState.classList.remove('hidden'); emptyState.classList.add('flex'); }
            if (crossSells) { crossSells.classList.add('hidden'); }
            if (footer) { footer.classList.add('hidden'); }
            return;
        }
        if (emptyState) { emptyState.classList.add('hidden'); emptyState.classList.remove('flex'); }
        if (crossSells) { crossSells.classList.remove('hidden'); }
        if (footer) { footer.classList.remove('hidden'); }

        cartState.items.forEach((item, idx) => {
            const imageUrl = item.image || (window.woocom_ajax && woocom_ajax.placeholder_image) || '';
            const el = document.createElement('div');
            el.className = 'bg-white border border-gray-100 rounded-xl p-3 flex gap-3 shadow-sm relative mb-3 hover:border-gray-200 transition-colors';
            el.innerHTML = `
                <button class="cart-remove-btn absolute top-2 right-2 transition-colors" style="color: #ef4444;" data-idx="${idx}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                </button>
                <div class="w-16 h-16 bg-gray-50 rounded-lg border border-gray-100 flex-shrink-0 flex items-center justify-center p-1 overflow-hidden">
                    <img src="${imageUrl}" alt="${item.title}" class="max-w-full max-h-full object-contain mix-blend-multiply">
                </div>
                <div class="flex-grow pt-0.5 pr-5">
                    <a href="${item.permalink}" class="hover:text-secondary">
                        <h3 class="text-[13px] text-gray-800 font-medium leading-tight mb-2 line-clamp-2">${item.title}</h3>
                    </a>
                    <div class="flex items-center gap-3 flex-wrap">
                        <div class="flex items-center border border-gray-200 rounded-md h-7 overflow-hidden">
                            <button class="cart-qty-btn px-2 text-gray-500 hover:bg-gray-100 h-full flex items-center justify-center transition-colors" data-idx="${idx}" data-action="dec">−</button>
                            <span class="cart-qty-display w-7 text-center text-[13px] font-semibold border-x border-gray-200 h-full flex items-center justify-center">${item.qty}</span>
                            <button class="cart-qty-btn px-2 text-gray-500 hover:bg-gray-100 h-full flex items-center justify-center transition-colors" data-idx="${idx}" data-action="inc">+</button>
                        </div>
                        <div class="text-[12px] text-gray-600 flex items-center gap-1">
                            <span class="text-gray-400 text-[10px]">×</span>
                            <span class="text-secondary font-bold text-sm">৳${cartFormatPrice(item.price)}</span>
                            <span class="text-gray-400 mx-1">=</span>
                            <span class="font-bold text-[14px] text-gray-800">৳${cartFormatPrice(item.price * item.qty)}</span>
                        </div>
                    </div>
                </div>`;
            list.appendChild(el);
        });

        // Re-bind buttons
        list.querySelectorAll('.cart-remove-btn').forEach(btn => btn.onclick = () => cartRemoveItem(btn.dataset.idx));
        list.querySelectorAll('.cart-qty-btn').forEach(btn => btn.onclick = () => cartUpdateQty(btn.dataset.idx, btn.dataset.action));
    }

    function cartUpdateTotals() {
        const drawerTotal = document.getElementById('cart-drawer-total');
        if (drawerTotal) drawerTotal.textContent = '৳' + cartFormatPrice(cartState.total);

        const stickyCount = document.getElementById('sticky-cart-count');
        const stickyTotal = document.getElementById('sticky-cart-total');
        const totalQty = cartState.items.reduce((s, i) => s + i.qty, 0);
        if (stickyCount) stickyCount.textContent = totalQty + ' Items';
        if (stickyTotal) stickyTotal.innerHTML = '<span class="mr-0.5">৳</span>' + cartFormatPrice(cartState.total);

        // Header counts
        document.querySelectorAll('.cart-count-global').forEach(el => {
            el.textContent = totalQty;
        });

        const promoProgress = document.getElementById('cart-promo-progress');
        const promoText = document.getElementById('cart-promo-text');
        if (promoProgress) {
            const pct = Math.min((cartState.total / PROMO_THRESHOLD) * 100, 100);
            promoProgress.style.width = pct + '%';
        }
        if (promoText) {
            const remaining = PROMO_THRESHOLD - cartState.total;
            promoText.innerHTML = remaining <= 0 ? `🎉 ${settings.cart_promo_title}` : `Add <span class="text-secondary font-bold">৳${cartFormatPrice(remaining)}</span> more to unlock!`;
        }
    }

    function cartRenderAll() {
        cartRenderItems();
        cartUpdateTotals();
    }

    function cartUpdateStateAndCrossSells(data) {
        if (data && data.success) {
            cartState.items = data.data.items;
            cartState.total = data.data.total;
            cartRenderAll();
            
            if (data.data.cross_sell_html) {
                const csTrack = document.getElementById('cross-sell-track');
                if (csTrack) {
                    csTrack.innerHTML = data.data.cross_sell_html;
                }
            }
        }
    }

    // Optimistic adds removed. Cart state is now always synced from server.

    function cartRemoveItem(idx) {
        const item = cartState.items[idx];
        
        // AJAX to server
        fetch(`${woocom_ajax.ajax_url}?action=woocom_remove_cart_item&cart_item_key=${encodeURIComponent(item.key)}&nonce=${woocom_ajax.cart_nonce}`)
            .then(() => {
                fetch(`${woocom_ajax.ajax_url}?action=woocom_get_cart_data&nonce=${woocom_ajax.cart_nonce}`)
                    .then(res => res.json())
                    .then(data => cartUpdateStateAndCrossSells(data));
            })
            .catch(err => console.error('Error removing item:', err));

        cartState.total -= item.price * item.qty;
        cartState.items.splice(idx, 1);
        cartRenderAll();
        
        // Update Woo Fragments
        if (window.jQuery) jQuery(document.body).trigger('removed_from_cart');
    }

    function cartUpdateQty(idx, action) {
        const item = cartState.items[idx];
        let newQty = item.qty;

        if (action === 'inc') {
            newQty++;
            cartState.total += item.price;
        } else {
            if (item.qty > 1) {
                newQty--;
                cartState.total -= item.price;
            } else {
                cartRemoveItem(idx);
                return;
            }
        }
        
        item.qty = newQty;

        // AJAX to server
        fetch(`${woocom_ajax.ajax_url}?action=woocom_update_cart_qty&cart_item_key=${encodeURIComponent(item.key)}&qty=${newQty}&nonce=${woocom_ajax.cart_nonce}`)
            .catch(err => console.error('Error updating qty:', err));

        cartRenderAll();
    }

    // Buy Now Button
    if (window.jQuery) {
        jQuery(document.body).on('click', '.buy-now-button, .buy_now_button', function(e) {
            const $btn = jQuery(this);
            // Skip single product page buy now buttons since they submit their own forms/logic
            if ($btn.closest('form.cart').length > 0) {
                return;
            }
            
            e.preventDefault();
            const productId = $btn.attr('data-product_id') || $btn.data('product_id');
            
            // Get selected quantity from input if available
            const qtyInput = document.querySelector('.qty-input, input[name="quantity"], .quantity input[type="number"]');
            const qty = qtyInput ? (parseInt(qtyInput.value) || 1) : 1;

            $btn.addClass('loading opacity-50 cursor-not-allowed').prop('disabled', true);

            const formData = new FormData();
            formData.append('action', 'woocommerce_add_to_cart');
            formData.append('product_id', productId);
            formData.append('quantity', qty);

            const ajaxUrl = (window.woocom_ajax && window.woocom_ajax.ajax_url) || '/wp-admin/admin-ajax.php';

            fetch(`${ajaxUrl}?action=woocommerce_add_to_cart`, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    if (data.product_url) {
                        window.location.href = data.product_url;
                        return;
                    }

                    alert((window.woocom_ajax && woocom_ajax.variation_unavailable_message) || 'Sorry, this product is unavailable. Please choose a different combination.');
                    $btn.removeClass('loading opacity-50 cursor-not-allowed').prop('disabled', false);
                    return;
                }
                // Redirect to checkout
                window.location.href = woocom_ajax.checkout_url;
            })
            .catch(err => {
                console.error('Buy now error:', err);
                $btn.removeClass('loading opacity-50 cursor-not-allowed').prop('disabled', false);
            });
        });
    }

    // Init Cart
    if (window.woocom_initial_cart) {
        cartState.items = window.woocom_initial_cart.items;
        cartState.total = window.woocom_initial_cart.total;
        cartRenderAll();
    }

    document.querySelectorAll('.woocom-stock-request-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const button = form.querySelector('button[type="submit"]');
            const message = form.querySelector('.woocom-stock-request-message');
            const phone = form.querySelector('input[name="phone"]');
            const quantity = form.querySelector('input[name="quantity"]');
            const originalText = button ? button.textContent : '';

            if (button) {
                button.disabled = true;
                button.textContent = 'Saving...';
            }
            if (message) {
                message.textContent = '';
                message.classList.remove('is-error', 'is-success');
            }

            const formData = new FormData();
            formData.append('action', 'woocom_stock_request');
            formData.append('nonce', woocom_ajax.stock_request_nonce || '');
            formData.append('product_id', form.dataset.productId || '');
            formData.append('request_type', form.dataset.requestType || '');
            formData.append('phone', phone ? phone.value : '');
            formData.append('quantity', quantity ? quantity.value : '1');
            formData.append('page_url', window.location.href);

            fetch(woocom_ajax.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (message) {
                    message.textContent = data && data.data && data.data.message ? data.data.message : '';
                    message.classList.add(data.success ? 'is-success' : 'is-error');
                }
                if (data.success && phone) {
                    phone.value = '';
                }
            })
            .catch(() => {
                if (message) {
                    message.textContent = 'Could not save your request. Please try again.';
                    message.classList.add('is-error');
                }
            })
            .finally(() => {
                if (button) {
                    button.disabled = false;
                    button.textContent = originalText;
                }
            });
        });
    });

    // Single Product AJAX Add to Cart
    const singleAddToCartBtn = document.getElementById('single-add-to-cart-btn');
    if (singleAddToCartBtn) {
        const form = singleAddToCartBtn.closest('form');
        const handleSingleAddToCart = function(e) {
            e.preventDefault();
            if (e.stopImmediatePropagation) {
                e.stopImmediatePropagation();
            }

            const btn = singleAddToCartBtn;
            if (btn.dataset.adding === '1') {
                return;
            }

            const originalContent = btn.innerHTML;

            const formData = new FormData(form);
            formData.append('action', 'woocommerce_add_to_cart');

            if (!formData.get('product_id') && formData.get('add-to-cart')) {
                formData.set('product_id', formData.get('add-to-cart'));
            }
            formData.delete('add-to-cart');

            const variationId = formData.get('variation_id');
            if (form.classList.contains('variations_form')) {
                if (!variationId || variationId === '0') {
                    alert('Please select a variant first.');
                    return;
                }
                formData.set('product_id', variationId);
            }

            Array.from(formData.entries()).forEach(([key, value]) => {
                if (key.indexOf('attribute_') === 0) {
                    formData.append(`variation[${key}]`, value);
                }
            });

            btn.innerHTML = '<span class="flex items-center gap-2"><svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Adding...</span>';
            btn.dataset.adding = '1';
            btn.disabled = true;

            const addToCartUrl = woocom_ajax.wc_ajax_url
                ? woocom_ajax.wc_ajax_url.replace('%%endpoint%%', 'add_to_cart')
                : `${window.location.origin}/?wc-ajax=add_to_cart`;

            fetch(addToCartUrl, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btn.innerHTML = originalContent;
                btn.dataset.adding = '0';
                btn.disabled = false;

                if (data.error && data.product_url) {
                    window.location.href = data.product_url;
                    return;
                }

                // Open Drawer First
                openCartDrawer();

                // Fire analytics add_to_cart event
                if (window.jQuery && window.woocomAnalyticsProduct) {
                    const p = window.woocomAnalyticsProduct;
                    jQuery(document.body).trigger('woocom_add_to_cart_tracked', [{
                        id: p.id,
                        name: p.name,
                        price: p.price,
                        quantity: parseInt(formData.get('quantity') || 1)
                    }]);
                }

                // Refresh state from server
                fetch(`${woocom_ajax.ajax_url}?action=woocom_get_cart_data&nonce=${woocom_ajax.cart_nonce}`)
                    .then(res => res.json())
                    .then(data => cartUpdateStateAndCrossSells(data));

                // Trigger Woo Fragments refresh
                if (window.jQuery) {
                    jQuery(document.body).trigger('added_to_cart', [data.fragments, data.cart_hash, jQuery(btn)]);
                }
            })
            .catch(err => {
                console.error(err);
                btn.innerHTML = originalContent;
                btn.dataset.adding = '0';
                btn.disabled = false;
            });
        };

        if (form.classList.contains('variations_form')) {
            singleAddToCartBtn.addEventListener('click', handleSingleAddToCart);
        } else {
            form.addEventListener('submit', handleSingleAddToCart);
        }
    }

    // Quantity selector buttons logic handled in content-single-product.php

    // Custom AJAX Add to Cart handler for all archive/home pages
    if (window.jQuery) {
        jQuery(document.body).on('click', '.woocom-custom-add-to-cart', function(e) {
            e.preventDefault();
            const $btn = jQuery(this);
            if ($btn.hasClass('loading')) return;
            
            $btn.addClass('loading opacity-50 cursor-not-allowed').prop('disabled', true);
            
            const productId = $btn.attr('data-product_id') || $btn.data('product_id');
            
            // Fetch quantity directly from sibling input if available
            const $card = $btn.closest('.latest-product-card');
            const $qtyInput = $card.length ? $card.find('.qty-input') : [];
            const quantity = $qtyInput.length ? (parseInt($qtyInput.val()) || 1) : (parseInt($btn.attr('data-quantity')) || 1);
            
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', quantity);
            
            const addToCartUrl = woocom_ajax.wc_ajax_url
                ? woocom_ajax.wc_ajax_url.replace('%%endpoint%%', 'add_to_cart')
                : `${window.location.origin}/?wc-ajax=add_to_cart`;
                
            fetch(addToCartUrl, {
                method: 'POST',
                body: formData
            })
            .then(res => {
                const contentType = res.headers.get("content-type");
                if (contentType && contentType.indexOf("application/json") !== -1) {
                    return res.json();
                } else {
                    return res.text().then(text => { return { error: false, htmlResponse: text }; });
                }
            })
            .then(data => {
                $btn.removeClass('loading opacity-50 cursor-not-allowed').prop('disabled', false);
                if (data.error && data.product_url) {
                    window.location.href = data.product_url;
                    return;
                }
                
                // Refresh cart state from server
                fetch(`${woocom_ajax.ajax_url}?action=woocom_get_cart_data&nonce=${woocom_ajax.cart_nonce}`)
                    .then(res => res.json())
                    .then(data => cartUpdateStateAndCrossSells(data));
                    
                // Trigger fragments refresh
                if (data.fragments) {
                    jQuery(document.body).trigger('added_to_cart', [data.fragments, data.cart_hash, $btn]);
                } else {
                    jQuery(document.body).trigger('added_to_cart', [{}, '', $btn]);
                }
            })
            .catch(err => {
                console.error('Add to Cart Error:', err);
                $btn.removeClass('loading opacity-50 cursor-not-allowed').prop('disabled', false);
                
                fetch(`${woocom_ajax.ajax_url}?action=woocom_get_cart_data&nonce=${woocom_ajax.cart_nonce}`)
                    .then(res => res.json())
                    .then(data => cartUpdateStateAndCrossSells(data));
            });
        });
    }

    // Global listener for WooCommerce "Added to Cart" event (for Home/Shop pages)
    if (window.jQuery) {
        jQuery(document.body).on('added_to_cart', function(event, fragments, cart_hash, $button) {
            // Only handle if it's NOT from our single product manual AJAX (to avoid double calls)
            if ($button && $button.attr('id') === 'single-add-to-cart-btn') return;
            
            // Refresh state from server to be safe
            fetch(`${woocom_ajax.ajax_url}?action=woocom_get_cart_data&nonce=${woocom_ajax.cart_nonce}`)
                .then(res => res.json())
                .then(data => cartUpdateStateAndCrossSells(data));
        });
    }

    // Initialize Swiper Sliders
    if (typeof Swiper !== 'undefined') {
        // Hero Slider
        new Swiper('.hero-swiper', {
            loop: true,
            speed: 600,
            autoplay: { delay: 4000, disableOnInteraction: false },
            pagination: { el: '.hero-pagination', clickable: true },
            navigation: {
                nextEl: '.hero-next',
                prevEl: '.hero-prev',
            },
            slidesPerView: 1,
            spaceBetween: 0
        });

        // Featured Products Slider
        new Swiper('.featured-swiper', {
            slidesPerView: 2,
            spaceBetween: 10,
            pagination: { el: '.featured-pagination', clickable: true },
            breakpoints: {
                640: { slidesPerView: 2.5, spaceBetween: 12 },
                768: { slidesPerView: 3.5, spaceBetween: 15 },
                1024: { slidesPerView: 5, spaceBetween: 15 },
                1280: { slidesPerView: 5, spaceBetween: 20 }
            }
        });

        // Category Slider
        new Swiper('.category-swiper', {
            slidesPerView: 'auto',
            spaceBetween: 15,
            pagination: { el: '.category-pagination', clickable: true },
            breakpoints: {
                320: { slidesPerView: 2.2, spaceBetween: 10 },
                480: { slidesPerView: 3.2, spaceBetween: 12 },
                768: { slidesPerView: 4.2, spaceBetween: 15 },
                1024: { slidesPerView: 5.2, spaceBetween: 15 },
                1280: { slidesPerView: 6.2, spaceBetween: 15 }
            }
        });


        // Dynamic Category Sliders
        document.querySelectorAll('.dynamic-category-swiper').forEach(el => {
            const catId = el.dataset.categoryId;
            new Swiper(el, {
                slidesPerView: 2,
                spaceBetween: 10,
                pagination: { el: `.dynamic-pagination-${catId}`, clickable: true },
                breakpoints: {
                    640: { slidesPerView: 2.5, spaceBetween: 12 },
                    768: { slidesPerView: 3.2, spaceBetween: 15 },
                    1024: { slidesPerView: 4, spaceBetween: 15 },
                    1280: { slidesPerView: 5, spaceBetween: 20 }
                }
            });
        });

        // Combo Deals Slider
        new Swiper('.combo-swiper', {
            slidesPerView: 1.2,
            spaceBetween: 15,
            pagination: { el: '.combo-pagination', clickable: true },
            breakpoints: {
                480: { slidesPerView: 1.5, spaceBetween: 15 },
                768: { slidesPerView: 2.5, spaceBetween: 20 },
                1024: { slidesPerView: 3, spaceBetween: 20 },
                1280: { slidesPerView: 4, spaceBetween: 24 }
            }
        });

        // Reels Slider
        new Swiper('.reels-swiper', {
            slidesPerView: 'auto',
            spaceBetween: 12,
            freeMode: true,
            pagination: { el: '.reels-pagination', clickable: true },
            breakpoints: {
                320: { slidesPerView: 2.2, spaceBetween: 10 },
                480: { slidesPerView: 3.2, spaceBetween: 12 },
                768: { slidesPerView: 4.2, spaceBetween: 15 },
                1024: { slidesPerView: 5.2, spaceBetween: 15 },
                1280: { slidesPerView: 6.2, spaceBetween: 15 }
            }
        });
    }

    // Load More functionality
    const loadMoreBtn = document.getElementById('foryou-load-more');
    if (loadMoreBtn) {
        let currentVisible = 10;
        const totalItems = document.querySelectorAll('.foryou-item').length;
        
        loadMoreBtn.addEventListener('click', () => {
            const items = document.querySelectorAll('.foryou-item');
            for (let i = currentVisible; i < currentVisible + 10 && i < totalItems; i++) {
                items[i].style.display = 'flex';
            }
            currentVisible += 10;
            if (currentVisible >= totalItems) loadMoreBtn.style.display = 'none';
        });
    }
    // Mobile Filter Drawer Logic
    const mobileFilterToggle = document.getElementById('mobile-filter-toggle');
    const mobileFilterClose = document.getElementById('mobile-filter-close');
    const shopSidebarContainer = document.getElementById('shop-sidebar-container');
    const shopSidebarContent = document.getElementById('shop-sidebar-content');

    if (mobileFilterToggle && shopSidebarContainer && shopSidebarContent) {
        mobileFilterToggle.addEventListener('click', () => {
            shopSidebarContainer.classList.remove('hidden', 'pointer-events-none');
            setTimeout(() => {
                shopSidebarContainer.classList.add('opacity-100');
                shopSidebarContent.classList.remove('-translate-x-full');
            }, 10);
            document.body.style.overflow = 'hidden';
        });

        const closeSidebar = () => {
            shopSidebarContainer.classList.remove('opacity-100');
            shopSidebarContent.classList.add('-translate-x-full');
            setTimeout(() => {
                shopSidebarContainer.classList.add('hidden', 'pointer-events-none');
            }, 300);
            document.body.style.overflow = '';
        };

        if (mobileFilterClose) mobileFilterClose.addEventListener('click', closeSidebar);
        shopSidebarContainer.addEventListener('click', (e) => {
            if (e.target === shopSidebarContainer) closeSidebar();
        });
    }

    // Sidebar Collapsible Sections
    const sidebarHeaders = document.querySelectorAll('.shop-sidebar .widget .cursor-pointer');
    sidebarHeaders.forEach(header => {
        header.addEventListener('click', () => {
            const widget = header.parentElement;
            widget.classList.toggle('is-collapsed');
        });
    });

    // Checkout Page Logic
    if (typeof jQuery !== 'undefined') {
        const $ = jQuery;
        
        // Checkout Quantity Update (Delegated)
        $(document.body).on('click', '.checkout-qty-plus, .checkout-qty-minus', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const $btn = $(this);
            const $input = $btn.siblings('input');
            const cart_item_key = $input.data('cart_item_key');
            let qty = parseInt($input.val());

            if ($btn.hasClass('checkout-qty-plus')) {
                qty++;
            } else {
                if (qty > 1) qty--;
            }

            $input.val(qty);

            $( '.woocommerce-checkout' ).addClass( 'processing' );

            $.ajax({
                type: 'POST',
                url: woocom_ajax.ajax_url,
                data: {
                    action: 'woocom_update_cart_qty',
                    cart_item_key: cart_item_key,
                    qty: qty,
                    nonce: woocom_ajax.cart_nonce
                },
                success: function() {
                    $(document.body).trigger('update_checkout');
                },
                complete: function() {
                    $( '.woocommerce-checkout' ).removeClass( 'processing' );
                }
            });
        });

        // Checkout Remove Item (Delegated)
        $(document.body).on('click', '.remove-item', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const $btn = $(this);
            const cart_item_key = $btn.data('cart_item_key');

            $( '.woocommerce-checkout' ).addClass( 'processing' );

            $.ajax({
                type: 'POST',
                url: woocom_ajax.ajax_url,
                data: {
                    action: 'woocom_remove_cart_item',
                    cart_item_key: cart_item_key,
                    nonce: woocom_ajax.cart_nonce
                },
                success: function() {
                    $(document.body).trigger('update_checkout');
                },
                complete: function() {
                    $( '.woocommerce-checkout' ).removeClass( 'processing' );
                }
            });
        });

        // Checkout Page AJAX Add to Cart for recommended products (Delegated)
        $(document.body).on('click', '.woocommerce-checkout .ajax_add_to_cart', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const $btn = $(this);
            const productId = $btn.data('product_id');

            if (!productId) return;

            // Show processing
            $( '.woocommerce-checkout' ).addClass( 'processing' );

            const addToCartUrl = woocom_ajax.wc_ajax_url
                ? woocom_ajax.wc_ajax_url.replace('%%endpoint%%', 'add_to_cart')
                : `${window.location.origin}/?wc-ajax=add_to_cart`;

            $.ajax({
                type: 'POST',
                url: addToCartUrl,
                data: {
                    product_id: productId,
                    quantity: 1
                },
                success: function(response) {
                    // Trigger update checkout to refresh order review list and totals
                    $(document.body).trigger('update_checkout');
                },
                complete: function() {
                    $( '.woocommerce-checkout' ).removeClass( 'processing' );
                }
            });
        });
    }

    // AJAX Search Logic
    const initSearch = (inputId, resultsId, formId) => {
        const searchInput = document.getElementById(inputId);
        const searchResults = document.getElementById(resultsId);
        const searchForm = formId ? document.getElementById(formId) : null;

        if (!searchInput || !searchResults) return;

        let debounceTimer;
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            clearTimeout(debounceTimer);

            if (query.length < 2) {
                searchResults.classList.add('hidden');
                searchResults.innerHTML = '';
                return;
            }

            debounceTimer = setTimeout(() => {
                searchResults.classList.remove('hidden');
                searchResults.innerHTML = '<div class="p-8 text-center"><div class="inline-block h-6 w-6 animate-spin rounded-full border-2 border-solid border-secondary border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]"></div><p class="mt-2 text-sm text-gray-500">Searching...</p></div>';

                const formData = new FormData();
                formData.append('action', 'woocom_ajax_search');
                formData.append('nonce', woocom_ajax.search_nonce);
                formData.append('query', query);

                const ajaxUrl = (window.woocom_ajax && window.woocom_ajax.ajax_url) || '/wp-admin/admin-ajax.php';

                fetch(ajaxUrl, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.text())
                .then(html => {
                    searchResults.innerHTML = html;
                })
                .catch(error => {
                    console.error('Search error:', error);
                    searchResults.classList.add('hidden');
                });
            }, 300);
        });

        if (searchForm) {
            document.addEventListener('click', function(e) {
                if (!searchForm.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.classList.add('hidden');
                }
            });
        }
    };

    // Initialize searches
    initSearch('desktop-search-input', 'search-results', 'ajax-search-form');
    initSearch('mobile-search-input', 'mobile-search-results');
    initSearch('mobile-header-search-input', 'mobile-header-search-results', 'mobile-header-search-form');

    // Back to Top Logic
    const backToTopBtn = document.getElementById('back-to-top');
    if (backToTopBtn) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 500) {
                backToTopBtn.classList.remove('opacity-0', 'translate-y-10', 'invisible');
            } else {
                backToTopBtn.classList.add('opacity-0', 'translate-y-10', 'invisible');
            }
        });

        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // Cross-sell Slider Logic
    const csTrack = document.getElementById('cross-sell-track');
    const csPrev = document.getElementById('cs-prev');
    const csNext = document.getElementById('cs-next');

    if (csTrack && csPrev && csNext) {
        const scrollStep = 180; // Approximate card width + gap
        let autoScrollTimer;

        const startAutoScroll = () => {
            stopAutoScroll();
            autoScrollTimer = setInterval(() => {
                if (csTrack.scrollLeft + csTrack.clientWidth >= csTrack.scrollWidth - 10) {
                    csTrack.scrollTo({ left: 0, behavior: 'smooth' });
                } else {
                    csTrack.scrollBy({ left: scrollStep, behavior: 'smooth' });
                }
            }, 3000);
        };

        const stopAutoScroll = () => {
            if (autoScrollTimer) clearInterval(autoScrollTimer);
        };

        csNext.addEventListener('click', () => {
            csTrack.scrollBy({ left: scrollStep, behavior: 'smooth' });
            stopAutoScroll();
            startAutoScroll();
        });

        csPrev.addEventListener('click', () => {
            csTrack.scrollBy({ left: -scrollStep, behavior: 'smooth' });
            stopAutoScroll();
            startAutoScroll();
        });

        // Pause on hover
        csTrack.addEventListener('mouseenter', stopAutoScroll);
        csTrack.addEventListener('mouseleave', () => {
            if (settings.cross_sell_autoslide) startAutoScroll();
        });

        if (settings.cross_sell_autoslide) {
            startAutoScroll();
        }
    }

    // Global Quantity adjustments inside product cards
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('qty-plus')) {
            const input = e.target.previousElementSibling;
            if (input && input.classList.contains('qty-input')) {
                let val = parseInt(input.value) || 1;
                input.value = val + 1;
                const container = e.target.closest('.latest-product-desktop-actions') || e.target.closest('.latest-product-card');
                if (container) {
                    const btn = container.querySelector('.woocom-custom-add-to-cart');
                    if (btn) {
                        btn.setAttribute('data-quantity', val + 1);
                    }
                }
            }
        } else if (e.target.classList.contains('qty-minus')) {
            const input = e.target.nextElementSibling;
            if (input && input.classList.contains('qty-input')) {
                let val = parseInt(input.value) || 1;
                if (val > 1) {
                    input.value = val - 1;
                    const container = e.target.closest('.latest-product-desktop-actions') || e.target.closest('.latest-product-card');
                    if (container) {
                        const btn = container.querySelector('.woocom-custom-add-to-cart');
                        if (btn) {
                            btn.setAttribute('data-quantity', val - 1);
                        }
                    }
                }
            }
        }
    });

    // Product Quick View Modal Logic
    const qvModal = document.getElementById('qv-modal');
    const qvOverlay = document.getElementById('qv-overlay');
    const qvClose = document.getElementById('qv-close');
    const qvContent = document.getElementById('qv-content');

    const openQvModal = () => {
        if (!qvModal) return;
        qvModal.classList.remove('hidden');
        setTimeout(() => {
            qvModal.classList.remove('opacity-0');
            const card = qvModal.querySelector('.relative.w-full');
            if (card) card.classList.remove('scale-95');
        }, 10);
    };

    const closeQvModal = () => {
        if (!qvModal) return;
        qvModal.classList.add('opacity-0');
        const card = qvModal.querySelector('.relative.w-full');
        if (card) card.classList.add('scale-95');
        setTimeout(() => {
            qvModal.classList.add('hidden');
            qvContent.innerHTML = `<svg class="animate-spin h-8 w-8 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 0 1 4 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>`;
        }, 300);
    };

    if (qvClose) qvClose.addEventListener('click', closeQvModal);
    if (qvOverlay) qvOverlay.addEventListener('click', closeQvModal);

    // Global listener for Quick View button click
    jQuery(document.body).on('click', '.woocom-quick-view-btn', function(e) {
        e.preventDefault();
        const productId = jQuery(this).attr('data-product_id');
        if (!productId) return;

        openQvModal();

        const ajaxUrl = (window.woocom_ajax && window.woocom_ajax.ajax_url) || '/wp-admin/admin-ajax.php';
        
        jQuery.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'woocom_quick_view',
                product_id: productId
            },
            success: function(res) {
                if (res.success && res.data.html) {
                    qvContent.innerHTML = res.data.html;
                    
                    // Bind Quick View Modal Internal interactions (Gallery & Quantity)
                    bindQvInteractions();
                } else {
                    qvContent.innerHTML = '<p class="text-red-500 font-bold">Failed to load product details.</p>';
                }
            },
            error: function() {
                qvContent.innerHTML = '<p class="text-red-500 font-bold">Error loading product details.</p>';
            }
        });
    });

    function bindQvInteractions() {
        // 1. Gallery Thumbnails switcher
        const thumbs = qvContent.querySelectorAll('.qv-thumb');
        const mainImg = qvContent.querySelector('#qv-main-img');
        thumbs.forEach(thumb => {
            thumb.onclick = function() {
                thumbs.forEach(t => t.classList.remove('border-secondary'));
                thumbs.forEach(t => t.classList.add('border-gray-200'));
                this.classList.remove('border-gray-200');
                this.classList.add('border-secondary');
                if (mainImg) mainImg.src = this.dataset.src;
            };
        });

        // 2. Quantity Selectors inside modal
        const qtyInput = qvContent.querySelector('.qv-qty-input');
        const plusBtn = qvContent.querySelector('.qv-qty-plus');
        const minusBtn = qvContent.querySelector('.qv-qty-minus');

        if (plusBtn && qtyInput) {
            plusBtn.onclick = function() {
                let val = parseInt(qtyInput.value) || 1;
                qtyInput.value = val + 1;
            };
        }
        if (minusBtn && qtyInput) {
            minusBtn.onclick = function() {
                let val = parseInt(qtyInput.value) || 1;
                if (val > 1) {
                    qtyInput.value = val - 1;
                }
            };
        }

        // 3. Add to Cart inside modal (AJAX)
        const addToCartBtn = qvContent.querySelector('.qv-add-to-cart-btn');
        if (addToCartBtn) {
            addToCartBtn.onclick = function() {
                const btn = this;
                if (btn.disabled) return;
                btn.disabled = true;
                btn.innerHTML = 'Adding...';

                const productId = btn.dataset.product_id;
                const qty = qtyInput ? (parseInt(qtyInput.value) || 1) : 1;

                const formData = new FormData();
                formData.append('product_id', productId);
                formData.append('quantity', qty);

                const ajaxUrl = (window.woocom_ajax && window.woocom_ajax.ajax_url) || '/wp-admin/admin-ajax.php';
                const addToCartUrl = (window.woocom_ajax && window.woocom_ajax.wc_ajax_url)
                    ? woocom_ajax.wc_ajax_url.replace('%%endpoint%%', 'add_to_cart')
                    : `${window.location.origin}/?wc-ajax=add_to_cart`;

                fetch(addToCartUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    closeQvModal();
                    openCartDrawer();
                    
                    // Refresh cart fragments
                    fetch(`${ajaxUrl}?action=woocom_get_cart_data&nonce=${woocom_ajax.cart_nonce}`)
                        .then(res => res.json())
                        .then(cartData => cartUpdateStateAndCrossSells(cartData));
                        
                    if (data.fragments) {
                        jQuery(document.body).trigger('added_to_cart', [data.fragments, data.cart_hash, jQuery(btn)]);
                    }
                })
                .catch(() => {
                    btn.disabled = false;
                    btn.innerHTML = 'Add To Cart';
                });
            };
        }

        // 4. Buy Now inside modal (AJAX & Redirect)
        const buyNowBtn = qvContent.querySelector('.qv-buy-now-btn');
        if (buyNowBtn) {
            buyNowBtn.onclick = function() {
                const btn = this;
                if (btn.disabled) return;
                btn.disabled = true;
                btn.innerHTML = 'Processing...';

                const productId = btn.dataset.product_id;
                const qty = qtyInput ? (parseInt(qtyInput.value) || 1) : 1;

                const formData = new FormData();
                formData.append('action', 'woocommerce_add_to_cart');
                formData.append('product_id', productId);
                formData.append('quantity', qty);

                const ajaxUrl = (window.woocom_ajax && window.woocom_ajax.ajax_url) || '/wp-admin/admin-ajax.php';

                fetch(`${ajaxUrl}?action=woocommerce_add_to_cart`, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.error && data.product_url) {
                        window.location.href = data.product_url;
                        return;
                    }
                    window.location.href = (window.woocom_ajax && woocom_ajax.checkout_url) || '/checkout/';
                })
                .catch(() => {
                    btn.disabled = false;
                    btn.innerHTML = 'Buy Now';
                });
            };
        }
    }

    // Smooth scroll centering for category tab buttons on mobile
    jQuery(document).on('click', '.foryou-tab-btn', function() {
        const $btn = jQuery(this);
        const $container = $btn.parent();
        if ($container.length) {
            const containerScrollLeft = $container.scrollLeft();
            const containerWidth = $container.outerWidth();
            const btnLeft = $btn.position().left;
            const btnWidth = $btn.outerWidth();
            
            $container.animate({
                scrollLeft: containerScrollLeft + btnLeft - (containerWidth / 2) + (btnWidth / 2)
            }, 300);
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initWoocomTheme);
} else {
    initWoocomTheme();
}
