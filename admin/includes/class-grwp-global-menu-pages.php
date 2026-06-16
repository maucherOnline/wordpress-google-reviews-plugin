<?php

Class GRWP_Global_Menu_Pages {

    protected $google_reviews_options;

    public function __construct() {
        $this->add_menu_pages();
    }

    private function add_menu_pages() {

        add_menu_page(
                /* translators: page title */
            __( 'Google Reviews', 'embedder-for-google-reviews' ),
            /* translators: menu title */
            __( 'Google Reviews', 'embedder-for-google-reviews' ),
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

        $settings             = $this->google_reviews_options = get_option( 'google_reviews_option_name' );
        $widget_type          = isset( $settings['style_2'] ) ? $settings['style_2'] : 'Slider';
        $is_connected         = ! empty( $settings['serp_business_name'] ) || ! empty( $settings['gmb_id_1'] );
        $reviews_loaded       = GRWP_Pro_API_Service::parse_pro_review_json() !== null;
        $is_premium           = grwp_fs()->is__premium_only();
        $docs                 = 'https://reviewsembedder.com/docs/how-to-overwrite-styles/?utm_source=wp_backend&utm_medium=preview&utm_campaign=docs';
        $upgrade_url          = 'https://reviewsembedder.com/?utm_source=wp_backend&utm_medium=upgrade_tab&utm_campaign=upgrade';
        ?>

        <!-- WP/Freemius braucht .wrap als Ziel fuer admin_notices und JS-Notices -->
        <div class="wrap grwp-outer-wrap">
            <h2 style="display:none;"></h2><!-- WP-Notices-Anker -->

        <div id="grwp-notices">
            <?php settings_errors(); ?>
        </div>

        <div id="grwp-dashboard">

            <!-- ── Header ── -->
            <div id="grwp-header">
                <h1>
                    <?php esc_html_e( 'Google Reviews', 'embedder-for-google-reviews' ); ?>
                    <?php if ( $is_premium ) : ?>
                        <span style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;border-radius:4px;font-size:.7rem;font-weight:700;padding:2px 8px;">PRO</span>
                    <?php endif; ?>
                </h1>
                <div class="grwp-header-right">
                    <?php if ( $is_connected && $reviews_loaded ) : ?>
                        <span class="grwp-status-badge">
                            <?php esc_html_e( 'Connected & reviews loaded', 'embedder-for-google-reviews' ); ?>
                        </span>
                    <?php elseif ( $is_connected ) : ?>
                        <span class="grwp-status-badge disconnected">
                            ⚡ <?php esc_html_e( 'Business set – pull reviews first', 'embedder-for-google-reviews' ); ?>
                        </span>
                    <?php else : ?>
                        <span class="grwp-status-badge disconnected">

                        </span>
                    <?php endif; ?>
                    <?php if ( ! $is_premium ) : ?>
                        <a href="<?php echo esc_url( $upgrade_url ); ?>" target="_blank" class="grwp-upgrade-btn">
                            🗲 <?php esc_html_e( ' Upgrade to PRO', 'embedder-for-google-reviews' ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ── Main settings card ── -->
            <form method="post" action="options.php">
                <?php
                // Alle Felder schreiben in google_reviews_option_name –
                // daher genuegt ein einziger settings_fields()-Aufruf
                settings_fields( 'google_reviews_option_group' );
                ?>
                <div id="grwp-main-card">

                    <!-- Tabs -->
                    <div id="grwp-tabs" role="tablist">
                        <button type="button" class="grwp-tab-btn active" data-tab="connect" role="tab">
                            <span class="dashicons dashicons-admin-site-alt3"></span>
                            <?php esc_html_e( 'Connect Google', 'embedder-for-google-reviews' ); ?>
                        </button>
                        <button type="button" class="grwp-tab-btn" data-tab="display" role="tab">
                            <span class="dashicons dashicons-admin-appearance"></span>
                            <?php esc_html_e( 'Display Settings', 'embedder-for-google-reviews' ); ?>
                        </button>
                        <button type="button" class="grwp-tab-btn" data-tab="slider" role="tab">
                            <span class="dashicons dashicons-slides"></span>
                            <?php esc_html_e( 'Slider Settings', 'embedder-for-google-reviews' ); ?>
                        </button>
                    </div>

                    <!-- Panel: Connect Google -->
                    <div id="grwp-panel-connect" class="grwp-tab-panel active" role="tabpanel">
                        <table class="form-table grwp-form-table" role="presentation"><tbody>
                            <?php do_settings_fields( 'google-reviews-admin', 'google_reviews_setting_section' ); ?>
                        </tbody></table>
                    </div>

                    <!-- Panel: Display Settings -->
                    <div id="grwp-panel-display" class="grwp-tab-panel" role="tabpanel">
                        <table class="form-table grwp-form-table" role="presentation"><tbody>
                            <?php do_settings_fields( 'google-reviews-admin', 'google_reviews_style_layout_setting_section' ); ?>
                        </tbody></table>
                    </div>

                    <!-- Panel: Slider Settings -->
                    <div id="grwp-panel-slider" class="grwp-tab-panel" role="tabpanel">
                        <table class="form-table grwp-form-table" role="presentation"><tbody>
                            <?php do_settings_fields( 'google-reviews-admin', 'google_reviews_slider_setting_section' ); ?>
                        </tbody></table>
                    </div>

                    <!-- Save row -->
                    <div id="grwp-save-row">
                        <?php submit_button( null, 'primary', 'submit', false ); ?>
                        <span style="color:#64748b;font-size:.82rem;">
                            <?php esc_html_e( 'Changes apply to all widgets on your site.', 'embedder-for-google-reviews' ); ?>
                        </span>
                    </div>

                </div><!-- /#grwp-main-card -->
            </form>

            <!-- ── Preview (collapsible) ── -->
            <div id="grwp-preview-toggle">
                <button type="button" id="grwp-preview-toggle-btn">
                    <span class="dashicons dashicons-visibility"></span>
                    <?php esc_html_e( 'Widget preview', 'embedder-for-google-reviews' ); ?>
                    <span id="grwp-preview-arrow" style="margin-left:auto;">▲</span>
                </button>
                <div id="grwp-preview-body">
                    <?php
                    ob_start();

                    if ( $widget_type === 'Slider' || $widget_type === 'Grid' ) :

                        for ( $x = 1; $x <= 8; $x++ ) : ?>

                            <div class="preview_section">
                                <?php if ( $widget_type === 'Slider' ) : ?>
                                    <div class="preview_section-header">
                                        <label>
                                            <?php printf(
                                                /* translators: %1$s style number, %2$s docs link */
                                                esc_html__( 'Style #%1$s – shortcode (%2$s)', 'embedder-for-google-reviews' ),
                                                esc_html( $x ),
                                                '<a href="' . esc_url( $docs ) . '" target="_blank">' . esc_html__( 'Docs', 'embedder-for-google-reviews' ) . '</a>'
                                            ); ?>
                                        </label>
                                    </div>
                                    <div class="preview_section-shortcode">
                                        <input type="text" readonly value="[google-reviews type='slider' place_info='true' style='<?php echo esc_attr( $x ); ?>']">
                                        <button class="grwp-copy-btn" type="button" data-clipboard="[google-reviews type='slider' place_info='true' style='<?php echo esc_attr( $x ); ?>']">
                                            📋 <span class="copy-btn"><?php esc_html_e( 'Copy', 'embedder-for-google-reviews' ); ?></span>
                                        </button>
                                    </div>
                                    <?php echo wp_kses( do_shortcode( '[google-reviews type="slider" place_info="true" style="' . esc_attr( $x ) . '"]' ), $allowed_html ); ?>
                                <?php else : ?>
                                    <div class="preview_section-header">
                                        <label>
                                            <?php printf(
                                                /* translators: %1$s style number, %2$s docs link */
                                                esc_html__( 'Style #%1$s – shortcode (%2$s)', 'embedder-for-google-reviews' ),
                                                esc_html( $x ),
                                                '<a href="' . esc_url( $docs ) . '" target="_blank">' . esc_html__( 'Docs', 'embedder-for-google-reviews' ) . '</a>'
                                            ); ?>
                                        </label>
                                    </div>
                                    <div class="preview_section-shortcode">
                                        <input type="text" readonly value="[google-reviews type='grid' max_reviews='10' place_info='true' style='<?php echo esc_attr( $x ); ?>']">
                                        <button class="grwp-copy-btn" type="button" data-clipboard="[google-reviews type='grid' max_reviews='10' place_info='true' style='<?php echo esc_attr( $x ); ?>']">
                                            📋 <span class="copy-btn"><?php esc_html_e( 'Copy', 'embedder-for-google-reviews' ); ?></span>
                                        </button>
                                    </div>
                                    <?php echo wp_kses( do_shortcode( '[google-reviews type="grid" max_reviews="10" place_info="true" style="' . esc_attr( $x ) . '"]' ), $allowed_html ); ?>
                                <?php endif; ?>
                            </div>

                        <?php endfor;

                    else :

                        if ( grwp_fs()->can_use_premium_code() ) : ?>
                            <div class="preview_section">
                                <label><?php esc_html_e( 'Floating Badge shortcode:', 'embedder-for-google-reviews' ); ?></label>
                                <input type="text" readonly value="[google-reviews type='badge']">
                                <button class="grwp-copy-btn" type="button" data-clipboard="[google-reviews type='badge']">
                                    📋 <span class="copy-btn"><?php esc_html_e( 'Copy', 'embedder-for-google-reviews' ); ?></span>
                                </button>
                                <?php echo wp_kses( do_shortcode( '[google-reviews type="badge"]' ), $allowed_html ); ?>
                            </div>
                        <?php else : ?>
                            <div class="preview_section">
                                <p>
                                    <?php printf(
                                        /* translators: %s upgrade link */
                                        esc_html__( 'Floating Badge preview requires PRO. %s', 'embedder-for-google-reviews' ),
                                        '<a href="' . esc_url( $upgrade_url ) . '" target="_blank">' . esc_html__( 'Upgrade now', 'embedder-for-google-reviews' ) . '</a>'
                                    ); ?>
                                </p>
                            </div>
                        <?php endif;

                    endif;

                    echo wp_kses( ob_get_clean(), $allowed_html );
                    ?>
                </div><!-- /#grwp-preview-body -->
            </div><!-- /#grwp-preview-toggle -->

        </div><!-- /#grwp-dashboard -->
        </div><!-- /.grwp-outer-wrap -->

        <script>
        (function($) {
            // JS ist in src/js/admin/google-reviews-admin.js und wird per Build kompiliert
        }(jQuery));
        </script>

    <?php }
}
