<?php

Class Pro_API_Service {

    public function __construct() {

        // Business search ajax handler
        add_action('wp_ajax_handle_serp_business_search', [$this, 'handle_serp_business_search']);
        add_action('wp_ajax_nopriv_handle_serp_business_search', [$this, 'handle_serp_business_search']);

        // Pull reviews ajax handler
        add_action('wp_ajax_handle_get_reviews_pro_api', [$this, 'get_reviews_pro_api']);
        add_action('wp_ajax_nopriv_handle_get_reviews_pro_api', [$this, 'get_reviews_pro_api']);

    }


    /**
     * Get reviews from Pro API
     * @return WP_REST_Response
     */
    public static function get_reviews_pro_api() {

        $google_reviews_options = get_option( 'google_reviews_option_name' );

        $data_id          = $google_reviews_options['serp_data_id'];
        $reviews_language = $google_reviews_options['reviews_language_3'];

        if ( empty( $data_id ) ) {
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

        update_option( 'gr_latest_results', [
            $data_id => json_encode( $get_reviews->results )
        ]);

        $response = new WP_REST_Response();
        $response->set_status(200);

        return $response;

    }

    /**
     * Handle Google business search
     * @return void
     */
    public static function handle_serp_business_search() {
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

    /**
     * Parse json results of Pro API and check for errors
     * @return mixed|WP_Error
     */
    public static function parse_pro_review_json() {

        $business  = get_option('google_reviews_option_name');
        $data_id = isset($business['serp_data_id']) && $business['serp_data_id'] ? $business['serp_data_id'] : null;

        $raw = get_option('gr_latest_results');

        if ( isset($raw[$data_id]) && $raw[$data_id] ) {
            $reviewArr = json_decode($raw[$data_id], true);
            $reviews   = $reviewArr;
        } else {
            $reviews = null;
        }

        return $reviews;

    }
}
