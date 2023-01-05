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

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Plugin Options/settings.
	 * @var null
	 */
	private $options = null;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->options = get_option( 'google_reviews_option_name' );

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action('admin_enqueue_scripts', [$this, 'enqueue_styles']);
		add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);

		if ( grwp_fs()->is__premium_only() ) {
			add_action('wp_ajax_handle_serp_business_search', [$this, 'handle_serp_business_search']);
			add_action('wp_ajax_nopriv_handle_serp_business_search', [$this, 'handle_serp_business_search']);
		}

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/google-reviews-public.css', array(), $this->version, 'all' );

		if (strtolower($this->options['style_2']) === 'slider'){
			wp_enqueue_style('swiperjs', plugin_dir_url( __FILE__ ) . 'css/swiper-bundle.min.css', [], '8.15');
		}

		wp_enqueue_style('mcustomscrollbar',  plugin_dir_url( __FILE__ ) . 'css/jquery.mCustomScrollbar.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/google-reviews-public.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script('matchheight', plugin_dir_url(__FILE__) . 'js/jquery.matchHeight.js', ['jquery'], '1.0.0', true);

		if (strtolower($this->options['style_2']) === 'slider'){
			wp_enqueue_script('swiperjs', plugin_dir_url( __FILE__ ) . 'js/swiper-bundle.min.js', ['jquery'], '8.15', true);
			wp_enqueue_script('sliderjs', plugin_dir_url( __FILE__ ) . 'js/slider.js', ['jquery'], time(), true);
		}
		wp_enqueue_script('mcustomscrollbar', plugin_dir_url( __FILE__ ) . 'js/jquery.mCustomScrollbar.min.js', ['jquery'], time(), true);
	}

	public function handle_serp_business_search() {
        $search_value = isset( $_GET['search'] ) ? sanitize_text_field($_GET['search']) : '';
        $language     = isset( $_GET['language'] ) ? sanitize_text_field($_GET['language']) : 'en';

        $install_id = grwp_fs()->get_site()->id;
        $secret_key = base64_encode( grwp_fs()->get_site()->secret_key );

        $new_hash_request_url = 'https://api.reviewsembedder.com/generate-hash.php';

        $new_hash = wp_remote_get( $new_hash_request_url, array(
        	'headers' => array(
        		'Authorization' => $secret_key
        	)
        ) );

        $license_request_url = sprintf( 'https://api.reviewsembedder.com/get-results.php?install_id=%s&search_value=%s&language=%s', $install_id, $search_value, $language );

        $get_results = wp_remote_get( $license_request_url, array(
        	'headers' => array(
        		'Authorization' => wp_remote_retrieve_body( $new_hash )
        	)
        ) );

        $get_results = json_decode( wp_remote_retrieve_body( $get_results ) );

        if ( isset( $get_results->error_message ) ) {
            wp_send_json_error( array(
            	'html' => $get_results->error_message
            ) );

            die();
        } else if ( isset( $get_results->html ) ) {
	        wp_send_json_success( array(
				'html' => $get_results->html
			) );

            die();
        }

		die();
    }
}
