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
                <nav class="nav-tab-wrapper menu">
                    <a href="#connect_settings"
                       class="nav-tab">
                        <?php _e('Connect Google', 'google-reviews'); ?>
                    </a>
                    <a href="#display_settings"
                       class="nav-tab">
                        <?php _e('Display Settings', 'google-reviews'); ?>
                    </a>
                    <a href="#embedding_instructions"
                       class="nav-tab">
                        <?php _e('Embedding Instructions', 'google-reviews'); ?>
                    </a>
                    <?php if ( ! grwp_fs()->is__premium_only() ) : ?>
                    <a href="admin.php?page=google-reviews-pricing"
                       class="nav-tab upgrade">
                        <?php _e('Upgrade to', 'google-reviews'); ?> <span><?php _e('PRO', 'google-reviews'); ?></span>
                    </a>
                    <?php endif; ?>
                </nav>

                <div class="tab-content">
                    <?php
                    settings_fields( 'google_reviews_option_group' );
                    do_settings_sections( 'google-reviews-admin' );
                    submit_button();
                    ?>
                </div>
            </form>

            <div class="preview_section">
                <h2>
                    <?php _e( 'Preview', 'google-reviews' ); ?>
                </h2>
                <?php echo wp_kses(do_shortcode('[google-reviews]'), $allowed_html); ?>
            </div>
        </div>
    <?php }
}
