<?php

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

class Episode {

    public function run() {
        
        add_action('init', array( $this, 'wpmr_episodes_cpt') );
        
		add_action('admin_enqueue_scripts', array($this, 'wpmr_enqueue_date_picker') );
 
		add_action('admin_menu',  array($this, 'wpmr_episodes_remove_unwanted_metabox' ) );
        
        add_filter('gettext', array($this, 'wpmr_enter_title' ) );

		add_action( 'load-post.php',  array($this, 'wpmr_episode_meta_boxes_setup' ) );
        
        add_action( 'load-post-new.php',  array($this, 'wpmr_episode_meta_boxes_setup' ) );
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
			'supports'      => array('title'),
			'has_archive'   => true,
			'taxonomies'  => array( ),
		);
		register_post_type( 'episodes', $args ); 
	}

	/* Meta box setup function. */
	public function wpmr_episode_meta_boxes_setup() {
		/* Add meta boxes on the 'add_meta_boxes' hook. */
		add_action( 'add_meta_boxes',  array($this, 'wpmr_add_episode_meta_boxes' ) );

		/* Save post meta on the 'save_post' hook. */
		add_action( 'save_post',  array($this, 'wpmr_save_episode_meta' ), 10, 2 );
	}
	
	/* Create one or more meta boxes to be displayed on the post editor screen. */
	public function wpmr_add_episode_meta_boxes() {

		add_meta_box(
			'wpmr-episode-meta',      // Unique ID
			esc_html__( 'Episode Details', 'wpmr' ),    // Title
			array ($this, 'wpmr_episode_meta_box' ),   // Callback function
			'episodes',         // Admin page (or post type)
			'normal',         // Context
			'high'         // Priority
		);
		
		add_meta_box(
			'wpmr-episode-medical-meta',      // Unique ID
			esc_html__( 'Medical Details', 'wpmr' ),    // Title
			array ($this, 'wpmr_episode_medical_meta_box' ),   // Callback function
			'episodes',         // Admin page (or post type)
			'normal',         // Context
			'high'         // Priority
		);
	}

	/* Display the post meta box. */
	public function wpmr_episode_meta_box( $post ) { ?>

		<div class="row">
			<?php wp_nonce_field( basename( __FILE__ ), 'wpmr_episode_meta_nonce' ); ?>
		
			<div class="col-md-6 form-group">
				<label class="wpmr-label" for="wpmr_episode_date"><?php _e( "Date of Creation:", 'wpmr' ); ?></label>
				<input class="form-control datepicker" type="text" name="wpmr_episode_date" id="wpmr_episode_date" value="<?php echo esc_attr( get_post_meta( $post->ID, 'wpmr_episode_date', true ) ); ?>" size="30" />
			</div>

			<?php
				// $patients = [];
				// foreach($query->posts as $patient) {
				// 	$patient_obj = array($patient->ID => $patient->post_title);
				// 	array_push($patients, $patient_obj);
				// }
			?>
			<div class="col-md-6 form-group">
				<label class="wpmr-label" for="wpmr_episode_patient"><?php _e( "Patient:", 'wpmr' ); ?></label>
				<!-- <input class="form-control" type="text" name="wpmr_episode_patient" id="wpmr_episode_patient" value="<?php echo esc_attr( get_post_meta( $post->ID, 'wpmr_episode_patient', true ) ); ?>" size="30" /> -->

				<select class="hidden" id="wpmr_episode_patient" name="wpmr_episode_patient">
	 				<option value="-1" selected disabled>Select Patient</option>
				<?php
				
					$selected_patient = get_post_meta( $post->ID, 'wpmr_episode_patient', true );
					$args = array(
						'posts_per_page'	=> -1,
						'post_type'		=> 'patients'
					);
					$query = new WP_Query($args);
					$posts = $query->posts;
					foreach($posts as $patient) {
						if($selected_patient == $patient->ID)
							$selected = "selected";
						else
							$selected = "";
						echo '<option value="' . $patient->ID . '" ' . $selected . '>'. $patient->post_title .'</option>';
					}
				?>
				</select>
			</div>
		</div>

		<script>
			var dateToday = new Date();
			var yrRange = dateToday.getFullYear() -100 + ":" + (dateToday.getFullYear());
			jQuery(document).ready(function(){
				jQuery('.datepicker').datepicker({
					changeMonth: true,
            		changeYear: true,
					yearRange: yrRange, 
				}); 
				jQuery("#wpmr_episode_patient").select2({
					width: '100%',
					placeholder: "Select Episode",
					allowClear: true
				})
			});
		</script>
		<style>
			.ui-datepicker {
				background: white;
				padding: 6px;
			}
			.wpmr-label {
				display: inline-block;
				width: 100px;
			}
		</style>
		<?php 
	}
	public function wpmr_episode_medical_meta_box( $post ) { ?>
	<div class="row">
		<div class="col-md-6 form-group">
			<label class="wpmr-label" for="wpmr_episode_ailemt"><?php _e( "Ailment:", 'wpmr' ); ?></label>
			<input class="form-control" type="text" name="wpmr_episode_ailment" id="wpmr_episode_ailment" value="<?php echo esc_attr( get_post_meta( $post->ID, 'wpmr_episode_ailment', true ) ); ?>" size="30" />
		</div>
		<div class="col-md-6 form-group">
			<label class="wpmr-label" for="wpmr_episode_notes"><?php _e( "Episode Notes:", 'wpmr' ); ?></label>
			<textarea class="form-control" type="text" name="wpmr_episode_notes" id="wpmr_episode_notes"><?php echo esc_attr( get_post_meta( $post->ID, 'wpmr_episode_notes', true ) ); ?></textarea>
		</div>
	</div>
		<?php
	}

	/* Save the meta box's post metadata. */
	public function wpmr_save_episode_meta( $post_id, $post ) {

		/* Verify the nonce before proceeding. */
		if ( !isset( $_POST['wpmr_episode_meta_nonce'] ) || !wp_verify_nonce( $_POST['wpmr_episode_meta_nonce'], basename( __FILE__ ) ) )
		return $post_id;
	
		/* Get the post type object. */
		$post_type = get_post_type_object( $post->post_type );
	
		/* Check if the current user has permission to edit the post. */
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

		$meta_data = [
			'wpmr_episode_date', 
			'wpmr_episode_patient', 
			'wpmr_episode_ailment',
			'wpmr_episode_notes'
		];

		foreach($meta_data as $data) {
			/* Get the posted data and sanitize it for use as an HTML class. */
			$new_meta_value = ( isset( $_POST[$data] ) ? sanitize_text_field( $_POST[$data] ) : '' );
				
			/* Get the meta key. */
			$meta_key = $data;

			/* Get the meta value of the custom field key. */
			$meta_value = get_post_meta( $post_id, $meta_key, true );

			/* If a new meta value was added and there was no previous value, add it. */
			if ( $new_meta_value && '' == $meta_value )
			add_post_meta( $post_id, $meta_key, $new_meta_value, true );

			/* If the new meta value does not match the old value, update it. */
			elseif ( $new_meta_value && $new_meta_value != $meta_value )
			update_post_meta( $post_id, $meta_key, $new_meta_value );

			/* If there is no new meta value but an old value exists, delete it. */
			elseif ( '' == $new_meta_value && $meta_value )
			delete_post_meta( $post_id, $meta_key, $meta_value );
		}
	}
	
	// function wpmr_home(){
	// 	echo "<h1>WP Checkup Home</h1>";
	// }

	public function wpmr_episodes_remove_unwanted_metabox() {
		remove_meta_box( 'commentsdiv','episodes','normal' );
		remove_meta_box( 'commentstatusdiv','episodes','normal' );
	}

	function wpmr_enqueue_date_picker(){
		wp_enqueue_script(
			'field-date', 
			get_template_directory_uri() . '/admin/field-date.js', 
			array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'),
			time(),
			true
		);  
	
		wp_enqueue_style( 'jquery-ui-datepicker' );
	}

	public function wpmr_enter_title( $input ) {
		global $post_type;
		if( is_admin() && 'Enter title here' == $input && 'episodes' == $post_type )
			return 'Enter Episode\'s Name';
		return $input;
	}
}