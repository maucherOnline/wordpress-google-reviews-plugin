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
 * Version:           1.4.7
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


// Important check to prevent conflicts between free and pro versions upon activation
if ( ! function_exists('grwp_fs') ) {

    function startup_fs()
    {
        // Create a helper function for easy SDK access.

        function grwp_fs()
        {
            global $grwp_fs;

            if (!isset($grwp_fs)) {
                // Include Freemius SDK.
                require_once dirname(__FILE__) . '/freemius/start.php';

                $grwp_fs = fs_dynamic_init(array(
                    'id' => '10211',
                    'slug' => 'embedder-for-google-reviews',
                    'premium_slug' => 'embedder-for-google-reviews-pro',
                    'type' => 'plugin',
                    'public_key' => 'pk_6823179f29a329a909c59a7a25a0a',
                    'is_premium' => false,
                    'premium_suffix' => 'Premium',
                    // If your plugin is a serviceware, set this option to false.
                    'has_premium_version' => true,
                    'has_addons' => false,
                    'has_paid_plans' => true,
                    'trial' => array(
                        'days' => 4,
                        'is_require_payment' => true,
                    ),
                    'has_affiliation' => 'all',
                    'menu' => array(
                        'slug' => 'google-reviews',
                        'first-path' => 'admin.php?page=google-reviews',
                        'contact' => false,
                        'support' => false,
                    ),
                    // Set the SDK to work in a sandbox mode (for development & testing).
                    // IMPORTANT: MAKE SURE TO REMOVE SECRET KEY BEFORE DEPLOYMENT.
                    'secret_key' => 'sk_kHyh8$^{Y;t{NIraPW)d6eNQ]S[9O',
                ));
            }


            return $grwp_fs;
        }

        // Init Freemius.
        grwp_fs();
        // Signal that SDK was initiated.
        do_action('grwp_fs_loaded');
    }

}

if ( function_exists( 'grwp_fs' ) ) {
    grwp_fs()->set_basename( true, __FILE__ );
}
else {

    /**
     * No code must be present outside of this block.
     * Else, pro plugin activation will throw an error while free version is activated
     */

    // start freemius sdk
    startup_fs();

    define( 'GRWP_GOOGLE_REVIEWS_VERSION', '1.4.7' );

    // Base path to plugin for includes
    define('GR_BASE_PATH', plugin_dir_path( __FILE__ ) );
    define('GR_BASE_PATH_ADMIN', plugin_dir_path( __FILE__ ) .'admin/' );
    define('GR_BASE_PATH_PUBLIC', plugin_dir_path( __FILE__ ) .'public/' );

    // Register class autoloader
    spl_autoload_register(function ($class) {
        $className = strtolower(str_replace('_', '-', $class));
        $adminfile = GR_BASE_PATH_ADMIN.'includes/class-'.$className.'.php';
        if (is_readable($adminfile)) {
            require_once $adminfile;
        }

        $publicfile = GR_BASE_PATH_PUBLIC.'includes/class-'.$className.'.php';
        if (is_readable($publicfile)) {
            require_once $publicfile;
        }
    });

    /**
     * The code that runs during plugin activation.
     */
    function grwp_activate_google_reviews() {
        require_once GR_BASE_PATH_ADMIN . 'includes/class-google-reviews-activator.php';
        GRWP_Google_Reviews_Activator::activate();
    }

    /**
     * The code that runs during plugin deactivation.
     */
    function grwp_deactivate_google_reviews() {
        require_once GR_BASE_PATH_ADMIN . 'includes/class-google-reviews-deactivator.php';
        GRWP_Google_Reviews_Deactivator::deactivate();
    }

    /**
     * The code that runs during plugin deletion.
     */
    function grwp_uninstall_google_reviews() {
        require_once GR_BASE_PATH_ADMIN . 'includes/class-google-reviews-uninstaller.php';
        GRWP_Google_Reviews_Uninstaller::uninstall();
    }

    // Register hooks
    register_activation_hook( __FILE__, 'grwp_activate_google_reviews' );
    register_deactivation_hook( __FILE__, 'grwp_deactivate_google_reviews' );
    register_uninstall_hook( __FILE__, 'grwp_uninstall_google_reviews' );

    $plugin = new GRWP_Google_Reviews_Startup();
    $plugin->run();

    // temporary from v1.4.5: remove old wp cron for free users
    if ( ! grwp_fs()->is__premium_only() ) {
        if ( wp_next_scheduled('get_google_reviews') ) {
            wp_clear_scheduled_hook( 'get_google_reviews' );
        }
    }

}
