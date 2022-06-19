<?php

/**
 *
 * The plugin bootstrap file
 *
 * This file is responsible for starting the plugin using the main plugin class file.
 *
 * @since 0.0.1
 * @package Natancordova_plugin
 *
 * @wordpress-plugin
 * Plugin Name:     Natan Cordova's Plugin
 * Description:     This plugin has the following features: *Add bibliographic citations to each publication and use it through a shortcode. *Check link status in post content.
 * Version:         0.0.1
 * Author:          Natan Cordova Ortíz
 * Author URI:      https://www.linkedin.com/in/natecordova/
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     natancordova-plugin
 * Domain Path:     /lang
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access not permitted.' );
}

if ( ! class_exists( 'natancordova_plugin' ) ) {

	/*
	 * main natancordova_plugin class
	 *
	 * @class natancordova_plugin
	 * @since 0.0.1
	 */
	class natancordova_plugin {

		/*
		 * natancordova_plugin plugin version
		 *
		 * @var string
		 */
		public $version = '4.7.5';

		/**
		 * The single instance of the class.
		 *
		 * @var natancordova_plugin
		 * @since 0.0.1
		 */
		protected static $instance = null;

		/**
		 * Main natancordova_plugin instance.
		 *
		 * @since 0.0.1
		 * @static
		 * @return natancordova_plugin - main instance.
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * natancordova_plugin class constructor.
		 */
		public function __construct() {
			$this->load_plugin_textdomain();
			$this->define_constants();
			$this->includes();
			$this->define_actions();
			$this->define_menus();

		}

		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'natancordova_plugin', false, basename( dirname( __FILE__ ) ) . '/lang/' );
		}

		/**
		 * Include required core files
		 */
		public function includes() {
           
			// Load custom functions and hooks
			require_once __DIR__ . '/includes/includes.php';
		}

		/**
		 * Get the plugin path.
		 *
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}


		/**
		 * Define natancordova_plugin constants
		 */
		private function define_constants() {
			define( 'NATANCORDOVA_PLUGIN_PLUGIN_FILE', __FILE__ );
			define( 'NATANCORDOVA_PLUGIN_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			define( 'NATANCORDOVA_PLUGIN_VERSION', $this->version );
			define( 'NATANCORDOVA_PLUGIN_PATH', $this->plugin_path() );
		}

		/**
		 * Define natancordova_plugin actions
		 */
		public function define_actions() {
			
			add_action( 'init', 'citation_register_meta_fields' );
			add_action( 'add_meta_boxes', 'citation_meta_boxes' );
			add_action( 'save_post', 'save_citation_post' );
			add_action( 'init', 'shortcodes_init' );
			add_action( 'nc_process_verify_links', 'nc_verify_links' );
			add_filter( 'cron_schedules', 'custom_cron_job_recurrence' );
			add_action( 'admin_init', 'custom_cron_job' );
			add_action( 'admin_menu', 'verify_links_menu' );
			
		}

		/**
		 * Define natancordova_plugin menus
		 */
		public function define_menus() {
         

			// Define menu_page and content

			function verify_links_menu(){
				add_menu_page(
					'Verify Links - Natan Cordova Plugin', 'Verify Links - NC', 'manage_options', 'verify-links-slug', 'verify_links_menu_page_display', 'dashicons-admin-links', 2
				);
			}

			function verify_links_menu_page_display(){
				echo "<h1> " . get_admin_page_title() . " </h1>
				<p>Tracking issues with links in Wordpress posts with cronJobs running <b>every minute</b> for testing purposes.</p>";

				ob_start();
				include_once plugin_dir_path(__FILE__). '/includes/verify-links-list-table-content.php';
				$template = ob_get_contents();
				ob_end_clean();
				echo $template;
			}

		}
	}

	$natancordova_plugin = new natancordova_plugin();
}
