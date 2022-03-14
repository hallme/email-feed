<?php

/**
 * Email Feed
 *
 * Creates a second feed at /emailfeed/ instead of /feed/ which is formatted to be used in emails
 *
 * @package   Email Feed
 * @author    Hall Internet Marketing <it@hallme.com>
 * @license   GPL-2.0+
 * @link      http://www.hallme.com/
 * @copyright 2014 Hall Internet Marketing
 *
 * @wordpress-plugin
 * Plugin Name:       Email Feed
 * Plugin URI:        https://github.com/hallme/email-feed
 * Description:       Creates a second feed at /emailfeed/ instead of /feed/ which is formatted to be used in emails
 * Version:           1.1.0
 * Author:            Hall Internet Marketing
 * Author URI:        https://www.hallme.com/
 * Text Domain:       emailfeed
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: https://github.com/hallme/email-feed
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class EmailFeed {

	/**
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'emailfeed';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by running setup on
	 *
	 * @since     1.0.0
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'setup_feed' ) );
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Calls flush_rules to clear the rule cache
	 *
	 * @since    1.0.0
	 */
	public static function refresh_rewrites() {
		global $wp_rewrite;
		$wp_rewrite->flush_rules( false );
	}

	/**
	 * Setup the feed on Init
	 *
	 * @since    1.0.0
	 */
	public function setup_feed() {
		add_feed( 'emailfeed', array( $this, 'feed_foutput' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function process_content( $str ) {
		if ( strpos( $str, '<img' ) !== false ) {
			preg_match_all( '|(<a.*?>)?<img.*?>(</a>)?|', $str, $matches );
			if ( ! empty( $matches[0] ) ) {
				foreach ( $matches[0] as $match ) {
					if ( strpos( $match, 'alignright' ) !== false ) {
						$align   = 'right';
						$padding = '0 0 10px 10px';
					} elseif ( strpos( $match, 'alignleft' ) !== false ) {
						$align   = 'left';
						$padding = '0 10px 10px 0';
					} else {
						$align   = 'center';
						$padding = '0 10px 10px 10px';
					}
					$str = str_replace( $match, '<table border="0" cellspacing="0" align="' . $align . '"><tr><td style="padding:' . $padding . ';">' . $match . '</td></tr></table>', $str );
				}
			}
		}
		return $str;
	}

	public function feed_foutput() {
		add_filter( 'the_content_feed', array( $this, 'process_content' ) );
		require WPINC . '/feed-rss2.php';
	}
}



add_action( 'plugins_loaded', array( 'EmailFeed', 'get_instance' ) );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 */
register_activation_hook( __FILE__, array( 'EmailFeed', 'refresh_rewrites' ) );
register_deactivation_hook( __FILE__, array( 'EmailFeed', 'refresh_rewrites' ) );
