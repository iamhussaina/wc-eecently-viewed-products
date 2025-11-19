<?php
/**
 * Loader for Hussainas WC History Tracker.
 * * This file includes the necessary class and initializes the instance.
 * It ensures the logic is decoupled from the theme's functions.php.
 *
 * @version     1.0.0
 * @author      Hussain Ahmed Shrabon
 * @license     GPL-2.0-or-later
 * @link        https://github.com/iamhussaina
 * @textdomain  hussainas
 
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define the path to the class file.
$hussainas_class_path = __DIR__ . '/src/class-hussainas-history-tracker.php';

if ( file_exists( $hussainas_class_path ) ) {
    require_once $hussainas_class_path;

    // Instantiate the class.
    $hussainas_tracker = new Hussainas_History_Tracker();
    $hussainas_tracker->init();
}
