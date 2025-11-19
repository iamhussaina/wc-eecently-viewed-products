<?php
/**
 * Class Hussainas_History_Tracker
 *
 * Handles the tracking of viewed products via cookies and renders
 * the recently viewed products list via a shortcode.
 *
 * @package Hussainas_WC_History
 * @version     1.0.0
 * @author      Hussain Ahmed Shrabon
 * @license     GPL-2.0-or-later
 * @link        https://github.com/iamhussaina
 * @textdomain  hussainas
 
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Hussainas_History_Tracker {

    /**
     * Cookie name identifier.
     *
     * @var string
     */
    private $cookie_name = 'hussainas_viewed_products';

    /**
     * Initialize hooks and filters.
     */
    public function init() {
        // Hook to track product views on singular product pages.
        add_action( 'template_redirect', [ $this, 'track_product_view' ] );

        // Register the shortcode for displaying the list.
        add_shortcode( 'hussainas_recently_viewed', [ $this, 'render_shortcode' ] );
    }

    /**
     * Track the current product view and store it in a cookie.
     *
     * Logic:
     * 1. Check if it's a single product page.
     * 2. Retrieve existing cookie data.
     * 3. Add current ID to the beginning of the array.
     * 4. Remove duplicates and limit the history size (e.g., 12 items).
     * 5. Update the cookie.
     *
     * @return void
     */
    public function track_product_view() {
        if ( ! is_singular( 'product' ) ) {
            return;
        }

        global $post;

        if ( empty( $post->ID ) ) {
            return;
        }

        $current_product_id = $post->ID;
        $viewed_products    = $this->get_viewed_products_data();

        // Prepend the current product ID to the list.
        if ( ! in_array( $current_product_id, $viewed_products, true ) ) {
            array_unshift( $viewed_products, $current_product_id );
        } else {
            // If it exists, move it to the top.
            $viewed_products = array_diff( $viewed_products, [ $current_product_id ] );
            array_unshift( $viewed_products, $current_product_id );
        }

        // Limit the storage to the last 15 items to keep cookie size optimal.
        $viewed_products = array_slice( $viewed_products, 0, 15 );

        // Set cookie for 30 days.
        setcookie( 
            $this->cookie_name, 
            implode( '|', $viewed_products ), 
            time() + ( 30 * DAY_IN_SECONDS ), 
            COOKIEPATH, 
            COOKIE_DOMAIN 
        );
    }

    /**
     * Retrieve viewed product IDs from the cookie.
     *
     * @return array List of product IDs.
     */
    private function get_viewed_products_data() {
        if ( ! isset( $_COOKIE[ $this->cookie_name ] ) ) {
            return [];
        }

        $cookie_value = sanitize_text_field( $_COOKIE[ $this->cookie_name ] );
        $product_ids  = explode( '|', $cookie_value );

        return array_map( 'absint', $product_ids );
    }

    /**
     * Render the recently viewed products shortcode.
     *
     * Usage: [hussainas_recently_viewed limit="4" columns="4"]
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML content of the product grid.
     */
    public function render_shortcode( $atts ) {
        // Ensure WooCommerce is active.
        if ( ! class_exists( 'WooCommerce' ) ) {
            return '';
        }

        $atts = shortcode_atts( [
            'limit'   => 4,
            'columns' => 4,
            'title'   => 'Recently Viewed Products',
        ], $atts, 'hussainas_recently_viewed' );

        $viewed_products = $this->get_viewed_products_data();

        if ( empty( $viewed_products ) ) {
            return ''; // Return nothing if no history exists.
        }

        // Query arguments to fetch products preserving the viewed order.
        $args = [
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => intval( $atts['limit'] ),
            'post__in'       => $viewed_products,
            'orderby'        => 'post__in',
        ];

        $query = new WP_Query( $args );

        if ( ! $query->have_posts() ) {
            return '';
        }

        ob_start();

        echo '<section class="hussainas-viewed-products">';
        
        if ( ! empty( $atts['title'] ) ) {
            echo '<h2 class="hussainas-section-title">' . esc_html( $atts['title'] ) . '</h2>';
        }

        // Set the column variable for WooCommerce loop.
        wc_set_loop_prop( 'columns', intval( $atts['columns'] ) );

        woocommerce_product_loop_start();

        while ( $query->have_posts() ) {
            $query->the_post();
            wc_get_template_part( 'content', 'product' );
        }

        woocommerce_product_loop_end();

        echo '</section>';

        wp_reset_postdata();

        return ob_get_clean();
    }
}
