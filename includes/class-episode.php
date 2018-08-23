<?php

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

class Episode {

    public function run() {
        
        add_action('init', array( $this, 'wpmr_episodes_cpt') );

    }
    
	public function wpmr_episodes_cpt() {
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

}