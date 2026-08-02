<?php
/**
 * Out-of-stock and pre-order phone request system.
 *
 * @package Woocom
 */

defined( 'ABSPATH' ) || exit;

function woocom_stock_requests_table_name() {
	global $wpdb;
	return $wpdb->prefix . 'woocom_stock_requests';
}

function woocom_create_stock_requests_table() {
	global $wpdb;

	$table_name      = woocom_stock_requests_table_name();
	$charset_collate = $wpdb->get_charset_collate();

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$sql = "CREATE TABLE {$table_name} (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		product_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
		product_title TEXT NOT NULL,
		request_type VARCHAR(30) NOT NULL DEFAULT 'out_of_stock',
		phone VARCHAR(40) NOT NULL,
		quantity INT UNSIGNED NOT NULL DEFAULT 1,
		page_url TEXT NULL,
		user_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
		status VARCHAR(30) NOT NULL DEFAULT 'new',
		created_at DATETIME NOT NULL,
		PRIMARY KEY  (id),
		KEY product_id (product_id),
		KEY request_type (request_type),
		KEY created_at (created_at)
	) {$charset_collate};";

	dbDelta( $sql );
	update_option( 'woocom_stock_requests_db_version', '1.2.0' );
}
add_action( 'after_switch_theme', 'woocom_create_stock_requests_table' );

function woocom_maybe_create_stock_requests_table() {
	if ( get_option( 'woocom_stock_requests_db_version' ) !== '1.2.0' ) {
		woocom_create_stock_requests_table();
	}
}
// Run only in wp-admin — no need to check DB schema on every frontend request.
add_action( 'admin_init', 'woocom_maybe_create_stock_requests_table' );

function woocom_get_product_request_type( $product ) {
	if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
		return '';
	}

	if ( 'onbackorder' === $product->get_stock_status() || $product->is_on_backorder( 1 ) ) {
		return 'pre_order';
	}

	if ( ! $product->is_in_stock() || 'outofstock' === $product->get_stock_status() ) {
		return 'out_of_stock';
	}

	return '';
}

function woocom_get_stock_request_label( $request_type ) {
	if ( 'pre_order' === $request_type ) {
		return get_option( 'woocom_text_pre_order', 'Pre Order' );
	}

	return get_option( 'woocom_text_stock_out', 'Out of stock' );
}

function woocom_render_stock_request_badge( $request_type ) {
	if ( ! $request_type ) {
		return '';
	}

	$class = 'pre_order' === $request_type ? 'woocom-stock-badge woocom-stock-badge--preorder' : 'woocom-stock-badge woocom-stock-badge--out';

	return '<span class="' . esc_attr( $class ) . '">' . esc_html( woocom_get_stock_request_label( $request_type ) ) . '</span>';
}

function woocom_render_stock_request_form( $product_id, $request_type = '', $context = 'single' ) {
	$product = wc_get_product( $product_id );
	if ( ! $product ) {
		return '';
	}

	$request_type = $request_type ? $request_type : woocom_get_product_request_type( $product );
	if ( ! $request_type ) {
		return '';
	}

	$is_archive = 'archive' === $context;
	$title      = 'pre_order' === $request_type ? get_option( 'woocom_text_pre_order', 'Pre Order' ) : get_option( 'woocom_text_stock_out', 'Stock Out' );
	$button     = $title;

	// Archive context: show a button linking to the product page instead of inline form
	if ( $is_archive ) {
		ob_start();
		?>
		<a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>" class="w-full border border-primary/40 text-primary hover:bg-primary hover:text-white font-bold py-1.5 sm:py-2.5 rounded-[6px] text-center transition-all duration-300 text-[13px] sm:text-[15px] flex items-center justify-center gap-1 sm:gap-2 mt-auto">
			<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
			<?php echo esc_html( $button ); ?>
		</a>
		<?php
		return ob_get_clean();
	}

	// Single product context: show phone + quantity form
	ob_start();
	?>
	<form class="woocom-stock-request-form woocom-stock-request-form--single" data-product-id="<?php echo esc_attr( $product_id ); ?>" data-request-type="<?php echo esc_attr( $request_type ); ?>">
		<div class="woocom-stock-request-title"><?php echo esc_html( $title ); ?></div>
		<div class="woocom-stock-request-fields">
			<input type="tel" name="phone" inputmode="tel" autocomplete="tel" required placeholder="<?php esc_attr_e( 'Mobile number', 'woocom' ); ?>">
			<input type="number" name="quantity" min="1" value="1" required placeholder="<?php esc_attr_e( 'Qty', 'woocom' ); ?>" class="woocom-stock-request-qty">
			<button type="submit"><?php echo esc_html( $button ); ?></button>
		</div>
		<div class="woocom-stock-request-message" aria-live="polite"></div>
	</form>
	<?php
	return ob_get_clean();
}

