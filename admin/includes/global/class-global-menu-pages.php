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

        $default_tab = null;
        $tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;

        ?>

        <div class="wrap">
            <h2>
                <?php _e( 'Google Reviews', 'google-reviews' ); ?>
            </h2>

            <?php
                settings_errors();
            ?>

            <form method="post" action="options.php">
                <nav class="nav-tab-wrapper">
                    <a href="?page=google-reviews" class="nav-tab <?php if($tab===null):?>nav-tab-active<?php endif; ?>">Connect Google</a>
                    <a href="?page=google-reviews&tab=display_settings" class="nav-tab <?php if($tab==='display_settings'):?>nav-tab-active<?php endif; ?>">Display Settings</a>
                </nav>

                <div class="tab-content">
                    <?php
                    settings_fields( 'google_reviews_option_group' );
                    do_settings_sections( 'google-reviews-admin' );
                    ?>
                </div>
                <?php

                submit_button();

                ?>
            </form>

            <h2>
                <?php _e( 'Preview', 'google-reviews' ); ?>
            </h2>
            <?php echo wp_kses(do_shortcode('[google-reviews]'), $allowed_html); ?>
        </div>
    <?php }
}
