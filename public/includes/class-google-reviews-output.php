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
     * Put together shortcode / html output
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

        // if dummy setting is active, get dummy content
        if ( $this->showdummy ) {

            $reviews = $this->get_dummy_content();

        }

        // else get real reviews
        else {

            if ( grwp_fs()->is__premium_only() ) {

                require_once GR_BASE_PATH_ADMIN . 'includes/pro/class-pro-api-service.php';
                $reviews = Pro_API_Service::parse_pro_review_json();

            }

            else {

                require_once GR_BASE_PATH_ADMIN . 'includes/free/class-free-api-service.php';
                $reviews = Free_API_Service::parse_free_review_json();

            }

        }

        // error handling
        if ( is_wp_error( $reviews ) || $reviews == '' || $reviews == null || ! is_array( $reviews ) ) {

            return __( 'No reviews available', 'google-reviews' );

        }

        return $this->reviews_output( $reviews, $review_type_override, $review_style_override );

    }

    private function reviews_output( $reviews, $review_type_override, $review_style_override ) {

        // check if style settings are overwritten by shortcode settings
        $layout_style = $this->options['layout_style'];
        if ( $review_style_override !== '' ) {

            $layout_style = $review_style_override;

        }

        // get style type
        $display_type = strtolower($this->options['style_2']);

        // loop through reviews
        $output = '<div id="g-review" class="' . $layout_style .'">';
        $slider_output = '';

        foreach ( $reviews as $review ) {

            // assign dummy content
            if  ( $this->showdummy ) {

                $name = $review['author_name'];
                $author_url = $review['author_url'];
                $profile_photo_url = $review['profile_photo_url'];
                $rating = $review['rating'];
                $text = $review['text'];

                $time = date('m/d/Y', $review['time']);

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

                }

                // use different array keys for free version results
                else {

                    $name = $review['author_name'];
                    $author_url = $review['author_url'];
                    $profile_photo_url = $review['profile_photo_url'];
                    $rating = $review['rating'];
                    $text = $review['text'];

                    $time = date('m/d/Y', $review['time']);


                }
            }

            // step over rating if minimum stars are not met
            if ( isset($this->options['filter_below_5_stars'])
                && $this->options['filter_below_5_stars'] ) {

                $min_rating = intval($this->options['filter_below_5_stars']);
                if ($rating < $min_rating) {
                    continue;
                }

            }

            // step over rating if review has no text
            if ( isset( $this->options['exclude_reviews_without_text'] )
                && $this->options['exclude_reviews_without_text'] ) {

                if ($text == '') {
                    continue;
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
                        continue 2;
                    }
                }

            }


            // count and prepare stars
            $star = '<img src="'. esc_attr(plugin_dir_url( __FILE__ )).'img/svg-star.svg" alt="" />';
            $star_output = '<span class="stars-wrapper">';
            for ($i = 1; $i <= $rating; $i++) {
                $star_output .= $star;
            }
            $star_output .= '</span>';

            // prepare time elapsed string
            if ( $this->showdummy ) {
                $star_output .= '<span class="time">' . $this->time_elapsed_string( date('Y-m-d h:i:s', $review['time']) ) .'</span>';
            }

            else {

                // time string for pro version
                if (grwp_fs()->is__premium_only()) {
                    $star_output .= '<span class="time">' . $review['date'] . '</span>';
                }

                // time string for free version
                else {
                    $star_output .= '<span class="time">' . $this->time_elapsed_string(date('Y-m-d h:i:s', $review['time'])) . '</span>';
                }
            }

            $google_svg = plugin_dir_url(__FILE__) . 'img/google-logo-svg.svg';

            // if is slider
            if ( $display_type === 'slider' && $review_type_override !== 'grid' ){

                $slide_duration = $this->options['slide_duration'] ?? '';

                ob_start();
                require 'partials/slider/markup.php';
                $slider_output .= ob_get_clean();

            }
            // if is grid
            else {

                ob_start();
                require 'partials/grid/markup.php';
                $output .= ob_get_clean();

            }
        }

        // add swiper header and footer if is slider
        if ( $display_type === 'slider' && $review_type_override !== 'grid' ) {

            ob_start();
            require 'partials/slider/slider-header.php';
            echo wp_kses($slider_output, $this->allowed_html);
            require 'partials/slider/slider-footer.php';

            $output .= ob_get_clean();

        }

        // set grid columns
        $db_grid_columns = isset($this->options['grid_columns']) ? $this->options['grid_columns'] : 3;
        $columns_css = '';

        // add slider styles if is slider
        if ( $display_type === 'slider' && $review_type_override !== 'grid'){
            ob_start();
            require 'partials/slider/style.php';
            $output .= ob_get_clean();
        }

        $output .= '</div>';

        return wp_kses($output, $this->allowed_html);

    }

}
