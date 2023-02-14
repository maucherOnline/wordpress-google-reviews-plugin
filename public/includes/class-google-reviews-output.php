<?php

/**
 * Define methods for public html output via shortcodes etc.
 */
class GRWP_Google_Reviews_Output {

	/**
	 * Plugin Options/settings.
	 * @var null
	 */
	protected $options = null;

    /**
     * Whether to show dummy content
     * @var bool
     */
    protected bool $showdummy = false;

    /**
     * Allowed HTML and HTML attributes
     * @var array
     */
    protected array $allowed_html;

	public function __construct() {

        require_once __DIR__ .'/allowed-html.php';

        global $allowed_html;
        $this->allowed_html = $allowed_html;

		$this->options = get_option( 'google_reviews_option_name' );
        $this->showdummy = isset( $this->options['show_dummy_content'] );

        add_shortcode('google-reviews', [ $this, 'reviews_shortcode' ] );

    }

    protected function time_elapsed_string($datetime, $full = false) {
		$now = new DateTime;
		$ago = new DateTime($datetime);
		$diff = $now->diff($ago);

		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;

		$string = array(
			'y' => array(
				__( 'year', 'google-reviews' ),
				__( 'years', 'google-reviews' )
			),
			'm' => array(
				__( 'month', 'google-reviews' ),
				__( 'months', 'google-reviews' )
			),
			'w' => array(
				__( 'week', 'google-reviews' ),
				__( 'weeks', 'google-reviews' )
			),
			'd' => array(
				__( 'day', 'google-reviews' ),
				__( 'days', 'google-reviews' )
			),
			'h' => array(
				__( 'hour', 'google-reviews' ),
				__( 'hours', 'google-reviews' )
			),
			'i' => array(
				__( 'minute', 'google-reviews' ),
				__( 'minutes', 'google-reviews' )
			),
			's' => array(
				__( 'second', 'google-reviews' ),
				__( 'seconds', 'google-reviews' )
			)
		);
		foreach ($string as $k => &$v) {
			if ($diff->$k) {
				$v = $diff->$k . ' ' . ($diff->$k > 1 ? $v[1] : $v[0]);
			} else {
				unset($string[$k]);
			}
		}

		if (!$full) $string = array_slice($string, 0, 1);
		return $string ? implode(', ', $string) . __( ' ago', 'google-reviews' ) : __( 'just now', 'google-reviews' );
	}

    /**
     * Get type override value from shortcode attributes
     * @param array $atts
     * @return string
     */
    protected function get_review_type_override( array $atts ) : string {

        $result = '';
        if ( isset( $atts['type'] ) ) {
            $result = $atts['type'] === 'grid' ? 'grid' : '';
        }

        return $result;
    }


    protected function get_star_output() {

    }

    /**
     * Get style override value from shortcode attributes
     * @param array $atts
     * @return string
     */
    protected function get_review_style_override( array $atts ) : string {

        $review_style_override = '';

        if ( isset($atts['style']) ) {
            $override = $atts['style'];

            switch ( $override ) {
                case '1':
                    $review_style_override = 'layout_style-1';
                    break;
                case '2':
                    $review_style_override = 'layout_style-2';
                    break;
                case '3':
                    $review_style_override = 'layout_style-3';
                    break;
                case '4':
                    $review_style_override = 'layout_style-4';
                    break;
                case '5':
                    $review_style_override = 'layout_style-5';
                    break;
                case '6':
                    $review_style_override = 'layout_style-6';
                    break;
                case '7':
                    $review_style_override = 'layout_style-7';
                    break;
                case '8':
                    $review_style_override = 'layout_style-8';
                    break;
            }
        }

        return $review_style_override;

    }

