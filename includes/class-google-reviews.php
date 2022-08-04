<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       test
 * @since      1.0.0
 *
 * @package    Google_Reviews
 * @subpackage Google_Reviews/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Google_Reviews
 * @subpackage Google_Reviews/includes
 * @author     David Maucher <hallo@maucher-online.com>
 */
class Google_Reviews {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Google_Reviews_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Plugin Options/settings.
	 * @var null
	 */
	protected $options = null;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		if ( defined( 'GOOGLE_REVIEWS_VERSION' ) ) {
			$this->version = GOOGLE_REVIEWS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'google-reviews';
		$this->options = get_option( 'google_reviews_option_name' );

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

        add_shortcode('google-reviews', [ $this, 'reviews_shortcode' ] );
        add_action ( 'get_google_reviews', [ $this, 'get_reviews' ]);

        if (!wp_next_scheduled('get_google_reviews')) {
            wp_schedule_event( time(), 'daily', 'get_google_reviews' );
        }

    }

    /**
     * Must be pubilc to get called by external methods
     */
    public function get_reviews() {

        $google_reviews_options = get_option( 'google_reviews_option_name' );
        $api_key_0 = $google_reviews_options['api_key_0'];
        $gmb_id_1 = $google_reviews_options['gmb_id_1'];

        // https://developers.google.com/maps/faq#languagesupport
        $reviews_language = $google_reviews_options['reviews_language_3'];
        $url = 'https://maps.googleapis.com/maps/api/place/details/json?place_id='
            .$gmb_id_1
            .'&key='
            .$api_key_0
            .'&fields=reviews&language='
            .$reviews_language;

        $result = wp_remote_get($url);

        update_option('gr_latest_results', json_encode($result));

    }

    /**
     * Parse json results and check for errors
     * @return mixed|WP_Error
     */
    public function parse_review_json() {

        $raw = get_option('gr_latest_results');
        $reviewArr = json_decode($raw, true);
        $reviewArrBody = json_decode($reviewArr['body'], true);

        if ($reviewArrBody['status'] === 'REQUEST_DENIED') {
            return new WP_Error(
                'REQUEST_DENIED',
                $reviewArrBody['error_message']
            );
        }
        if ($reviewArrBody['status'] === 'INVALID_REQUEST') {
            return new WP_Error(
                'INVALID_REQUEST',
                __('Invalid request. Please check your place ID for errors.', 'google-reviews')
            );
        }

        $reviews = $reviewArrBody['result']['reviews'];

        return $reviews;
    }

	private function time_elapsed_string($datetime, $full = false) {
		$now = new DateTime;
		$ago = new DateTime($datetime);
		$diff = $now->diff($ago);

		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;

		$string = array(
			'y' => array(
				__( 'year', 'google-reviews' ),
				__( 'years', 'google-reviews' )
			),
			'm' => array(
				__( 'month', 'google-reviews' ),
				__( 'months', 'google-reviews' )
			),
			'w' => array(
				__( 'week', 'google-reviews' ),
				__( 'weeks', 'google-reviews' )
			),
			'd' => array(
				__( 'day', 'google-reviews' ),
				__( 'days', 'google-reviews' )
			),
			'h' => array(
				__( 'hour', 'google-reviews' ),
				__( 'hours', 'google-reviews' )
			),
			'i' => array(
				__( 'minute', 'google-reviews' ),
				__( 'minutes', 'google-reviews' )
			),
			's' => array(
				__( 'second', 'google-reviews' ),
				__( 'seconds', 'google-reviews' )
			)
		);
		foreach ($string as $k => &$v) {
			if ($diff->$k) {
				$v = $diff->$k . ' ' . ($diff->$k > 1 ? $v[1] : $v[0]);
			} else {
				unset($string[$k]);
			}
		}

		if (!$full) $string = array_slice($string, 0, 1);
		return $string ? implode(', ', $string) . ' ' . __( 'ago', 'google-reviews' ) : __( 'just now', 'google-reviews' );
	}

