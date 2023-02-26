<?php

/**
 * Define methods for public html output via shortcodes etc.
 */
class GRWP_Google_Reviews_Output {

    /**
     * Whether to show dummy content
     * @var bool
     */
    protected $showdummy = false;

    /**
     * Allowed HTML and HTML attributes
     * @var array
     */
    protected $allowed_html;

    /**
     * Gobal reviews data
     * @var array|null
     */
    protected $reviews;

    /**
     * Flag if reviews are erroneous
     * @var bool
     */
    protected $reviews_have_error = false;

    /**
     * Constructor method
     */
	public function __construct() {

        require_once __DIR__ .'/allowed-html.php';

        global $allowed_html;
        $this->allowed_html = $allowed_html;

		$this->options = get_option( 'google_reviews_option_name' );
        $this->showdummy = isset( $this->options['show_dummy_content'] );
        $this->reviews = $this->get_review_data();

        // check for errors and set flag
        if ( is_wp_error( $this->reviews )
            || $this->reviews == ''
            || $this->reviews == null
            || ! is_array( $this->reviews )) {
            $this->reviews_have_error = true;
        }

    }

    /**
     * Prepare time string for reviews
     * @param $datetime
     * @param $full
     * @return string|null
     * @throws Exception
     */
    protected function time_elapsed_string( $datetime, $full = false ) {
		$now = new DateTime;
		$ago = new DateTime($datetime);
		$diff = $now->diff($ago);

		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;

		$string = array(
			'y' => array(
				__( 'year', 'grwp' ),
				__( 'years', 'grwp' )
			),
			'm' => array(
				__( 'month', 'grwp' ),
				__( 'months', 'grwp' )
			),
			'w' => array(
				__( 'week', 'grwp' ),
				__( 'weeks', 'grwp' )
			),
			'd' => array(
				__( 'day', 'grwp' ),
				__( 'days', 'grwp' )
			),
			'h' => array(
				__( 'hour', 'grwp' ),
				__( 'hours', 'grwp' )
			),
			'i' => array(
				__( 'minute', 'grwp' ),
				__( 'minutes', 'grwp' )
			),
			's' => array(
				__( 'second', 'grwp' ),
				__( 'seconds', 'grwp' )
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
		return $string ? implode(', ', $string) . __( ' ago', 'grwp' ) : __( 'just now', 'grwp' );
	}

    /**
     * Count and prepare stars
     * @param $review
     * @return string
     */
    protected function get_star_output( $review ) {

        $path = esc_attr( GR_PLUGIN_DIR_URL );
        $star = sprintf('<img src="%sdist/images/svg-star.svg" alt="" />', $path);
        $star_output = '<span class="stars-wrapper">';
        for ( $i = 1; $i <= $review['rating']; $i++ ) {
            $star_output .= $star;
        }
        $star_output .= '</span>';

        $star_output .= sprintf('<span class="time">%s</span>', $review['time']);

        return $star_output;

    }

    /**
     * Get dummy review content
     * @return array[]
     */
    protected function get_dummy_content() {

        $reviews = array( array(
            'author_name'               => __( 'Lorem Ipsum', 'grwp' ),
            'author_url'                => '#',
            'language'                  => 'en',
            'profile_photo_url'         => GR_PLUGIN_DIR_URL . 'dist/images/sample-photo.png',
            'rating'                    => 5,
            'relative_time_description' => __( 'three months ago', 'grwp' ),
            'text'                      => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', 'grwp' ),
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
    protected function step_over_review( $rating, $text ) {

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

    /**
     * Prepare and map review data
     * @param $reviews_raw
     * @return array
     */
    protected function map_review_data( $reviews_raw ) {

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
     * Get raw review data from database
     * @return void|array
     */
    protected function get_review_data() {

        // if dummy setting is active, get dummy content
        if ( $this->showdummy ) {

            $reviews_raw = $this->get_dummy_content();

        }

        // else get real reviews
        else {

            if ( grwp_fs()->is__premium_only() ) {

                $reviews_raw = GRWP_Pro_API_Service::parse_pro_review_json();

            }

            else {

                $reviews_raw = GRWP_Free_API_Service::parse_free_review_json();

            }

        }

        return $this->map_review_data( $reviews_raw );

    }

}