    /**
     * Get dummy review content
     * @return array[]
     */
    protected function get_dummy_content() : array {

        $reviews = array( array(
            'author_name'               => __( 'Lorem Ipsum', 'google-reviews' ),
            'author_url'                => '#',
            'language'                  => 'en',
            'profile_photo_url'         => plugin_dir_url(__FILE__) . 'img/sample-photo.png',
            'rating'                    => 5,
            'relative_time_description' => __( 'three months ago', 'google-reviews' ),
            'text'                      => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', 'google-reviews' ),
            'time'                      => '1643630205'
        ) );

        for ( $i = 0; $i <= 4; $i++ ) {
            $reviews[] = $reviews[0];
        }

        return $reviews;

    }

    /**
     * Check if review should be left out, due to global settings
     * @param $rating
     * @param $text
     * @return bool
     */
    private function step_over_review( $rating, $text ) {

        // step over rating if minimum stars are not met
        if ( isset($this->options['filter_below_5_stars'])
            && $this->options['filter_below_5_stars'] ) {

            $min_rating = intval($this->options['filter_below_5_stars']);
            if ($rating < $min_rating) {
                return true;
            }

        }

        // step over rating if review has no text
        if ( isset( $this->options['exclude_reviews_without_text'] )
            && $this->options['exclude_reviews_without_text'] ) {

            if ( $text == '' ) {
                return true;
            }

        }

        // step over rating if review contains certain words
        if ( isset( $this->options['filter_words'] )
            && $this->options['filter_words'] !== '' ) {

            $words_str = rtrim($this->options['filter_words'], ',');
            $words_arr = explode(',', $words_str);
            foreach ($words_arr as $word) {
                $word = trim($word);

                if (str_contains($text, $word) ) {
                    return true;
                }
            }

        }

        return false;

    }

    private function map_review_data( $reviews_raw ) {

        // map reviews from different raw data to universal format
        $reviews = [];
        foreach ( $reviews_raw as $review ) {

            // assign dummy content
            if  ( $this->showdummy ) {

                $name = $review['author_name'];
                $author_url = $review['author_url'];
                $profile_photo_url = $review['profile_photo_url'];
                $rating = $review['rating'];
                $text = $review['text'];
                $time = $this->time_elapsed_string( date('Y-m-d h:i:s', $review['time']) );

            }

            // else, use real content, but...
            else {

                // use different array keys for pro version results
                if ( grwp_fs()->is__premium_only() ) {

                    $name              = $review['user']['name'];
                    $author_url        = $review['user']['link'];
                    $profile_photo_url = $review['user']['thumbnail'];
                    $rating            = $review['rating'];
                    $text              = $review['snippet'];
                    $time              = $review['date'];
                }

                // use different array keys for free version results
                else {

                    $name = $review['author_name'];
                    $author_url = $review['author_url'];
                    $profile_photo_url = $review['profile_photo_url'];
                    $rating = $review['rating'];
                    $text = $review['text'];
                    $time = $this->time_elapsed_string(date('Y-m-d h:i:s', $review['time']));

                }

            }

            // check if rating should be left out, due to global settings
            if ( $this->step_over_review($rating, $text) ) {
                continue;
            }

            $reviews[] = [
                'name'                 => $name,
                'author_url'           => $author_url,
                'profile_photo_url'    => $profile_photo_url,
                'rating'               => $rating,
                'text'                 => $text,
                'time'                 => $time
            ];

        }

        return $reviews;

    }

    /**
     * Prepare raw review data
     * @return void|array
     */
    private function get_review_data() {

        // if dummy setting is active, get dummy content
        if ( $this->showdummy ) {

            $reviews_raw = $this->get_dummy_content();

        }

        // else get real reviews
        else {

            if ( grwp_fs()->is__premium_only() ) {

                require_once GR_BASE_PATH_ADMIN . 'includes/pro/class-pro-api-service.php';
                $reviews_raw = Pro_API_Service::parse_pro_review_json();

            }

            else {

                require_once GR_BASE_PATH_ADMIN . 'includes/free/class-free-api-service.php';
                $reviews_raw = Free_API_Service::parse_free_review_json();

            }

        }

        return $this->map_review_data($reviews_raw);

    }


