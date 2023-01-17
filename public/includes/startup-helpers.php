<?php


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
    require_once GR_BASE_PATH_ADMIN . 'hooks/includes/class-google-reviews-activator.php';
    GRWP_Google_Reviews_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-google-reviews-deactivator.php
 */
function grwp_deactivate_google_reviews() {
    require_once GR_BASE_PATH_ADMIN . 'hooks/includes/class-google-reviews-deactivator.php';
    GRWP_Google_Reviews_Deactivator::deactivate();
}

/**
 * The code that runs during plugin deletion.
 * This action is documented in includes/class-google-reviews-uninstaller.php
 */
function grwp_uninstall_google_reviews() {
    require_once GR_BASE_PATH_ADMIN . 'hooks/includes/class-google-reviews-uninstaller.php';
    GRWP_Google_Reviews_Uninstaller::uninstall();
}

register_activation_hook( __FILE__, 'grwp_activate_google_reviews' );
register_deactivation_hook( __FILE__, 'grwp_deactivate_google_reviews' );
register_uninstall_hook( __FILE__, 'grwp_uninstall_google_reviews' );

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
