<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       https://laccadive.io/
 * @since      1.0.0
 *
 * @package    Wp_Medical_Records
 * @subpackage Wp_Medical_Records/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Wp_Medical_Records
 * @subpackage Wp_Medical_Records/includes
 * @author     Laccadive IO <muhammad@laccadive.io>
 */
class Wp_Medical_Records_Loader {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $actions    The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $filters    The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $filters;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->actions = array();
		$this->filters = array();

	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $hook             The name of the WordPress action that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the action is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $hook             The name of the WordPress filter that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array                $hooks            The collection of hooks that is being registered (that is, actions or filters).
	 * @param    string               $hook             The name of the WordPress filter that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         The priority at which the function should be fired.
	 * @param    int                  $accepted_args    The number of arguments that should be passed to the $callback.
	 * @return   array                                  The collection of actions and filters registered with WordPress.
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {

		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args
		);

		return $hooks;

	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		require_once plugin_dir_path( __FILE__ ) . 'class-patient-meta-install.php';

		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		add_action('init', array( $this, 'wpmr_patients_cpt') );
		add_action('init', array( $this, 'wpmr_episodes_cpt') );
		add_action('init', array( $this, 'wpmr_visits_cpt') );
		
		$wpmr_meta = new WPMR_Meta();
		add_action('init', array($wpmr_meta,'patient_meta_install'));

        // hook into init for single site, priority 0 = highest priority
        add_action('init', array($wpmr_meta,'patient_meta_integrate_wpdb'));
	}

	public static function wpmr_patients_cpt() {
		$labels = array(
			'name'               => _x( 'Patients', 'Patients for WPMR' ),
			'singular_name'      => _x( 'Patient', 'Patient for WPMR' ),
			'add_new'            => _x( 'Add New', 'patient' ),
			'add_new_item'       => __( 'Add New Patient' ),
			'edit_item'          => __( 'Edit Patient' ),
			'new_item'           => __( 'New Patient' ),
			'all_items'          => __( 'All Patients' ),
			'view_item'          => __( 'View Patient' ),
			'search_items'       => __( 'Search Patients' ),
			'not_found'          => __( 'No patients found' ),
			'not_found_in_trash' => __( 'No patients found in the Trash' ), 
			'parent_item_colon'  => '',
			'menu_name'          => 'Patients'
		);
		$args = array(
			'labels'        => $labels,
			'description'   => '',
			'public'        => true,
			'show_in_menu' => false,
			// 'menu_position' => 2,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
			'has_archive'   => true,
			'taxonomies'  => array( ),
		);
		register_post_type( 'patients', $args ); 
	}

	public static function wpmr_episodes_cpt() {
		$labels = array(
			'name'               => _x( 'Episodes', 'Episodes for WPMR' ),
			'singular_name'      => _x( 'Episode', 'Episode for WPMR' ),
			'add_new'            => _x( 'Add New', 'episode' ),
			'add_new_item'       => __( 'Add New Episode' ),
			'edit_item'          => __( 'Edit Episode' ),
			'new_item'           => __( 'New Episode' ),
			'all_items'          => __( 'All Episodes' ),
			'view_item'          => __( 'View Episode' ),
			'search_items'       => __( 'Search Episodes' ),
			'not_found'          => __( 'No episode found' ),
			'not_found_in_trash' => __( 'No episodes found in the Trash' ), 
			'parent_item_colon'  => '',
			'menu_name'          => 'Episodes'
		);
		$args = array(
			'labels'        => $labels,
			'description'   => '',
			'public'        => true,
			'show_in_menu' => false,
			// 'menu_position' => 2,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
			'has_archive'   => true,
			'taxonomies'  => array( ),
		);
		register_post_type( 'episodes', $args ); 
	}

	public static function wpmr_visits_cpt() {
		$labels = array(
			'name'               => _x( 'Visits', 'Visits for WPMR' ),
			'singular_name'      => _x( 'Visit', 'Visit for WPMR' ),
			'add_new'            => _x( 'Add New', 'visit' ),
			'add_new_item'       => __( 'Add New Visit' ),
			'edit_item'          => __( 'Edit Visit' ),
			'new_item'           => __( 'New Visit' ),
			'all_items'          => __( 'All Visits' ),
			'view_item'          => __( 'View Visit' ),
			'search_items'       => __( 'Search Visits' ),
			'not_found'          => __( 'No visits found' ),
			'not_found_in_trash' => __( 'No visits found in the Trash' ), 
			'parent_item_colon'  => '',
			'menu_name'          => 'Visits'
		);
		$args = array(
			'labels'        => $labels,
			'description'   => '',
			'public'        => true,
			'show_in_menu' => false,
			// 'menu_position' => 2,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
			'has_archive'   => true,
			'taxonomies'  => array( ),
		);
		register_post_type( 'visits', $args ); 
	}
}
