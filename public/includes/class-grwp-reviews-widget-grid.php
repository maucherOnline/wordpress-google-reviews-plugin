<?php

class GRWP_Reviews_Widget_Grid
    extends
    GRWP_Google_Reviews_Output {

    public function render( $style_type ) {

        // error handling
        if ( $this->reviews_have_error ) {

            return __( 'No reviews available', 'grwp' );

        }

        $google_svg = GR_PLUGIN_DIR_URL . 'dist/images/google-logo-svg.svg';

        // loop through reviews
        $output = sprintf('<div id="g-review" class="%s">', $style_type);
        $slider_output = '';

        foreach ( $this->reviews as $review ) {

            $star_output = $this->get_star_output($review);

            ob_start();
            require 'partials/grid/markup.php';
            $output .= ob_get_clean();

        }

        $output .= '</div>';

        return wp_kses( $output, $this->allowed_html );

    }
}