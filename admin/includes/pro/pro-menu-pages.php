<?php

Class Pro_Menu_Pages {

    public function __construct() {
        $this->add_menu_pages();
    }

    private function add_menu_pages() {

        add_submenu_page(
            'google-reviews',
            'How to',
            'How to',
            'manage_options',
            'how-to-premium-version',
            array($this, 'google_reviews_create_sub_page_how_to_premium')
        );

    }

    /**
     * Backend how to subpage for premium version
     * @return void
     */
    public function google_reviews_create_sub_page_how_to_premium() {
        global $allowed_html;
        global $imgpath;

        $imgpath = plugin_dir_url(__FILE__) .'img/';

        echo wp_kses('<div class="wrap">', $allowed_html);
        require_once plugin_dir_path(__FILE__) .'/includes/how-to-premium.php';
        echo wp_kses('</div>', $allowed_html);
    }
}
