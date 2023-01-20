<?php

Class Free_API_Service {

    public function __construct() {

        // Pull reviews ajax handler
        add_action('wp_ajax_get_reviews_free_api', [$this, 'get_reviews_free_api']);
        add_action('wp_ajax_nopriv_get_reviews_free_api', [$this, 'get_reviews_free_api']);

    }

    /**
     * Get reviews from Free API
     * @return void
     */
    public static function get_reviews_free_api() {

        // ChIJI5n1ruzXmUcRw9tApHpHqmo

        $place_id = isset( $_GET['place_id'] ) ? sanitize_text_field($_GET['place_id']) : '';
        $language = isset( $_GET['language'] ) ? sanitize_text_field($_GET['language']) : 'en';

        $url = 'https://api.reviewsembedder.com/free-api.php?gmb='.$place_id.'&language='.$language;

        $result = wp_remote_get($url);

        $get_results = json_decode( wp_remote_retrieve_body( $result ) );

        if ( isset( $get_results->error_message ) ) {

            wp_send_json_error( array(
                'html' => $get_results->error_message
            ) );

            die();

        }

        else if ( isset( $get_results->result ) ) {

            update_option('gr_latest_results_free', json_encode($get_results->result));

            wp_send_json_success( array(
                'html' => $get_results->result
            ) );

            die();

        }

        die();

    }

    /**
     * Parse json results of Free API and check for errors
     * @return mixed|WP_Error
     */
    public static function parse_free_review_json() {

        $raw =  get_option('gr_latest_results_free');
        $reviewArr = json_decode($raw, true);

        return $reviewArr['reviews'];

    }

}