    /**
     * Grid HTML
     * @return string
     */
    private function grid_html( $style_type ) {

        $reviews = $this->get_review_data();

        // error handling
        if ( is_wp_error( $reviews ) || $reviews == '' || $reviews == null || ! is_array( $reviews ) ) {

            return __( 'No reviews available', 'google-reviews' );

        }

        // loop through reviews
        $output = sprintf('<div id="g-review" class="%s">', $style_type);
        $slider_output = '';

        foreach ( $reviews as $review ) {

            // count and prepare stars
            $star = sprintf('<img src="%simg/svg-star.svg" alt="" />', esc_attr(plugin_dir_url( __FILE__ )));
            $star_output = '<span class="stars-wrapper">';
            for ($i = 1; $i <= $review['rating']; $i++) {
                $star_output .= $star;
            }
            $star_output .= '</span>';

            $star_output .= sprintf('<span class="time">%s</span>', $review['time']);

            $google_svg = plugin_dir_url(__FILE__) . 'img/google-logo-svg.svg';

            ob_start();
            require 'partials/grid/markup.php';
            $output .= ob_get_clean();

        }

        $output .= '</div>';

        return wp_kses($output, $this->allowed_html);
    }

    /**
     * Slider HTML
     * @return string
     */
    private function slider_html( $style_type ) {

        $reviews = $this->get_review_data();

        // error handling
        if ( is_wp_error( $reviews ) || $reviews == '' || $reviews == null || ! is_array( $reviews ) ) {

            return __( 'No reviews available', 'google-reviews' );

        }

        // loop through reviews
        $output = sprintf('<div id="g-review" class="%s">', $style_type);
        $slider_output = '';

        foreach ( $reviews as $review ) {

            // count and prepare stars
            $star = sprintf('<img src="%simg/svg-star.svg" alt="" />', esc_attr(plugin_dir_url( __FILE__ )));
            $star_output = '<span class="stars-wrapper">';
            for ($i = 1; $i <= $review['rating']; $i++) {
                $star_output .= $star;
            }
            $star_output .= '</span>';

            $star_output .= sprintf('<span class="time">%s</span>', $review['time']);

            $google_svg = plugin_dir_url(__FILE__) . 'img/google-logo-svg.svg';

            $slide_duration = $this->options['slide_duration'] ?? '';

            ob_start();
            require 'partials/slider/markup.php';
            $slider_output .= ob_get_clean();


        }

        ob_start();
        require 'partials/slider/slider-header.php';
        echo wp_kses($slider_output, $this->allowed_html);
        require 'partials/slider/slider-footer.php';

        $output .= ob_get_clean();

        $output .= '</div>';

        return wp_kses($output, $this->allowed_html);
    }


    /**
     * Returns the correct HTML markup for each widget type
     * @param $widget_type
     * @param $style_type
     * @return string|void
     */
    private function reviews_html( $widget_type, $style_type ) {

        if ( $widget_type === 'slider' ) {
            return $this->slider_html( $style_type );
        }

        return $this->grid_html( $style_type );

    }

    /**
     * Parse shortcode data, return html
     * @param array|null $atts
     * @return string
     */
    public function reviews_shortcode( $atts = null ) : string {

        // get style/type override values
        $review_type_override = '';
        $review_style_override = '';

        if ( $atts ) {

            $review_type_override = $this->get_review_type_override( $atts );
            $review_style_override = $this->get_review_style_override( $atts );

        }

        // check if style type is overwritten by shortcode attributes
        $style_type = $this->options['layout_style'];
        if ( $review_style_override !== '' ) {

            $style_type = $review_style_override;

        }

        // check if widget type is overwritten by shortcode attributes
        $widget_type = strtolower($this->options['style_2']);
        if ( $review_type_override !== '' ) {

            $widget_type = $review_type_override;

        }

        return $this->reviews_html( $widget_type, $style_type );

    }

}
