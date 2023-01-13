<?php

/**
 * @author            PARETO Digital
 * @copyright         2022 PARETO Digital GmbH & Co. KG
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Embedder for Google Reviews
 * Plugin URI:        https://paretodigital.io
 * Description:       This Google Reviews Plugin pulls reviews from Google profiles and displays them on your website.
 * Version:           1.3.5
 * Requires at least: 5.4
 * Requires PHP:      7.4
 * Tested up to:      6.1.1
 * Author:            ReviewsEmbedder.com
 * Author URI:        https://reviewsembedder.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       embedder-for-google-reviews
 * Domain Path:       /languages
 */

if ( function_exists( 'grwp_fs' ) ) {
    grwp_fs()->set_basename( true, __FILE__ );
}
else {
    // Create a helper function for easy SDK access.
    function grwp_fs() {
        global $grwp_fs;

        if ( ! isset( $grwp_fs ) ) {
            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/freemius/start.php';

            $grwp_fs = fs_dynamic_init( array(
                'id'                  => '10211',
                'slug'                => 'embedder-for-google-reviews',
                'premium_slug'        => 'embedder-for-google-reviews-pro',
                'type'                => 'plugin',
                'public_key'          => 'pk_6823179f29a329a909c59a7a25a0a',
                'is_premium'          => false,
                'premium_suffix'      => 'Premium',
                // If your plugin is a serviceware, set this option to false.
                'has_premium_version' => true,
                'has_addons'          => false,
                'has_paid_plans'      => true,
                'trial'               => array(
                    'days'               => 4,
                    'is_require_payment' => true,
                ),
                'menu'                => array(
                    'slug'           => 'google-reviews',
                    'first-path'     => 'admin.php?page=google-reviews',
                    'contact'        => false,
                    'support'        => false,
                ),
                // Set the SDK to work in a sandbox mode (for development & testing).
                // IMPORTANT: MAKE SURE TO REMOVE SECRET KEY BEFORE DEPLOYMENT.
                'secret_key'          => 'sk_n1k[Rs%2sq?HC_3k23p=Gw88*{gMm',
            ) );
        }

        return $grwp_fs;
    }

    // Init Freemius.
    grwp_fs();
    // Signal that SDK was initiated.
    do_action( 'grwp_fs_loaded' );


    require plugin_dir_path( __FILE__ ) . 'includes/class-google-reviews.php';
    require plugin_dir_path( __FILE__ ) . 'includes/startup-helpers.php';
    require plugin_dir_path( __FILE__ ) . 'includes/rest-endpoints.php';

}
