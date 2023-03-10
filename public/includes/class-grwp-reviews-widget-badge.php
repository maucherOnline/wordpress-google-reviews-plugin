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
        $output .= '<div class="g-review-sidebar right hide"></div>';

		return wp_kses( $output, $this->allowed_html );

	}

}
