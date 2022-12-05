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
 * Version:           1.1.0
 * Requires at least: 5.4
 * Requires PHP:      7.4
 * Tested up to:      6.0
 * Author:            ParetoDigital.de
 * Author URI:        https://paretodigital.de
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       embedder-for-google-reviews
 * Domain Path:       /languages
 */

if ( ! function_exists( 'grwp_fs' ) ) {
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
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'GRWP_GOOGLE_REVIEWS_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-google-reviews-activator.php
 */
function grwp_activate_google_reviews() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-google-reviews-activator.php';
	GRWP_Google_Reviews_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-google-reviews-deactivator.php
 */
function grwp_deactivate_google_reviews() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-google-reviews-deactivator.php';
	GRWP_Google_Reviews_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'grwp_activate_google_reviews' );
register_deactivation_hook( __FILE__, 'grwp_deactivate_google_reviews' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-google-reviews.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function grwp_run_google_reviews() {

	$plugin = new GRWP_Google_Reviews();
	$plugin->run();

}
grwp_run_google_reviews();
