This repository is fork of original simply-static-callback plugin available at WordPress Plugin directory.Orignal version was downloaded from https://downloads.wordpress.org/plugin/simply-static-callback.1.0.2.zip for further improvements and bug fixes for seamless integration with Netlify.

We have renamed it to ``simply-static-netlify-callback`` to distinguish it as a fork. Please check this project website for detailed tutorial. 


=== Simply Static Callback ===
Contributors: zearg
link: https://www.yeswehack.com
Tags: simply static, callback
Requires at least: 5.4
Tested up to: 5.9
Requires PHP: 7.4
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

WordPress plugin that works with Simply Static plugin (required).
It sends a callback to URI when Simply Static plugin generation is over

== Installation ==

1. Download, install and active Simply Static (simply-static) plugin.
2. Upload `simply-static-callback` to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Go to Simply Static > Settings. A "Callback" settings tab is visible.

== Screenshots ==

1. Settings

== Changelog ==

= 1.0.2 =
* Fix infinite loop on call error

= 1.0.1 =
* Fix callback enabled option

= 1.0.0 =
* Add callback options
* Add callback task

== Upgrade Notice ==

Nothing relevant

== Frequently Asked Questions ==

= What does Simply Static Callback do? =

Simply Static Callback will make an API call to endpoint of your choice at the end of Simply Static generation.

You can choose to use HTTP headers to protect your server from being called by any user.

You can choose wich information should be added in the body of the API call (name of the generated ZIP or name of the generated folder).

= Why I need Simply Static plugin too? =

Simply Static Callback works with Simply Static, it adds more feature with more options to the base plugin. It cannot be used alone.

= Why plugin does not activate? =

You should activate Simply Static, before trying activate Simply Static Callback.

== Author word ==

Developed by Arthur Bouchard (www.arthurbouchard.com) for YesWeHack (www.yeswehack.com)
2022
