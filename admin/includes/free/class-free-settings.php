<?php

Class Free_Settings {

    private $google_reviews_options;

    public function __construct() {
        $this->google_reviews_options = get_option( 'google_reviews_option_name' );
        $this->add_settings();
    }

    private function add_settings() {
        add_settings_field(
            'gmb_id_1', // id
            __( 'Place ID', 'google-reviews' ), // title
            array( $this, 'gmb_id_1_callback' ), // callback
            'google-reviews-admin', // page
            'google_reviews_setting_section' // section
        );
    }

    /**
     * Echo place ID field
     */
    public function gmb_id_1_callback() {
        global $allowed_html; ?>

        <div class="serp-container">
            <div class="serp-search">

        <?php
        printf(
            '<input class="regular-text free" 
                            type="text" 
                            name="google_reviews_option_name[gmb_id_1]" 
                            id="gmb_id_1" 
                            value="%s"
                            placeholder="%s">',
            isset( $this->google_reviews_options['gmb_id_1'] ) ? esc_attr( $this->google_reviews_options['gmb_id_1']) : '',
            __( 'Paste the Place ID of your business here.', 'google-reviews' )
        ); ?>

                <a class="button pull-reviews free"><?php _e('Pull reviews', 'google-reviews');?></a>
            </div>
        </div>


        <?php
        $echo = '<p id="errors"></p>';
        $echo .= '<p>Search for your business and copy/paste the Place ID into the field above.</p>';
        $echo .= '<br><br><iframe height="200" style="height: 200px; width: 100%; max-width: 700px;display:block;" src="https://geo-devrel-javascript-samples.web.app/samples/places-placeid-finder/app/dist/" allow="fullscreen; "></iframe>';
        echo wp_kses($echo, $allowed_html);

        //printf(__('<br><br><a href="%s" target="_blank">Head over to Google</a> and search for your business. Then copy the Place ID and paste it in the field above.', 'google-reviews'), 'https://developers.google.com/maps/documentation/places/web-service/place-id#find-id');
        printf( __( '<p><strong>Attention</strong>: Google\'s free version only allows for pulling 5 reviews. <br>If you want to circumvent this, <a href="%s">upgrade to the PRO version</a> and pull ALL reviews.</p>', 'google-reviews' ), get_site_url().'/wp-admin/admin.php?page=google-reviews-pricing');

    }

    /**
     * Deprecated
     * Echo API key field
     */
    public function api_key_0_callback() {
        printf(
            '<input class="regular-text" type="text" name="google_reviews_option_name[api_key_0]" id="api_key_0" value="%s">',
            isset( $this->google_reviews_options['api_key_0'] ) ? esc_attr( $this->google_reviews_options['api_key_0']) : ''
        );
        printf( __( '<div><p>Head over to <a href="%s" target="_blank">Google Developer Console</a> and create an API key. See short <a href="%s" target="_self">explainer video here.</a></p></div>', 'google-reviews' ), 'https://console.cloud.google.com/apis/dashboard', 'https://www.youtube.com/watch?v=feM25lZkLkA' );
    }

}
