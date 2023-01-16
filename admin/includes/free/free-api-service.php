<?php

Class Free_API_Service {

    public function __construct() {
        add_action('updated_option', array($this, 'on_saving_options'), 10, 3);
    }

    /**
     * Fire API request when free api options are changed to check
     * for connection issues and get latest reviews
     * @param $option_name
     * @param $before
     * @param $after
     */
    public function on_saving_options($option_name, $before, $after) {

        // get newest results if free API  settings were changed
        if ($option_name === 'google_reviews_option_name') {

            GRWP_Google_Reviews::get_reviews_free_api();

            $review_json = GRWP_Google_Reviews::parse_review_json();

            if ( is_wp_error( $review_json ) ) {

                add_settings_error(

                    'google_reviews_option_name',
                    esc_attr( 'settings_updated' ),
                    $review_json->get_error_message()

                );

            }

        }

    }
}
