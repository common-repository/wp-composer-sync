=== WP Composer Sync ===
Contributors: harmonicnewmedia
Tags: composer
Requires at least: 4.6
Tested up to: 4.7
Stable tag: 1.0
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

WP Composer Sync is a Wordpress plugin that keeps a Wordpress installation's composer.json synchronised with the plugins installed.

== Description ==

== Installation ==

Regardsless of installation method the machine where you are running Wordpress requires Subversion to be installed to allow access to Wordpress's SVN repositories. On *NIX based servers you can simply:

```
apt -yq install subversion
```

### New Wordpress Install

You can use composer to create your new Wordpress instance and include WP Composer Sync. In the root of your project folder simply create a **composer.json** file containing:

```
{
    "name": "The name of your WordPress site",
    "extra": {
        "composer-wp": {
            "repositories": [],
            "vendors": {},
            "installer": {
                "wordpress-path": "web"
            }
        }
    },
    "require": {
        "wordpress/wordpress": "^4.9.7",
        "wordpress-plugin/wp-composer-sync": "*"
    },
    "scripts": {
        "pre-cmd": [
            "composer global require balbuf/composer-wp && composer global update balbuf/composer-wp"
        ],
        "pre-install-cmd": "@pre-cmd",
        "pre-update-cmd": "@pre-cmd"
    }
}
```

This will install wordpress in a web/ sub-directory (recommended, but you can change the web root by modifying the "wordpress-path"). It will also globally install [link](https://github.com/balbuf/composer-wp  "Balbuf's Composer-WP"). 

### Existing Wordpress Install

If you wish to install WP Composer Sync into an existing (composer managed) Wordpress install start by installing [link](https://github.com/balbuf/composer-wp  "Balbuf's Composer-WP"):

```
$ composer global require balbuf/composer-wp
```

Now you need to add WP Composer Sync to your Wordpress install either via wp-admin or via composer:

```
$ composer global require wordpress/wp-composer-sync
```

## Usage

Once installed the plugin should be largely set and forget, however there a couple of gotchas.

1) Commercial plugins that are not available from the Wordpress.org plugins repository **will still be added** you will need to manually modify the repository (eg. wpackagist, wp-premium or using a custom installer or repository) or add your custom plugins first. WP Composer Sync will then honor these repositories and keep them synchronised as well.
2) When composer installs a package, it completely empties the target directory before installing the new files. As such, the WordPress path should be designated for WordPress core files only, as anything else (e.g. plugins, themes, and wp-config.php) will be wiped away on install or update. It is recommended that you *keep your wp-config.php file in the parent directory of the WordPress path* (WordPress can find it there automatically) and *replace the wp-content directory with a symlink* to your real wp-content folder. See  [link](https://github.com/balbuf/composer-wp  "Balbuf's Composer-WP") for more details.
3) Compposer require versioning wildcards (eg. *, 1.*, etc) will not be honoured (as Wordpress does not adhere to this). They will be replaced by the exact version number in use by Wordpress.

## About

### Why?
Composer is a great way to manage Wordpress and its plugins. However in real life it runs the risk of becoming outdated with the actual contents of the website when admin users are installing plugins via wp-admin or S/FTP.

### How?
Using Wordpress's "plugins_loaded" action and [link](https://github.com/balbuf/composer-wp  "Balbuf's Composer-WP"), this plugin will check your composer.json file for any outdated or unfound plugins and will automatically add them to the require section.

#### Roadmap

The plugin is production ready however there are some additional functions and features which will be added in the future:

1) Add ability to select bewteen https://github.com/balbuf/composer-wp and wpackagist
2) Currently only syncs plugin and wordpress core, add ability to sync themes
3) Research some ways to monitor plugin-uploads and changes more effectively (eg. [link](https://codex.wordpress.org/Plugin_API/Action_Reference/upgrader_process_complete "Upgrader_Process_complete") [link](https://github.com/WordPress/WordPress/blob/master/wp-admin/includes/class-plugin-upgrader.php "2"),  [link](https://www.sitepoint.com/wordpress-plugin-updates-right-way/ "Wordpress Updates") and [link](https://codex.wordpress.org/Creating_Tables_with_Plugins#Adding_an_Upgrade_Function "Adding an upgrade function") or Transients [link](https://code.tutsplus.com/tutorials/a-guide-to-the-wordpress-http-api-automatic-plugin-updates--wp-25181 "1") [link](https://stackoverflow.com/questions/32196219/my-plugin-not-updating-properly-issue-with-upgrader-process-complete/50914655#50914655 "2") or [link](https://wordpress.stackexchange.com/questions/123732/get-latest-plugin-version-from-wp-api "Plugins API"))

== Changelog ==

= 1.0 =
* Initial version