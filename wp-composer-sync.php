<?php
/*
Plugin Name: WP Composer Sync
Plugin URI: http://wordpress.org/#
Description: Automatically keeps your composer.json file in sync with plugins installed via wp-admin.
Author: Craig Harman
Version: 1.0
Author URI: http://harmoinc.com.au/
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

include plugin_dir_path(__FILE__) . 'CompSyncIdentifyPlugin.php';

register_activation_hook(__FILE__, 'compsync_bulk_update_composer');
//add_action('upgrader_process_complete', 'flag_composer_as_dirty', 10, 2); // Core or plugin has been updated
add_action('plugins_loaded', 'compsync_bulk_update_composer');

$compSyncComposerFileLocation = ABSPATH . 'composer.json';
$compSyncPluginComposerRoot = 'wordpress-plugin/';
$compSyncComposer = null;
compsync_load_composer();

/**
 * Load the composer file into memory
 *
 * @return void
 */
function compsync_load_composer() {
    global $compSyncComposer,$compSyncComposerFileLocation;

    if (!is_file($compSyncComposerFileLocation)) { // Find the composer file either in web root or one below
        $compSyncComposerFileLocation = ABSPATH . '../composer.json';
    }
    $string = file_get_contents($compSyncComposerFileLocation);

    //TODO: If can't find file create a composer file with the latest version of wordpress required

    $compSyncComposer = json_decode($string, false);
    //TODO: If the above fails warn the user that the composer.json is probably invalid
}

/**
 * Update all plugins that are installed
 *
 * @return void
 */
function compsync_bulk_update_composer() {
    global $wp_version, $compSyncComposer, $compSyncPluginComposerRoot;

    // Check if get_plugins() function exists. This is required on the front end of the
    // site, since it is in a file that is normally only loaded in the admin.
    if (!function_exists('get_plugins')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $allPlugins = get_plugins();

    $compSyncComposerPlugins = $compSyncComposer->require;
    $newComposerRequire = ['wordpress/wordpress' => '^' . $wp_version];
    $pluginChanged = false;

    foreach ($allPlugins as $key => $plugin) {
        // $pluginDetails = shellexec('cd ' . $compSyncComposerFileLocation . ' && composer show -a wordpress-plugin/' . $pluginName);
        // echo $pluginDetails . '<br />';
        $ip = new CompSyncIdentifyPlugin($key, $plugin);
        $pluginDetails = $ip->getPlugin();
        $pluginURI = $ip->getPluginUri();
        $compSyncComposerSlug = $compSyncPluginComposerRoot . $pluginURI;
        $found = false;

        foreach ($compSyncComposerPlugins as $key => $value) {
            if (strpos($key, '/' . $pluginURI) !== false) {
                $found = true;
                $newComposerRequire[$key] = '^' . $pluginDetails['Version'];
                if ($compSyncComposerPlugins->{$key} != $pluginDetails['Version']) {
                    $pluginChanged = true;
                }
            }
        }

        if (!$found) {
            $newComposerRequire[$compSyncComposerSlug] = '^' . $pluginDetails['Version'];
            $pluginChanged = true;
        }
    }

    if ($pluginChanged) {
        compsync_generate_composer($newComposerRequire);
    }
}

/**
 * GIven an array of plugins, add them to composer.json
 *
 * @param Array $plugins
 * @return void
 */
function compsync_generate_composer($plugins) {
    global $compSyncComposer,$compSyncComposerFileLocation;

    $compSyncComposer->require = $plugins;
    $jsonComposer = json_encode($compSyncComposer, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

    file_put_contents($compSyncComposerFileLocation, $jsonComposer);
}
