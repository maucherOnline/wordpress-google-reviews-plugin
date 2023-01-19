<?php

Class Global_WP_Cron {

    public function __construct() {

        add_action ( 'get_google_reviews', [$this, 'get_reviews'] );

    }

    public static function get_reviews() {

        if ( grwp_fs()->is__premium_only() ) {
            Pro_API_Service::get_reviews_pro_api();
        } else {
            Free_API_Service::get_reviews_free_api();
        }

    }

}
