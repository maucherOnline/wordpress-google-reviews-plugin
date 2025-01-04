<?php

Class GRWP_Global_Menu_Pages {

    protected $google_reviews_options;

    public function __construct() {
        $this->add_menu_pages();
    }

    private function add_menu_pages() {

        add_menu_page(
            __( 'Google Reviews', 'embedder-for-google-reviews' ), // page_title
            __( 'Google Reviews', 'embedder-for-google-reviews' ), // menu_title
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
                <?php _e( 'Google Reviews', 'embedder-for-google-reviews' ); ?>
            </h2>

            <?php
                settings_errors();
            ?>

            <form method="post" action="options.php">
                <nav class="nav-tab-wrapper menu">
                    <a href="#connect_settings"
                       class="nav-tab">
                        <?php _e('Connect Google', 'embedder-for-google-reviews'); ?>
                    </a>
                    <a href="#display_settings"
                       class="nav-tab">
                        <?php _e('Display Settings', 'embedder-for-google-reviews'); ?>
                    </a>
                    <!--
                    <a href="#embedding_instructions"
                       class="nav-tab">
                        <?php //_e('Embedding Instructions', 'embedder-for-google-reviews'); ?>
                    </a>-->
                    <?php if ( ! grwp_fs()->is__premium_only() ) : ?>
                    <a href="https://reviewsembedder.com/?utm_source=wp_backend&utm_medium=upgrade_tab&utm_campaign=upgrade"
                       class="nav-tab upgrade"
                       target="_blank">
                        <?php _e('Upgrade to', 'embedder-for-google-reviews'); ?> <span><?php _e('PRO', 'embedder-for-google-reviews'); ?></span>
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

            <?php
            $settings = $this->google_reviews_options = get_option( 'google_reviews_option_name' );
            $widget_type = $settings['style_2']; ?>
            <h2>
                <?php _e( 'Preview', 'embedder-for-google-reviews' ); ?>
            </h2>
            <?php
            $docs = 'https://reviewsembedder.com/docs/how-to-overwrite-styles/?utm_source=wp_backend&utm_medium=preview&utm_campaign=docs';

            if ($widget_type === 'Slider' || $widget_type === 'Grid') : ?>

            <?php
                ob_start();
                for ($x = 1; $x <= 8 ; $x++) :

                ?>

                <div class="preview_section">

                    <?php
                    if ($widget_type === 'Slider') { ?>
                        <label>
                            <?php echo sprintf(
                                    __('Use this shortcode to display the widget (<a href="%s" target="_blank">Documentation</a>).', 'embedder-for-google-reviews'),
                                    $docs
                            ); ?>
                            <input type="text" disabled value="[google-reviews type='slider' place_info='true' style='<?php echo $x; ?>']">
                        </label>
                    <?php
                        echo wp_kses( do_shortcode( '[google-reviews type="slider" place_info="true" style="' . $x . '"]' ), $allowed_html );
                    } else { ?>
                        <label>
                            <?php echo sprintf(
	                            __('Use this shortcode to display the widget (<a href="%s" target="_blank">Documentation</a>).', 'embedder-for-google-reviews'),
	                            $docs
                            ); ?>
                            <input type="text" disabled value="[google-reviews type='grid' max_reviews='10' place_info='true' style='<?php echo $x; ?>']">
                        </label>
                        <?php
                        echo wp_kses( do_shortcode( '[google-reviews type="grid" max_reviews="10" place_info="true" style="' . $x . '"]' ), $allowed_html );
                    }
                    ?>
                </div>

            <?php
                endfor;

                else : ?>

                <div class="preview_section">
                    <label>
		                <?php echo sprintf(
			                __('Use this shortcode to display the widget (<a href="%s" target="_blank">Documentation</a>).', 'embedder-for-google-reviews'),
			                $docs
		                ); ?>
                        <input type="text" disabled value="[google-reviews type='badge']">
                    </label>
                    <?php
                    echo wp_kses( do_shortcode( '[google-reviews type="badge"]' ), $allowed_html );
                    ?>
                </div>

                <?php
            endif;
            echo ob_get_clean();
            ?>

        </div>
    <?php }
}
