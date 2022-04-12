<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              test
 * @since             1.0.0
 * @package           Google_Reviews
 *
 * @wordpress-plugin
 * Plugin Name:       Google Reviews
 * Plugin URI:        test
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            David Maucher
 * Author URI:        test
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       google-reviews
 * Domain Path:       /languages
 */

if ( ! function_exists( 'grw_fs' ) ) {
    // Create a helper function for easy SDK access.
    function grw_fs() {
        global $grw_fs;

        if ( ! isset( $grw_fs ) ) {
            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/freemius/start.php';

            $grw_fs = fs_dynamic_init( array(
                'id'                  => '10211',
                'slug'                => 'google-reviews-widget',
                'type'                => 'plugin',
                'public_key'          => 'pk_6823179f29a329a909c59a7a25a0a',
                'is_premium'          => true,
                'premium_suffix'      => 'Professional',
                // If your plugin is a serviceware, set this option to false.
                'has_premium_version' => true,
                'has_addons'          => false,
                'has_paid_plans'      => true,
                'trial'               => array(
                    'days'               => 7,
                    'is_require_payment' => true,
                ),
                'menu'                => array(
                    'slug'           => 'google-reviews',
                    'first-path'     => 'admin.php?page=google-reviews',
                    'support'        => false,
                ),
                // Set the SDK to work in a sandbox mode (for development & testing).
                // IMPORTANT: MAKE SURE TO REMOVE SECRET KEY BEFORE DEPLOYMENT.
                'secret_key'          => 'sk_n1k[Rs%2sq?HC_3k23p=Gw88*{gMm',
            ) );
        }

        return $grw_fs;
    }

    // Init Freemius.
    grw_fs();
    // Signal that SDK was initiated.
    do_action( 'grw_fs_loaded' );
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'GOOGLE_REVIEWS_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-google-reviews-activator.php
 */
function activate_google_reviews() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-google-reviews-activator.php';
	Google_Reviews_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-google-reviews-deactivator.php
 */
function deactivate_google_reviews() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-google-reviews-deactivator.php';
	Google_Reviews_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_google_reviews' );
register_deactivation_hook( __FILE__, 'deactivate_google_reviews' );

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
function run_google_reviews() {

	$plugin = new Google_Reviews();
	$plugin->run();

}
run_google_reviews();
