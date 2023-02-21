<?php

Class GRWP_Free_Settings {

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

        add_settings_field(
            'video_intro', // id
            '', // title
            array( $this, 'video_intro_callback' ), // callback
            'google-reviews-admin', // page
            'google_reviews_setting_section' // section
        );
    }

    /**
     * Show modal with video introduction
     */
    public function video_intro_callback() { ?>
        <div id="how_to_modal" class="modal hide">

            <!-- Modal content -->
            <div class="modal-inner">
                <div class="modal-content">
                    <span id="modal_close" class="close">&times;</span>
                    <h3><?php _e('How to use this plugin', 'google-reviews'); ?></h3>
                    <p><?php _e('Explained in less than 1 minute...', 'google-reviews'); ?></p>
                    <div class="responsive_iframe">
                        <iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/y3xwRn7Shfo" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                    </div>
                </div>
            </div>

            <div class="modal-overlay"></div>

        </div>

        <?php
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
        $video_link = get_site_url() .'/wp-admin/admin.php?page=how-to-free-version';
        $echo = '<p id="errors"></p>';
        $echo .= sprintf('<p>Search for your business in the map below and copy/paste the Place ID into the field above (<a href="%s" target="_blank">short explainer video</a>).</p>', $video_link );
        $echo .= '<br><h4>Look up your Place ID (and paste it in the field above)</h4><iframe id="mapFrame" height="200" style="height: 200px; width: 100%; max-width: 700px;display:block;" src="https://geo-devrel-javascript-samples.web.app/samples/places-placeid-finder/app/dist/" allow="fullscreen;"></iframe>';
        $echo .= sprintf( __( '<p><strong>Attention</strong>: Google\'s free version only allows for pulling 5 reviews. <br><a href="%s">Upgrade to the PRO version</a> to show ALL your reviews.</p>', 'google-reviews' ), get_site_url().'/wp-admin/admin.php?page=google-reviews-pricing');
        echo wp_kses($echo, $allowed_html);

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
