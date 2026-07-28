<?php
/**
 * Analytics Integration: GTM, GA4, Meta Pixel
 *
 * @package Woocom
 */

// ─── Snippet Injection ───────────────────────────────────────────────────────

add_action( 'wp_head', 'woocom_gtm_head_snippet', 1 );
function woocom_gtm_head_snippet() {
	$id = woocom_analytics_id( 'woocom_gtm_id' );
	if ( ! $id ) return;
	?>
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?php echo esc_js( $id ); ?>');</script>
	<?php
}

add_action( 'wp_body_open', 'woocom_gtm_body_snippet', 1 );
function woocom_gtm_body_snippet() {
	$id = woocom_analytics_id( 'woocom_gtm_id' );
	if ( ! $id ) return;
	?>
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr( $id ); ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<?php
}

add_action( 'wp_head', 'woocom_ga4_snippet', 2 );
function woocom_ga4_snippet() {
	$id = woocom_analytics_id( 'woocom_ga4_id' );
	if ( ! $id ) return;
	?>
	<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $id ); ?>"></script>
	<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?php echo esc_js( $id ); ?>');</script>
	<?php
}

add_action( 'wp_head', 'woocom_pixel_base_snippet', 2 );
function woocom_pixel_base_snippet() {
	$id = woocom_analytics_id( 'woocom_pixel_id' );
	if ( ! $id ) return;
	?>
	<script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','<?php echo esc_js( $id ); ?>');fbq('track','PageView');</script>
	<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=<?php echo esc_attr( $id ); ?>&ev=PageView&noscript=1"/></noscript>
	<?php
}

// ─── WooCommerce Events ───────────────────────────────────────────────────────

/** ViewContent / view_item — single product page */
add_action( 'wp_footer', 'woocom_track_view_item', 5 );
function woocom_track_view_item() {
	if ( ! is_product() ) return;
	if ( ! woocom_any_analytics_active() ) return;

	global $product;
	if ( ! $product ) $product = wc_get_product( get_the_ID() );
	if ( ! $product ) return;

	$data = array(
		'id'       => $product->get_id(),
		'name'     => $product->get_name(),
		'price'    => (float) $product->get_price(),
		'sku'      => $product->get_sku(),
		'currency' => get_woocommerce_currency(),
		'category' => implode( ', ', wp_get_post_terms( $product->get_id(), 'product_cat', array( 'fields' => 'names' ) ) ),
	);
	?>
	<script>
	window.woocomAnalyticsProduct = <?php echo wp_json_encode( $data ); ?>;
	(function(){
		var p = window.woocomAnalyticsProduct;
		var item = {item_id:String(p.id),item_name:p.name,price:p.price,item_category:p.category};
		<?php if ( woocom_analytics_id( 'woocom_gtm_id' ) ) : ?>
		window.dataLayer=window.dataLayer||[];dataLayer.push({ecommerce:null});
		dataLayer.push({event:'view_item',ecommerce:{currency:p.currency,value:p.price,items:[item]}});
		<?php endif; ?>
		<?php if ( woocom_analytics_id( 'woocom_ga4_id' ) ) : ?>
		if(typeof gtag==='function')gtag('event','view_item',{currency:p.currency,value:p.price,items:[item]});
		<?php endif; ?>
		<?php if ( woocom_analytics_id( 'woocom_pixel_id' ) ) : ?>
		if(typeof fbq==='function')fbq('track','ViewContent',{content_ids:[String(p.id)],content_name:p.name,content_type:'product',value:p.price,currency:p.currency});
		<?php endif; ?>
	})();
	</script>
	<?php
}

