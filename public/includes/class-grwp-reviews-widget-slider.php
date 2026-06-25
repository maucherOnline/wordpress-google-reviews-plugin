<?php

class GRWP_Reviews_Widget_Slider
    extends
    GRWP_Google_Reviews_Output {

    /**
     * Slider HTML
     * @return string
     */
    public function render( $style_type, $link_user_profiles, $max_reviews = null, $show_place_info = false, $txt = '' ) {

        // error handling
        if ( $this->reviews_have_error ) {
            /* translators: no reviews available */
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

        $hide_slider_arrows = false;
        if ( isset($this->options['hide_slider_arrows']) ) {
            if ( $this->options['hide_slider_arrows'] === '1' ) {
                $hide_slider_arrows = true;
            }
        }

        // Global setting: hide company header overrides shortcode place_info attribute
        if ( ! empty( $this->options['hide_company_header'] ) ) {
            $show_place_info = false;
        }

	    $google_svg = GR_PLUGIN_DIR_URL . 'dist/images/google-logo-svg.svg';

		$output = '';

		// When the compact header is shown it carries the top spacing, so the
		// slider body/track top offsets are removed (see header-compact.scss).
		$compact_header_class = $this->compact_header_active( $show_place_info ) ? ' grwp-has-compact-header' : '';

	    $output = sprintf( '<div id="g-review" class="%s grwp_grid %s%s">', $style_type, $hide_date, $compact_header_class );

		$output .= $this->render_company_header( $show_place_info, $show_verified, $txt, 'Lorem Ipsum Business Title', true );

	    $output .= '<div class="grwp_body">';

        // loop through reviews
        $output .= sprintf('<div id="g-review" class="%s">', $style_type);
        $slider_output = '';

        $count = 0;
        foreach ( $this->reviews as $review ) {

            if ( $max_reviews && is_numeric( $max_reviews ) && intval($max_reviews) <= $count ) {
                break;
            }

            $star_output = $this->get_star_output($review);

            $slide_duration = isset($this->options['slide_duration']) ? intval($this->options['slide_duration']) * 1000 : '';

            ob_start();
            $markup_file = ( $style_type === 'layout_style-10' )
                ? 'partials/slider/markup-style10.php'
                : 'partials/slider/markup.php';
            require $markup_file;
            $slider_output .= ob_get_clean();

            $count++;

        }

        ob_start();
        require 'partials/slider/slider-header.php';
        echo wp_kses( $slider_output, $this->allowed_html );
        require 'partials/slider/slider-footer.php';

        $output .= ob_get_clean();

        $output .= '</div></div>';

        // The compact header carries its own "See all reviews" button, so skip
        // the standalone one below the widget to avoid a duplicate.
        if ( ! $this->compact_header_active( $show_place_info ) ) {
            $output .= $this->get_button_output();
        }

        $output .= '</div>';

        return wp_kses( $output, $this->allowed_html );

    }

}
