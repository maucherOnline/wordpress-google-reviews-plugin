<?php

Class Pro_Settings {

    private $google_reviews_options;

    public function __construct() {

        $this->google_reviews_options = get_option( 'google_reviews_option_name' );
        $this->add_settings();

    }

    private function add_settings() {
        add_settings_field(
            'serp_business_name', // id
            __( 'Search for your business:', 'google-reviews' ), // title
            array( $this, 'serp_business_name_callback' ), // callback
            'google-reviews-admin', // page
            'google_reviews_setting_section' // section
        );

        add_settings_field(
            'serp_data_id', // id
            false, // title
            array( $this, 'serp_data_id_callback' ), // callback
            'google-reviews-admin', // page
            'google_reviews_setting_section', // section
            array( 'class' => 'hidden' )
        );

        add_settings_field(
            'filter_below_5_stars', // id
            __('Minimum rating (stars)', 'google-reviews'), // title
            array($this, 'filter_below_5_stars_callback'), // callback
            'google-reviews-admin', // page
            'google_reviews_style_layout_setting_section' // section
        );
    }

    public function filter_below_5_stars_callback() {
        global $allowed_html;

        ob_start();
        ?>

        <input type="number"
               name="google_reviews_option_name[filter_below_5_stars]"
               id="filter_below_5_stars"
               min="1"
               max="5"
               step="1"
               value="<?php echo esc_attr( ! empty( $this->google_reviews_options['filter_below_5_stars'] ) ? $this->google_reviews_options['filter_below_5_stars'] : '5' ); ?>"
        />

        <?php
        $html = ob_get_clean();

        echo wp_kses($html, $allowed_html);

    }


    /**
     * Echo Business Search Field
     */
    public function serp_business_name_callback() {
        ob_start();
        $search_disabled = isset($this->google_reviews_options['serp_business_name']) && $this->google_reviews_options['serp_business_name'] !== '' ? 'disabled' : '';
        ?>

        <div class="serp-container">
            <div class="serp-search">
                <input type="search"
                       class="regular-text js-serp-business-search"
                       name="google_reviews_option_name[serp_business_name]"
                       id="serp_business_name"
                       value="<?php echo esc_attr( isset( $this->google_reviews_options['serp_business_name'] ) ? $this->google_reviews_options['serp_business_name'] : '' ); ?>"
                       autocomplete="off"
                       placeholder="<?php _e('Search for your business', 'google-reviews');?>"
                />
                <div class="button-row">
                    <a class="button search-business pro" <?php echo $search_disabled; ?>>
                        <?php _e('Search business', 'google-reviews');?>
                    </a>
                    <a class="button pull-reviews pro">
                        <?php _e('Pull reviews', 'google-reviews');?>
                    </a>
                </div>
                <fieldset class="serp-results"></fieldset><!-- /.serp-results -->
            </div><!-- /.serp-search -->

            <p id="error"> </p><!-- /.serp-error -->
        </div><!-- /.serp-container -->

        <p>
            <?php _e( 'Details like country, state, city and/or phone number may help achieving more accurate results.', 'google-reviews' ); ?>
        </p>

        <?php
        $html = ob_get_clean();

        echo $html;
    }

    /**
     * Echo Hidden SERP Data ID Field
     */
    public function serp_data_id_callback() {
        global $allowed_html;
        ob_start();
        ?>

        <input type="hidden" class="hidden js-serp-data-id" name="google_reviews_option_name[serp_data_id]" id="serp_data_id" value="<?php echo esc_attr( isset( $this->google_reviews_options['serp_data_id'] ) ? $this->google_reviews_options['serp_data_id'] : '' ); ?>">

        <?php
        $html = ob_get_clean();

        echo wp_kses($html, $allowed_html);
    }

}
