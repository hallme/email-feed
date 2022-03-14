=== Email Feed ===
Contributors: hallme
Tags: rss, emails
Requires at least: 3.0
Tested up to: 5.9.2
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Creates a second feed at /emailfeed/ instead of /feed/ which is formatted to be used in emails

== Description ==

This Plugin creates an additial RSS feed at /emailfeed/ instead of /feed/ which is formatted to be used in emails

This feed formats images properly for sending out in emails through various rss2email services.

== Installation ==

1. Install the plugin using the WordPress built in plugin installer or by uploading the folder `emailfeed` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= How do I access my email feed? =

Find your current RSS feed. This is usually something like /feed/ or /blog/feed/ and simply replace feed with emailfeed


== Changelog ==

= 1.1.0 =
* Change to use WordPress RSS file as an include so it stays up to date with the current WordPress version.

= 1.0.1 =
* Fix bug with the regex if the image is followed by an a tag instead of wrapped in an a tag

= 1.0 =
* Initial Release