/** InitiateCheckout / begin_checkout */
add_action( 'wp_footer', 'woocom_track_begin_checkout', 5 );
function woocom_track_begin_checkout() {
	if ( ! is_checkout() || is_wc_endpoint_url( 'order-received' ) ) return;
	if ( ! woocom_any_analytics_active() ) return;
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) return;

	$items    = array();
	$pids     = array();
	$currency = get_woocommerce_currency();

	foreach ( WC()->cart->get_cart() as $ci ) {
		$p       = $ci['data'];
		$items[] = array(
			'item_id'   => $p->get_id(),
			'item_name' => $p->get_name(),
			'price'     => (float) $p->get_price(),
			'quantity'  => (int) $ci['quantity'],
		);
		$pids[] = $p->get_id();
	}

	$total = (float) WC()->cart->get_cart_contents_total();
	?>
	<script>
	(function(){
		var items=<?php echo wp_json_encode( $items ); ?>;
		var pids=<?php echo wp_json_encode( $pids ); ?>;
		var total=<?php echo wp_json_encode( $total ); ?>;
		var cur='<?php echo esc_js( $currency ); ?>';
		<?php if ( woocom_analytics_id( 'woocom_gtm_id' ) ) : ?>
		window.dataLayer=window.dataLayer||[];dataLayer.push({ecommerce:null});
		dataLayer.push({event:'begin_checkout',ecommerce:{currency:cur,value:total,items:items}});
		<?php endif; ?>
		<?php if ( woocom_analytics_id( 'woocom_ga4_id' ) ) : ?>
		if(typeof gtag==='function')gtag('event','begin_checkout',{currency:cur,value:total,items:items});
		<?php endif; ?>
		<?php if ( woocom_analytics_id( 'woocom_pixel_id' ) ) : ?>
		if(typeof fbq==='function')fbq('track','InitiateCheckout',{content_ids:pids.map(String),num_items:items.length,value:total,currency:cur});
		<?php endif; ?>
	})();
	</script>
	<?php
}

/**
 * Purchase — thank you page.
 *
 * Strategy:
 *  1. woocommerce_thankyou collects order data and stores it in a transient.
 *  2. wp_footer outputs the <script> AFTER GTM is guaranteed to have loaded.
 *
 * This fixes the timing/race-condition where inline woocommerce_thankyou output
 * runs before the GTM container JS is executed, causing dataLayer.push() to
 * succeed but GTM never picking up the event because its listeners aren't ready.
 */
add_action( 'woocommerce_thankyou', 'woocom_collect_purchase_data', 5 );
function woocom_collect_purchase_data( $order_id ) {
	if ( ! $order_id ) return;

	// ── Analytics must be active ──────────────────────────────────────────────
	if ( ! woocom_any_analytics_active() ) return;

	// ── Deduplication via browser cookie (30 min) ─────────────────────────────
	// Check if this purchase event has already been tracked.
	// The cookie is set via browser-side JS to avoid "headers already sent" warnings
	// since this hook runs inside the template rendering process.
	$cookie_name = 'woocom_pf_' . $order_id;
	if ( isset( $_COOKIE[ $cookie_name ] ) ) return;

	$order = wc_get_order( $order_id );
	if ( ! $order ) return;

	$items = array();
	$pids  = array();

	foreach ( $order->get_items() as $item ) {
		$product = $item->get_product();
		$cats    = $product
			? wp_get_post_terms( $product->get_id(), 'product_cat', array( 'fields' => 'names' ) )
			: array();
		$cat_str = is_array( $cats ) ? implode( ', ', $cats ) : '';
		$sku     = $product ? $product->get_sku() : '';

		$items[] = array(
			'item_id'       => (string) $item->get_product_id(),
			'item_name'     => $item->get_name(),
			'item_category' => $cat_str,
			'item_sku'      => $sku,
			'price'         => (float) $order->get_item_subtotal( $item, false ),
			'quantity'      => (int) $item->get_quantity(),
		);
		$pids[] = (string) $item->get_product_id();
	}

	$od = array(
		'transaction_id' => (string) $order->get_order_number(),
		'value'          => (float) $order->get_total(),
		'tax'            => (float) $order->get_total_tax(),
		'shipping'       => (float) $order->get_shipping_total(),
		'currency'       => $order->get_currency(),
		'coupon'         => implode( ',', $order->get_coupon_codes() ),
	);

	// Stash for wp_footer — fires AFTER GTM container is ready.
	$GLOBALS['woocom_purchase_payload'] = array(
		'order' => $od,
		'items' => $items,
		'pids'  => $pids,
	);
}


