<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       test
 * @since      1.0.0
 *
 * @package    Google_Reviews
 * @subpackage Google_Reviews/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Google_Reviews
 * @subpackage Google_Reviews/public
 * @author     David Maucher <hallo@maucher-online.com>
 */
class GRWP_Google_Reviews_Public {

	private $plugin_name;

	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		if ( is_admin() && ! $this->is_plugin_admin_page() ) {
			return;
		}

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in GRWP_Google_Reviews_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The GRWP_Google_Reviews_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

        wp_enqueue_style( $this->plugin_name, GR_PLUGIN_DIR_URL . 'dist/css/google-reviews-public.css', array(), $this->version, 'all' );

        $options = get_option( 'google_reviews_option_name' );
        if ( ! empty( $options['hide_profile_picture'] ) ) {
            wp_add_inline_style( $this->plugin_name, '.gr-profile { display: none !important; }' );
        }
        if ( ! empty( $options['disable_box_shadow'] ) ) {
            wp_add_inline_style( $this->plugin_name, '#g-review[class*="layout_style"] .g-review { box-shadow: none !important; }' );
        }
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if ( is_admin() && ! $this->is_plugin_admin_page() ) {
			return;
		}

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in GRWP_Google_Reviews_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The GRWP_Google_Reviews_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

        wp_enqueue_script( $this->plugin_name, GR_PLUGIN_DIR_URL . 'dist/js/public-bundle.js', array( 'jquery' ), $this->version, true );

        $options = get_option( 'google_reviews_option_name' );
        $slider_delay = isset($options['slide_duration']) ? intval($options['slide_duration']) : 0;
        $disable_slider_loop = isset($options['disable_loop_slider']) && $options['disable_loop_slider'] == '1' ? $options['disable_loop_slider'] : false;

        $swiper_data = array(
            'disableLoop'   => $disable_slider_loop,
            'autoplayDelay' => $slider_delay,
            'showMoreText'  => __( 'Show more', 'embedder-for-google-reviews' ),
        );
        wp_localize_script( $this->plugin_name, 'swiperSettings', $swiper_data );
	}

	/**
	 * Check whether the current admin request belongs to this plugin.
	 *
	 * @return bool
	 */
	private function is_plugin_admin_page() {
		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';

		return (
			'google-reviews' === $page ||
			'how-to-free-version' === $page ||
			'how-to-premium-version' === $page
		);
	}

}
