<?php

class GRWP_Reviews_Widget_Grid
    extends
    GRWP_Google_Reviews_Output {

    public function render( $style_type, $link_user_profiles, $max_reviews = null, $show_place_info = false, $txt = '' ) {

        // error handling
        if ( $this->reviews_have_error ) {
            /* translators: No reviews available */
            return __( 'No reviews available', 'embedder-for-google-reviews' );
        }

	    $hide_date = '';
	    if ( isset($this->options['hide_date_string']) ) {
		    if ( $this->options['hide_date_string'] !== '' ) {
			    $hide_date = 'hide_date';
		    }
	    }

        $show_verified = false;
        if ( isset($this->options['show_verified']) ) {
            if ( $this->options['show_verified'] === '1' ) {
                $show_verified = true;
            }
        }

        $google_svg = GR_PLUGIN_DIR_URL . 'dist/images/google-logo-svg.svg';
        $verified_svg = GR_PLUGIN_DIR_URL . 'dist/images/verified-badge.svg';
        $url = 'https://reviewsembedder.com';

        // Global setting: hide company header overrides shortcode place_info attribute
        if ( ! empty( $this->options['hide_company_header'] ) ) {
            $show_place_info = false;
        }


        // Show-more button: number of review ROWS visible before "Load more".
        // The cards-per-row count is responsive, so JS derives the visible card
        // count from rows x columns and keeps it in sync on resize.
        $show_more_attr = '';
        $show_more_rows = 0;
        if ( ! empty( $this->options['show_more_grid'] ) ) {
            $show_more_rows = isset( $this->options['show_more_grid_initial'] ) && intval( $this->options['show_more_grid_initial'] ) > 0
                ? intval( $this->options['show_more_grid_initial'] )
                : 2;
            $show_more_attr = ' data-grwp-show-more-rows="' . esc_attr( $show_more_rows ) . '"';
        }

        // Number of cards that will actually be rendered (respecting max_reviews)
        $total_cards = count( $this->reviews );
        if ( $max_reviews && is_numeric( $max_reviews ) ) {
            $total_cards = min( $total_cards, intval( $max_reviews ) );
        }

        // Truncated up front when "Show more" is active and there are extra cards.
        // While truncated the "See all reviews" button stays hidden (see CSS);
        // JS removes this class once every review has been revealed.
        $truncated_class = ( $show_more_rows > 0 && $total_cards > $show_more_rows )
            ? ' grwp-truncated'
            : '';

	    $stars = $this->get_total_stars();

	    $output = sprintf( '<div id="g-review" class="%s grwp_grid %s%s"%s>', $style_type, $hide_date, $truncated_class, $show_more_attr );

		if ( $show_place_info ) {

			$this->place_title = $this->place_title === '' ? 'Lorem Ipsum Business' : $this->place_title;

			$output .= '<div class="grwp_header">';
			$output .= '<div class="grwp_header-inner">';

            $output .= sprintf( '<h3 class="grwp_business-title">%s</h3>', $this->place_title );
			$output .= sprintf(
				'<span class="grwp_total-rating">%s</span><span class="grwp_5_stars">%s</span>',
				$this->rating_formatted,
                /* translators: Out of 5 stars */
				__( 'Out of 5 stars', 'embedder-for-google-reviews' )
			);
			$output .= $stars;
			$output .= sprintf(
            /* translators: Overall rating out of %s Google reviews */
				'<h3 class="grwp_overall">' . __( 'Overall rating out of %s Google reviews', 'embedder-for-google-reviews' ) . '</h3>',
				$this->total_reviews
			);
            if ($show_verified) {
                $output .= sprintf(
                    /* translators: 'Verified by' badge */
                    '<div class="grwp_verified"><a href="%s" target="_blank">'.__('Verified by', 'embedder-for-google-reviews').' <img src="'.$verified_svg.'" alt="'.$txt.'" /></a></div>',
                    $url);
            }
			$output .= '</div></div>';

		}

		$output .= '<div class="grwp_body">';

		// loop through reviews
        $count = 0;
        foreach ( $this->reviews as $review ) {

            if ( $max_reviews && is_numeric( $max_reviews ) && intval($max_reviews) <= $count ) {
                break;
            }

            $star_output = $this->get_star_output($review);

            // Hide cards beyond the initial rows up front so they don't flash on load.
            // Columns are unknown server-side, so assume one column (the safe minimum):
            // never show more than intended; JS then reveals enough to fill each row.
            $card_hidden_class = ( $show_more_rows > 0 && $count >= $show_more_rows )
                ? ' grwp-card-hidden'
                : '';

            ob_start();
            $markup_file = ( $style_type === 'layout_style-10' )
                ? 'partials/grid/markup-style10.php'
                : 'partials/grid/markup.php';
            require $markup_file;
            $output .= ob_get_clean();

            $count++;

        }

        $output .= '</div>';

        $output .= $this->get_button_output();

        $output .= '</div>';

        return wp_kses( $output, $this->allowed_html );

    }
}
