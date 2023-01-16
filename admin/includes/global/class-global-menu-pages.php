<?php

Class Global_Menu_Pages {

    public function __construct() {
        $this->add_menu_pages();
    }

    private function add_menu_pages() {

        add_menu_page(
            __( 'Google Reviews', 'google-reviews' ), // page_title
            __( 'Google Reviews', 'google-reviews' ), // menu_title
            'manage_options', // capability
            'google-reviews', // menu_slug
            array( $this, 'google_reviews_create_admin_page' ), // function
            'dashicons-star-filled', // icon_url
            75 // position
        );

    }

    /**
     * Create admin page on backend
     */
    public function google_reviews_create_admin_page() {
        global $allowed_html;

        ?>

        <div class="wrap">
            <h2>
                <?php _e( 'Google Reviews', 'google-reviews' ); ?>
            </h2>
            <?php settings_errors(); ?>

            <form method="post" action="options.php">
                <?php
                settings_fields( 'google_reviews_option_group' );
                submit_button();
                do_settings_sections( 'google-reviews-admin' );
                ?>
            </form>
            <h2>
                <?php _e( 'Preview', 'google-reviews' ); ?>
            </h2>
            <?php echo wp_kses(do_shortcode('[google-reviews]'), $allowed_html); ?>
        </div>
    <?php }
}