    /**
     * Put together shortcode / html output
     * @return string|void
     */
    public function reviews_shortcode() {


        if ( $this->options['show_dummy_content'] ) {
        	$reviews = array( array(
        		'author_name'               => __( 'Lorem Ipsum', 'google-reviews' ),
        		'author_url'                => '#',
        		'language'                  => 'en',
        		'profile_photo_url'         => plugin_dir_url( __FILE__ ) . 'img/sample-photo.png',
        		'rating'                    => 5,
        		'relative_time_description' => __( 'three months ago', 'google-reviews' ),
        		'text'                      => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', 'google-reviews' ),
        		'time'                      => '1643630205'
        	) );

        	for ( $i = 0; $i <= 4; $i++ ) {
        		$reviews[] = $reviews[0];
        	}
        } else {
        	$reviews = $this->parse_review_json();
        }

        if (is_wp_error($reviews) || $reviews == '' || $reviews == null) {
            return __( 'No reviews available', 'google-reviews' );
        }

        $output = '<div id="g-review" class="' . $this->options['layout_style'] .'">';
	    $slider_output = '';
        foreach ($reviews as $review) {

            $name = $review['author_name'];
            $author_url = $review['author_url'];
            $profile_photo_url = $review['profile_photo_url'];
            $rating = $review['rating'];
            $text = $review['text'];

            $time = date('m/d/Y', $review['time']);

            $star = '<img src="'. esc_attr(plugin_dir_url( __FILE__ )).'img/svg-star.svg" alt="" />';
            $star_output = '<span class="stars-wrapper">';
            for ($i = 1; $i <= $rating; $i++) {
                $star_output .= $star;
            }

            $star_output .= '</span>';

            $star_output .= '<span class="time">' . $this->time_elapsed_string( date('Y-m-d h:i:s', $review['time']) ) .'</span>';
	        $google_svg = plugin_dir_url( __FILE__ ) . 'img/google-logo-svg.svg';

	        // @todo: get settings and display grid and/or slider.
	        $display_type = strtolower($this->options['style_2']);

	        if ($display_type === 'slider'){
		        $slide_duration = $this->options['slide_duration'];

	        	ob_start();
	        	require 'partials/slider/markup.php';
	        	$slider_output .= ob_get_clean();
	        }else{
	        	ob_start();
		        require 'partials/grid/markup.php';
		        $output .= ob_get_clean();
	        }
        }


	    if ($display_type === 'slider'){
		    ob_start();
		    require_once 'partials/slider/slider-header.php';
		    echo $slider_output;
		    require_once 'partials/slider/slider-footer.php';

		    $output .= ob_get_clean();
	    }

	    $db_grid_columns = isset($this->options['grid_columns']) ? $this->options['grid_columns'] : 3;
	    $columns_css = '';

	    for ($x = 0; $x < $db_grid_columns; $x++){
		    $columns_css .= '1fr ';
	    }

        if ($display_type === 'slider'){
        	ob_start();
	        require 'partials/slider/style.php';
	        $output .= ob_get_clean();
        }else{
        	ob_start();
	        require 'partials/grid/style.php';
	        $output .= ob_get_clean();
        }

        $output .= '</div>';

        return $output;

    }

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Google_Reviews_Loader. Orchestrates the hooks of the plugin.
	 * - Google_Reviews_i18n. Defines internationalization functionality.
	 * - Google_Reviews_Admin. Defines all hooks for the admin area.
	 * - Google_Reviews_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-google-reviews-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-google-reviews-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-google-reviews-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-google-reviews-public.php';



		$this->loader = new Google_Reviews_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Google_Reviews_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Google_Reviews_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

	    if ( is_admin() ) {

            $plugin_admin = new GoogleReviews($this->get_plugin_name(), $this->get_version());

            $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
            $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

        }

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Google_Reviews_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Google_Reviews_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
