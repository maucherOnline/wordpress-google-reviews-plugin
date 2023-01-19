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

        if (isset($this->options['style_2'])) {
            if (strtolower($this->options['style_2']) === 'slider') {
                wp_enqueue_style('swiperjs', plugin_dir_url(__FILE__) . 'css/swiper-bundle.min.css', [], '8.15');
            }
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

        if (isset($this->options['style_2'])) {
            if (strtolower($this->options['style_2']) === 'slider') {
                wp_enqueue_script('swiperjs', plugin_dir_url(__FILE__) . 'js/swiper-bundle.min.js', ['jquery'], '8.15', true);
                wp_enqueue_script('sliderjs', plugin_dir_url(__FILE__) . 'js/slider.js', ['jquery'], time(), true);
            }
        }
		wp_enqueue_script('mcustomscrollbar', plugin_dir_url( __FILE__ ) . 'js/jquery.mCustomScrollbar.min.js', ['jquery'], time(), true);
	}

}
