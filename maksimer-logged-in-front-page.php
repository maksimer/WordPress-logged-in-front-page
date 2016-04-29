<?php 
/**
 * Plugin Name: Maksimer, logged in front page
 * Plugin URI: http://www.maksimer.no
 * Description: Adds an option to add a custom front-page for logged in users.
 * Version: 1.1.1
 * Author: Maksimer AS
 * Author URI: http://www.maksimer.no
 * Text Domain: maksimer_logged_in_front_page
 * Domain Path: /assets/languages
 */



if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}



/*
 * Plugin update stuff
*/
require_once 'assets/update/plugin_update_check.php';
$MyUpdateChecker = new PluginUpdateChecker_2_0 ( 'https://kernl.us/api/v1/updates/5717bbcdec6661c742cadd23/', __FILE__, 'maksimer_logged_in_front_page', 1 );



if ( ! class_exists( 'Maksimer_Logged_In_Front_Page' ) ) :
	class Maksimer_Logged_In_Front_Page {

		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
			add_action( 'admin_init' , array( $this , 'register_fields' ) );
			add_action( 'template_redirect', array( $this, 'redirect_users' ) );
			add_filter( 'display_post_states', array( $this, 'post_state' ) );
			add_filter( 'body_class', array( $this, 'front_body_class' ) );
		}





		/*
		 * Load textdomain
		*/
		public function load_textdomain() {
			load_plugin_textdomain( 'maksimer_logged_in_front_page', false, basename( dirname( __FILE__ ) ) . '/assets/languages' );
		} // load_textdomain()





		/*
		 * Enqueue scripts
		*/
		public function admin_enqueue_scripts( $hook ) {
			if ( 'options-reading.php' != $hook ) {
				return;
			}

			wp_enqueue_script( 'maksimer-logged-in-front-page', plugins_url( '/maksimer-logged-in-front-page/assets/js/maksimer-logged-in-front-page.js' ), array( 'jquery' ) );
		} // admin_enqueue_scripts()





		/*
		 * Register settings fields
		*/
		public function register_fields() {
			register_setting( 'reading', 'maksimer_logged_in_front_page', 'esc_attr' );
			add_settings_field(
				'maksimer_logged_in_front_page',
				'<label for="maksimer_logged_in_front_page">' . __( 'Logged in front page' , 'maksimer_logged_in_front_page' ) . '</label>',
				array( $this, 'field_output' ),
				'reading'
			);
		} // register_fields()





		/*
		 * Admin field output
		*/
		public function field_output() {
?>
			<ul class="logged-in-front-page">
				<li><label for="page_on_front"><?php printf( __( 'Logged in front page: %s', 'maksimer_logged_in_front_page' ), wp_dropdown_pages( array( 'name' => 'maksimer_logged_in_front_page', 'echo' => 0, 'show_option_none' => __( '&mdash; Select &mdash;' ), 'option_none_value' => '0', 'selected' => get_option( 'maksimer_logged_in_front_page' ) ) ) ); ?></label></li>
			</ul>
<?php
		} // field_output()





		/*
		 * Redirect users
		*/
		public function redirect_users() {
			$front_page = get_option( 'maksimer_logged_in_front_page' );

			if ( true == $front_page ) {
				if ( is_front_page() && is_user_logged_in() ) {
					$url = get_the_permalink( $front_page );
					wp_redirect( esc_url( $url ), 301 );
					exit;
				}

				if ( ( get_the_id() == $front_page ) && !is_user_logged_in() ) {
					wp_redirect( esc_url( home_url() ), 301 );
					exit;
				}
			}
		} // redirect_users()





		/*
		 * Add a label to selected page in the wp-list-table
		*/
		public function post_state( $post_states ) {
			$front_page = get_option( 'maksimer_logged_in_front_page' );

			if ( ( true == $front_page ) && ( $front_page == get_the_id() ) ) {
				$post_states[] = __( 'Logged in front page' , 'maksimer_logged_in_front_page' );
			}

			return $post_states;
		} // post_state()





		/*
		 * Add body_class "home" on logged in front page
		*/
		function front_body_class( $classes ) {
			$front_page = get_option( 'maksimer_logged_in_front_page' );

			if ( ( true == $front_page ) && ( $front_page == get_the_id() ) ) {
				$classes[] = 'home logged-in-home';
			}

			return $classes;
		} // front_body_class()
	}

	$maksimer_logged_in_front_page = new Maksimer_Logged_In_Front_Page();

endif;