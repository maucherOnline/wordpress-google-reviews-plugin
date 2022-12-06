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
class GRWP_Google_Reviews {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      GRWP_Google_Reviews_Loader    $loader    Maintains and registers all hooks for the plugin.
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

		if ( defined( 'GRWP_GOOGLE_REVIEWS_VERSION' ) ) {
			$this->version = GRWP_GOOGLE_REVIEWS_VERSION;
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

    public static function get_reviews() {

        $google_reviews_options = get_option( 'google_reviews_option_name' );

        if ( grwp_fs()->is__premium_only() ) {

	        $data_id          = $google_reviews_options['serp_data_id'];
	        $reviews_language = $google_reviews_options['reviews_language_3'];

	        if ( empty( $data_id ) ) {
	        	update_option( 'gr_latest_results', null );

	        	return;
	        }

			$install_id = grwp_fs()->get_site()->id;
	        $secret_key = base64_encode( grwp_fs()->get_site()->secret_key );

	        $new_hash_request_url = 'https://api.reviewsembedder.com/generate-hash.php';

	        $new_hash = wp_remote_get( $new_hash_request_url, array(
	        	'headers' => array(
	        		'Authorization' => $secret_key
	        	)
	        ) );

	        $license_request_url = sprintf( 'https://api.reviewsembedder.com/get-reviews.php?install_id=%s&data_id=%s&language=%s', $install_id, $data_id, $reviews_language );

	        $get_reviews = wp_remote_get( $license_request_url, array(
	        	'headers' => array(
	        		'Authorization' => wp_remote_retrieve_body( $new_hash )
	        	)
	        ) );

	        $get_reviews = json_decode( wp_remote_retrieve_body( $get_reviews ) );

	        update_option( 'gr_latest_results', json_encode( $get_reviews->results ) );

        } else {

        	$api_key_0 = $google_reviews_options['api_key_0'];
	        $gmb_id_1  = $google_reviews_options['gmb_id_1'];

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


    }

    /**
     * Parse json results and check for errors
     * @return mixed|WP_Error
     */

    public static function parse_review_json() {

		if ( grwp_fs()->is__premium_only() ) {
			$install_id           = grwp_fs()->get_site()->id;
			$secret_key           = base64_encode( grwp_fs()->get_site()->secret_key );
	        $new_hash_request_url = 'https://api.reviewsembedder.com/generate-hash.php';

	        $new_hash = wp_remote_get( $new_hash_request_url, array(
	        	'headers' => array(
	        		'Authorization' => $secret_key
	        	)
	        ) );

			$is_valid_url = sprintf( 'https://api.reviewsembedder.com/validate-reviews.php?install_id=%s&validate_reviews=1', $install_id );

			$is_valid = wp_remote_get( $is_valid_url, array(
	        	'headers' => array(
	        		'Authorization' => wp_remote_retrieve_body( $new_hash )
	        	)
	        ) );

	        $is_valid = json_decode( wp_remote_retrieve_body( $is_valid ) );

	        if ( ! $is_valid->results ) {
	        	return;
	        }

			$raw       = get_option('gr_latest_results');
	        $reviewArr = json_decode($raw, true);
	        $reviews   = $reviewArr['reviews'];

		} else {

			$raw           = get_option('gr_latest_results');
	        $reviewArr     = json_decode($raw, true);
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

		}

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
    public function reviews_shortcode($atts = null) {

        $allowed_html = [
            'img' => [
                'title'             => [],
                'src'               => [],
                'alt'               => [],
                'width'             => [],
                'height'            => [],
                'class'             => [],
                'data-imgtype'      => [],
                'referrerpolicy'    => [],
            ],
            'style'                     => [],
            'div'                       => [
                'class'                 => [],
                'id'                    => [],
                'data-swiper-autoplay'  => [],
            ],
            'a' => [
                'href'      => [],
                'target'    => [],
            ],
            'p' => [],
            'span' => [
                'class' => [],
                'id'    => [],
            ],
            'br' => [],
        ];

        // check if shortcode attributes are provided
        if ( $atts ) {
            $review_type_override = $atts['type'] == 'grid' ? 'grid' : '';
        }

        // prevent php notice if undefined
        $showdummy = isset($this->options['show_dummy_content']) ? true : false;

        if ( $showdummy ) {
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

        	if ( grwp_fs()->is__premium_only() ) {
        		if ( $showdummy ) {
	    			$name = $review['author_name'];
		            $author_url = $review['author_url'];
		            $profile_photo_url = $review['profile_photo_url'];
		            $rating = $review['rating'];
		            $text = $review['text'];

		            $time = date('m/d/Y', $review['time']);
        		} else {
		        	$name              = $review['user']['name'];
		        	$author_url        = $review['user']['link'];
		        	$profile_photo_url = $review['user']['thumbnail'];
		        	$rating            = $review['rating'];
		        	$text              = $review['snippet'];
        		}
        	} else {
	            $name = $review['author_name'];
	            $author_url = $review['author_url'];
	            $profile_photo_url = $review['profile_photo_url'];
	            $rating = $review['rating'];
	            $text = $review['text'];

	            $time = date('m/d/Y', $review['time']);
        	}

            $star = '<img src="'. esc_attr(plugin_dir_url( __FILE__ )).'img/svg-star.svg" alt="" />';
            $star_output = '<span class="stars-wrapper">';
            for ($i = 1; $i <= $rating; $i++) {
                $star_output .= $star;
            }

            $star_output .= '</span>';

            if ( grwp_fs()->is__premium_only() ) {
            	if ( $showdummy ) {
            		$star_output .= '<span class="time">' . $this->time_elapsed_string( date('Y-m-d h:i:s', $review['time']) ) .'</span>';
            	} else {
            		$star_output .= '<span class="time">' . $review['date'] .'</span>';
            	}
            } else {
            	$star_output .= '<span class="time">' . $this->time_elapsed_string( date('Y-m-d h:i:s', $review['time']) ) .'</span>';
            }

	        $google_svg = plugin_dir_url( __FILE__ ) . 'img/google-logo-svg.svg';

	        // @todo: get settings and display grid and/or slider.
	        $display_type = strtolower($this->options['style_2']);

            // if is slider
	        if ( $display_type === 'slider' && $review_type_override !== 'grid' ){

		        $slide_duration = $this->options['slide_duration'] ?? '';

	        	ob_start();
	        	require 'partials/slider/markup.php';
	        	$slider_output .= ob_get_clean();

	        }
            // if is grid
            else {

	        	ob_start();
		        require 'partials/grid/markup.php';
		        $output .= ob_get_clean();

	        }
        }

        // add swiper header and footer if is slider
	    if ( $display_type === 'slider' && $review_type_override !== 'grid' ) {

		    ob_start();
		    require_once 'partials/slider/slider-header.php';
		    echo wp_kses($slider_output, $allowed_html);
		    require_once 'partials/slider/slider-footer.php';

		    $output .= ob_get_clean();

	    }

        // set grid columns
	    $db_grid_columns = isset($this->options['grid_columns']) ? $this->options['grid_columns'] : 3;
	    $columns_css = '';

	    for ($x = 0; $x < $db_grid_columns; $x++){
		    $columns_css .= '1fr ';
	    }

        // add slider styles if is slider
        if ( $display_type === 'slider' && $review_type_override !== 'grid'){
        	ob_start();
	        require 'partials/slider/style.php';
	        $output .= ob_get_clean();
        }
        // else add grid styles
        else {
        	ob_start();
	        require 'partials/grid/style.php';
	        $output .= ob_get_clean();
        }

        $output .= '</div>';

        return wp_kses($output, $allowed_html);

    }

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - GRWP_Google_Reviews_Loader. Orchestrates the hooks of the plugin.
	 * - GRWP_Google_Reviews_i18n . Defines internationalization functionality.
	 * - Google_Reviews_Admin. Defines all hooks for the admin area.
	 * - GRWP_Google_Reviews_Public. Defines all hooks for the public side of the site.
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



		$this->loader = new GRWP_Google_Reviews_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the GRWP_Google_Reviews_i18n  class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new GRWP_Google_Reviews_i18n ();

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

            $plugin_admin = new GRWP_Google_ReviewsAdmin($this->get_plugin_name(), $this->get_version());

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

		$plugin_public = new GRWP_Google_Reviews_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action( 'wp_ajax_handle_serp_business_search', $plugin_public, 'handle_serp_business_search' );
        $this->loader->add_action( 'wp_ajax_nopriv_handle_serp_business_search', $plugin_public, 'handle_serp_business_search' );

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
	 * @return    GRWP_Google_Reviews_Loader    Orchestrates the hooks of the plugin.
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
