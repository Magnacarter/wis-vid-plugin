<?php
/**
 * The plugin bootstrap file
 *
 * @since             1.0.0
 * @package           WisVid/plugin
 * @wordpress-plugin
 * Plugin Name:       Wis-Vid
 * Plugin URI:        
 * Version:           1.0.0
 * Author:            Adam Carter
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wis-vid
 * Domain Path:       /languages
 */
namespace WisVid\plugin;
use WisVid\plugin\classes\Display_Vid;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Cheatin&#8217?' );
}

$plugin_url = plugin_dir_url( __FILE__ );
define( 'WISVID_URL', $plugin_url );
define( 'WISVID_DIR', plugin_dir_path( __DIR__ ) );
define( 'WISVID_VER', '1.0.0' );

/**
 * Class Init_Plugin
 */
class Init_Plugin {

	/**
	 * @var $instance
	 */
	private static $instance;

	/**
	 * Construct function
	 *
	 * @return void
	 */
	public function __construct() {
		// Load public scripts and styles
		add_action( 'wp_enqueue_scripts', array( $this, 'public_scripts' ) );
		$this->ajax_login_init();

		register_activation_hook( __FILE__, array( $this, 'activate_plugin' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate_plugin' ) );
		register_uninstall_hook( __FILE__, array( $this, 'uninstall_plugin' ) );

		self::init_autoloader();

		new Display_Vid();
	}

	/**
	 * Enqueue scripts and styles
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function public_scripts() {
		wp_enqueue_style(  'wisvid_style',  WISVID_URL . 'css/wis-styles.css', WISVID_VER );
		wp_enqueue_script( 'wisvid_script', WISVID_URL . 'js/wisvid.js', array( 'jquery' ), WISVID_VER, false );
	}

	public function ajax_login_init() {
		wp_localize_script( 'wisvid_script', 'ajax_login_object', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'redirecturl' => home_url(),
			'loadingmessage' => __('Sending user info, please wait...')
		));
	}

	/**
	 * Plugin activation handler
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function activate_plugin() {
		self::init_autoloader();
		flush_rewrite_rules();
	}

	/**
	 * The plugin is deactivating.  Delete out the rewrite rules option.
	 *
	 * @since 1.0.1
	 *
	 * @return void
	 */
	public function deactivate_plugin() {
		delete_option( 'rewrite_rules' );
	}

	/**
	 * Uninstall plugin handler
	 *
	 * @since 1.0.1
	 *
	 * @return void
	 */
	public function uninstall_plugin() {
		delete_option( 'rewrite_rules' );
	}

	/**
	 * Kick off the plugin by initializing the plugin files.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function init_autoloader() {
		require_once 'classes/class-display-vid.php';
	}

	/**
	 * Return active instance of Init_Plugin, create one if it doesn't exist
	 *
	 * @return object $instance
	 */
	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			$class = __CLASS__;
			self::$instance = new $class;
		}
		return self::$instance;
	}
}
Init_Plugin::get_instance();