function woocom_handle_stock_request() {
	check_ajax_referer( 'woocom_stock_request', 'nonce' );

	$product_id   = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$request_type = isset( $_POST['request_type'] ) ? sanitize_key( wp_unslash( $_POST['request_type'] ) ) : '';
	$phone        = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$quantity     = isset( $_POST['quantity'] ) ? max( 1, absint( $_POST['quantity'] ) ) : 1;
	$page_url     = isset( $_POST['page_url'] ) ? esc_url_raw( wp_unslash( $_POST['page_url'] ) ) : '';
	$product      = $product_id ? wc_get_product( $product_id ) : false;

	if ( ! $product || ! in_array( $request_type, array( 'out_of_stock', 'pre_order' ), true ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid product request.', 'woocom' ) ) );
	}

	if ( strlen( preg_replace( '/\D+/', '', $phone ) ) < 8 ) {
		wp_send_json_error( array( 'message' => __( 'Please enter a valid mobile number.', 'woocom' ) ) );
	}

	global $wpdb;
	$table_name = woocom_stock_requests_table_name();
	$inserted   = $wpdb->insert(
		$table_name,
		array(
			'product_id'    => $product_id,
			'product_title' => $product->get_name(),
			'request_type'  => $request_type,
			'phone'         => $phone,
			'quantity'      => $quantity,
			'page_url'      => $page_url,
			'user_id'       => get_current_user_id(),
			'status'        => 'new',
			'created_at'    => current_time( 'mysql' ),
		),
		array( '%d', '%s', '%s', '%s', '%d', '%s', '%d', '%s', '%s' )
	);

	if ( ! $inserted ) {
		wp_send_json_error( array( 'message' => __( 'Could not save your request. Please try again.', 'woocom' ) ) );
	}

	wp_send_json_success( array( 'message' => __( 'Thanks! Your pre-order request has been submitted.', 'woocom' ) ) );
}
add_action( 'wp_ajax_woocom_stock_request', 'woocom_handle_stock_request' );
add_action( 'wp_ajax_nopriv_woocom_stock_request', 'woocom_handle_stock_request' );

function woocom_stock_requests_admin_menu() {
	// Pre-order Requests — separate top-level menu with cart icon
	add_menu_page(
		__( 'Pre-order Requests', 'woocom' ),
		__( 'Pre-orders', 'woocom' ),
		'manage_options',
		'woocom-preorder-requests',
		'woocom_preorder_requests_admin_page',
		'dashicons-cart',
		56
	);

	// Stock Requests — submenu under theme settings
	add_submenu_page(
		'woocom-settings',
		__( 'Stock Out Requests', 'woocom' ),
		__( 'Stock Out Requests', 'woocom' ),
		'manage_options',
		'woocom-stock-requests',
		'woocom_stock_requests_admin_page'
	);
}
add_action( 'admin_menu', 'woocom_stock_requests_admin_menu', 20 );

/**
 * Pre-order Requests admin page.
 */
function woocom_preorder_requests_admin_page() {
	global $wpdb;
	$table_name = woocom_stock_requests_table_name();
	$requests   = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE request_type = %s ORDER BY created_at DESC LIMIT 200", 'pre_order' ) );

	// Handle status update
	if ( isset( $_GET['action'], $_GET['request_id'], $_GET['_wpnonce'] ) && 'update_status' === $_GET['action'] ) {
		if ( wp_verify_nonce( $_GET['_wpnonce'], 'woocom_update_request_status' ) ) {
			$new_status = isset( $_GET['status'] ) ? sanitize_key( $_GET['status'] ) : '';
			$request_id = absint( $_GET['request_id'] );
			if ( $request_id && in_array( $new_status, array( 'new', 'contacted', 'completed', 'cancelled' ), true ) ) {
				$wpdb->update( $table_name, array( 'status' => $new_status ), array( 'id' => $request_id ), array( '%s' ), array( '%d' ) );
				echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Status updated.', 'woocom' ) . '</p></div>';
				// Re-fetch
				$requests = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE request_type = %s ORDER BY created_at DESC LIMIT 200", 'pre_order' ) );
			}
		}
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Pre-order Requests', 'woocom' ); ?></h1>
		<p class="description"><?php esc_html_e( 'Customers who submitted pre-order requests for out-of-stock products.', 'woocom' ); ?></p>
		<table class="widefat striped" style="margin-top:15px;">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Date', 'woocom' ); ?></th>
					<th><?php esc_html_e( 'Product', 'woocom' ); ?></th>
					<th><?php esc_html_e( 'Mobile', 'woocom' ); ?></th>
					<th><?php esc_html_e( 'Quantity', 'woocom' ); ?></th>
					<th><?php esc_html_e( 'Status', 'woocom' ); ?></th>
					<th><?php esc_html_e( 'Actions', 'woocom' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( $requests ) : ?>
					<?php foreach ( $requests as $request ) : ?>
						<tr>
							<td><?php echo esc_html( date_i18n( 'd M Y, h:i A', strtotime( $request->created_at ) ) ); ?></td>
							<td>
								<strong><?php echo esc_html( $request->product_title ); ?></strong>
								<div class="row-actions">
									<a href="<?php echo esc_url( get_edit_post_link( $request->product_id ) ); ?>"><?php esc_html_e( 'Edit product', 'woocom' ); ?></a>
									<?php if ( $request->page_url ) : ?>
										| <a href="<?php echo esc_url( $request->page_url ); ?>" target="_blank"><?php esc_html_e( 'View page', 'woocom' ); ?></a>
									<?php endif; ?>
								</div>
							</td>
							<td><strong><?php echo esc_html( $request->phone ); ?></strong></td>
							<td><?php echo esc_html( isset( $request->quantity ) ? $request->quantity : 1 ); ?></td>
							<td>
								<?php
								$status_colors = array( 'new' => '#2271b1', 'contacted' => '#dba617', 'completed' => '#00a32a', 'cancelled' => '#d63638' );
								$color = isset( $status_colors[ $request->status ] ) ? $status_colors[ $request->status ] : '#666';
								?>
								<span style="background:<?php echo esc_attr( $color ); ?>;color:#fff;padding:3px 10px;border-radius:12px;font-size:12px;font-weight:600;">
									<?php echo esc_html( ucfirst( $request->status ) ); ?>
								</span>
							</td>
							<td>
								<?php
								$statuses = array( 'contacted', 'completed', 'cancelled' );
								foreach ( $statuses as $s ) {
									if ( $s === $request->status ) continue;
									$url = wp_nonce_url( admin_url( 'admin.php?page=woocom-preorder-requests&action=update_status&request_id=' . $request->id . '&status=' . $s ), 'woocom_update_request_status' );
									echo '<a href="' . esc_url( $url ) . '" style="margin-right:8px;text-decoration:none;">' . esc_html( ucfirst( $s ) ) . '</a>';
								}
								?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr><td colspan="6"><?php esc_html_e( 'No pre-order requests yet.', 'woocom' ); ?></td></tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	<?php
}

/**
 * Stock Out Requests admin page (existing, under theme settings).
 */
function woocom_stock_requests_admin_page() {
	global $wpdb;
	$table_name = woocom_stock_requests_table_name();
	$requests   = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE request_type = %s ORDER BY created_at DESC LIMIT 200", 'out_of_stock' ) );

	// Handle status update
	if ( isset( $_GET['action'], $_GET['request_id'], $_GET['_wpnonce'] ) && 'update_status' === $_GET['action'] ) {
		if ( wp_verify_nonce( $_GET['_wpnonce'], 'woocom_update_request_status' ) ) {
			$new_status = isset( $_GET['status'] ) ? sanitize_key( $_GET['status'] ) : '';
			$request_id = absint( $_GET['request_id'] );
			if ( $request_id && in_array( $new_status, array( 'new', 'contacted', 'completed', 'cancelled' ), true ) ) {
				$wpdb->update( $table_name, array( 'status' => $new_status ), array( 'id' => $request_id ), array( '%s' ), array( '%d' ) );
				echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Status updated.', 'woocom' ) . '</p></div>';
				// Re-fetch
				$requests = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE request_type = %s ORDER BY created_at DESC LIMIT 200", 'out_of_stock' ) );
			}
		}
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Stock Out Requests', 'woocom' ); ?></h1>
		<p class="description"><?php esc_html_e( 'Customers who requested notification when out-of-stock products are back in stock.', 'woocom' ); ?></p>
		<table class="widefat striped" style="margin-top:15px;">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Date', 'woocom' ); ?></th>
					<th><?php esc_html_e( 'Product', 'woocom' ); ?></th>
					<th><?php esc_html_e( 'Mobile', 'woocom' ); ?></th>
					<th><?php esc_html_e( 'Quantity', 'woocom' ); ?></th>
					<th><?php esc_html_e( 'Status', 'woocom' ); ?></th>
					<th><?php esc_html_e( 'Actions', 'woocom' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( $requests ) : ?>
					<?php foreach ( $requests as $request ) : ?>
						<tr>
							<td><?php echo esc_html( date_i18n( 'd M Y, h:i A', strtotime( $request->created_at ) ) ); ?></td>
							<td>
								<strong><?php echo esc_html( $request->product_title ); ?></strong>
								<div class="row-actions">
									<a href="<?php echo esc_url( get_edit_post_link( $request->product_id ) ); ?>"><?php esc_html_e( 'Edit product', 'woocom' ); ?></a>
									<?php if ( $request->page_url ) : ?>
										| <a href="<?php echo esc_url( $request->page_url ); ?>" target="_blank"><?php esc_html_e( 'View page', 'woocom' ); ?></a>
									<?php endif; ?>
								</div>
							</td>
							<td><strong><?php echo esc_html( $request->phone ); ?></strong></td>
							<td><?php echo esc_html( isset( $request->quantity ) ? $request->quantity : 1 ); ?></td>
							<td>
								<?php
								$status_colors = array( 'new' => '#2271b1', 'contacted' => '#dba617', 'completed' => '#00a32a', 'cancelled' => '#d63638' );
								$color = isset( $status_colors[ $request->status ] ) ? $status_colors[ $request->status ] : '#666';
								?>
								<span style="background:<?php echo esc_attr( $color ); ?>;color:#fff;padding:3px 10px;border-radius:12px;font-size:12px;font-weight:600;">
									<?php echo esc_html( ucfirst( $request->status ) ); ?>
								</span>
							</td>
							<td>
								<?php
								$statuses = array( 'contacted', 'completed', 'cancelled' );
								foreach ( $statuses as $s ) {
									if ( $s === $request->status ) continue;
									$url = wp_nonce_url( admin_url( 'admin.php?page=woocom-stock-requests&action=update_status&request_id=' . $request->id . '&status=' . $s ), 'woocom_update_request_status' );
									echo '<a href="' . esc_url( $url ) . '" style="margin-right:8px;text-decoration:none;">' . esc_html( ucfirst( $s ) ) . '</a>';
								}
								?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr><td colspan="6"><?php esc_html_e( 'No stock out requests yet.', 'woocom' ); ?></td></tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	<?php
}

