<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://laccadive.io/
 * @since      1.0.0
 *
 * @package    Wp_Medical_Records
 * @subpackage Wp_Medical_Records/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Medical_Records
 * @subpackage Wp_Medical_Records/admin
 * @author     Laccadive IO <muhammad@laccadive.io>
 */
class Wp_Medical_Records_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action('admin_menu', 'wp_checkup_setup_menu');
 
		function wp_checkup_setup_menu(){
			add_menu_page( 'Main Menu Page', 'Medical Records', 'manage_options', 
			'wpmr-main', 'wp_checkup_init' );
			// add_submenu_page( 'wpmr-main', 'Patients', 'Patients', 
			// 'manage_options', 'patients', 'patients_page' );
			global $submenu;
			$submenu['wpmr-main'][] = array( 'Patients', true, 'edit.php?post_type=patients' );

			add_action( 'all_admin_notices', 'wpmr_admin_tabs' );
		}
		 
		function wp_checkup_init(){
			echo "<h1>Hello World!</h1>";
		}

		function patients_page() {
			echo "<h1>Hello World 2!</h1>";
		}

		function wpmr_admin_tabs() {
			if(get_current_screen()->id == 'edit-patients') {
				echo '<h1 class="nav-tab-wrapper">';
				echo '<a href="edit.php?post_type=patients" class="nav-tab nav-tab-1">New Patient</a>';
				echo '<a href="edit.php?post_type=patients" class="nav-tab nav-tab-active nav-tab-1">Patients</a>';
				echo '<a href="edit.php?post_type=patients" class="nav-tab nav-tab-1">Episodes</a>';
				echo '<a href="edit.php?post_type=patients" class="nav-tab nav-tab-1">Visits</a>';
				echo '<a href="edit.php?post_type=patients" class="nav-tab nav-tab-1">Appointments</a>';
				echo '</h1>';
			}
		}

		add_shortcode('test', 'form_creation');
		function form_creation(){
			?>
			<form>
			First name: <input type="text" name="firstname"><br>
			Last name: <input type="text" name="lastname"><br>
			Date: <input type="text" name="date"><br>
			Time: <input type="text" name="time"><br>
			Message: <textarea name="message"></textarea><br />
			<input type="submit" value="Submit" />
			</form>
			<?php
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Medical_Records_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Medical_Records_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-medical-records-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Medical_Records_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Medical_Records_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-medical-records-admin.js', array( 'jquery' ), $this->version, false );

	}

}
