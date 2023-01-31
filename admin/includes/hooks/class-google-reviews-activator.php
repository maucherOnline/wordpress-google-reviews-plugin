<?php

/**
 * Fired during plugin activation
 *
 * @link       test
 * @since      1.0.0
 *
 * @package    Google_Reviews
 * @subpackage Google_Reviews/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Google_Reviews
 * @subpackage Google_Reviews/includes
 * @author     David Maucher <hallo@maucher-online.com>
 */
class GRWP_Google_Reviews_Activator {

    /**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

        // add dummy content setting as default
        $google_reviews_options = get_option( 'google_reviews_option_name' );
        if ( ! $google_reviews_options ) {
            $google_reviews_options = [];
            $google_reviews_options['dummy_content'] = '1';
            add_option('google_reviews_option_name', $google_reviews_options);
        }

        // add pro version results field
        if ( ! get_option('gr_latest_results') ) {
            add_option('gr_latest_results','');
        }

        // add free version results field
        if ( ! get_option('gr_latest_results_free') ) {
            add_option('gr_latest_results_free', '');
        }

        // add wp cron
        if ( ! wp_next_scheduled('get_google_reviews' ) ) {

            wp_schedule_event( time(), 'weekly', 'get_google_reviews' );

        }



	}

}
