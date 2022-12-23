<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       test
 * @since      1.0.0
 *
 * @package    Google_Reviews
 * @subpackage Google_Reviews/admin
 */

// https://jeremyhixon.com/tool/wordpress-option-page-generator/
class GRWP_Google_ReviewsAdmin {

    private $google_reviews_options;

    /**
     * Allowed html tags for echo
     *
     * @since    1.0.0
     * @access   private
     * @var      array    $allowed_html    Allowed html tags for echo
     */
    private $allowed_html;

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;


    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in GRWP_Google_Reviews_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The GRWP_Google_Reviews_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style( 'admin-' . $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/google-reviews-admin.css', array(), $this->version, 'all' );

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in GRWP_Google_Reviews_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The GRWP_Google_Reviews_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script( 'admin-' . $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/google-reviews-admin.js', array( 'jquery' ), $this->version, false );

        wp_localize_script( 'admin-' . $this->plugin_name, 'js_global', array(
            'wp_ajax_url' => admin_url( 'admin-ajax.php' ),
            'language'    => $this->google_reviews_options['reviews_language_3']
        ) );

    }

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {
	    $this->google_reviews_options = get_option( 'google_reviews_option_name' );

        add_action( 'admin_menu', array( $this, 'google_reviews_add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'google_reviews_page_init' ) );
        add_action( 'updated_option', array( $this, 'on_saving_options' ), 10, 3 );

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        $this->allowed_html = [
            'img' => [
                'title'             => [],
                'src'               => [],
                'alt'               => [],
                'width'             => [],
                'height'            => [],
                'class'             => [],
                'data-imgtype'      => [],
                'referrerpolicy'    => [],
            ],
            'style'                     => [],
            'div'                       => [
                'class'                 => [],
                'id'                    => [],
                'data-swiper-autoplay'  => [],
            ],
            'a' => [
                'href'      => [],
                'target'    => [],
            ],
            'p' => [],
            'span' => [
                'class' => [],
                'id'    => [],
            ],
            'br' => [],
            'iframe' => [
                'src'       => [],
                'width'     => [],
                'height'    => [],
                'style'     => [],
                'allow'     => []

            ],
            'input' => [
                'class'     => [],
                'id'        => [],
                'type'      => [],
                'name'      => [],
                'checked'   => [],
                'value'     => [],
                'min'       => [],
                'max'       => [],
                'step'      => [],

            ],
        ];

    }


    /**
     * Add menu page to backend
     */
    public function google_reviews_add_plugin_page() {

        add_menu_page(
            __( 'Google Reviews', 'google-reviews' ), // page_title
            __( 'Google Reviews', 'google-reviews' ), // menu_title
            'manage_options', // capability
            'google-reviews', // menu_slug
            array( $this, 'google_reviews_create_admin_page' ), // function
            'dashicons-star-filled', // icon_url
            75 // position
        );

        if ( grwp_fs()->is__premium_only() ) {

            add_submenu_page(
                'google-reviews',
                'How to',
                'How to',
                'manage_options',
                'how-to-premium-version',
                array($this, 'google_reviews_create_sub_page_how_to_premium')
            );

        }

        else {

            add_submenu_page(
                'google-reviews',
                'How to',
                'How to',
                'manage_options',
                'how-to-free-version',
                array($this, 'google_reviews_create_sub_page_how_to')
            );

        }

    }

    /**
     * Backend how to subpage for premium version
     * @return void
     */
    public function google_reviews_create_sub_page_how_to_premium() {
        global $imgpath;
        $imgpath = plugin_dir_url(__FILE__) .'img/';

        echo '<div class="wrap">';
        require_once plugin_dir_path(__FILE__) .'/includes/how-to-premium.php';
        echo '</div>';
    }

    /**
     * Backend how to subpage for free version
     * @return void
     */
    public function google_reviews_create_sub_page_how_to() {
        global $imgpath;
        $imgpath = plugin_dir_url(__FILE__) .'img/';

        echo '<div class="wrap">';
        require_once plugin_dir_path(__FILE__) .'/includes/how-to.php';
        echo '</div>';
    }

    /**
     * Fire API request when core options are changed to check
     * for connection issues and get latest reviews
     * @param $option_name
     * @param $before
     * @param $after
     */
    public function on_saving_options($option_name, $before, $after) {

        /*
        // get newest results if core API  settings were changed
        if ($option_name === 'google_reviews_option_name') {

            GRWP_Google_Reviews::get_reviews();

            $review_json = GRWP_Google_Reviews::parse_review_json();

            if ( is_wp_error( $review_json ) ) {

                add_settings_error(

                    'google_reviews_option_name',
                    esc_attr( 'settings_updated' ),
                    $review_json->get_error_message()

                );

            }

        }

        */

    }

    /**
     * Create admin page on backend
     */
    public function google_reviews_create_admin_page() {
        if (empty($this->google_reviews_options))
            $this->google_reviews_options = get_option( 'google_reviews_option_name' );

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
            <?php echo wp_kses(do_shortcode('[google-reviews]'), $this->allowed_html); ?>
        </div>
    <?php }

    /**
     * Register settings, sections and option fields
     */
    public function google_reviews_page_init() {

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

        if ( grwp_fs()->is__premium_only() ) {
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
        } else {
            add_settings_field(
                'api_key_0', // id
                __( 'API Key', 'google-reviews' ), // title
                array( $this, 'api_key_0_callback' ), // callback
                'google-reviews-admin', // page
                'google_reviews_setting_section' // section
            );

            add_settings_field(
                'gmb_id_1', // id
                __( 'Place ID', 'google-reviews' ), // title
                array( $this, 'gmb_id_1_callback' ), // callback
                'google-reviews-admin', // page
                'google_reviews_setting_section' // section
            );
        }

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
            __( 'Style', 'google-reviews' ), // title
            array( $this, 'style_2_callback' ), // callback
            'google-reviews-admin', // page
            'google_reviews_style_layout_setting_section' // section
        );

        /*
        if(strtolower($this->google_reviews_options['style_2']) === 'grid'){
	        add_settings_field(
		        'grid_columns', // id
		        'Grid Columns', // title
		        array( $this, 'grid_columns_callback' ), // callback
		        'google-reviews-admin', // page
		        'google_reviews_style_layout_setting_section' // section
	        );
        }else{
	        add_settings_field(
		        'slide_duration', // id
		        __( 'Slide duration (milliseconds)', 'google-reviews' ), // title
		        array( $this, 'slide_duration_callback' ), // callback
		        'google-reviews-admin', // page
		        'google_reviews_style_layout_setting_section' // section
	        );
        }
        */

	    add_settings_field(
		    'layout_style', // id
		    __( 'Layout style', 'google-reviews' ), // title
		    array( $this, 'layout_style_callback' ), // callback
		    'google-reviews-admin', // page
		    'google_reviews_style_layout_setting_section' // section
	    );

        // Option for filtering out bad reviews
        if ( grwp_fs()->is__premium_only() ) {
            add_settings_field(
                'filter_below_5_stars', // id
                __('Minimum rating (stars)', 'google-reviews'), // title
                array($this, 'filter_below_5_stars_callback'), // callback
                'google-reviews-admin', // page
                'google_reviews_style_layout_setting_section' // section
            );
        }

        add_settings_field(
            'reviews_instructions', // id
            __( 'Embedding instructions', 'google-reviews' ), // title
            array( $this, 'reviews_instructions_callback' ), // callback
            'google-reviews-admin', // page
            'google_reviews_style_layout_setting_section' // section
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
		    $sanitary_values['filter_below_5_stars'] = $input['filter_below_5_stars'];
	    }

        if ( isset( $input['reviews_language_3'] ) ) {
            $sanitary_values['reviews_language_3'] = $input['reviews_language_3'];
        }

        return $sanitary_values;
    }

    public function filter_below_5_stars_callback() {
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

        echo wp_kses($html, $this->allowed_html);

    }

    public function google_reviews_section_info() {
        // additional output possible
    }

    public function show_dummy_content_callback() {
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

        echo wp_kses($html, $this->allowed_html);
    }

    /**
     * Echo Business Search Field
     */
    public function serp_business_name_callback() {
        ob_start();
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
                <a class="button pull-reviews"><?php _e('Pull reviews', 'google-reviews');?></a>

                <fieldset class="serp-results"></fieldset><!-- /.serp-results -->
            </div><!-- /.serp-search -->

            <div class="serp-error"> </div><!-- /.serp-error -->
        </div><!-- /.serp-container -->

        <p>
            <?php _e( 'Details like country, state, city and/or phone number may help for achieving more accurate results.', 'google-reviews' ); ?>
        </p>

        <?php
        $html = ob_get_clean();

        echo $html;
    }

    /**
     * Echo Hidden SERP Data ID Field
     */
    public function serp_data_id_callback() {
        ob_start();
        ?>

        <input type="hidden" class="hidden js-serp-data-id" name="google_reviews_option_name[serp_data_id]" id="serp_data_id" value="<?php echo esc_attr( isset( $this->google_reviews_options['serp_data_id'] ) ? $this->google_reviews_options['serp_data_id'] : '' ); ?>">

        <?php
        $html = ob_get_clean();

        echo $html;
    }

    /**
     * Echo API key field
     */
    public function api_key_0_callback() {
        printf(
            '<input class="regular-text" type="text" name="google_reviews_option_name[api_key_0]" id="api_key_0" value="%s">',
            isset( $this->google_reviews_options['api_key_0'] ) ? esc_attr( $this->google_reviews_options['api_key_0']) : ''
        );
        printf( __( '<div><p>Head over to <a href="%s" target="_blank">Google Developer Console</a> and create an API key. See short <a href="%s" target="_self">explainer video here.</a></p></div>', 'google-reviews' ), 'https://console.cloud.google.com/apis/dashboard', 'https://www.youtube.com/watch?v=feM25lZkLkA' );
        printf( __( '<p style="
    padding: 1rem;
    border: 2px solid #1f6bae;
    background: white;
    margin-top: 1rem;
    max-width: 665px;
"><strong>Attention</strong>: Google\'s free version only allows for pulling 5 reviews. If you want to circumvent this, <a href="%s">upgrade to the PRO version</a> and pull ALL reviews, without messing around with API keys and Google :)</p>', 'google-reviews' ), get_site_url().'/wp-admin/admin.php?page=google-reviews-pricing');
    }

    /**
     * Echo place ID field
     */
    public function gmb_id_1_callback() {
        printf(
            '<input class="regular-text" type="text" name="google_reviews_option_name[gmb_id_1]" id="gmb_id_1" value="%s">',
            isset( $this->google_reviews_options['gmb_id_1'] ) ? esc_attr( $this->google_reviews_options['gmb_id_1']) : ''
        );

        $echo = '<div><p>' . __( 'Search for your business below and paste the place ID into the field above.', 'google-reviews' ) . '</p></div>';
        $echo .= '<iframe height="200" style="height: 200px; width: 100%; max-width: 700px;display:block;" src="https://geo-devrel-javascript-samples.web.app/samples/places-placeid-finder/app/dist/" allow="fullscreen; "></iframe>';

        echo wp_kses($echo, $this->allowed_html);

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
		$layout_style = $this->google_reviews_options['layout_style'];

		if (empty($layout_style)){
			$layout_style = '1';
		}

        $layout_styles_count = 4;
		?>

        <select name="google_reviews_option_name[layout_style]" id="layout_style">
            <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
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

        $current =  $this->google_reviews_options['reviews_language_3'];

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
        <div>
            <p>
                <?php _e( 'Use this shortcode to show your reviews on pages and posts:', 'google-reviews' ); ?>
            </p>
            <input class="shortcode-container" type="text" disabled="" value="[google-reviews]">
        </div>

        <?php
    }

}
