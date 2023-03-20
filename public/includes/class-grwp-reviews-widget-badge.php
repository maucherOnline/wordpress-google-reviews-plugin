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
			<img src="<?php echo GR_PLUGIN_DIR_URL .'/dist/images/g-stars.svg'; ?>"
			     alt=""
			     class="g-stars"
			/>
			<span class="g-rating">

			</span>
		</a>


<?php
        $output .= ob_get_clean();
		$output .= '</div>';
        $output .= '<div class="g-review-sidebar right hide"><div class="grwp-header">'.__("Google Reviews", "grwp");
        $output .= sprintf('<img src="%s/dist/images/g-stars.svg" alt="" class="g-logo" />', GR_PLUGIN_DIR_URL);
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

}
