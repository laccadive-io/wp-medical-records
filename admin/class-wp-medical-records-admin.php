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

		add_action('admin_menu', array($this, 'wpmr_setup_menu' ) );
 
		add_action( 'all_admin_notices', array($this, 'wpmr_admin_tabs' ) );

		add_action( 'admin_head',  array($this, 'menu_highlight' ) );
		// add_action('enqueue_scripts', array($this, 'enqueue_styles'));
		// add_shortcode('test',  array($this, 'form_creation' ) );
	}

	public function reports_page() {
		$tab = $_GET['tab'];
		switch ($tab) {
			case 'patients':
				require_once( plugin_dir_path( __FILE__ ) . '../includes/reports/patient-reports.php');
				break;
			case 'doctors':
				?>
				<h1>Doctors</h1>
				<?php
				break;
			default:
				?>
				<h1>Content yet to be loaded.</h1>
				<?php
				break;
		} 
	}

	public function wpmr_setup_menu(){
		add_menu_page( 'Main Menu Page', 'Medical Records', 'manage_options', 
		'wpmr-main', array( $this, 'wpmr_home' ) );
		// add_submenu_page( 'wpmr-main', 'Patients', 'Patients', 'manage_options', 'edit.php?post_type=patients' );
		add_submenu_page( 'wpmr-main', 'Reports', 'Reports' , 'manage_options', 'wpmr-reports', array( $this, 'reports_page' ) );

		global $submenu;
		$submenu['wpmr-main'][] = array( 'Patients', true, 'edit.php?post_type=patients' );
		$submenu['wpmr-main'][] = array( 'Allergies', true, 'edit-tags.php?taxonomy=allergy&post_type=patients' );
		$submenu['wpmr-main'][] = array( 'Doctors', true, 'edit.php?post_type=doctors' );
		$submenu['wpmr-main'][] = array( 'Episodes', true, 'edit.php?post_type=episodes' );
		$submenu['wpmr-main'][] = array( 'Visits', true, 'edit.php?post_type=visits' );
		$submenu['wpmr-main'][] = array( 'Admissions', true, 'edit.php?post_type=admissions' );

	}

	public function wpmr_home() {
		$totalPatients = get_posts([
		  'post_type' => 'patients',
		  'post_status' => 'publish',
		  'numberposts' => -1
		]);

		$totalAllergies = get_terms([
		  'taxonomy' => 'allergy',
		  'hide_empty' => false,
		]);

		$totalDoctors = get_posts([
		  'post_type' => 'doctors',
		  'post_status' => 'publish',
		  'numberposts' => -1
		]);

		$totalEpisodes = get_posts([
		  'post_type' => 'episodes',
		  'post_status' => 'publish',
		  'numberposts' => -1
		]);
		?>
			<h1>WP Medical Records</h1>
			<div class="row" style="width:1200px;">
				<div class="col-sm-12 col-md-3">
					<div class="card">
						<div class="card-body">
							<h4><?= count($totalPatients) ?></h4>
							<p><?= count($totalPatients) == 1 ? 'Patient': 'Patients';  ?></p>
						</div>
					</div>
				</div>
				<div class="col-sm-12 col-md-3">
					<div class="card">
						<div class="card-body">
							<h4><?= count($totalAllergies); ?></h4>
							<p><?= count($totalAllergies) == 1 ? 'Allergy': 'Allergies'; ?></p>
						</div>
					</div>
				</div>
				<div class="col-sm-12 col-md-3">
					<div class="card">
						<div class="card-body">
							<h4><?= count($totalDoctors) ?></h4>
							<p><?= count($totalDoctors) == 1 ? 'Doctor': 'Doctors' ?></p>
						</div>
					</div>
				</div>
				<div class="col-sm-12 col-md-3">
					<div class="card">
						<div class="card-body">
							<h4><?= count($totalEpisodes); ?></h4>
							<p><?= count($totalEpisodes) == 1 ?  'Episode': 'Episodes'; ?></p>
						</div>
					</div>
				</div>
			</div>
		<?php
	}
	
	public function wpmr_admin_tabs() {
		$cs = get_current_screen()->id;
		if(in_array($cs, ['edit-patients', 'patients', 'edit-episodes', 'edit-visits', 'edit-admissions', 'edit-doctors'])) {
			?>
			<h1 class="nav-tab-wrapper">
				<a href="post-new.php?post_type=patients" class="nav-tab <?php if($cs == 'patients') echo 'nav-tab-active'; else echo ''; ?> nav-tab-1">New Patient</a>
				<a href="edit.php?post_type=patients" class="nav-tab <?php if($cs == 'edit-patients') echo 'nav-tab-active'; else echo ''; ?>  nav-tab-2">Patients</a>
				<a href="edit.php?post_type=doctors" class="nav-tab <?php if($cs == 'edit-doctors') echo 'nav-tab-active'; else echo ''; ?>  nav-tab-2">Doctors</a>
				<a href="edit.php?post_type=episodes" class="nav-tab <?php if($cs == 'edit-episodes') echo 'nav-tab-active'; else echo ''; ?> nav-tab-3">Episodes</a>
				<a href="edit.php?post_type=visits" class="nav-tab <?php if($cs == 'edit-visits') echo 'nav-tab-active'; else echo ''; ?> nav-tab-4">Visits</a>
				<a href="edit.php?post_type=admissions" class="nav-tab <?php if($cs == 'edit-admissions') echo 'nav-tab-active'; else echo ''; ?> nav-tab-4">Admissions</a>
			</h1>
			<?php
		}
		if( $cs == 'medical-records_page_wpmr-reports' ) {
			$tab = $_GET['tab'];
			?>
			<h1 class="nav-tab-wrapper">
				<a href="admin.php?page=wpmr-reports&tab=patients" class="nav-tab <?php if( $cs == 'medical-records_page_wpmr-reports' && $tab == 'patients' ) echo 'nav-tab-active'; else echo ''; ?> nav-tab-1">Patients</a>
				<a href="admin.php?page=wpmr-reports&tab=doctors" class="nav-tab <?php if( $cs == 'medical-records_page_wpmr-reports' && $tab == 'doctors' ) echo 'nav-tab-active'; else echo ''; ?> nav-tab-1">Doctors</a>
				<a href="admin.php?page=wpmr-reports&tab=episodes" class="nav-tab <?php if( $cs == 'medical-records_page_wpmr-reports' && $tab == 'episodes' ) echo 'nav-tab-active'; else echo ''; ?> nav-tab-1">Episodes</a>
				<a href="admin.php?page=wpmr-reports&tab=visits" class="nav-tab <?php if( $cs == 'medical-records_page_wpmr-reports' && $tab == 'visits' ) echo 'nav-tab-active'; else echo ''; ?> nav-tab-1">Visits</a>
				<a href="admin.php?page=wpmr-reports&tab=admissions" class="nav-tab <?php if( $cs == 'medical-records_page_wpmr-reports' && $tab == 'admissions' ) echo 'nav-tab-active'; else echo ''; ?> nav-tab-1">Admissions</a>
			</h1>
			<?php
		}
	}
	 
	public function menu_highlight(){
		global $parent_file, $submenu_file, $post_type;
		switch ( $post_type ) {
			case 'patients':
				$parent_file = 'wpmr-main'; 
				if( $submenu_file == "edit-tags.php?taxonomy=allergy&amp;post_type=patients")
					$submenu_file = "edit-tags.php?taxonomy=allergy&post_type=patients";
				else
					$submenu_file = 'edit.php?post_type=patients';
				break;
			case 'doctors':
				$parent_file = 'wpmr-main'; 
				$submenu_file = 'edit.php?post_type=doctors';
				break;
			case 'episodes':
				$parent_file = 'wpmr-main'; 
				$submenu_file = 'edit.php?post_type=episodes';
				break;
			case 'visits':
				$parent_file = 'wpmr-main'; 
				$submenu_file = 'edit.php?post_type=visits';
				break;
			case 'admissions':
				$parent_file = 'wpmr-main'; 
				$submenu_file = 'edit.php?post_type=admissions';
				break;
		}
	}

	public function form_creation(){
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
		wp_enqueue_style( 'bootstrap', plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'jquery-ui', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'select2', plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), $this->version, 'all' );

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
		wp_enqueue_script( 'bootstrap', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'select2', plugin_dir_url( __FILE__ ) . 'js/select2.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'd3', plugin_dir_url( __FILE__ ) . 'js/d3.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'Chart', plugin_dir_url( __FILE__ ) . 'js/Chart.min.js', array( 'jquery' ), $this->version, false );

	}
}
