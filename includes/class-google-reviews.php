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

    /**
     * Put together shortcode / html output
     * @return string|void
     */
    public function reviews_shortcode() {

        $reviews = $this->parse_review_json();

        if (is_wp_error($reviews) || $reviews == '' || $reviews == null) {
            return __( 'No reviews available', 'google-reviews' );
        }

        $output = '<div id="g-review">';

        foreach ($reviews as $review) {

            $name = $review['author_name'];
            $author_url = $review['author_url'];
            $profile_photo_url = $review['profile_photo_url'];
            $rating = $review['rating'];
            $text = $review['text'];

            $time = date('m/d/Y', $review['time']);

            $star = '<img src="'. plugin_dir_url( __FILE__ ).'img/svg-star.svg" alt="" />';
            $star_output = '';
            for ($i = 1; $i <= $rating; $i++) {
                $star_output .= $star;
            }

            $output .= '
                <div class="g-review">
                    <div class="gr-inner-header">
                        <img 
                            class="gr-profile"
                            src="'.$profile_photo_url.'" 
                            width="50" 
                            height="50" 
                            alt=""
                            data-imgtype="image/png" 
                            referrerpolicy="no-referrer"
                        />
                        <img 
                            src="'.plugin_dir_url( __FILE__ ).'img/google-logo-svg.svg" 
                            alt=""
                            class="gr-google" 
                        />
                        <p><a href="'.$author_url.'" target="_blank">'.$name.'</a>
                        <br>
                        <span class="gr-stars">'.$star_output.'</span></p>
                    </div>
                    
                    <div class="gr-inner-body">
                        <p>'.$text.'</p>
                    </div>
                </div>
                ';
        }
        $output .= '
            <style>
            #g-review {
                display: flex;
                flex-flow: row wrap;
                justify-content: space-between;
                gap: 32px;
            }
            #g-review .g-review {
                flex-basis: calc(50% - 80px);
                padding: 32px;
                box-shadow: 0 0 8px grey;
                background-color: white;
            }
            #g-review .gr-inner-header {
                display: flex;
                flex-flow: row wrap;
                position: relative;           
            }
            #g-review .gr-inner-header p {
                margin: 0;
                flex-basis: calc(100% - 60px);
                font-size: 16px;
                line-height: 1.5;
            }
            #g-review .gr-inner-header img.gr-profile {
                margin: 0 10px 10px 0;
            }
            #g-review .gr-inner-header img.gr-google {
                position: absolute;
                right: 0;
                top: 0;
                width: 18px;
                height: 18px;
            }
                
            #g-review .g-review .gr-stars img {
                display: inline-block !important;
                width: 18px !important;
                height: 18px !important;
                margin: 0 4px 0 0 !important;
                vertical-align: middle !important;        
            }
            </style>
            </div>';

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
