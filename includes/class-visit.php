<?php

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

class Visit {

    public function run() {
        
        add_action('init', array( $this, 'wpmr_visits_cpt') );

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