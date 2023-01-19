<?php

/**
 * The code that runs during plugin activation.
 */
function grwp_activate_google_reviews() {
    require_once GR_BASE_PATH_ADMIN . 'hooks/includes/class-google-reviews-activator.php';
    GRWP_Google_Reviews_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function grwp_deactivate_google_reviews() {
    require_once GR_BASE_PATH_ADMIN . 'hooks/includes/class-google-reviews-deactivator.php';
    GRWP_Google_Reviews_Deactivator::deactivate();
}

/**
 * The code that runs during plugin deletion.
 */
function grwp_uninstall_google_reviews() {
    require_once GR_BASE_PATH_ADMIN . 'hooks/includes/class-google-reviews-uninstaller.php';
    GRWP_Google_Reviews_Uninstaller::uninstall();
}

// Register hooks
register_activation_hook( __FILE__, 'grwp_activate_google_reviews' );
register_deactivation_hook( __FILE__, 'grwp_deactivate_google_reviews' );
register_uninstall_hook( __FILE__, 'grwp_uninstall_google_reviews' );

// Start plugin
require_once GR_BASE_PATH_PUBLIC . 'includes/class-google-reviews-loader.php';
$plugin = new GRWP_Google_Reviews_Startup();
$plugin->run();
