<?php

Class Free_API_Service {

    public function __construct() {
        add_action('updated_option', array($this, 'on_saving_options'), 10, 3);
    }

    /**
     * Get reviews from Free API
     * @return void
     */
    static function get_reviews_free_api() {

        $google_reviews_options = get_option( 'google_reviews_option_name' );
        $gmb_id_1  = $google_reviews_options['gmb_id_1'];
        $reviews_language = $google_reviews_options['reviews_language_3'];
        $url = 'https://api.reviewsembedder.com/free-api.php?gmb='.$gmb_id_1.'&language='.$reviews_language;

        $result = wp_remote_get($url);

        $review_json = Free_API_Service::parse_free_review_json();

        if ( is_wp_error( $review_json ) ) {

            add_settings_error(

                'google_reviews_option_name',
                esc_attr( 'settings_updated' ),
                $review_json->get_error_message()

            );

        }

        update_option('gr_latest_results_free', json_encode($result));

    }

    /**
     * Check for errors in the saved free API response
     * @param $option_name
     * @param $before
     * @param $after
     */
    public function on_saving_options($option_name, $before, $after) {

        // get newest results if free API  settings were changed
        if ($option_name === 'google_reviews_option_name') {

            Free_API_Service::get_reviews_free_api();

            $review_json = Free_API_Service::parse_free_review_json();

            if ( is_wp_error( $review_json ) ) {

                add_settings_error(

                    'google_reviews_option_name',
                    esc_attr( 'settings_updated' ),
                    $review_json->get_error_message()

                );

            }

        }

    }

    /**
     * Parse json results of Free API and check for errors
     * @return mixed|WP_Error
     */
    public static function parse_free_review_json() {

        $raw =  get_option('gr_latest_results_free');

        if ($raw == null || $raw == '') {
            return new WP_Error(
                'REQUEST_DENIED',
                'Result was emtpy.'
            );
        }

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

        return $reviews;

    }
}
