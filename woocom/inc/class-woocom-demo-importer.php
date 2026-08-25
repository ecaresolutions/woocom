<?php
/**
 * Woocom Demo Importer and Exporter Engine
 *
 * @package Woocom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Woocom_Demo_Importer {

	/**
	 * Available demos list
	 */
	public static function get_demos() {
		return array(
			'grocery' => array(
				'name'        => esc_html__( 'Grocery & Organic Food', 'woocom' ),
				'slug'        => 'grocery',
				'screenshot'  => get_template_directory_uri() . '/screenshot.png',
				'preview_url' => 'https://woocom-grocery.test', // Placeholder preview
				'description' => esc_html__( 'Complete organic grocery store layout with custom typography, clean banners, green accent colors, and custom product displays.', 'woocom' ),
			),
			'gadget' => array(
				'name'        => esc_html__( 'WoocomGadget', 'woocom' ),
				'slug'        => 'gadget',
				'screenshot'  => get_template_directory_uri() . '/screenshot.png',
				'preview_url' => 'https://woocom-gadget.test',
				'description' => esc_html__( 'A premium high-tech gadget and electronics storefront layout with custom blue accent colors, responsive product grids, and advanced banner displays.', 'woocom' ),
			),
			'default' => array(
				'name'        => esc_html__( 'Default Classic Store', 'woocom' ),
				'slug'        => 'default',
				'screenshot'  => get_template_directory_uri() . '/screenshot.png',
				'preview_url' => 'https://woocom.test',
				'description' => esc_html__( 'A clean general-purpose e-commerce storefront layout suitable for any product niche.', 'woocom' ),
			)
		);
	}

	/**
	 * List of all registered options in woocom-settings-group
	 */
	public static function get_theme_option_keys() {
		return array(
			// Branding
			'theme_logo',
			'footer_logo',
			'woocom_primary_color',
			'woocom_secondary_color',
			'woocom_main_background_color',
			'contact_phone',
			'contact_email',
			'contact_address',
			'social_facebook',
			'social_instagram',
			'social_twitter',
			'social_youtube',
			'woocom_font_bengali',
			'woocom_font_english',
			
			// Header
			'sticky_header',
			'nav_bg_color',
			'nav_text_color',
			'nav_hover_color',
			'nav_vertical_padding',
			
			// Banners
			'hero_banner_1',
			'hero_banner_1_link',
			'hero_banner_2',
			'hero_banner_2_link',
			'hero_side_banner',
			'hero_side_banner_link',
			'promo_banner_1',
			'promo_banner_1_link',
			'promo_banner_2',
			'promo_banner_2_link',
			'woocom_hero_slides',
			
			// Layout / Visibility
			'show_hero_section',
			'show_featured_categories',
			'show_top_selling',
			'show_category_sections',
			'show_combo_offers',
			'woocom_show_just_for_you',
			'ticker_enabled',
			'show_dual_banners',
			
			// Content Settings
			'woocom_combo_title',
			'woocom_combo_image',
			'woocom_top_selling_title',
			'woocom_top_selling_image',
			'woocom_featured_orderby',
			'woocom_featured_order',
			'woocom_latest_orderby',
			'woocom_latest_order',
			'woocom_just_for_you_title',
			'woocom_just_for_you_image',
			
			// Collections
			'woocom_featured_categories',
			'woocom_category_sections',
			'woocom_combo_bundles',
			
			// Cart & Checkout
			'enable_cart_drawer',
			'cart_drawer_floating_visibility',
			'cart_drawer_title',
			'cart_promo_enabled',
			'cart_promo_title',
			'cart_promo_min_amount',
			'show_cross_sell',
			'cross_sell_title',
			'cross_sell_autoslide',
			'checkout_button_shake',
			'sticky_checkout_mobile',
			
			// Product Actions
			'product_add_to_cart_button_color',
			'product_buy_now_button_color',
			'product_whatsapp_button_color',
			'product_call_button_color',
			'variation_unavailable_message',
			
			// Translation / Language Texts
			'woocom_text_add_to_cart',
			'woocom_text_buy_now',
			'woocom_text_see_details',
			'woocom_text_stock_out',
			'woocom_text_pre_order',
			
			// Footer
			'woocom_footer_information_title',
			'woocom_footer_shop_title',
			'woocom_footer_support_title',
			'woocom_footer_policy_title',
			'woocom_footer_information_links',
			'woocom_footer_shop_links',
			'woocom_footer_support_links',
			'woocom_footer_policy_links',
			'woocom_whatsapp_number',
			
			// Analytics
			'woocom_enable_gtm',
			'woocom_gtm_id',
			'woocom_enable_ga4',
			'woocom_ga4_id',
			'woocom_enable_pixel',
			'woocom_pixel_id',
			
			// Ticker
			'ticker_enabled',
			'ticker_text',
			'ticker_bg_color',
			'ticker_text_color',
			'ticker_speed',
			'ticker_font_size',
			'ticker_padding',
			'ticker_icon',
		);
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		// AJAX Import Handlers
		add_action( 'wp_ajax_woocom_import_start', array( $this, 'ajax_import_start' ) );
		add_action( 'wp_ajax_woocom_import_content_chunk', array( $this, 'ajax_import_content_chunk' ) );
		add_action( 'wp_ajax_woocom_import_options', array( $this, 'ajax_import_options' ) );
		add_action( 'wp_ajax_woocom_import_widgets', array( $this, 'ajax_import_widgets' ) );
		add_action( 'wp_ajax_woocom_import_finalize', array( $this, 'ajax_import_finalize' ) );

		// Admin Post Exporter Action Handlers (for Devs)
		add_action( 'admin_post_woocom_export_options', array( $this, 'handle_export_options' ) );
		add_action( 'admin_post_woocom_export_widgets', array( $this, 'handle_export_widgets' ) );
	}

	/**
	 * 1. Ajax: Start Import
	 */
	public function ajax_import_start() {
		check_ajax_referer( 'woocom_import_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Unauthorized user.', 'woocom' ) ) );
		}

		$demo = isset( $_POST['demo'] ) ? sanitize_key( $_POST['demo'] ) : '';
		$clean_install = isset( $_POST['clean_install'] ) && $_POST['clean_install'] === 'true';

		$demos = self::get_demos();
		if ( ! isset( $demos[ $demo ] ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid demo layout selected.', 'woocom' ) ) );
		}

		// Wipe out database if Clean Install is checked
		if ( $clean_install ) {
			$this->purge_existing_content();
		}

		// Set up target file path
		$demo_dir = get_template_directory() . '/inc/demo-data/' . $demo;
		$xml_path = $demo_dir . '/content.xml';

		if ( ! file_exists( $xml_path ) ) {
			wp_send_json_error( array( 'message' => sprintf( esc_html__( 'Content XML file not found at %s. Please run Developer Export first.', 'woocom' ), 'inc/demo-data/' . $demo . '/content.xml' ) ) );
		}

		// Parse XML elements count to report total steps to frontend
		$xml = simplexml_load_file( $xml_path, 'SimpleXMLElement', LIBXML_NOCDATA );
		if ( ! $xml ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Failed to parse content XML file.', 'woocom' ) ) );
		}

		$total_items = count( $xml->channel->item );

		// Save state in transients
		set_transient( 'woocom_import_current_demo', $demo, HOUR_IN_SECONDS );
		set_transient( 'woocom_import_processed_items', 0, HOUR_IN_SECONDS );
		set_transient( 'woocom_import_id_mappings', array(), HOUR_IN_SECONDS );

		wp_send_json_success( array(
			'message'     => esc_html__( 'Import initialized. Preparing database...', 'woocom' ),
			'total_items' => $total_items,
		) );
	}

	/**
	 * Purges existing pages, posts, products, and categories
	 */
	private function purge_existing_content() {
		global $wpdb;

		// Post Types to delete
		$post_types = array( 'post', 'page', 'product', 'product_variation', 'shop_order', 'attachment' );
		foreach ( $post_types as $type ) {
			$posts = get_posts( array(
				'post_type'   => $type,
				'numberposts' => -1,
				'post_status' => 'any',
			) );

			foreach ( $posts as $post ) {
				wp_delete_post( $post->ID, true ); // Bypass trash
			}
		}

		// Delete Terms and Custom Product attributes, but preserve default Uncategorized
		$taxonomies = array( 'product_cat', 'product_tag', 'category', 'post_tag' );
		foreach ( $taxonomies as $taxonomy ) {
			$terms = get_terms( array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
			) );

			if ( ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					// Don't delete default term
					if ( in_array( $term->slug, array( 'uncategorized', 'all-products' ), true ) ) {
						continue;
					}
					wp_delete_term( $term->term_id, $taxonomy );
				}
			}
		}
		
		// Delete custom options to reset theme settings to default
		$keys = self::get_theme_option_keys();
		foreach ( $keys as $key ) {
			delete_option( $key );
		}
		delete_option( 'woocom_setup_complete' );
	}

	/**
	 * 2. Ajax: Import Content Chunk
	 */
	public function ajax_import_content_chunk() {
		check_ajax_referer( 'woocom_import_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Unauthorized user.', 'woocom' ) ) );
		}

		$demo = get_transient( 'woocom_import_current_demo' );
		$processed = (int) get_transient( 'woocom_import_processed_items' );
		$id_mappings = get_transient( 'woocom_import_id_mappings' );

		if ( ! $demo ) {
			wp_send_json_error( array( 'message' => esc_html__( 'No active import session found.', 'woocom' ) ) );
		}

		$xml_path = get_template_directory() . '/inc/demo-data/' . $demo . '/content.xml';
		$xml = simplexml_load_file( $xml_path, 'SimpleXMLElement', LIBXML_NOCDATA );
		
		if ( ! $xml ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Failed to reload XML file.', 'woocom' ) ) );
		}

		$items = $xml->channel->item;
		$total_items = count( $items );
		$chunk_size = 5; // Import 5 items per request to stay under timeout limits
		
		$end_index = min( $processed + $chunk_size, $total_items );
		
		// Load standard WP post/media creation utilities
		if ( ! function_exists( 'wp_crop_image' ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}
		if ( ! function_exists( 'download_url' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
		if ( ! function_exists( 'media_handle_sideload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/media.php';
		}

		// WordPress namespace prefixes in WXR
		$wp_ns = 'http://wordpress.org/export/1.2/';
		
		for ( $i = $processed; $i < $end_index; $i++ ) {
			$item = $items[ $i ];
			
			// Parse WXR specific elements
			$post_id     = (int) $item->children( $wp_ns )->post_id;
			$post_type   = (string) $item->children( $wp_ns )->post_type;
			$post_status = (string) $item->children( $wp_ns )->status;
			$post_name   = (string) $item->children( $wp_ns )->post_name;
			$post_parent = (int) $item->children( $wp_ns )->post_parent;
			$menu_order  = (int) $item->children( $wp_ns )->menu_order;

			// Handle attachments (Images/Media)
			if ( $post_type === 'attachment' ) {
				$attachment_url = (string) $item->children( 'http://wordpress.org/export/1.2/attachment_url' )->attachment_url;
				if ( ! empty( $attachment_url ) ) {
					// Sideload media
					$new_attachment_id = $this->sideload_media_file( $attachment_url, $item );
					if ( $new_attachment_id ) {
						$id_mappings[ $post_id ] = $new_attachment_id;
					}
				}
				continue;
			}

			// Check if already imported (prevent duplicate)
			$existing_post = $this->get_existing_post( $post_name, $post_type );
			if ( $existing_post ) {
				$id_mappings[ $post_id ] = $existing_post->ID;
				continue;
			}

			// Map parent ID
			$new_parent = 0;
			if ( $post_parent && isset( $id_mappings[ $post_parent ] ) ) {
				$new_parent = $id_mappings[ $post_parent ];
			}

			// Insert Post
			$post_data = array(
				'post_title'     => (string) $item->title,
				'post_content'   => (string) $item->children( 'http://purl.org/rss/1.0/modules/content/' )->encoded,
				'post_excerpt'   => (string) $item->children( 'http://wordpress.org/export/1.2/excerpt/' )->encoded,
				'post_type'      => $post_type,
				'post_status'    => $post_status ? $post_status : 'publish',
				'post_name'      => $post_name,
				'post_parent'    => $new_parent,
				'menu_order'     => $menu_order,
				'post_author'    => get_current_user_id(),
			);

			$new_post_id = wp_insert_post( $post_data );
			
			if ( ! is_wp_error( $new_post_id ) && $new_post_id ) {
				$id_mappings[ $post_id ] = $new_post_id;

				// Import Meta fields
				if ( isset( $item->children( $wp_ns )->postmeta ) ) {
					foreach ( $item->children( $wp_ns )->postmeta as $meta ) {
						$meta_key = (string) $meta->meta_key;
						$meta_val = (string) $meta->meta_value;
						
						// Handle thumbnail meta mapping
						if ( $meta_key === '_thumbnail_id' ) {
							$old_thumb_id = (int) $meta_val;
							if ( isset( $id_mappings[ $old_thumb_id ] ) ) {
								update_post_meta( $new_post_id, '_thumbnail_id', $id_mappings[ $old_thumb_id ] );
							}
						} else {
							update_post_meta( $new_post_id, $meta_key, maybe_unserialize( $meta_val ) );
						}
					}
				}

				// Import Taxonomies (Categories, tags, etc.)
				if ( isset( $item->category ) ) {
					foreach ( $item->category as $cat ) {
						$domain = (string) $cat['domain'];
						$slug   = (string) $cat['nicename'];
						$name   = (string) $cat;
						
						if ( ! empty( $domain ) && ! empty( $slug ) ) {
							// Create term if it doesn't exist
							$term = get_term_by( 'slug', $slug, $domain );
							if ( ! $term ) {
								$term_data = wp_insert_term( $name, $domain, array( 'slug' => $slug ) );
								$term_id   = ! is_wp_error( $term_data ) ? $term_data['term_id'] : 0;
							} else {
								$term_id = $term->term_id;
							}

							if ( $term_id ) {
								wp_set_object_terms( $new_post_id, array( $term_id ), $domain, true );
							}
						}
					}
				}
			}
		}

		// Save state back to transient
		set_transient( 'woocom_import_processed_items', $end_index, HOUR_IN_SECONDS );
		set_transient( 'woocom_import_id_mappings', $id_mappings, HOUR_IN_SECONDS );

		wp_send_json_success( array(
			'processed' => $end_index,
			'total'     => $total_items,
			'message'   => sprintf( esc_html__( 'Imported content: %d of %d items completed...', 'woocom' ), $end_index, $total_items )
		) );
	}

	/**
	 * Helper: Sideload media file from URL
	 */
	private function sideload_media_file( $url, $item ) {
		// Clean and sanitize url
		$url = esc_url_raw( $url );
		if ( empty( $url ) ) return 0;

		// Check if file is already imported
		$post_name = (string) $item->children( 'http://wordpress.org/export/1.2/' )->post_name;
		$existing = $this->get_existing_post( $post_name, 'attachment' );
		if ( $existing ) {
			return $existing->ID;
		}

		// Sideload file
		$tmp = download_url( $url );
		if ( is_wp_error( $tmp ) ) {
			return 0; // Skip if download failed
		}

		$file_array = array(
			'name'     => basename( $url ),
			'tmp_name' => $tmp,
		);

		// Upload the file to uploads directory
		$attachment_id = media_handle_sideload( $file_array, 0, (string) $item->title );
		
		if ( ! is_wp_error( $attachment_id ) ) {
			// Update meta data if any
			$wp_ns = 'http://wordpress.org/export/1.2/';
			if ( isset( $item->children( $wp_ns )->postmeta ) ) {
				foreach ( $item->children( $wp_ns )->postmeta as $meta ) {
					update_post_meta( $attachment_id, (string) $meta->meta_key, maybe_unserialize( (string) $meta->meta_value ) );
				}
			}
			return $attachment_id;
		}

		return 0;
	}

	/**
	 * Helper: Fetch existing post by name and type
	 */
	private function get_existing_post( $slug, $post_type ) {
		if ( empty( $slug ) ) return null;
		
		$posts = get_posts( array(
			'name'           => $slug,
			'post_type'      => $post_type,
			'post_status'    => 'any',
			'posts_per_page' => 1,
		) );

		return ! empty( $posts ) ? $posts[0] : null;
	}

	/**
	 * 3. Ajax: Import Theme Options
	 */
	public function ajax_import_options() {
		check_ajax_referer( 'woocom_import_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Unauthorized user.', 'woocom' ) ) );
		}

		$demo = get_transient( 'woocom_import_current_demo' );
		if ( ! $demo ) {
			wp_send_json_error( array( 'message' => esc_html__( 'No active import session found.', 'woocom' ) ) );
		}

		$options_path = get_template_directory() . '/inc/demo-data/' . $demo . '/options.json';
		if ( ! file_exists( $options_path ) ) {
			// Skip quietly if settings files aren't bundled yet
			wp_send_json_success( array( 'message' => esc_html__( 'Settings file not found. Skipping option setup...', 'woocom' ) ) );
		}

		$raw_data = file_get_contents( $options_path );
		$options = json_decode( $raw_data, true );

		if ( ! is_array( $options ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid theme settings JSON data.', 'woocom' ) ) );
		}

		// Retrieve all registered keys to prevent importing arbitrary keys
		$registered_keys = self::get_theme_option_keys();

		// Sideload images/logos in option URL fields if needed
		$id_mappings = get_transient( 'woocom_import_id_mappings' ) ?: array();

		foreach ( $options as $key => $val ) {
			if ( in_array( $key, $registered_keys, true ) ) {
				// Sideload media option mapping (if logo or banner contains an attachment ID in import package)
				if ( in_array( $key, array( 'theme_logo', 'footer_logo', 'promo_banner_1', 'promo_banner_2', 'hero_side_banner' ), true ) && is_numeric( $val ) ) {
					$old_id = (int) $val;
					if ( isset( $id_mappings[ $old_id ] ) ) {
						$val = $id_mappings[ $old_id ];
					}
				}
				
				update_option( $key, $val );
			}
		}

		wp_send_json_success( array( 'message' => esc_html__( 'Custom theme settings applied successfully.', 'woocom' ) ) );
	}

	/**
	 * 4. Ajax: Import Widgets
	 */
	public function ajax_import_widgets() {
		check_ajax_referer( 'woocom_import_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Unauthorized user.', 'woocom' ) ) );
		}

		$demo = get_transient( 'woocom_import_current_demo' );
		if ( ! $demo ) {
			wp_send_json_error( array( 'message' => esc_html__( 'No active import session found.', 'woocom' ) ) );
		}

		$widgets_path = get_template_directory() . '/inc/demo-data/' . $demo . '/widgets.json';
		if ( ! file_exists( $widgets_path ) ) {
			wp_send_json_success( array( 'message' => esc_html__( 'Widgets file not found. Skipping widget setup...', 'woocom' ) ) );
		}

		$raw_data = file_get_contents( $widgets_path );
		$data = json_decode( $raw_data, true );

		if ( ! is_array( $data ) || ! isset( $data['sidebars'] ) || ! isset( $data['widgets'] ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid widget configuration data.', 'woocom' ) ) );
		}

		// Map widget settings
		foreach ( $data['widgets'] as $widget_name => $instances ) {
			$existing_instances = get_option( 'widget_' . $widget_name ) ?: array();
			
			foreach ( $instances as $instance_id => $settings ) {
				$existing_instances[ $instance_id ] = $settings;
			}
			
			update_option( 'widget_' . $widget_name, $existing_instances );
		}

		// Assign widgets to sidebars
		update_option( 'sidebars_widgets', $data['sidebars'] );

		wp_send_json_success( array( 'message' => esc_html__( 'Footer and Sidebar widgets configured successfully.', 'woocom' ) ) );
	}

	/**
	 * 5. Ajax: Finalize Import
	 */
	public function ajax_import_finalize() {
		check_ajax_referer( 'woocom_import_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Unauthorized user.', 'woocom' ) ) );
		}

		$demo = get_transient( 'woocom_import_current_demo' ) ?: 'default';
		$id_mappings = get_transient( 'woocom_import_id_mappings' ) ?: array();

		// Clear transients
		delete_transient( 'woocom_import_current_demo' );
		delete_transient( 'woocom_import_processed_items' );
		delete_transient( 'woocom_import_id_mappings' );

		// Create WooCommerce pages if WooCommerce is active and they don't exist
		if ( class_exists( 'WooCommerce' ) ) {
			if ( class_exists( 'WC_Install' ) && method_exists( 'WC_Install', 'create_pages' ) ) {
				WC_Install::create_pages();
			}

			// Automatically seed demo products and categories dynamically based on active demo layout
			$this->seed_demo_products( $demo );
		}

		// Create Home page if it doesn't exist
		$home_page = get_page_by_path( 'home' );
		if ( ! $home_page ) {
			$home_page_id = wp_insert_post( array(
				'post_title'   => 'Home',
				'post_content' => '',
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_name'    => 'home',
			) );
			if ( ! is_wp_error( $home_page_id ) && $home_page_id ) {
				$home_page = get_post( $home_page_id );
			}
		}

		// Set Static Front Page (Home)
		if ( $home_page ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $home_page->ID );
		}

		// Create Blog page if it doesn't exist
		$blog_page = get_page_by_path( 'blog' );
		if ( ! $blog_page ) {
			$blog_page_id = wp_insert_post( array(
				'post_title'   => 'Blog',
				'post_content' => '',
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_name'    => 'blog',
			) );
			if ( ! is_wp_error( $blog_page_id ) && $blog_page_id ) {
				$blog_page = get_post( $blog_page_id );
			}
		}

		// Set Blog page as posts page
		if ( $blog_page ) {
			update_option( 'page_for_posts', $blog_page->ID );
		}

		// Set WooCommerce Shop Page IDs in settings
		$shop_page = get_page_by_path( 'shop' );
		if ( $shop_page && class_exists( 'WooCommerce' ) ) {
			update_option( 'woocommerce_shop_page_id', $shop_page->ID );
			update_option( 'woocommerce_cart_page_id', get_page_by_path( 'cart' ) ? get_page_by_path( 'cart' )->ID : 0 );
			update_option( 'woocommerce_checkout_page_id', get_page_by_path( 'checkout' ) ? get_page_by_path( 'checkout' )->ID : 0 );
			update_option( 'woocommerce_myaccount_page_id', get_page_by_path( 'my-account' ) ? get_page_by_path( 'my-account' )->ID : 0 );
		}

		// Mark setup as complete
		update_option( 'woocom_setup_complete', '1' );

		// Assign Navigation Menus to locations
		$menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
		if ( ! empty( $menus ) && ! is_wp_error( $menus ) ) {
			$locations = get_theme_mod( 'nav_menu_locations' ) ?: array();
			
			foreach ( $menus as $menu ) {
				if ( strtolower( $menu->name ) === 'primary' || strtolower( $menu->name ) === 'main' ) {
					$locations['menu-1'] = $menu->term_id;
				}
			}
			set_theme_mod( 'nav_menu_locations', $locations );
		}

		wp_send_json_success( array( 'message' => esc_html__( 'All done! Site converted into WooCommerce shop successfully.', 'woocom' ) ) );
	}

	/**
	 * Helper: Download and import an external image as a WordPress attachment
	 */
	private function import_external_image( $url, $title ) {
		$url = esc_url_raw( $url );
		if ( empty( $url ) ) {
			return 0;
		}

		// Check if file is already imported by looking up its slug/name
		$filename = sanitize_file_name( basename( parse_url( $url, PHP_URL_PATH ) ) );
		$existing = get_posts( array(
			'post_type'      => 'attachment',
			'name'           => pathinfo( $filename, PATHINFO_FILENAME ),
			'posts_per_page' => 1,
			'post_status'    => 'any',
		) );
		if ( ! empty( $existing ) ) {
			return $existing[0]->ID;
		}

		// Ensure WordPress media upload functions are loaded
		if ( ! function_exists( 'download_url' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
		if ( ! function_exists( 'media_handle_sideload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/media.php';
		}
		if ( ! function_exists( 'wp_crop_image' ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}

		// Download the file
		$tmp = download_url( $url );
		if ( is_wp_error( $tmp ) ) {
			return 0;
		}

		$file_array = array(
			'name'     => $filename,
			'tmp_name' => $tmp,
		);

		// Sideload the media file
		$attachment_id = media_handle_sideload( $file_array, 0, $title );
		if ( ! is_wp_error( $attachment_id ) ) {
			return $attachment_id;
		}

		return 0;
	}

	/**
	 * Seed demo categories, products, and sliders with images based on the active demo
	 */
	private function seed_demo_products( $demo ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		// Check if any product exists
		$existing_products = get_posts( array(
			'post_type'      => 'product',
			'post_status'    => 'any',
			'posts_per_page' => 1,
		) );

		if ( ! empty( $existing_products ) ) {
			return; // Avoid seeding if products are already present
		}

		if ( $demo === 'grocery' ) {
			// 1. Create Categories and import their thumbnails
			$cat_data = array(
				'organic-honey' => array(
					'name'  => 'Organic Honey',
					'image' => 'https://images.unsplash.com/photo-1587049352846-4a222e784d38?auto=format&fit=crop&w=400&h=400&q=80'
				),
				'fresh-fruits' => array(
					'name'  => 'Fresh Fruits',
					'image' => 'https://images.unsplash.com/photo-1619546813926-a78fa6372cd2?auto=format&fit=crop&w=400&h=400&q=80'
				),
				'fresh-vegetables' => array(
					'name'  => 'Fresh Vegetables',
					'image' => 'https://images.unsplash.com/photo-1566385278603-605b637f3ae6?auto=format&fit=crop&w=400&h=400&q=80'
				),
				'dairy-eggs' => array(
					'name'  => 'Dairy & Eggs',
					'image' => 'https://images.unsplash.com/photo-1516448620398-c5f44bf9f441?auto=format&fit=crop&w=400&h=400&q=80'
				)
			);

			$cat_ids = array();
			foreach ( $cat_data as $slug => $data ) {
				$term = wp_insert_term( $data['name'], 'product_cat', array( 'slug' => $slug ) );
				$term_id = ! is_wp_error( $term ) ? $term['term_id'] : get_term_by( 'slug', $slug, 'product_cat' )->term_id;
				
				if ( $term_id ) {
					$img_id = $this->import_external_image( $data['image'], $data['name'] );
					if ( $img_id ) {
						update_term_meta( $term_id, 'thumbnail_id', $img_id );
					}
					$cat_ids[ $slug ] = $term_id;
				}
			}

			// Update theme options with created category IDs
			if ( ! empty( $cat_ids ) ) {
				$id_strings = array_map( 'strval', array_values( $cat_ids ) );
				update_option( 'woocom_featured_categories', $id_strings );
				update_option( 'woocom_category_sections', $id_strings );
			}

			// 2. Create products with images and prices (10 per category)
			$products_data = array(
				// Organic Honey
				array('title' => 'Organic Raw Honey 500g', 'price' => 450, 'slug' => 'organic-honey', 'image' => 'https://images.unsplash.com/photo-1587049352846-4a222e784d38?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Mustard Flower Honey 1kg', 'price' => 380, 'slug' => 'organic-honey', 'image' => 'https://images.unsplash.com/photo-1558642452-9d2a7deb7f62?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Black Seed Honey 500g', 'price' => 650, 'slug' => 'organic-honey', 'image' => 'https://images.unsplash.com/photo-1471193945509-9ad0617afabf?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Sundarban Wild Honey 1kg', 'price' => 950, 'slug' => 'organic-honey', 'image' => 'https://images.unsplash.com/photo-1587049352851-8d4e89134292?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Litchi Flower Honey 500g', 'price' => 300, 'slug' => 'organic-honey', 'image' => 'https://images.unsplash.com/photo-1622484211148-716598e09141?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Comb Honey Premium 250g', 'price' => 550, 'slug' => 'organic-honey', 'image' => 'https://images.unsplash.com/photo-1587049352847-81a56d773cae?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Organic Ginger Honey 300g', 'price' => 420, 'slug' => 'organic-honey', 'image' => 'https://images.unsplash.com/photo-1612499259837-142bf353272e?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Lemon Infused Honey 300g', 'price' => 400, 'slug' => 'organic-honey', 'image' => 'https://images.unsplash.com/photo-1601614272186-044e135be362?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Cinnamon Honey Spread 250g', 'price' => 480, 'slug' => 'organic-honey', 'image' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Pure Eucalyptus Honey 500g', 'price' => 600, 'slug' => 'organic-honey', 'image' => 'https://images.unsplash.com/photo-1600712242805-5f780e723166?auto=format&fit=crop&w=400&h=400&q=80'),

				// Fresh Fruits
				array('title' => 'Fresh Rajshahi Mangoes 5kg', 'price' => 600, 'slug' => 'fresh-fruits', 'image' => 'https://images.unsplash.com/photo-1553279768-865429fa0078?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Premium Black Dates 500g', 'price' => 650, 'slug' => 'fresh-fruits', 'image' => 'https://images.unsplash.com/photo-1569870499705-504209102861?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Organic Red Apple 1kg', 'price' => 280, 'slug' => 'fresh-fruits', 'image' => 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Sweet Green Grapes 1kg', 'price' => 350, 'slug' => 'fresh-fruits', 'image' => 'https://images.unsplash.com/photo-1537089946813-ac98789935e6?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Fresh Banana Champa 1 Dozen', 'price' => 90, 'slug' => 'fresh-fruits', 'image' => 'https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Ripe Papaya Premium 1pc', 'price' => 150, 'slug' => 'fresh-fruits', 'image' => 'https://images.unsplash.com/photo-1526318896980-cf78c088247c?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Juicy Orange Malta 1kg', 'price' => 240, 'slug' => 'fresh-fruits', 'image' => 'https://images.unsplash.com/photo-1611080626919-7cf5a9dbab5b?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Fresh Pineapple Sylhet 1pc', 'price' => 80, 'slug' => 'fresh-fruits', 'image' => 'https://images.unsplash.com/photo-1550258987-190a2d41a8ba?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Pomegranate Red 1kg', 'price' => 420, 'slug' => 'fresh-fruits', 'image' => 'https://images.unsplash.com/photo-1587132137056-bfbf0166836e?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Fresh Guava 1kg', 'price' => 120, 'slug' => 'fresh-fruits', 'image' => 'https://images.unsplash.com/photo-1601275868399-45bec4f4cd9d?auto=format&fit=crop&w=400&h=400&q=80'),

				// Fresh Vegetables
				array('title' => 'Fresh Red Tomatoes 1kg', 'price' => 120, 'slug' => 'fresh-vegetables', 'image' => 'https://images.unsplash.com/photo-1595855759920-86582396756a?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Organic Green Cucumber 1kg', 'price' => 80, 'slug' => 'fresh-vegetables', 'image' => 'https://images.unsplash.com/photo-1449300079323-02e209d9d3a6?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Fresh Potato Premium 2kg', 'price' => 90, 'slug' => 'fresh-vegetables', 'image' => 'https://images.unsplash.com/photo-1518977676601-b53f82aba655?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Organic Red Onion 1kg', 'price' => 110, 'slug' => 'fresh-vegetables', 'image' => 'https://images.unsplash.com/photo-1508747703725-719ae2f286cf?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Green Chili Premium 250g', 'price' => 40, 'slug' => 'fresh-vegetables', 'image' => 'https://images.unsplash.com/photo-1588252303782-cb80119cb665?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Fresh Eggplant Purple 1kg', 'price' => 100, 'slug' => 'fresh-vegetables', 'image' => 'https://images.unsplash.com/photo-1601493700631-2b16ec4b4716?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Organic Cauliflower 1pc', 'price' => 60, 'slug' => 'fresh-vegetables', 'image' => 'https://images.unsplash.com/photo-1568584711075-3d021a7c3ecf?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Fresh Green Cabbage 1pc', 'price' => 50, 'slug' => 'fresh-vegetables', 'image' => 'https://images.unsplash.com/photo-1550147760-44c9966d6bc7?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Organic Carrot Orange 1kg', 'price' => 130, 'slug' => 'fresh-vegetables', 'image' => 'https://images.unsplash.com/photo-1598170845058-32b9d6a5da37?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Fresh Lemon Elachi 4pcs', 'price' => 30, 'slug' => 'fresh-vegetables', 'image' => 'https://images.unsplash.com/photo-1590502593747-42a996133562?auto=format&fit=crop&w=400&h=400&q=80'),

				// Dairy & Eggs
				array('title' => 'Pure Cow Milk 1L', 'price' => 90, 'slug' => 'dairy-eggs', 'image' => 'https://images.unsplash.com/photo-1563636619-e9143da7973b?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Organic Farm Eggs 1 Dozen', 'price' => 150, 'slug' => 'dairy-eggs', 'image' => 'https://images.unsplash.com/photo-1516448620398-c5f44bf9f441?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Pure Cow Ghee 1L', 'price' => 1200, 'slug' => 'dairy-eggs', 'image' => 'https://images.unsplash.com/photo-1631515243349-e0cb75fb8d3a?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Fresh Salted Butter 200g', 'price' => 220, 'slug' => 'dairy-eggs', 'image' => 'https://images.unsplash.com/photo-1589985270826-4b7bb135bc9d?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Sweet Yogurt Sweet 1kg', 'price' => 280, 'slug' => 'dairy-eggs', 'image' => 'https://images.unsplash.com/photo-1488477181946-6428a0291777?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Sour Yogurt Premium 500g', 'price' => 140, 'slug' => 'dairy-eggs', 'image' => 'https://images.unsplash.com/photo-1571244856353-fb0e5340632a?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Paneer Fresh Cheese 250g', 'price' => 180, 'slug' => 'dairy-eggs', 'image' => 'https://images.unsplash.com/photo-1552767059-ce182ead6c1b?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Fresh Heavy Cream 200ml', 'price' => 160, 'slug' => 'dairy-eggs', 'image' => 'https://images.unsplash.com/photo-1553909489-cd47e0907980?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Organic Quail Eggs 24pcs', 'price' => 110, 'slug' => 'dairy-eggs', 'image' => 'https://images.unsplash.com/photo-1618258380237-775797300c1c?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Premium Chocolate Milk 250ml', 'price' => 50, 'slug' => 'dairy-eggs', 'image' => 'https://images.unsplash.com/photo-1556881286-fc6915169721?auto=format&fit=crop&w=400&h=400&q=80')
			);

			$first_successful_img = 0;
			foreach ( $products_data as $pd ) {
				$post_id = wp_insert_post( array(
					'post_title'   => $pd['title'],
					'post_content' => 'This is a premium organic product sourced from nature.',
					'post_status'  => 'publish',
					'post_type'    => 'product',
				) );

				if ( ! is_wp_error( $post_id ) ) {
					update_post_meta( $post_id, '_price', $pd['price'] );
					update_post_meta( $post_id, '_regular_price', $pd['price'] + rand(20, 100) );
					update_post_meta( $post_id, '_sale_price', $pd['price'] );
					update_post_meta( $post_id, '_sku', 'GR-' . strtoupper( str_replace( '-', '', sanitize_title( $pd['title'] ) ) ) );
					update_post_meta( $post_id, '_visibility', 'visible' );
					update_post_meta( $post_id, '_stock_status', 'instock' );
					
					$img_id = $this->import_external_image( $pd['image'], $pd['title'] );
					if ( ! $img_id && $first_successful_img ) {
						$img_id = $first_successful_img;
					}
					if ( $img_id && ! $first_successful_img ) {
						$first_successful_img = $img_id;
					}
					if ( $img_id ) {
						update_post_meta( $post_id, '_thumbnail_id', $img_id );
					}
					wp_set_object_terms( $post_id, array( $cat_ids[ $pd['slug'] ] ), 'product_cat' );
				}
			}

			// 3. Set up grocery home slides
			$slide_img_url = 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1200&h=400&q=80';
			$slide_img_id = $this->import_external_image( $slide_img_url, 'Grocery Hero Slide' );
			if ( $slide_img_id ) {
				$slides = array(
					array(
						'image' => (string) $slide_img_id,
						'link'  => '#'
					)
				);
				update_option( 'woocom_hero_slides', wp_json_encode( $slides ) );
			}

			// Set brand color
			update_option( 'woocom_primary_color', '#056600' );

			// 4. Seed dual promo banners
			$promo_1_url = 'https://images.unsplash.com/photo-1506084868230-bb9d95c24759?auto=format&fit=crop&w=600&h=300&q=80';
			$promo_1_img = $this->import_external_image( $promo_1_url, 'Promo Banner 1' );
			if ( $promo_1_img ) {
				update_option( 'promo_banner_1', wp_get_attachment_url( $promo_1_img ) );
			}
			$promo_2_url = 'https://images.unsplash.com/photo-1490818384979-8bb4a7c2e7a5?auto=format&fit=crop&w=600&h=300&q=80';
			$promo_2_img = $this->import_external_image( $promo_2_url, 'Promo Banner 2' );
			if ( $promo_2_img ) {
				update_option( 'promo_banner_2', wp_get_attachment_url( $promo_2_img ) );
			}

		} elseif ( $demo === 'gadget' ) {
			// gadget demo
			// 1. Create Categories and import their thumbnails
			$cat_data = array(
				'smartphones' => array(
					'name'  => 'Smartphones',
					'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=400&h=400&q=80'
				),
				'smart-watches' => array(
					'name'  => 'Smart Watches',
					'image' => 'https://images.unsplash.com/photo-1542496658-e33a6d0d50f6?auto=format&fit=crop&w=400&h=400&q=80'
				),
				'audio' => array(
					'name'  => 'Audio Devices',
					'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=400&h=400&q=80'
				),
				'accessories' => array(
					'name'  => 'Accessories',
					'image' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=400&h=400&q=80'
				)
			);

			$cat_ids = array();
			foreach ( $cat_data as $slug => $data ) {
				$term = wp_insert_term( $data['name'], 'product_cat', array( 'slug' => $slug ) );
				$term_id = ! is_wp_error( $term ) ? $term['term_id'] : get_term_by( 'slug', $slug, 'product_cat' )->term_id;
				
				if ( $term_id ) {
					$img_id = $this->import_external_image( $data['image'], $data['name'] );
					if ( $img_id ) {
						update_term_meta( $term_id, 'thumbnail_id', $img_id );
					}
					$cat_ids[ $slug ] = $term_id;
				}
			}

			// Update theme options with created category IDs
			if ( ! empty( $cat_ids ) ) {
				$id_strings = array_map( 'strval', array_values( $cat_ids ) );
				update_option( 'woocom_featured_categories', $id_strings );
				update_option( 'woocom_category_sections', $id_strings );
			}

			// 2. Create products with images and prices (10 per category)
			$products_data = array(
				// Smartphones
				array('title' => 'iPhone 15 Pro Max 256GB', 'price' => 135000, 'slug' => 'smartphones', 'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Samsung Galaxy S24 Ultra', 'price' => 125000, 'slug' => 'smartphones', 'image' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Google Pixel 8 Pro', 'price' => 95000, 'slug' => 'smartphones', 'image' => 'https://images.unsplash.com/photo-1598327106026-d9521da673d1?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'OnePlus 12 5G 512GB', 'price' => 85000, 'slug' => 'smartphones', 'image' => 'https://images.unsplash.com/photo-1565630916779-e303be97b6f5?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Xiaomi 14 Ultra 5G', 'price' => 105000, 'slug' => 'smartphones', 'image' => 'https://images.unsplash.com/photo-1580910051074-3eb694886505?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Nothing Phone 2', 'price' => 55000, 'slug' => 'smartphones', 'image' => 'https://images.unsplash.com/photo-1616348436168-de43ad0db179?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Redmi Note 13 Pro+', 'price' => 38000, 'slug' => 'smartphones', 'image' => 'https://images.unsplash.com/photo-1523206489230-c012c64b2b48?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Realme 12 Pro Plus', 'price' => 35000, 'slug' => 'smartphones', 'image' => 'https://images.unsplash.com/photo-1573148195900-7845dcb9b127?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Poco X6 Pro 5G', 'price' => 32000, 'slug' => 'smartphones', 'image' => 'https://images.unsplash.com/photo-1598327106026-d9521da673d1?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Infinix Note 40 Pro', 'price' => 26000, 'slug' => 'smartphones', 'image' => 'https://images.unsplash.com/photo-1557180295-76eee20ae8aa?auto=format&fit=crop&w=400&h=400&q=80'),

				// Smart Watches
				array('title' => 'Apple Watch Ultra 2', 'price' => 95000, 'slug' => 'smart-watches', 'image' => 'https://images.unsplash.com/photo-1434494878577-86c23bcb06b9?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Samsung Galaxy Watch 6', 'price' => 28000, 'slug' => 'smart-watches', 'image' => 'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Garmin Fenix 7 Pro', 'price' => 75000, 'slug' => 'smart-watches', 'image' => 'https://images.unsplash.com/photo-1579586337278-3befd40fd17a?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Amazfit GTR 4 Smart', 'price' => 18000, 'slug' => 'smart-watches', 'image' => 'https://images.unsplash.com/photo-1517502884422-41eaaced0168?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Huawei Watch GT 4', 'price' => 22000, 'slug' => 'smart-watches', 'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Fitbit Sense 2 Advanced', 'price' => 24000, 'slug' => 'smart-watches', 'image' => 'https://images.unsplash.com/photo-1575311373937-040b8e1fd5b6?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Redmi Watch 4 Active', 'price' => 8500, 'slug' => 'smart-watches', 'image' => 'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Haylou Solar Lite', 'price' => 3200, 'slug' => 'smart-watches', 'image' => 'https://images.unsplash.com/photo-1542496658-e33a6d0d50f6?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Colmi C81 Premium', 'price' => 4200, 'slug' => 'smart-watches', 'image' => 'https://images.unsplash.com/photo-1517502884422-41eaaced0168?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Kieslect Ks Pro Calling', 'price' => 6500, 'slug' => 'smart-watches', 'image' => 'https://images.unsplash.com/photo-1434494878577-86c23bcb06b9?auto=format&fit=crop&w=400&h=400&q=80'),

				// Audio Devices
				array('title' => 'Sony WH-1000XM5 ANC', 'price' => 38000, 'slug' => 'audio', 'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Apple AirPods Pro 2', 'price' => 26000, 'slug' => 'audio', 'image' => 'https://images.unsplash.com/photo-1588449668365-d15e397f6787?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Bose QuietComfort Ultra', 'price' => 42000, 'slug' => 'audio', 'image' => 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'JBL Charge 5 Bluetooth', 'price' => 15500, 'slug' => 'audio', 'image' => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Sennheiser Accentum ANC', 'price' => 18000, 'slug' => 'audio', 'image' => 'https://images.unsplash.com/photo-1583394838336-acd977736f90?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Anker Soundcore Space One', 'price' => 8500, 'slug' => 'audio', 'image' => 'https://images.unsplash.com/photo-1484704849700-f032a568e944?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'OnePlus Buds 3 Pro', 'price' => 7500, 'slug' => 'audio', 'image' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Marshall Emberton II', 'price' => 18500, 'slug' => 'audio', 'image' => 'https://images.unsplash.com/photo-1593121925328-78b1b8860941?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Realme Buds Air 5 Pro', 'price' => 5800, 'slug' => 'audio', 'image' => 'https://images.unsplash.com/photo-1606220588913-b3aacb4d2f46?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'QCY T13 ANC Wireless', 'price' => 1800, 'slug' => 'audio', 'image' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?auto=format&fit=crop&w=400&h=400&q=80'),

				// Accessories
				array('title' => 'Anker PowerCore 24K', 'price' => 12500, 'slug' => 'accessories', 'image' => 'https://images.unsplash.com/photo-1609592424109-dd9892f1b17c?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Logitech MX Master 3S', 'price' => 11500, 'slug' => 'accessories', 'image' => 'https://images.unsplash.com/photo-1615663245857-ac93bb7c39e7?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Keychron K2 Mechanical', 'price' => 9500, 'slug' => 'accessories', 'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Ugreen Nexode 100W GaN', 'price' => 4800, 'slug' => 'accessories', 'image' => 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Belkin 3-in-1 Charging', 'price' => 14500, 'slug' => 'accessories', 'image' => 'https://images.unsplash.com/photo-1622448255288-c71c4c1a704e?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'SanDisk Extreme 1TB SSD', 'price' => 11500, 'slug' => 'accessories', 'image' => 'https://images.unsplash.com/photo-1597872200319-3814819cb40f?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Baseus GaN5 Pro 65W', 'price' => 2800, 'slug' => 'accessories', 'image' => 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Razer DeathAdder Essential', 'price' => 2200, 'slug' => 'accessories', 'image' => 'https://images.unsplash.com/photo-1615663245857-ac93bb7c39e7?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Samsung EVO Plus 128GB', 'price' => 1600, 'slug' => 'accessories', 'image' => 'https://images.unsplash.com/photo-1597872200319-3814819cb40f?auto=format&fit=crop&w=400&h=400&q=80'),
				array('title' => 'Mcdodo Fast Charging Cable', 'price' => 850, 'slug' => 'accessories', 'image' => 'https://images.unsplash.com/photo-1609592424109-dd9892f1b17c?auto=format&fit=crop&w=400&h=400&q=80')
			);

			$first_successful_img = 0;
			foreach ( $products_data as $pd ) {
				$post_id = wp_insert_post( array(
					'post_title'   => $pd['title'],
					'post_content' => 'This is a high-performance premium tech gadget designed to elevate your lifestyle.',
					'post_status'  => 'publish',
					'post_type'    => 'product',
				) );

				if ( ! is_wp_error( $post_id ) ) {
					update_post_meta( $post_id, '_price', $pd['price'] );
					update_post_meta( $post_id, '_regular_price', $pd['price'] + rand(100, 1000) );
					update_post_meta( $post_id, '_sale_price', $pd['price'] );
					update_post_meta( $post_id, '_sku', 'GD-' . strtoupper( str_replace( '-', '', sanitize_title( $pd['title'] ) ) ) );
					update_post_meta( $post_id, '_visibility', 'visible' );
					update_post_meta( $post_id, '_stock_status', 'instock' );
					
					$img_id = $this->import_external_image( $pd['image'], $pd['title'] );
					if ( ! $img_id && $first_successful_img ) {
						$img_id = $first_successful_img;
					}
					if ( $img_id && ! $first_successful_img ) {
						$first_successful_img = $img_id;
					}
					if ( $img_id ) {
						update_post_meta( $post_id, '_thumbnail_id', $img_id );
					}
					wp_set_object_terms( $post_id, array( $cat_ids[ $pd['slug'] ] ), 'product_cat' );
				}
			}

			// 3. Set up gadget home slides
			$slide_img_url = 'https://images.unsplash.com/photo-1468495244123-6c6c332eeece?auto=format&fit=crop&w=1200&h=400&q=80';
			$slide_img_id = $this->import_external_image( $slide_img_url, 'Gadget Hero Slide' );
			if ( $slide_img_id ) {
				$slides = array(
					array(
						'image' => (string) $slide_img_id,
						'link'  => '#'
					)
				);
				update_option( 'woocom_hero_slides', wp_json_encode( $slides ) );
			}

			// Set brand color
			update_option( 'woocom_primary_color', '#2563EB' );

			// 4. Seed dual promo banners
			$promo_1_url = 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=600&h=300&q=80';
			$promo_1_img = $this->import_external_image( $promo_1_url, 'Gadget Promo Banner 1' );
			if ( $promo_1_img ) {
				update_option( 'promo_banner_1', wp_get_attachment_url( $promo_1_img ) );
			}
			$promo_2_url = 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=600&h=300&q=80';
			$promo_2_img = $this->import_external_image( $promo_2_url, 'Gadget Promo Banner 2' );
			if ( $promo_2_img ) {
				update_option( 'promo_banner_2', wp_get_attachment_url( $promo_2_img ) );
			}

		} else {
			// default demo
			// 1. Create Categories and import their thumbnails
			$cat_data = array(
				'fashion' => array(
					'name'  => 'Fashion & Apparel',
					'image' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=400&h=400&q=80'
				),
				'electronics' => array(
					'name'  => 'Electronics',
					'image' => 'https://images.unsplash.com/photo-1498049794561-7780e7231661?auto=format&fit=crop&w=400&h=400&q=80'
				),
				'home-living' => array(
					'name'  => 'Home & Living',
					'image' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=400&h=400&q=80'
				)
			);

			$cat_ids = array();
			foreach ( $cat_data as $slug => $data ) {
				$term = wp_insert_term( $data['name'], 'product_cat', array( 'slug' => $slug ) );
				$term_id = ! is_wp_error( $term ) ? $term['term_id'] : get_term_by( 'slug', $slug, 'product_cat' )->term_id;
				
				if ( $term_id ) {
					$img_id = $this->import_external_image( $data['image'], $data['name'] );
					if ( $img_id ) {
						update_term_meta( $term_id, 'thumbnail_id', $img_id );
					}
					$cat_ids[ $slug ] = $term_id;
				}
			}

			// Update theme options with created category IDs
			if ( ! empty( $cat_ids ) ) {
				$id_strings = array_map( 'strval', array_values( $cat_ids ) );
				update_option( 'woocom_featured_categories', $id_strings );
				update_option( 'woocom_category_sections', $id_strings );
			}

			// 2. Create products with images and prices
			$products_data = array(
				array(
					'title' => 'Wireless Bluetooth Headset',
					'price' => 1500,
					'cats'  => array( $cat_ids['electronics'] ),
					'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=400&h=400&q=80'
				),
				array(
					'title' => 'Smart Fitness Watch',
					'price' => 2500,
					'cats'  => array( $cat_ids['electronics'] ),
					'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=400&h=400&q=80'
				),
				array(
					'title' => 'Casual Cotton T-Shirt',
					'price' => 450,
					'cats'  => array( $cat_ids['fashion'] ),
					'image' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=400&h=400&q=80'
				),
				array(
					'title' => 'Premium Leather Wallet',
					'price' => 950,
					'cats'  => array( $cat_ids['fashion'] ),
					'image' => 'https://images.unsplash.com/photo-1627124718515-e23974d6f806?auto=format&fit=crop&w=400&h=400&q=80'
				),
				array(
					'title' => 'Ceramic Coffee Mug',
					'price' => 250,
					'cats'  => array( $cat_ids['home-living'] ),
					'image' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=400&h=400&q=80'
				),
				array(
					'title' => 'Minimalist Table Lamp',
					'price' => 1200,
					'cats'  => array( $cat_ids['home-living'] ),
					'image' => 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?auto=format&fit=crop&w=400&h=400&q=80'
				),
			);

			$first_successful_img = 0;
			foreach ( $products_data as $pd ) {
				$post_id = wp_insert_post( array(
					'post_title'   => $pd['title'],
					'post_content' => 'This is a premium product designed for everyday comfort.',
					'post_status'  => 'publish',
					'post_type'    => 'product',
				) );

				if ( ! is_wp_error( $post_id ) ) {
					update_post_meta( $post_id, '_price', $pd['price'] );
					update_post_meta( $post_id, '_regular_price', $pd['price'] + 100 );
					update_post_meta( $post_id, '_sale_price', $pd['price'] );
					update_post_meta( $post_id, '_sku', 'DF-' . strtoupper( sanitize_title( $pd['title'] ) ) );
					update_post_meta( $post_id, '_visibility', 'visible' );
					update_post_meta( $post_id, '_stock_status', 'instock' );
					
					$img_id = $this->import_external_image( $pd['image'], $pd['title'] );
					if ( ! $img_id && $first_successful_img ) {
						$img_id = $first_successful_img;
					}
					if ( $img_id && ! $first_successful_img ) {
						$first_successful_img = $img_id;
					}
					if ( $img_id ) {
						update_post_meta( $post_id, '_thumbnail_id', $img_id );
					}
					wp_set_object_terms( $post_id, $pd['cats'], 'product_cat' );
				}
			}

			// 3. Set up default home slides
			$slide_img_url = 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&w=1200&h=400&q=80';
			$slide_img_id = $this->import_external_image( $slide_img_url, 'Default Hero Slide' );
			if ( $slide_img_id ) {
				$slides = array(
					array(
						'image' => (string) $slide_img_id,
						'link'  => '#'
					)
				);
				update_option( 'woocom_hero_slides', wp_json_encode( $slides ) );
			}

			// Set brand color
			update_option( 'woocom_primary_color', '#1E5D02' );

			// 4. Seed dual promo banners
			$promo_1_url = 'https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=600&h=300&q=80';
			$promo_1_img = $this->import_external_image( $promo_1_url, 'Default Promo Banner 1' );
			if ( $promo_1_img ) {
				update_option( 'promo_banner_1', wp_get_attachment_url( $promo_1_img ) );
			}
			$promo_2_url = 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&w=600&h=300&q=80';
			$promo_2_img = $this->import_external_image( $promo_2_url, 'Default Promo Banner 2' );
			if ( $promo_2_img ) {
				update_option( 'promo_banner_2', wp_get_attachment_url( $promo_2_img ) );
			}
		}
	}

	/**
	 * Developer: Handle Options Export
	 */
	public function handle_export_options() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized user.', 'woocom' ) );
		}

		$keys = self::get_theme_option_keys();
		$export = array();

		foreach ( $keys as $key ) {
			$val = get_option( $key );
			if ( $val !== false ) {
				$export[ $key ] = $val;
			}
		}

		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="options.json"' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate' );
		header( 'Pragma: public' );
		
		echo wp_json_encode( $export, JSON_PRETTY_PRINT );
		exit;
	}

	/**
	 * Developer: Handle Widgets Export
	 */
	public function handle_export_widgets() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized user.', 'woocom' ) );
		}

		$sidebars = get_option( 'sidebars_widgets' );
		$widgets = array();

		if ( is_array( $sidebars ) ) {
			foreach ( $sidebars as $sidebar_id => $widget_ids ) {
				if ( $sidebar_id === 'wp_inactive_widgets' || ! is_array( $widget_ids ) ) {
					continue;
				}
				
				foreach ( $widget_ids as $widget_id ) {
					// Extract widget base name (e.g. text-2 -> base text, id 2)
					preg_match( '/^(.+)-(\d+)$/', $widget_id, $matches );
					if ( isset( $matches[1] ) && isset( $matches[2] ) ) {
						$base = $matches[1];
						$id = $matches[2];
						
						$instances = get_option( 'widget_' . $base );
						if ( isset( $instances[ $id ] ) ) {
							$widgets[ $base ][ $id ] = $instances[ $id ];
						}
					}
				}
			}
		}

		$export = array(
			'sidebars' => $sidebars,
			'widgets'  => $widgets,
		);

		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="widgets.json"' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate' );
		header( 'Pragma: public' );
		
		echo wp_json_encode( $export, JSON_PRETTY_PRINT );
		exit;
	}
}

new Woocom_Demo_Importer();
