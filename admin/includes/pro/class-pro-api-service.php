<?php

Class Pro_API_Service {

    public function __construct() {

        add_action('wp_ajax_handle_serp_business_search', [$this, 'handle_serp_business_search']);
        add_action('wp_ajax_nopriv_handle_serp_business_search', [$this, 'handle_serp_business_search']);

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
