<?php

class GRWP_WP_Cron {

    protected static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function plugin_setup() {
        add_action( 'get_google_reviews', array($this, 'wp_cron_reviews' ) );
    }

    public function wp_cron_reviews() {

        if ( grwp_fs()->is__premium_only() ) {

            require_once GR_BASE_PATH_ADMIN . 'includes/pro/class-pro-api-service.php';
            GRWP_Pro_API_Service::get_reviews_pro_api();

        }

    }

}
