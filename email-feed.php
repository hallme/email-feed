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
 * Plugin URI:        https://github.com/hallme/emailfeed
 * Description:       Creates a second feed at /emailfeed/ instead of /feed/ which is formatted to be used in emails
 * Version:           1.0.0
 * Author:            Hall Internet Marketing
 * Author URI:        http://www.hallme.com/
 * Text Domain:       emailfeed
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: https://github.com/hallme/emailfeed
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class EmailFeed {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

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
		add_action('init', array( $this, 'setup_feed' ) );
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
	public static function refresh_rewrites( ) {
		global $wp_rewrite;
		$wp_rewrite->flush_rules( false );
	}

	/**
	 * Setup the feed on Init
	 *
	 * @since    1.0.0
	 */
	public function setup_feed() {
		add_feed('emailfeed', array( $this, 'feed_foutput' ) );
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
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function process_content($str) {
		if(strpos($str, '<img') !== false) {
			preg_match_all('|(<a.*?>)?<img.*?>(</a>)?|', $str, $matches);
			if(!empty($matches[0])) {
				foreach($matches[0] as $match) {
					if(strpos($match, 'alignright') !== false) {
						$align = 'right';
						$padding = '0 0 10px 10px';
					}
					elseif(strpos($match, 'alignleft') !== false) {
						$align = 'left';
						$padding = '0 10px 10px 0';
					}
					else {
						$align = 'center';
						$padding = '0 10px 10px 10px';
					}
					$str = str_replace($match, '<table border="0" cellspacing="0" align="'.$align.'"><tr><td style="padding:'.$padding.';">'.$match.'</td></tr></table>', $str);
				}
			}
		}
		return $str;
	}

	public function feed_foutput() {
// This is taken from wp-includes/feed-rss2.php and only modified slightly
header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
$more = 1;

echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>

<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	<?php
	/**
	 * Fires at the end of the RSS root to add namespaces.
	 *
	 * @since 2.0.0
	 */
	do_action( 'rss2_ns' );
	?>
>

<channel>
	<title><?php bloginfo_rss('name'); wp_title_rss(); ?></title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss("description") ?></description>
	<lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
	<language><?php bloginfo_rss( 'language' ); ?></language>
	<?php
	$duration = 'hourly';
	/**
	 * Filter how often to update the RSS feed.
	 *
	 * @since 2.1.0
	 *
	 * @param string $duration The update period.
	 *                         Default 'hourly'. Accepts 'hourly', 'daily', 'weekly', 'monthly', 'yearly'.
	 */
	?>
	<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', $duration ); ?></sy:updatePeriod>
	<?php
	$frequency = '1';
	/**
	 * Filter the RSS update frequency.
	 *
	 * @since 2.1.0
	 *
	 * @param string $frequency An integer passed as a string representing the frequency
	 *                          of RSS updates within the update period. Default '1'.
	 */
	?>
	<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', $frequency ); ?></sy:updateFrequency>
	<?php
	/**
	 * Fires at the end of the RSS2 Feed Header.
	 *
	 * @since 2.0.0
	 */
	do_action( 'rss2_head');

	while( have_posts()) : the_post();
	?>
	<item>
		<title><?php the_title_rss() ?></title>
		<link><?php the_permalink_rss() ?></link>
		<comments><?php comments_link_feed(); ?></comments>
		<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
		<dc:creator><![CDATA[<?php the_author() ?>]]></dc:creator>
		<?php the_category_rss('rss2') ?>

		<guid isPermaLink="false"><?php the_guid(); ?></guid>
<?php if (get_option('rss_use_excerpt')) : ?>
		<description><![CDATA[<?php the_excerpt_rss(); ?>]]></description>
<?php else : ?>
		<description><![CDATA[<?php the_excerpt_rss(); ?>]]></description>
	<?php $content = self::process_content(get_the_content_feed('rss2')); ?>
	<?php if ( strlen( $content ) > 0 ) : ?>
		<content:encoded><![CDATA[<?php echo $content; ?>]]></content:encoded>
	<?php else : ?>
		<content:encoded><![CDATA[<?php the_excerpt_rss(); ?>]]></content:encoded>
	<?php endif; ?>
<?php endif; ?>
		<wfw:commentRss><?php echo esc_url( get_post_comments_feed_link(null, 'rss2') ); ?></wfw:commentRss>
		<slash:comments><?php echo get_comments_number(); ?></slash:comments>
<?php rss_enclosure(); ?>
	<?php
	/**
	 * Fires at the end of each RSS2 feed item.
	 *
	 * @since 2.0.0
	 */
	do_action( 'rss2_item' );
	?>
	</item>
	<?php endwhile; ?>
</channel>
</rss>
<?php
	}

}



add_action( 'plugins_loaded', array( 'EmailFeed', 'get_instance' ) );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 */
register_activation_hook( __FILE__, array( 'EmailFeed', 'refresh_rewrites' ) );
register_deactivation_hook( __FILE__, array( 'EmailFeed', 'refresh_rewrites' ) );
