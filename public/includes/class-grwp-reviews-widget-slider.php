<?php

class GRWP_Reviews_Widget_Slider
    extends
    GRWP_Google_Reviews_Output {

    /**
     * Slider HTML
     * @return string
     */
    public function render( $style_type ) {

        // error handling
        if ( $this->reviews_have_error ) {

            return __( 'No reviews available', 'google-reviews' );

        }

        $google_svg =  GR_PLUGIN_DIR_URL . 'dist/images/google-logo-svg.svg';

        // loop through reviews
        $output = sprintf('<div id="g-review" class="%s">', $style_type);
        $slider_output = '';

        foreach ( $this->reviews as $review ) {

            $star_output = $this->get_star_output($review);

            $slide_duration = $this->options['slide_duration'] ?? '';

            ob_start();
            require 'partials/slider/markup.php';
            $slider_output .= ob_get_clean();


        }

        ob_start();
        require 'partials/slider/slider-header.php';
        echo wp_kses( $slider_output, $this->allowed_html );
        require 'partials/slider/slider-footer.php';

        $output .= ob_get_clean();

        $output .= '</div>';

        return wp_kses( $output, $this->allowed_html );

    }

}
