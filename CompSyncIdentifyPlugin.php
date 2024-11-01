<?php

/**
 * Handles returnin the wordpress plugin URI of a plugin
 */
class CompSyncIdentifyPlugin {
    private $plugin = null;
    private $pluginFile = '';

    /**
     * Pass in the Name of the plugin (xxx/xxx.php) or the plugin array
     *
     * @param [String or Array] $plugin
     *
     * @return [Array] The plugin details as per https://codex.wordpress.org/Function_Reference/get_plugins
     */
    public function __construct(String $pluginFile, array $plugin = null) {
        if (!function_exists('get_plugin_data')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        if ($plugin == null) {
            $plugin = get_plugin_data($pluginFile, false);
        }
        $this->plugin = $plugin;
        $this->pluginFile = $pluginFile;
    }

    public function getPlugin() {
        return $this->plugin;
    }

    /**
     * Use the WP API call to get the latest version of the plugin
     *
     * @return void
     */
    public function getVersion() {
    }

    /**
     * Returns true if haystack starts with needle
     *
     * @param [String] $needle
     * @param [String] $haystack
     * @return void
     */
    private function startsWith(String $needle, String $haystack) {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }

    /**
     * Get the name of the plugin
     *
     * @return String The URI of the plugin
     */
    public function getPluginUri() {
        if (($slashPos = strpos($this->pluginFile, '/')) !== false) {
            return substr($this->pluginFile, 0, $slashPos);
        } elseif ($this->startsWith('http://wordpress.org/plugins/', $this->plugin['PluginURI'])) {
            return substr($this->plugin['PluginURI'], strlen('http://wordpress.org/plugins/'), -1);
        } elseif (!empty($this->plugin['TextDomain'])) {
            return $this->plugin['TextDomain'];
        }
        return sanitize_title(strtolower($this->plugin['Name']));
    }
}
