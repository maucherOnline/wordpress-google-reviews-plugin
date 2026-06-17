<?php

Class GRWP_Global_Settings {

    private $google_reviews_options;
    private $settings_slug;

    public function __construct() {

        $this->google_reviews_options = get_option( 'google_reviews_option_name' );
        $this->settings_slug = 'google-reviews-admin';

        $this->add_api_settings();
        $this->add_display_settings();
        $this->add_slider_settings();
        $this->add_grid_settings();
        $this->add_legacy_settings();

    }

    /**
     * API settings
     */
    private function add_api_settings() {

        register_setting(
            'google_reviews_option_group', // option_group
            'google_reviews_option_name', // option_name
            array( $this, 'google_reviews_sanitize' ) // sanitize_callback
        );

        add_settings_section(
            'google_reviews_setting_section', // id
            '', // title
            array( $this, 'google_reviews_section_info' ), // callback
            $this->settings_slug // page
        );

	    if ( ! grwp_fs()->is__premium_only() ) {
            add_settings_field(
                'show_upgrade_message', // id
                '', // title
                array( $this, 'show_upgrade_message_callback' ), // callback
                $this->settings_slug, // page
                'google_reviews_setting_section' // section
            );
        }


        add_settings_field(
            'show_verified', // id
            /* translators: Show dummy content */
            __( 'Show \'verified\' badge', 'embedder-for-google-reviews' ),
            array( $this, 'show_verified_callback' ), // callback
            $this->settings_slug, // page
            'google_reviews_setting_section' // section
        );

        add_settings_field(
            'reviews_language_3', // id
            /* translators: Reviews language */
            __( 'Reviews language', 'embedder-for-google-reviews' ),
            array( $this, 'reviews_language_3_callback' ), // callback
            $this->settings_slug, // page
            'google_reviews_setting_section' // section
        );

        add_settings_field(
            'style_2_in_connect',
            __( 'Layout type', 'embedder-for-google-reviews' ),
            array( $this, 'style_2_callback' ),
            $this->settings_slug,
            'google_reviews_legacy_setting_section'
        );

    }

    public function show_upgrade_message_callback() {
        $upgrade_url = 'https://reviewsembedder.com/?utm_source=wp_backend&utm_medium=upgrade_tab&utm_campaign=upgrade_banner';
        ?>
        <div id="grwp-upgrade-banner" style="background:#fffbeb;border:1px solid #fde68a;border-left:4px solid #f59e0b;border-radius:6px;padding:12px 16px;display:flex;align-items:flex-start;gap:12px;margin:8px 0 4px;">
            <span style="font-size:1.1rem;line-height:1.5;">⚡</span>
            <div style="flex:1;font-size:.85rem;color:#78350f;line-height:1.6;">
                <?php
                printf(
                    wp_kses(
                    /* translators: %s is replaced with "Attention" in bold. */
                        sprintf( __('%s: the free version only allows for pulling 20 reviews.', 'embedder-for-google-reviews'),
                            '<strong>' . __('Attention', 'embedder-for-google-reviews') . '</strong>'
                        ),
                        array('strong' => array()) // Allowed HTML tags
                    )
                );
                ?>
                <a href="<?php echo esc_url( $upgrade_url ); ?>" target="_blank" style="color:#92400e;font-weight:600;">
                    <?php esc_html_e( 'Upgrade to PRO ', 'embedder-for-google-reviews' ); ?>
                </a>
                <?php esc_html_e( '– to display ALL reviews and hide bad ones.', 'embedder-for-google-reviews' ); ?>
            </div>
            <button type="button" id="grwp-upgrade-banner-close" style="background:none;border:none;cursor:pointer;color:#92400e;font-size:1rem;line-height:1;padding:2px 0 0;flex-shrink:0;" aria-label="<?php esc_attr_e( 'Close', 'embedder-for-google-reviews' ); ?>">✕</button>
        </div>
        <script>
        (function($){
            if ( localStorage.getItem('grwp_hide_upgrade_banner') ) {
                //$('#grwp-upgrade-banner').hide();
            }
            $('#grwp-upgrade-banner-close').on('click', function(){
                $('#grwp-upgrade-banner').slideUp(200);
                //localStorage.setItem('grwp_hide_upgrade_banner', '1');
            });
        }(jQuery));
        </script>

        <?php
    }

    /**
     * Display settings
     */
    private function add_display_settings() {

        // settings for styles and layout
        register_setting(
            'google_reviews_style_group', // option_group
            'google_reviews_style', // option_name
            array( $this, 'google_reviews_sanitize' ) // sanitize_callback
        );

        // add style and layout settings section
        add_settings_section(
            'google_reviews_style_layout_setting_section', // id
            '', // title
            array( $this, 'google_reviews_display_section_info' ), // callback
            $this->settings_slug // page
        );



        add_settings_field(
            'filter_below_5_stars', // id
            /* translators: Minimum rating (stars) */
            __('Minimum rating (stars)', 'embedder-for-google-reviews'),
            array($this, 'filter_below_5_stars_callback'), // callback
            $this->settings_slug, // page
            'google_reviews_style_layout_setting_section' // section
        );

        add_settings_field(
            'exclude_reviews_without_text', // id
            /* translators: Exclude reviews without text */
            __('Exclude reviews without text', 'embedder-for-google-reviews'),
            array($this, 'exclude_reviews_without_text_callback'), // callback
            $this->settings_slug, // page
            'google_reviews_style_layout_setting_section' // section
        );

	    add_settings_field(
		    'hide_date_string', // id
            /* translators: Hide review date */
		    __('Hide review date', 'embedder-for-google-reviews'),
		    array($this, 'hide_date_string_callback'), // callback
		    $this->settings_slug, // page
		    'google_reviews_style_layout_setting_section' // section
	    );
        
        add_settings_field(
            'link_users_profiles', // id
            /* translators: Link to users Google prilfe */
            __('Link to users Profile <br> (uncheck for better SEO)', 'embedder-for-google-reviews'),
            array($this, 'link_users_profiles_callback'), // callback
            $this->settings_slug, // page
            'google_reviews_style_layout_setting_section' // section
        );

        add_settings_field(
            'filter_words', // id
            /* translators: Filter by words (comma separated) */
            __('Filter by words (comma separated)', 'embedder-for-google-reviews'),
            array($this, 'filter_words_callback'), // callback
            $this->settings_slug, // page
            'google_reviews_style_layout_setting_section' // section
        );

        add_settings_field(
            'hide_company_header', // id
            /* translators: Hide company header section */
            __('Hide company header section', 'embedder-for-google-reviews'),
            array($this, 'hide_company_header_callback'), // callback
            $this->settings_slug, // page
            'google_reviews_style_layout_setting_section' // section
        );

        add_settings_field(
            'show_dummy_content', // id
            /* translators: Show dummy content */
            __( 'Show dummy content', 'embedder-for-google-reviews' ),
            array( $this, 'show_dummy_content_callback' ), // callback
            $this->settings_slug, // page
            'google_reviews_style_layout_setting_section' // section
        );

        add_settings_field(
            'hide_profile_picture', // id
            /* translators: Hide reviewer profile picture */
            __( 'Hide reviewer profile picture', 'embedder-for-google-reviews' ),
            array( $this, 'hide_profile_picture_callback' ), // callback
            $this->settings_slug, // page
            'google_reviews_style_layout_setting_section' // section
        );

        add_settings_field(
            'disable_box_shadow', // id
            /* translators: Remove card box shadows */
            __( 'Remove card box shadows', 'embedder-for-google-reviews' ),
            array( $this, 'disable_box_shadow_callback' ), // callback
            $this->settings_slug, // page
            'google_reviews_style_layout_setting_section' // section
        );
    }

    /**
     * Grid settings
     * @return void
     */
    private function add_grid_settings() {

        add_settings_section(
            'google_reviews_grid_setting_section', // id
            '', // title
            array( $this, 'google_reviews_display_grid_info' ), // callback
            $this->settings_slug // page
        );

        add_settings_field(
            'show_more_grid', // id
            /* translators: Show "Load more" button on grid */
            __('Show "Load more" button', 'embedder-for-google-reviews'),
            array($this, 'show_more_grid_callback'), // callback
            $this->settings_slug, // page
            'google_reviews_grid_setting_section' // section
        );

        add_settings_field(
            'show_more_grid_initial', // id
            /* translators: Initially visible cards (grid load-more) */
            __('Initially visible cards', 'embedder-for-google-reviews'),
            array($this, 'show_more_grid_initial_callback'), // callback
            $this->settings_slug, // page
            'google_reviews_grid_setting_section' // section
        );
    }

    public function google_reviews_display_grid_info() { ?>
        <h2 id="grid_settings"><?php esc_html_e( 'Grid settings', 'embedder-for-google-reviews' ); ?></h2>
        <?php
    }

    /**
     * Slider settings
     * @return void
     */
    private function add_slider_settings() {
        // settings for styles and layout
        register_setting(
            'google_reviews_slider_settings_group', // option_group
            'google_reviews_slider_settings', // option_name
            array( $this, 'google_reviews_sanitize' ) // sanitize_callback
        );

        add_settings_section(
            'google_reviews_slider_setting_section', // id
            '', // title
            array( $this, 'google_reviews_display_slider_info' ), // callback
            $this->settings_slug // page
        );

        add_settings_field(
            'slide_duration', // id
            /* translators: Layout type */
            __( 'Slide Duration (seconds). <br> Use \'0\' to disable autoplay', 'embedder-for-google-reviews' ),
            array( $this, 'slide_duration_callback' ), // callback
            $this->settings_slug, // page
            'google_reviews_slider_setting_section' // section
        );

        add_settings_field(
            'hide_slider_arrows', // id
            /* translators: Layout type */
            __( 'Hide slider arrows', 'embedder-for-google-reviews' ),
            array( $this, 'hide_slider_arrows_callback' ), // callback
            $this->settings_slug, // page
            'google_reviews_slider_setting_section' // section
        );

        add_settings_field(
            'disable_loop_slider', // id
            /* translators: Layout type */
            __( 'Disable slider endless loop', 'embedder-for-google-reviews' ),
            array( $this, 'disable_loop_slider_callback' ), // callback
            $this->settings_slug, // page
            'google_reviews_slider_setting_section' // section
        );
    }

    /**
     * Slide duration
     * @return void
     */
    public function slide_duration_callback() {
        global $allowed_html;

        ob_start();
        ?>

        <?php if ( grwp_fs()->is__premium_only() ) : ?>
            <input type="number"
                   name="google_reviews_option_name[slide_duration]"
                   id="slide_duration"
                   min="0"
                   step="1"
                   value="<?php echo esc_attr( ! empty( $this->google_reviews_options['slide_duration'] ) ? $this->google_reviews_options['slide_duration'] : '0' ); ?>"
                    <?php echo ! grwp_fs()->is__premium_only() ? 'readonly ' : ''; ?>
            />
        <?php else : ?>
            <div class="tooltip">
                <input type="hidden"
                       name="google_reviews_option_name[slide_duration]"
                       value="5"
                />
                <input type="number"
                       name="google_reviews_option_name[slide_duration]"
                       id="slide_duration"
                       value="5"
                       disabled
                />
                <span class="tooltiptext">PRO Feature <br> <a href="https://reviewsembedder.com/?utm_source=wp_backend&utm_medium=slide_duration&utm_campaign=upgrade" target="_blank">⚡ Upgrade now</a></span>
            </div>
        <?php endif; ?>

        <?php
        $html = ob_get_clean();

        echo wp_kses($html, $allowed_html);

    }

    public function hide_slider_arrows_callback() {
        global $allowed_html;
        ob_start();
        ?>

        <?php if ( grwp_fs()->is__premium_only() ) : ?>
            <input type="checkbox"
                   name="google_reviews_option_name[hide_slider_arrows]"
                   id="hide_slider_arrows"
                   value="1"
                    <?php echo esc_attr( ! empty( $this->google_reviews_options['hide_slider_arrows'] ) ? 'checked' : '' ); ?>
            />

        <?php else : ?>
            <div class="tooltip">
                <input type="hidden"
                       name="google_reviews_option_name[hide_slider_arrows]"
                       id="hide_slider_arrows"
                       value="0"
                />

                <input type="checkbox"
                       name="google_reviews_option_name[hide_slider_arrows]"
                       disabled
                />
                <span class="tooltiptext">PRO Feature <br> <a href="https://reviewsembedder.com/?utm_source=wp_backend&utm_medium=slide_duration&utm_campaign=upgrade" target="_blank">⚡ Upgrade now</a></span>
            </div>
        <?php endif; ?>

        <span>
            <?php esc_html_e( 'Yes', 'embedder-for-google-reviews' ); ?>
        </span>

        <?php
        $html = ob_get_clean();

        echo wp_kses($html, $allowed_html);
    }

    public function disable_loop_slider_callback() {
        global $allowed_html;
        ob_start();
        ?>

        <?php if ( grwp_fs()->is__premium_only() ) : ?>
            <input type="checkbox"
                   name="google_reviews_option_name[disable_loop_slider]"
                   id="disable_loop_slider"
                   value="1"
                    <?php echo esc_attr( ! empty( $this->google_reviews_options['disable_loop_slider'] ) ? 'checked' : '' ); ?>
            />

        <?php else : ?>
            <div class="tooltip">
                <input type="hidden"
                       name="google_reviews_option_name[disable_loop_slider]"
                       id="disable_loop_slider"
                       value="0"
                />

                <input type="checkbox"
                       name="google_reviews_option_name[disable_loop_slider]"
                       disabled
                />
                <span class="tooltiptext">PRO Feature <br> <a href="https://reviewsembedder.com/?utm_source=wp_backend&utm_medium=slide_duration&utm_campaign=upgrade" target="_blank">⚡ Upgrade now</a></span>
            </div>
        <?php endif; ?>

        <span>
            <?php esc_html_e( 'Yes', 'embedder-for-google-reviews' ); ?>
        </span>

        <?php
        $html = ob_get_clean();

        echo wp_kses($html, $allowed_html);
    }


    /**
     * Sanitize user input
     * @param $input
     * @return array
     */
    public function google_reviews_sanitize($input) {
        $sanitary_values = array();

        if ( isset( $input['show_dummy_content'] ) ) {
            $sanitary_values['show_dummy_content'] = sanitize_text_field( $input['show_dummy_content'] );
        }

        if ( isset( $input['show_verified'] ) ) {
            $sanitary_values['show_verified'] = sanitize_text_field( $input['show_verified'] );
        }

        if ( isset( $input['serp_business_name'] ) ) {
            $sanitary_values['serp_business_name'] = sanitize_text_field( $input['serp_business_name'] );
        }

        if ( isset( $input['serp_data_id'] ) ) {
            $sanitary_values['serp_data_id'] = sanitize_text_field( $input['serp_data_id'] );
        }

        if ( isset( $input['api_key_0'] ) ) {
            $sanitary_values['api_key_0'] = sanitize_text_field( $input['api_key_0'] );
        }

        if ( isset( $input['gmb_id_1'] ) ) {
            $sanitary_values['gmb_id_1'] = sanitize_text_field( $input['gmb_id_1'] );
        }

        if ( isset( $input['style_2'] ) ) {
            $sanitary_values['style_2'] = $input['style_2'];
        }

        if ( isset( $input['grid_columns'] ) ) {
            $sanitary_values['grid_columns'] = $input['grid_columns'];
        }

        if ( isset( $input['layout_style'] ) ) {
            $sanitary_values['layout_style'] = $input['layout_style'];
        }

        if ( isset( $input['show_dummy_content'] ) ) {
            $sanitary_values['show_dummy_content'] = sanitize_text_field( $input['show_dummy_content'] );
        }

        if ( isset( $input['filter_below_5_stars'] ) ) {
            $sanitary_values['filter_below_5_stars'] = sanitize_text_field($input['filter_below_5_stars']);
        }

        if ( isset( $input['slide_duration'] ) ) {
            $sanitary_values['slide_duration'] = sanitize_text_field($input['slide_duration']);
        }

        if ( isset( $input['exclude_reviews_without_text'] ) ) {
            $sanitary_values['exclude_reviews_without_text'] = $input['exclude_reviews_without_text'];
        }

	    if ( isset( $input['hide_date_string'] ) ) {
		    $sanitary_values['hide_date_string'] = $input['hide_date_string'];
	    }

        if ( isset( $input['filter_words'] ) ) {
            $sanitary_values['filter_words'] = $input['filter_words'];
        }

        if ( isset( $input['hide_company_header'] ) ) {
            $sanitary_values['hide_company_header'] = sanitize_text_field( $input['hide_company_header'] );
        }

        if ( isset( $input['hide_profile_picture'] ) ) {
            $sanitary_values['hide_profile_picture'] = sanitize_text_field( $input['hide_profile_picture'] );
        }

        if ( isset( $input['disable_box_shadow'] ) ) {
            $sanitary_values['disable_box_shadow'] = sanitize_text_field( $input['disable_box_shadow'] );
        }

        if ( isset( $input['show_more_grid'] ) ) {
            $sanitary_values['show_more_grid'] = sanitize_text_field( $input['show_more_grid'] );
        }

        if ( isset( $input['show_more_grid_initial'] ) ) {
            $sanitary_values['show_more_grid_initial'] = absint( $input['show_more_grid_initial'] );
        }

        if ( isset( $input['link_users_profiles'] ) ) {
            $sanitary_values['link_users_profiles'] = $input['link_users_profiles'];
        }

        if ( isset( $input['hide_slider_arrows'] ) ) {
            $sanitary_values['hide_slider_arrows'] = $input['hide_slider_arrows'];
        }

        if ( isset( $input['disable_loop_slider'] ) ) {
            $sanitary_values['disable_loop_slider'] = $input['disable_loop_slider'];
        }

        if ( isset( $input['reviews_language_3'] ) ) {
            $sanitary_values['reviews_language_3'] = $input['reviews_language_3'];
        }

        return $sanitary_values;
    }

    public function google_reviews_section_info() { ?>
        <h2 id="connect_settings"><?php esc_html_e( 'Global settings for showing reviews', 'embedder-for-google-reviews' ); ?></h2>

        <?php
    }

    public function google_reviews_display_section_info() { ?>
        <h2 id="display_settings"><?php esc_html_e( 'Display settings', 'embedder-for-google-reviews' );?></h2>

        <?php
    }

    public function google_reviews_display_slider_info() { ?>
        <h2 id="slider_settings"><?php esc_html_e( 'Slider settings', 'embedder-for-google-reviews' ); ?></h2>

        <?php
    }

    /**
     * Show dummy content
     * @return void
     */
    public function show_dummy_content_callback() {
        global $allowed_html;
        ob_start();
        ?>

        <input type="checkbox"
               name="google_reviews_option_name[show_dummy_content]"
               value="1"
               id="show_dummy_content"
            <?php echo esc_attr( ! empty( $this->google_reviews_options['show_dummy_content'] ) ? 'checked' : '' ); ?>
        >

        <span>
            <?php esc_html_e( 'Yes', 'embedder-for-google-reviews' ); ?>
        </span>

        <?php
        $html = ob_get_clean();

        echo wp_kses($html, $allowed_html);
    }

    /**
     * Hide reviewer profile picture
     * @return void
     */
    public function hide_profile_picture_callback() {
        global $allowed_html;
        ob_start(); ?>

        <input type="checkbox"
               name="google_reviews_option_name[hide_profile_picture]"
               value="1"
               id="hide_profile_picture"
            <?php echo esc_attr( ! empty( $this->google_reviews_options['hide_profile_picture'] ) ? 'checked' : '' ); ?>
        >

        <span>
            <?php esc_html_e( 'Yes', 'embedder-for-google-reviews' ); ?>
        </span>

        <?php
        $html = ob_get_clean();
        echo wp_kses( $html, $allowed_html );
    }

    /**
     * Remove card box shadows
     * @return void
     */
    public function disable_box_shadow_callback() {
        global $allowed_html;
        ob_start(); ?>

        <input type="checkbox"
               name="google_reviews_option_name[disable_box_shadow]"
               value="1"
               id="disable_box_shadow"
            <?php echo esc_attr( ! empty( $this->google_reviews_options['disable_box_shadow'] ) ? 'checked' : '' ); ?>
        >

        <span>
            <?php esc_html_e( 'Yes', 'embedder-for-google-reviews' ); ?>
        </span>

        <?php
        $html = ob_get_clean();
        echo wp_kses( $html, $allowed_html );
    }

    /**
     * Show dummy content
     * @return void
     */
    public function show_verified_callback() {
        global $allowed_html;
        ob_start();
        ?>

        <input type="checkbox"
               name="google_reviews_option_name[show_verified]"
               value="1"
               id="show_verified"
            <?php echo esc_attr( ! empty( $this->google_reviews_options['show_verified'] ) ? 'checked' : '' ); ?>
        >

        <span>
            <?php esc_html_e( 'Yes', 'embedder-for-google-reviews' ); ?>
        </span>

        <?php
        $html = ob_get_clean();

        echo wp_kses($html, $allowed_html);
    }

    /**
     * Filter below 5 stars
     * @return void
     */
    public function filter_below_5_stars_callback() {
        global $allowed_html;

        ob_start();
        ?>
        <?php if ( ! grwp_fs()->is__premium_only() ) : ?>
        <div class="tooltip">
        <?php endif; ?>

            <input type="number"
                   name="google_reviews_option_name[filter_below_5_stars]"
                   id="filter_below_5_stars"
                   min="1"
                   max="5"
                   step="1"
                   value="<?php echo esc_attr( ! empty( $this->google_reviews_options['filter_below_5_stars'] ) ? $this->google_reviews_options['filter_below_5_stars'] : '1' ); ?>"
                   <?php echo ! grwp_fs()->is__premium_only() ? 'disabled' : ''; ?>
            />

        <?php if ( ! grwp_fs()->is__premium_only() ) : ?>
            <span class="tooltiptext">PRO Feature <br> <a href="https://reviewsembedder.com/?utm_source=wp_backend&utm_medium=minimum_rating&utm_campaign=upgrade" target="_blank">⚡ Upgrade now</a></span>
        </div>
	    <?php endif; ?>

        <?php
        $html = ob_get_clean();

        echo wp_kses($html, $allowed_html);

    }

    /**
     * Link to User's Google Profiles
     * @return void
     */
    public function link_users_profiles_callback() {
        global $allowed_html;

        ob_start();
        ?>
        <?php if ( ! grwp_fs()->is__premium_only() ) : ?>
            <div class="tooltip">
        <?php endif; ?>

        <?php if ( grwp_fs()->is__premium_only() ) : ?>
            <input type="checkbox"
                   name="google_reviews_option_name[link_users_profiles]"
                   id="link_users_profiles"
                   value="1"
                <?php echo esc_attr( ! empty( $this->google_reviews_options['link_users_profiles'] ) ? 'checked' : '' ); ?>
            />

        <?php else : ?>
            <input type="hidden"
                   name="google_reviews_option_name[link_users_profiles]"
                   id="link_users_profiles"
                   value="1"
            />

            <input type="checkbox"
                   name="google_reviews_option_name[link_users_profiles]"
                   id="link_users_profiles"
                   checked
                   disabled
            />
        <?php endif; ?>

        <span>
            <?php esc_html_e( 'Yes', 'embedder-for-google-reviews' ); ?>
        </span>

        <?php if ( ! grwp_fs()->is__premium_only() ) : ?>
            <span class="tooltiptext">PRO Feature <br> <a href="https://reviewsembedder.com/?utm_source=wp_backend&utm_medium=link_profile&utm_campaign=upgrade" target="_blank">⚡ Upgrade now</a></span>
            </div>
        <?php endif; ?>

        <?php
        $html = ob_get_clean();

        echo wp_kses($html, $allowed_html);

    }

    /**
     * Exclude reviews without text
     * @return void
     */
    public function exclude_reviews_without_text_callback() {
        global $allowed_html;

        ob_start(); ?>

        <?php if ( ! grwp_fs()->is__premium_only() ) : ?>
        <div class="tooltip">
        <?php endif; ?>

        <input type="checkbox"
               name="google_reviews_option_name[exclude_reviews_without_text]"
               value="1"
               id="exclude_reviews_without_text"
               <?php echo esc_attr( ! empty( $this->google_reviews_options['exclude_reviews_without_text'] ) ? 'checked' : '' ); ?>
               <?php echo grwp_fs()->is__premium_only() ? '' : 'disabled'; ?>
        >

        <span>
            <?php esc_html_e( 'Yes', 'embedder-for-google-reviews' ); ?>
        </span>

	    <?php if ( ! grwp_fs()->is__premium_only() ) : ?>
            <span class="tooltiptext">PRO Feature <br> <a href="https://reviewsembedder.com/?utm_source=wp_backend&utm_medium=textless_reviews&utm_campaign=upgrade" target="_blank">⚡ Upgrade now</a></span>
            </div>
	    <?php endif; ?>

        <?php

        $html = ob_get_clean();

        echo wp_kses($html, $allowed_html);

    }

	/**
	 * Hide date string
	 * @return void
	 */
	public function hide_date_string_callback() {
		global $allowed_html;

		ob_start(); ?>

		<?php if ( ! grwp_fs()->is__premium_only() ) : ?>
            <div class="tooltip">
		<?php endif; ?>

        <input type="checkbox"
               name="google_reviews_option_name[hide_date_string]"
               value="1"
               id="hide_date_string"
			<?php echo esc_attr( ! empty( $this->google_reviews_options['hide_date_string'] ) ? 'checked' : '' ); ?>
			<?php echo grwp_fs()->is__premium_only() ? '' : 'disabled'; ?>
        >

        <span>
            <?php esc_html_e( 'Yes', 'embedder-for-google-reviews' ); ?>
        </span>

		<?php if ( ! grwp_fs()->is__premium_only() ) : ?>
            <span class="tooltiptext">PRO Feature <br> <a href="https://reviewsembedder.com/?utm_source=wp_backend&utm_medium=textless_reviews&utm_campaign=upgrade" target="_blank">⚡ Upgrade now</a></span>
            </div>
		<?php endif; ?>

		<?php

		$html = ob_get_clean();

		echo wp_kses($html, $allowed_html);

	}

    /**
     * Filter specific words
     * @return void
     */
    public function filter_words_callback() {
        global $allowed_html;

        ob_start();
        ?>

        <?php if ( ! grwp_fs()->is__premium_only() ) : ?>
        <div class="tooltip">
        <?php endif; ?>

        <textarea
           name="google_reviews_option_name[filter_words]"
           id="filter_words"
           rows="2"
           <?php echo grwp_fs()->is__premium_only() ? '' : 'disabled'; ?>
        ><?php echo esc_attr( ! empty( $this->google_reviews_options['filter_words'] ) ? $this->google_reviews_options['filter_words'] : '' ); ?></textarea>

	    <?php if ( ! grwp_fs()->is__premium_only() ) : ?>
            <span class="tooltiptext">PRO Feature <br> <a href="https://reviewsembedder.com/?utm_source=wp_backend&utm_medium=filter_words&utm_campaign=upgrade" target="_blank">⚡ Upgrade now</a></span>
            </div>
	    <?php endif; ?>

        <?php
        $html = ob_get_clean();

        echo wp_kses($html, $allowed_html);

    }

    /**
     * Hide company header section
     * @return void
     */
    public function hide_company_header_callback() {
        global $allowed_html;

        ob_start(); ?>

        <input type="checkbox"
               name="google_reviews_option_name[hide_company_header]"
               value="1"
               id="hide_company_header"
            <?php echo esc_attr( ! empty( $this->google_reviews_options['hide_company_header'] ) ? 'checked' : '' ); ?>
        >

        <span>
            <?php esc_html_e( 'Yes', 'embedder-for-google-reviews' ); ?>
        </span>

        <?php
        $html = ob_get_clean();

        echo wp_kses($html, $allowed_html);
    }

    /**
     * Show "Load more" button on grid widget
     * @return void
     */
    public function show_more_grid_callback() {
        global $allowed_html;

        ob_start(); ?>

        <input type="checkbox"
               name="google_reviews_option_name[show_more_grid]"
               value="1"
               id="show_more_grid"
            <?php echo esc_attr( ! empty( $this->google_reviews_options['show_more_grid'] ) ? 'checked' : '' ); ?>
        >

        <span>
            <?php esc_html_e( 'Yes', 'embedder-for-google-reviews' ); ?>
        </span>

        <?php
        $html = ob_get_clean();
        echo wp_kses( $html, $allowed_html );
    }

    /**
     * Number of cards visible before "Load more" is clicked
     * @return void
     */
    public function show_more_grid_initial_callback() {
        global $allowed_html;

        $value = isset( $this->google_reviews_options['show_more_grid_initial'] )
            ? intval( $this->google_reviews_options['show_more_grid_initial'] )
            : 6;
        if ( $value < 1 ) $value = 6;

        ob_start(); ?>

        <input type="number"
               name="google_reviews_option_name[show_more_grid_initial]"
               id="show_more_grid_initial"
               min="1"
               step="1"
               value="<?php echo esc_attr( $value ); ?>"
        >
        <p class="description" style="margin-top:6px;">
            <?php esc_html_e( 'Only applies when "Show \'Load more\' button" is enabled above.', 'embedder-for-google-reviews' ); ?>
        </p>

        <?php
        $html = ob_get_clean();
        echo wp_kses( $html, $allowed_html );
    }

    /**
     * Echo layout option field
     */
    public function style_2_callback() {
        ?> <select name="google_reviews_option_name[style_2]" id="style_2">
            <?php $selected = (isset( $this->google_reviews_options['style_2'] ) && $this->google_reviews_options['style_2'] === 'Slider') ? 'selected' : '' ; ?>
            <option <?php echo esc_attr($selected); ?> value="Slider">
                <?php esc_html_e( 'Slider', 'embedder-for-google-reviews' ); ?>
            </option>
            <?php $selected = (isset( $this->google_reviews_options['style_2'] ) && $this->google_reviews_options['style_2'] === 'Grid') ? 'selected' : '' ; ?>
            <option <?php echo esc_attr($selected); ?> value="Grid">
                <?php esc_html_e( 'Grid', 'embedder-for-google-reviews' ); ?>
            </option>

            <?php if ( ! grwp_fs()->is__premium_only() ) : ?>

            <option disabled value="Badge">
			    <?php esc_html_e( 'Floating Badge (PRO)', 'embedder-for-google-reviews' ); ?>
            </option>

            <?php else : ?>

            <?php $selected = (isset( $this->google_reviews_options['style_2'] ) && $this->google_reviews_options['style_2'] === 'Badge') ? 'selected' : '' ; ?>
            <option <?php echo esc_attr($selected); ?> value="Badge">
                <?php esc_html_e( 'Floating Badge', 'embedder-for-google-reviews' ); ?>
            </option>

            <?php endif; ?>

        </select> <?php
    }

    public function grid_columns_callback() {
        $columns = $this->google_reviews_options['grid_columns'] ?? '';

        if (empty($columns)){
            $columns = 3;
        }

        ?>

        <select name="google_reviews_option_name[grid_columns]" id="grid_columns">
            <option <?php selected($columns, '1'); ?> value="1"><?php esc_attr_e('1', 'embedder-for-google-reviews'); ?></option>
            <option <?php selected($columns, '2'); ?> value="2"><?php esc_attr_e('2', 'embedder-for-google-reviews'); ?></option>
            <option <?php selected($columns, '3'); ?> value="3"><?php esc_attr_e('3', 'embedder-for-google-reviews'); ?></option>
        </select>

        <?php

    }

    /**
     * Legacy settings tab (Layout type + Design type)
     * Kept for users who rely on the saved values without shortcode attributes.
     */
    private function add_legacy_settings() {

        add_settings_section(
            'google_reviews_legacy_setting_section',
            '',
            array( $this, 'google_reviews_display_legacy_info' ),
            $this->settings_slug
        );

        add_settings_field(
            'layout_style',
            __( 'Design type', 'embedder-for-google-reviews' ),
            array( $this, 'layout_style_callback' ),
            $this->settings_slug,
            'google_reviews_legacy_setting_section'
        );

    }

    public function google_reviews_display_legacy_info() { ?>
        <h2 id="legacy_settings"><?php esc_html_e( 'Legacy options', 'embedder-for-google-reviews' ); ?></h2>
        <p style="color:#64748b;font-size:.85rem;margin:0 0 8px;">
            <?php esc_html_e( 'These settings apply globally for users who embed the widget without shortcode attributes. New installations should use the shortcode attributes instead.', 'embedder-for-google-reviews' ); ?>
        </p>
        <?php
    }

    public function layout_style_callback() {
        $layout_style = isset($this->google_reviews_options['layout_style']) ? $this->google_reviews_options['layout_style'] : '';

        if ( empty( $layout_style ) ) {
            $layout_style = 'layout_style-7';
        }
        ?>
        <select name="google_reviews_option_name[layout_style]" id="layout_style">
            <?php for ( $i = 1; $i <= 10; $i++ ) : ?>
                <option
                    <?php selected( $layout_style, 'layout_style-' . $i ); ?>
                    value="<?php echo esc_attr( sprintf( 'layout_style-%s', $i ) ); ?>"
                >
                    <?php printf(
                        /* translators: Design */
                        esc_html__( 'Design #%s', 'embedder-for-google-reviews' ),
                        esc_html( $i )
                    ); ?>
                </option>
            <?php endfor; ?>
        </select>
        <?php
    }

    /**
     * Echo language field
     */
    public function reviews_language_3_callback() {
        $languages = [
            'en'            => 'English',
            'ar'            => 'Arabic',
            'bg'            => 'Bulgarian',
            'bn'            => 'Bengali',
            'ca'            => 'Catalan',
            'cs'            => 'Czech',
            'da'            => 'Danish',
            'de'            => 'German',
            'el'            => 'Greek',
            'es'            => 'Spanish',
            'es-419'        => 'Spanish (Latin America)',
            'eu'            => 'Basque',
            'fa'            => 'Farsi',
            'fi'            => 'Finnish',
            'fil'           => 'Filipino',
            'fr'            => 'French',
            'gl'            => 'Galician',
            'gu'            => 'Gujarati',
            'hi'            => 'Hindi',
            'hr'            => 'Croatian',
            'hu'            => 'Hungarian',
            'id'            => 'Indonesian',
            'it'            => 'Italian',
            'iw'            => 'Hebrew',
            'ja'            => 'Japanese',
            'kn'            => 'Kannada',
            'ko'            => 'Korean',
            'lt'            => 'Lithuanian',
            'lv'            => 'Latvian',
            'ml'            => 'Malayalam',
            'mr'            => 'Marathi',
            'nl'            => 'Dutch',
            'no'            => 'Norwegian',
            'pl'            => 'Polish',
            'pt'            => 'Portuguese',
            'pt-BR'         => 'Portuguese (Brazil)',
            'pt-PT'         => 'Portuguese (Portugal)',
            'ro'            => 'Romanian',
            'ru'            => 'Russian',
            'sk'            => 'Slovak',
            'sl'            => 'Slovenian',
            'sr'            => 'Serbian',
            'sv'            => 'Swedish',
            'ta'            => 'Tamil',
            'te'            => 'Telugu',
            'th'            => 'Thai',
            'tl'            => 'Tagalog',
            'tr'            => 'Turkish',
            'uk'            => 'Ukrainian',
            'vi'            => 'Vietnamese',
            'zh'            => 'Chinese (Simplified)',
            'zh-HK'         => 'Chinese (Hongkong)',
            'zh-Hant'       => 'Chinese (Traditional)',
        ];

        $current = isset($this->google_reviews_options['reviews_language_3']) ? $this->google_reviews_options['reviews_language_3'] : 'en';

        ?>
        <select name="google_reviews_option_name[reviews_language_3]" id="reviews_language_3">
            <option value="">Choose language</option>
            <?php
            foreach ($languages as $key => $language) {
                if ($key === $current) {
                    echo '<option value="'.esc_attr($key).'" selected>'.esc_attr($language).'</option>';
                } else {
                    echo '<option value="'.esc_attr($key).'">'.esc_attr($language).'</option>';
                }

            } ?>
        </select> <?php
    }

    /**
     * Echo shortcode instructions
     */
    public function reviews_instructions_callback() { ?>
        <div id="instructions">
            <p>
                <?php esc_html_e( 'Use this shortcode to show your reviews on pages and posts:', 'embedder-for-google-reviews' ); ?>
            </p>
            <input class="shortcode-container" type="text" disabled="" value="[google-reviews]">
            <p>
                <?php
                echo sprintf(
                /* translators: %s: URL */
                    esc_html__('<a href="%s" target="_blank">See</a>, how to overwrite styles, widget types and other settings.', 'embedder-for-google-reviews'),
                    esc_url("https://reviewsembedder.com/docs/how-to-overwrite-styles/?utm_source=wp_backend&utm_medium=instructions&utm_campaign=overwrite_styles_types")
                );
                ?>
            </p>
        </div>

        <?php
    }

}
