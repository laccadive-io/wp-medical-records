<?php

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

class Patient {

    public function run() {
        
        add_action('init', array( $this, 'wpmr_patients_cpt') );
        
		add_action('admin_enqueue_scripts', array($this, 'wpmr_enqueue_date_picker') );
 
		add_action('admin_menu',  array($this, 'wpmr_remove_unwanted_metabox' ) );
        
        add_filter('gettext', array($this, 'wpmr_enter_title' ) );

		add_action( 'load-post.php',  array($this, 'wpmr_patient_meta_boxes_setup' ) );
        
        add_action( 'load-post-new.php',  array($this, 'wpmr_patient_meta_boxes_setup' ) );
    }

	public function wpmr_patients_cpt() {
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
			'show_in_menu' =>  false,
			// 'menu_position' => 2,
			'supports'      => array( 'title', 'thumbnail' ),
			'has_archive'   => true,
			'taxonomies'  => array( ),
		);
		register_post_type( 'patients', $args ); 
	}
    
	/* Meta box setup function. */
	public function wpmr_patient_meta_boxes_setup() {
		/* Add meta boxes on the 'add_meta_boxes' hook. */
		add_action( 'add_meta_boxes',  array($this, 'wpmr_add_patient_meta_boxes' ) );

		/* Save post meta on the 'save_post' hook. */
		add_action( 'save_post',  array($this, 'wpmr_save_patient_meta' ), 10, 2 );
	}

	/* Create one or more meta boxes to be displayed on the post editor screen. */
	public function wpmr_add_patient_meta_boxes() {

		add_meta_box(
		'wpmr-patient-meta',      // Unique ID
		esc_html__( 'Patient Details', 'wpmr' ),    // Title
		array ($this, 'wpmr_patient_meta_box' ),   // Callback function
		'patients',         // Admin page (or post type)
		'normal',         // Context
		'high'         // Priority
		);
		
		add_meta_box(
			'wpmr-patient-medical-meta',      // Unique ID
			esc_html__( 'Medical Details', 'wpmr' ),    // Title
			array ($this, 'wpmr_patient_medical_meta_box' ),   // Callback function
			'patients',         // Admin page (or post type)
			'normal',         // Context
			'high'         // Priority
		);
	}

	/* Display the post meta box. */
	public function wpmr_patient_meta_box( $post ) { ?>

		<div class="row">
			<?php wp_nonce_field( basename( __FILE__ ), 'wpmr_patient_meta_nonce' ); ?>
		
			<div class="col-md-6 form-group">
				<label class="wpmr-label" for="wpmr_patient_dob"><?php _e( "Date of Birth:", 'wpmr' ); ?></label>
				<input class="form-control datepicker" type="text" name="wpmr_patient_dob" id="wpmr_patient_dob" value="<?php echo esc_attr( get_post_meta( $post->ID, 'wpmr_patient_dob', true ) ); ?>" size="30" />
			</div>

			<?php
				$gender = get_post_meta( $post->ID, 'wpmr_patient_gender', true );
			?>
			<div class="col-md-6 form-group">
				<label class="wpmr-label" for="wpmr_patient_gender"><?php _e( "Gender:", 'wpmr' ); ?></label>
				<select class="custom-select" id="wpmr_patient_gender" name="wpmr_patient_gender">
					<option value="male" <?php if($gender == 'male') echo "selected"; ?> >Male</option>
					<option value="female" <?php if($gender == 'female') echo "selected"; ?>>Female</option>
				</select>
			</div>

			<div class="col-md-6 form-group">
				<label class="wpmr-label" for="wpmr_patient_address_one"><?php _e( "Address Line 1:", 'wpmr' ); ?></label>
				<input class="form-control" type="text" name="wpmr_patient_address_one" id="wpmr_patient_address_one" value="<?php echo esc_attr( get_post_meta( $post->ID, 'wpmr_patient_address_one', true ) ); ?>" size="30" />			
			</div>

			<div class="col-md-6 form-group">
				<label class="wpmr-label" for="wpmr_patient_address_two"><?php _e( "Address Line 2:", 'wpmr' ); ?></label>
				<input class="form-control" type="text" name="wpmr_patient_address_two" id="wpmr_patient_address_two" value="<?php echo esc_attr( get_post_meta( $post->ID, 'wpmr_patient_address_two', true ) ); ?>" size="30" />			
			</div>

			<div class="col-md-6 form-group">
				<label class="wpmr-label" for="wpmr_patient_city"><?php _e( "City:", 'wpmr' ); ?></label>
				<input class="form-control" type="text" name="wpmr_patient_city" id="wpmr_patient_city" value="<?php echo esc_attr( get_post_meta( $post->ID, 'wpmr_patient_city', true ) ); ?>" size="30" />			
			</div>

			<div class="col-md-6 form-group">
				<label class="wpmr-label" for="wpmr_patient_country"><?php _e( "Country:", 'wpmr' ); ?></label>
				<input class="form-control" type="text" name="wpmr_patient_country" id="wpmr_patient_country" value="<?php echo esc_attr( get_post_meta( $post->ID, 'wpmr_patient_country', true ) ); ?>" size="30" />			
			</div>

			<div class="col-md-6 form-group">
				<label class="wpmr-label" for="wpmr_patient_mobile"><?php _e( "Mobile Number:", 'wpmr' ); ?></label>
				<input class="form-control" type="text" name="wpmr_patient_mobile" id="wpmr_patient_mobile" value="<?php echo esc_attr( get_post_meta( $post->ID, 'wpmr_patient_mobile', true ) ); ?>" size="30" />			
			</div>

			<div class="col-md-6 form-group">
				<label class="wpmr-label" for="wpmr_patient_email"><?php _e( "Email Address:", 'wpmr' ); ?></label>
				<input class="form-control" type="text" name="wpmr_patient_email" id="wpmr_patient_email" value="<?php echo esc_attr( get_post_meta( $post->ID, 'wpmr_patient_email', true ) ); ?>" size="30" />			
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
	public function wpmr_patient_medical_meta_box( $post ) { ?>
	<div class="row">
		<div class="col-md-6 form-group">
			<label class="wpmr-label " for="wpmr_patient_allergies"><?php _e( "Allergies:", 'wpmr' ); ?></label>
			<input class="form-control" type="text" name="wpmr_patient_allergies" id="wpmr_patient_allergies" value="<?php echo esc_attr( get_post_meta( $post->ID, 'wpmr_patient_allergies', true ) ); ?>" size="30" />
		</div>

		<div class="col-md-6 form-group">
			<label class="wpmr-label" for="wpmr_patient_bloodgroup"><?php _e( "Blood Group:", 'wpmr' ); ?></label>
			<input class="form-control" type="text" name="wpmr_patient_bloodgroup" id="wpmr_patient_bloodgroup" value="<?php echo esc_attr( get_post_meta( $post->ID, 'wpmr_patient_bloodgroup', true ) ); ?>" size="30" />
		</div>

		<div class="col-md-6 form-group">
			<label class="wpmr-label" for="wpmr_patient_notes"><?php _e( "Notes:", 'wpmr' ); ?></label>
			<textarea class="form-control" type="text" name="wpmr_patient_notes" id="wpmr_patient_notes" ><?php echo esc_attr( get_post_meta( $post->ID, 'wpmr_patient_notes', true ) ); ?></textarea>
		</div>
	</div>
		<?php
	}

	/* Save the meta box's post metadata. */
	public function wpmr_save_patient_meta( $post_id, $post ) {

		/* Verify the nonce before proceeding. */
		if ( !isset( $_POST['wpmr_patient_meta_nonce'] ) || !wp_verify_nonce( $_POST['wpmr_patient_meta_nonce'], basename( __FILE__ ) ) )
		return $post_id;
	
		/* Get the post type object. */
		$post_type = get_post_type_object( $post->post_type );
	
		/* Check if the current user has permission to edit the post. */
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

		$meta_data = [
			'wpmr_patient_dob', 
			'wpmr_patient_gender', 
			'wpmr_patient_address_one', 
			'wpmr_patient_address_two', 
			'wpmr_patient_city', 
			'wpmr_patient_country', 
			'wpmr_patient_mobile', 
			'wpmr_patient_email',
			'wpmr_patient_allergies',
			'wpmr_patient_bloodgroup',
			'wpmr_patient_notes'
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

	public function wpmr_remove_unwanted_metabox() {
		remove_meta_box( 'commentsdiv','patients','normal' );
		remove_meta_box( 'commentstatusdiv','patients','normal' );
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
		if( is_admin() && 'Enter title here' == $input && 'patients' == $post_type )
			return 'Enter Patient\'s Full Name';
		return $input;
	}
}