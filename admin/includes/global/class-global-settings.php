<?php

Class Global_Settings {
    private $google_reviews_options;

    public function __construct() {
        $this->google_reviews_options = get_option( 'google_reviews_option_name' );
        $this->add_settings();

    }

    private function add_settings() {

        /**
         * API settings
         */
        register_setting(
            'google_reviews_option_group', // option_group
            'google_reviews_option_name', // option_name
            array( $this, 'google_reviews_sanitize' ) // sanitize_callback
        );

        add_settings_section(
            'google_reviews_setting_section', // id
            __( 'Global settings for showing reviews', 'google-reviews' ), // title
            array( $this, 'google_reviews_section_info' ), // callback
            'google-reviews-admin' // page
        );

        add_settings_field(
            'show_dummy_content', // id
            __( 'Show dummy content', 'google-reviews' ), // title
            array( $this, 'show_dummy_content_callback' ), // callback
            'google-reviews-admin', // page
            'google_reviews_setting_section' // section
        );

        add_settings_field(
            'reviews_language_3', // id
            __( 'Reviews language', 'google-reviews' ), // title
            array( $this, 'reviews_language_3_callback' ), // callback
            'google-reviews-admin', // page
            'google_reviews_setting_section' // section
        );

        /**
         * Display settings
         */
        // settings for styles and layout
        register_setting(
            'google_reviews_style_group', // option_group
            'google_reviews_style', // option_name
            array( $this, 'google_reviews_sanitize' ) // sanitize_callback
        );

        // add style and layout settings section
        add_settings_section(
            'google_reviews_style_layout_setting_section', // id
            __( 'Display settings', 'google-reviews' ), // title
            array( $this, 'google_reviews_section_info' ), // callback
            'google-reviews-admin' // page
        );

        // add style and layout settings field
        add_settings_field(
            'style_2', // id
            __( 'Layout type', 'google-reviews' ), // title
            array( $this, 'style_2_callback' ), // callback
            'google-reviews-admin', // page
            'google_reviews_style_layout_setting_section' // section
        );

        add_settings_field(
            'layout_style', // id
            __( 'Design type', 'google-reviews' ), // title
            array( $this, 'layout_style_callback' ), // callback
            'google-reviews-admin', // page
            'google_reviews_style_layout_setting_section' // section
        );

        add_settings_field(
            'filter_below_5_stars', // id
            __('Minimum rating (stars)', 'google-reviews'), // title
            array($this, 'filter_below_5_stars_callback'), // callback
            'google-reviews-admin', // page
            'google_reviews_style_layout_setting_section' // section
        );

        add_settings_field(
            'exclude_reviews_without_text', // id
            __('Exclude reviews without text', 'google-reviews'), // title
            array($this, 'exclude_reviews_without_text_callback'), // callback
            'google-reviews-admin', // page
            'google_reviews_style_layout_setting_section' // section
        );

        add_settings_field(
            'filter_words', // id
            __('Filter by words (comma separated)', 'google-reviews'), // title
            array($this, 'filter_words_callback'), // callback
            'google-reviews-admin', // page
            'google_reviews_style_layout_setting_section' // section
        );

        /**
         * Embeddding instructions
         */
        add_settings_section(
            'google_reviews_embedding_instructions_section', // id
            __( 'Embedding instructions', 'google-reviews' ), // title
            array( $this, 'reviews_instructions_section' ), // callback
            'google-reviews-admin' // page
        );

        add_settings_field(
            'embedding_instructions', // id
            __( 'Shortcode', 'google-reviews' ), // title
            array( $this, 'reviews_instructions_callback' ), // callback
            'google-reviews-admin', // page
            'google_reviews_embedding_instructions_section' // section
        );

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

        if ( isset( $input['exclude_reviews_without_text'] ) ) {
            $sanitary_values['exclude_reviews_without_text'] = $input['exclude_reviews_without_text'];
        }

        if ( isset( $input['filter_words'] ) ) {
            $sanitary_values['filter_words'] = $input['filter_words'];
        }

        if ( isset( $input['reviews_language_3'] ) ) {
            $sanitary_values['reviews_language_3'] = $input['reviews_language_3'];
        }

        return $sanitary_values;
    }

    public function google_reviews_section_info() {
        // additional output possible
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
            <?php _e( 'Yes', 'google-reviews' ); ?>
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
     * Exclude reviews without text
     * @return void
     */
    public function exclude_reviews_without_text_callback() {
        global $allowed_html;

        ob_start(); ?>

        <input type="checkbox"
               name="google_reviews_option_name[exclude_reviews_without_text]"
               value="1"
               id="exclude_reviews_without_text"
               <?php echo esc_attr( ! empty( $this->google_reviews_options['exclude_reviews_without_text'] ) ? 'checked' : '' ); ?>
        >

        <span>
            <?php _e( 'Yes', 'google-reviews' ); ?>
        </span>

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

        <textarea
           name="google_reviews_option_name[filter_words]"
           id="filter_words"
           rows="2"
        ><?php echo esc_attr( ! empty( $this->google_reviews_options['filter_words'] ) ? $this->google_reviews_options['filter_words'] : '' ); ?></textarea>

        <?php
        $html = ob_get_clean();

        echo wp_kses($html, $allowed_html);

    }

    /**
     * Echo layout option field
     */
    public function style_2_callback() {
        ?> <select name="google_reviews_option_name[style_2]" id="style_2">
            <?php $selected = (isset( $this->google_reviews_options['style_2'] ) && $this->google_reviews_options['style_2'] === 'Slider') ? 'selected' : '' ; ?>
            <option <?php echo esc_attr($selected); ?>>
                <?php _e( 'Slider', 'google-reviews' ); ?>
            </option>
            <?php $selected = (isset( $this->google_reviews_options['style_2'] ) && $this->google_reviews_options['style_2'] === 'Grid') ? 'selected' : '' ; ?>
            <option <?php echo esc_attr($selected); ?>>
                <?php _e( 'Grid', 'google-reviews' ); ?>
            </option>
        </select> <?php
    }

    public function grid_columns_callback() {
        $columns = $this->google_reviews_options['grid_columns'] ?? '';

        if (empty($columns)){
            $columns = 3;
        }

        ?>

        <select name="google_reviews_option_name[grid_columns]" id="grid_columns">
            <option <?php selected($columns, '1'); ?> value="1"><?php esc_attr_e('1'); ?></option>
            <option <?php selected($columns, '2'); ?> value="2"><?php esc_attr_e('2'); ?></option>
            <option <?php selected($columns, '3'); ?> value="3"><?php esc_attr_e('3'); ?></option>
        </select>

        <?php

    }

    public function layout_style_callback() {
        $layout_style = isset($this->google_reviews_options['layout_style']) ? $this->google_reviews_options['layout_style'] : '';

        if (empty($layout_style)){
            $layout_style = '1';
        }

        $layout_styles_count = 4;
        ?>

        <select name="google_reviews_option_name[layout_style]" id="layout_style">
            <?php for ( $i = 1; $i <= 8; $i++ ) : ?>
                <option
                    <?php selected( $layout_style, 'layout_style-' . $i ); ?>
                        value="<?php echo esc_attr( sprintf( 'layout_style-%s', $i ) ) ?>"
                >
                    <?php esc_attr_e( __( 'Design', 'google-reviews' ) . ' #' . $i ); ?>
                </option>
            <?php endfor; ?>
        </select>

        <?php
    }

    public function slide_duration_callback() {
        $slide_duration = $this->google_reviews_options['slide_duration'] ?? '';

        if (empty($slide_duration)){
            $slide_duration = '1500';
        }

        ?>

        <input type="number" min="50" max="9999" step="50" name="google_reviews_option_name[slide_duration]" value="<?php echo esc_attr($slide_duration); ?>">

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
    public function reviews_instructions_callback() {
        ?>
        <div id="instructions">
            <p>
                <?php _e( 'Use this shortcode to show your reviews on pages and posts:', 'google-reviews' ); ?>
            </p>
            <input class="shortcode-container" type="text" disabled="" value="[google-reviews]">
        </div>

        <?php
    }

    public function reviews_instructions_section() {

    }
}
