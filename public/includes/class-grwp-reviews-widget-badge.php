<?php

class GRWP_Reviews_Widget_Badge
	extends
	GRWP_Google_Reviews_Output {

	/**
	 * Slider HTML
	 * @return string
	 */
	public function render( $max_reviews = null ) {

		// error handling
		if ( $this->reviews_have_error ) {

			return __( 'No reviews available', 'grwp' );

		}

		$all_options = get_option( 'google_reviews_option_name' );
        $data_id = $all_options['serp_data_id'];
        $place_info = json_decode(get_option('grwp_place_info')[$data_id], true);
        $rating_rounded = intval(round($place_info['rating']));
        $rating_formatted = number_format($place_info['rating'], 1);
        $reviews = $place_info['reviews'];
        $title = $place_info['title'];

        $stars = $this->get_stars($rating_rounded);

		// loop through reviews
		$output = '<div id="g-review" class="badge">';
        ob_start();
        ?>

		<a href="#badge_list"
		   class="g-badge"
		   target="_blank">
			<img src="<?php echo GR_PLUGIN_DIR_URL .'/dist/images/google-logo-svg.svg'; ?>"
			     alt=""
			     class="g-logo"
			/>
			<span class="g-label">
				<?php _e('Our Google Reviews', 'grwp'); ?>
											</span>
            <?php echo $stars; ?>
			<span class="g-rating">
                <?php echo $rating_formatted; ?>
			</span>
		</a>


<?php
        $output .= ob_get_clean();
		$output .= '</div>';
        $output .= '<div class="g-review-sidebar right hide"><div class="grwp-header">';
        $output .= sprintf('<span class="business-title">%s</span>', $title);
        $output .=  $stars;
        $output .= sprintf('<span class="rating">%s</span>', $rating_formatted);
        $output .= '<span class="grwp-close"></span></div>';
        $output .= '<div class="grwp-sidebar-inner">';

		$google_svg = GR_PLUGIN_DIR_URL . 'dist/images/google-logo-svg.svg';

		$count = 0;
		foreach ( $this->reviews as $review ) {
			if ( $max_reviews && is_numeric( $max_reviews ) && intval($max_reviews) <= $count ) {
				break;
			}
			$star_output = $this->get_star_output($review);

			ob_start();
			require 'partials/grid/markup.php';
			$output .= ob_get_clean();

			$count++;
		}

        $output .= '</div></div>';

		return wp_kses( $output, $this->allowed_html );

	}

	/**
	 * Count and prepare stars
	 * @param $rating
	 * @return string
	 */
	private function get_stars( $rating ) {

		$path = esc_attr( GR_PLUGIN_DIR_URL );
		$star = sprintf('<img src="%sdist/images/svg-star.svg" alt="" />', $path);
		$star_output = '<span class="stars-wrapper">';
		for ( $i = 1; $i <= $rating; $i++ ) {
			$star_output .= $star;
		}
		$star_output .= '</span>';

		return $star_output;

	}

}