/** Output purchase script at wp_footer — AFTER GTM container has loaded. */
add_action( 'wp_footer', 'woocom_output_purchase_script', 99 );
function woocom_output_purchase_script() {
	if ( empty( $GLOBALS['woocom_purchase_payload'] ) ) return;

	$payload = $GLOBALS['woocom_purchase_payload'];
	$o       = $payload['order'];
	$items   = $payload['items'];
	$pids    = $payload['pids'];
	?>
	<script>
	/* WooCommerce Purchase — GTM/GA4/Pixel */
	(function(){
		var o     = <?php echo wp_json_encode( $o );     ?>;
		var items = <?php echo wp_json_encode( $items ); ?>;
		var pids  = <?php echo wp_json_encode( $pids );  ?>;

		// Set a browser-scoped cookie to prevent duplicate purchase events on page refresh.
		// Doing it via JS avoids PHP "headers already sent" warnings.
		var cookieName = 'woocom_pf_' + o.transaction_id;
		document.cookie = cookieName + '=1; path=/; max-age=1800; SameSite=Lax' + (window.location.protocol === 'https:' ? '; Secure' : '');

		<?php if ( woocom_analytics_id( 'woocom_gtm_id' ) ) : ?>
		window.dataLayer = window.dataLayer || [];
		dataLayer.push({ ecommerce: null }); // Clear previous ecommerce object
		dataLayer.push({
			event: 'purchase',
			ecommerce: {
				transaction_id : o.transaction_id,
				value          : o.value,
				tax            : o.tax,
				shipping       : o.shipping,
				currency       : o.currency,
				coupon         : o.coupon,
				items          : items
			}
		});
		<?php endif; ?>

		<?php if ( woocom_analytics_id( 'woocom_ga4_id' ) ) : ?>
		if ( typeof gtag === 'function' ) {
			gtag( 'event', 'purchase', {
				transaction_id : o.transaction_id,
				value          : o.value,
				tax            : o.tax,
				shipping       : o.shipping,
				currency       : o.currency,
				coupon         : o.coupon,
				items          : items
			});
		}
		<?php endif; ?>

		<?php if ( woocom_analytics_id( 'woocom_pixel_id' ) ) : ?>
		if ( typeof fbq === 'function' ) {
			fbq( 'track', 'Purchase', {
				content_ids  : pids,
				content_type : 'product',
				value        : o.value,
				currency     : o.currency,
				num_items    : items.length
			});
		}
		<?php endif; ?>
	})();
	</script>
	<?php
}

/** AddToCart — JS listener (archive + single product) */
add_action( 'wp_footer', 'woocom_track_add_to_cart_listener', 20 );
function woocom_track_add_to_cart_listener() {
	if ( ! woocom_any_analytics_active() ) return;
	$currency = get_woocommerce_currency();
	?>
	<script>
	(function($){
		function fireAddToCart(id, name, price, qty) {
			id   = id   || 0;
			name = name || '';
			price = parseFloat(price) || 0;
			qty   = parseInt(qty)     || 1;
			var val = price * qty;
			var cur = '<?php echo esc_js( $currency ); ?>';
			var item = {item_id:String(id),item_name:name,price:price,quantity:qty};
			<?php if ( woocom_analytics_id( 'woocom_gtm_id' ) ) : ?>
			window.dataLayer=window.dataLayer||[];dataLayer.push({ecommerce:null});
			dataLayer.push({event:'add_to_cart',ecommerce:{currency:cur,value:val,items:[item]}});
			<?php endif; ?>
			<?php if ( woocom_analytics_id( 'woocom_ga4_id' ) ) : ?>
			if(typeof gtag==='function')gtag('event','add_to_cart',{currency:cur,value:val,items:[item]});
			<?php endif; ?>
			<?php if ( woocom_analytics_id( 'woocom_pixel_id' ) ) : ?>
			if(typeof fbq==='function')fbq('track','AddToCart',{content_ids:[String(id)],content_type:'product',value:val,currency:cur});
			<?php endif; ?>
		}

		// Archive / shop page — standard WC AJAX add-to-cart
		$(document.body).on('added_to_cart', function(e, fragments, hash, $btn) {
			if (!$btn || $btn.attr('id') === 'single-add-to-cart-btn') return;
			fireAddToCart(
				$btn.data('product_id') || $btn.data('product-id'),
				$btn.attr('data-product_name') || '',
				$btn.data('price') || 0,
				$btn.data('quantity') || 1
			);
		});

		// Single product page — triggered by main.js after successful AJAX
		$(document.body).on('woocom_add_to_cart_tracked', function(e, d) {
			if (d) fireAddToCart(d.id, d.name, d.price, d.quantity);
		});
	})(jQuery);
	</script>
	<?php
}

// ─── Helpers ─────────────────────────────────────────────────────────────────

function woocom_analytics_id( $option ) {
	if ( strpos( $option, 'gtm' ) !== false && ! get_option( 'woocom_enable_gtm' ) ) return '';
	if ( strpos( $option, 'ga4' ) !== false && ! get_option( 'woocom_enable_ga4' ) ) return '';
	if ( strpos( $option, 'pixel' ) !== false && ! get_option( 'woocom_enable_pixel' ) ) return '';
	return sanitize_text_field( get_option( $option, '' ) );
}

function woocom_any_analytics_active() {
	return get_option( 'woocom_enable_gtm' ) || get_option( 'woocom_enable_ga4' ) || get_option( 'woocom_enable_pixel' );
}
