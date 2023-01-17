<?php

add_action( 'rest_api_init', 'add_reviews_rest_endpoint');

function add_reviews_rest_endpoint() {
    register_rest_route( 'google-reviews/v1', 'pull-reviews/', array(
        'methods' => 'GET',
        'callback' => 'reviews_rest_endpoint_callback',
    ) );

}

function reviews_rest_endpoint_callback ($request) {

    $answer = GRWP_Google_Reviews::get_reviews();

    $response = new WP_REST_Response($answer);
    $response->set_status(200);
    return $response;
}

