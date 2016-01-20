<?php
/**
 * Plugin Name:     Leafly Specials
 * Plugin URI:      http://www.wpdispensary.com
 * Description:     Easily display your leafly dispensary specials on your own website
 * Version:         1.0
 * Author:          WP Dispensary
 * Author URI:      http://www.wpdispensary.com
 * Text Domain:     leafly-specials
 *
 * @package         LeaflySpecials
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


if( ! class_exists( 'LeaflySpecials' ) ) {


    /**
     * Main LeaflySpecials class
     *
     * @since       1.0.0
     */
    class LeaflySpecials {


        /**
         * @access      private
         * @since       1.0.0
         * @var         LeaflySpecials $instance The one true LeaflySpecials
         */
        private static $instance;


        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      self::$instance The one true LeaflySpecials
         */
        public static function instance() {
            if( ! self::$instance ) {
                self::$instance = new LeaflySpecials();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->hooks();
            }

            return self::$instance;
        }


        /**
         * Setup plugin constants
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        private function setup_constants() {
            // Plugin path
            define( 'LEAFLYSPECIALS_DIR', plugin_dir_path( __FILE__ ) );

            // Plugin URL
            define( 'LEAFLYSPECIALS_URL', plugin_dir_url( __FILE__ ) );
        }


        /**
         * Include required files
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function includes() {
            require_once LEAFLYSPECIALS_DIR . 'includes/functions.php';
            require_once LEAFLYSPECIALS_DIR . 'includes/shortcodes.php';
            require_once LEAFLYSPECIALS_DIR . 'includes/scripts.php';
            require_once LEAFLYSPECIALS_DIR . 'includes/widget.php';
        }


        /**
         * Run action and filter hooks
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function hooks() {
        }


        /**
         * Internationalization
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function load_textdomain() {
            // Set filter for language directory
            $lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
            $lang_dir = apply_filters( 'lealyspecials_language_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), '' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'leafly-specials', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/leafly-specials/' . $mofile;

            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/leafly-specials/ folder
                load_textdomain( 'leafly-specials', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/leafly-specials/languages/ folder
                load_textdomain( 'leafly-specials', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'leafly-specials', false, $lang_dir );
            }
        }
    }
}


/**
 * The main function responsible for returning the one true LeaflySpecials
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      LeaflySpecials The one true LeaflySpecials
 */
function lealyspecials_load() {
    return LeaflySpecials::instance();
}
add_action( 'plugins_loaded', 'lealyspecials_load' );


/**
 * Create the Leafly Connect settings page
 *
 * @since       1.0.0
 */

// create custom plugin settings menu

if ( ! function_exists( 'lcwp_create_menu' ) ) {

	add_action('admin_menu', 'lcwp_create_menu');
	function lcwp_create_menu() {
		//create new top-level menu
		add_submenu_page( 'options-general.php', 'Leafly Connect', 'Leafly Connect', 'manage_options', 'leafly_connect', 'lcwp_settings_page' );
		//call register settings function
		add_action( 'admin_init', 'register_mysettings' );
	}

}

if ( ! function_exists( 'register_mysettings' ) ) {

	function register_mysettings() {
		//register our settings
		register_setting( 'lcwp-settings-group', 'app_id' );
		register_setting( 'lcwp-settings-group', 'app_key' );
	}

}

if ( ! function_exists( 'lcwp_settings_page' ) ) {

	function lcwp_settings_page() {
	?>
	<div class="wrap">
	<h2>Leafly Connect</h2>
	<p>In order to utilize the Leafly plugins from <a href="http://www.wpdispensary.com/">WP Dispensary</a>, you need to create your API ID and KEY. Learn more about this <a href="http://developer.leafly.com/" target="_blank">here</a></p>

	<form method="post" action="options.php">
		<?php settings_fields( 'lcwp-settings-group' ); ?>
		<table class="form-table">
			<tr valign="top">
			<th scope="row">APP ID:</th>
			<td><input type="text" name="app_id" value="<?php echo get_option('app_id'); ?>" /></td>
			</tr>
			 
			<tr valign="top">
			<th scope="row">APP KEY</th>
			<td><input type="text" name="app_key" value="<?php echo get_option('app_key'); ?>" /></td>
			</tr>

		</table>
		
		<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>

	</form>
	</div>
	<?php }

}