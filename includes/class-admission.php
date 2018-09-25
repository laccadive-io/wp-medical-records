<?php

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

class Admission {

    public function run() {
        
        add_action('init', array( $this, 'wpmr_admissions_cpt') );
        
		add_action('admin_enqueue_scripts', array( $this, 'wpmr_enqueue_date_picker') );
 
		add_action('admin_menu',  array( $this, 'wpmr_remove_unwanted_metabox' ) );
        
        add_filter('gettext', array( $this, 'wpmr_enter_title' ) );

		add_action( 'load-post.php',  array( $this, 'wpmr_admission_meta_boxes_setup' ) );
        
		add_action( 'load-post-new.php',  array( $this, 'wpmr_admission_meta_boxes_setup' ) );
		
		add_action( 'wp_ajax_nopriv_patient_episodes_ajax', array( $this, 'wpmr_patient_episodes_ajax' ) );
		add_action( 'wp_ajax_patient_episodes_ajax', array( $this, 'wpmr_patient_episodes_ajax' ) );
    }    
		
	public static function wpmr_admissions_cpt() {
		$labels = array(
			'name'               => _x( 'Admissions', 'Admissions for WPMR' ),
			'singular_name'      => _x( 'Admission', 'Admission for WPMR' ),
			'add_new'            => _x( 'Add New', 'admission' ),
			'add_new_item'       => __( 'Add New Admission' ),
			'edit_item'          => __( 'Edit Admission' ),
			'new_item'           => __( 'New Admission' ),
			'all_items'          => __( 'All Admissions' ),
			'view_item'          => __( 'View Admission' ),
			'search_items'       => __( 'Search Admissions' ),
			'not_found'          => __( 'No admissions found' ),
			'not_found_in_trash' => __( 'No admissions found in the Trash' ), 
			'parent_item_colon'  => '',
			'menu_name'          => 'Admissions'
		);
		$args = array(
			'labels'        => $labels,
			'description'   => '',
			'public'        => true,
			'show_in_menu' => false,
			// 'menu_position' => 2,
			'supports'      => array( 'title' ),
			'has_archive'   => true,
			'taxonomies'  => array( ),
		);
		register_post_type( 'admissions', $args ); 
	}

	
	/* Meta box setup function. */
	public function wpmr_admission_meta_boxes_setup() {
		/* Add meta boxes on the 'add_meta_boxes' hook. */
		add_action( 'add_meta_boxes',  array($this, 'wpmr_add_admission_meta_boxes' ) );

		/* Save post meta on the 'save_post' hook. */
		add_action( 'save_post',  array($this, 'wpmr_save_admission_meta' ), 10, 2 );
	}
	
	/* Create one or more meta boxes to be displayed on the post editor screen. */
	public function wpmr_add_admission_meta_boxes() {

		add_meta_box(
			'wpmr-admission-meta',      // Unique ID
			esc_html__( 'Admission Details', 'wpmr' ),    // Title
			array ($this, 'wpmr_admission_meta_box' ),   // Callback function
			'admissions',         // Admin page (or post type)
			'normal',         // Context
			'high'         // Priority
		);
		
		add_meta_box(
			'wpmr-admission-medical-meta',      // Unique ID
			esc_html__( 'Medical Details', 'wpmr' ),    // Title
			array ($this, 'wpmr_admission_medical_meta_box' ),   // Callback function
			'admissions',         // Admin page (or post type)
			'normal',         // Context
			'high'         // Priority
		);
		
		add_meta_box(
			'wpmr-admission-vital-meta',      // Unique ID
			esc_html__( 'Vitals', 'wpmr' ),    // Title
			array ($this, 'wpmr_admission_vital_meta_box' ),   // Callback function
			'admissions',         // Admin page (or post type)
			'normal',         // Context
			'high'         // Priority
		);
	}

	/* Display the post meta box. */
	public function wpmr_admission_meta_box( $post ) { ?>

		<div class="row">
			<?php wp_nonce_field( basename( __FILE__ ), 'wpmr_admission_meta_nonce' ); ?>
		
			<div class="col-md-6 form-group">
				<label class="wpmr-label" for="wpmr_admission_date"><?php _e( "Admission Date:", 'wpmr' ); ?></label>
				<input class="form-control datepicker" type="text" name="wpmr_admission_date" id="wpmr_admission_date" value="<?php echo esc_attr( get_post_meta( $post->ID, 'wpmr_admission_date', true ) ); ?>" size="30" />
			</div>

			<?php
				// $patients = [];
				// foreach($query->posts as $patient) {
				// 	$patient_obj = array($patient->ID => $patient->post_title);
				// 	array_push($patients, $patient_obj);
				// }
			?>
			<div class="col-md-6 form-group">
				<label class="wpmr-label" for="wpmr_admission_discharge_date"><?php _e( "Discharge Date:", 'wpmr' ); ?></label>
				<input class="form-control datepicker" type="text" name="wpmr_admission_discharge_date" id="wpmr_admission_discharge_date" value="<?php echo esc_attr( get_post_meta( $post->ID, 'wpmr_admission_discharge_date', true ) ); ?>" size="30" />
			</div>

			<div class="col-md-6 form-group">
				<label class="wpmr-label" for="wpmr_admission_patient"><?php _e( "Patient:", 'wpmr' ); ?></label>
				<!-- <input class="form-control" type="text" name="wpmr_admission_patient" id="wpmr_admission_patient" value="<?php //echo esc_attr( get_post_meta( $post->ID, 'wpmr_admission_patient', true ) ); ?>" size="30" /> -->

				<select class="hidden" id="wpmr_admission_patient" name="wpmr_admission_patient">
	 				<option value="-1" selected disabled>Select Patient</option>
				<?php
				
					$selected_patient = get_post_meta( $post->ID, 'wpmr_admission_patient', true );
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

			
			<div class="<?php echo isset($selected_patient) ? 'col-md-6 form-group' : ' col-md-6 form-group hidden' ?>" id="episodeSelect">
				<label class="wpmr-label" for="wpmr_admission_patient"><?php _e( "Episode:", 'wpmr' ); ?></label>
				<!-- <input class="form-control" type="text" name="wpmr_admission_patient" id="wpmr_admission_patient" value="<?php //echo esc_attr( get_post_meta( $post->ID, 'wpmr_admission_patient', true ) ); ?>" size="30" /> -->

				<select class="hidden" id="wpmr_admission_episode" name="wpmr_admission_episode">
	 				<option value="-1" disabled>Select Episode</option>
				<?php
				
				
					$selected_episode = get_post_meta( $post->ID, 'wpmr_admission_episode', true );
					$args = array(
						'posts_per_page'	=> -1,
						'post_type'		=> 'episodes',
						'meta_key'			 => 'wpmr_episode_patient',
						'meta_value'		 => $selected_patient
					);
					$query = new WP_Query($args);
					$posts = $query->posts;
					foreach($posts as $episode) {
						if($selected_episode == $episode->ID)
							$selected = "selected";
						else
							$selected = "";
						echo '<option value="' . $episode->ID . '" ' . $selected . '>'. $episode->post_title .'</option>';
					}
				?>
				</select>
			</div>
		</div>

		

		<script>
			var dateToday = new Date();
			var yrRange = dateToday.getFullYear() -100 + ":" + (dateToday.getFullYear());
			(function($){
				$(document).ready(function() {
					$('.datepicker').datepicker({
						changeMonth: true,
						changeYear: true,
						yearRange: yrRange,
					}); 
					$("#wpmr_admission_patient").chosen({
						disable_search_threshold: 1,
						allow_single_deselect: true,
						disable_search: false,
						no_results_text: "Oops, nothing found!",
						width: "95%"
					}).change(function(data) {
						console.log(data.currentTarget.value)
						$.ajax({
							method: 'get',
							url: window.ajaxurl,
							data: {
								action: 'patient_episodes_ajax',
								patientId: data.currentTarget.value,
							},
							success: function(data) {
								var jsonData = JSON.parse(data);
								var options = [];
								if(jsonData) {
									options = jsonData.map(item => (
										`<option value=${item.ID}>${item.post_title}</option>`
									))
									options.unshift(`<option value"-1" selected disabled>Select Episode</option>`)
								} else {
									options = '<span>This patient does not have an episode yet. Please create one first.</span>';
								}
								$("#wpmr_admission_episode").html(options)
								$("#wpmr_admission_episode").trigger("chosen:updated");
								$("#episodeSelect").show();
							}
						})
					})					
				});

				$("#wpmr_admission_episode").chosen({
					disable_search_threshold: 1,
					allow_single_deselect: true,
					disable_search: false,
					no_results_text: "Oops, nothing found!",
					width: "95%"
				});
			})(jQuery)
			function calculateBmi() {
				var weight = document.getElementById('wpmr_admission_weight').value
				var height = document.getElementById('wpmr_admission_height').value
				if(weight > 0 && height > 0) {	
					var finalBmi = (weight/(height/100*height/100)).toFixed(2)
					document.getElementById('wpmr_admission_bmi').value = finalBmi;
					if(finalBmi < 18.5){
						var bmiResult = "Underweight"
					}
					if(finalBmi > 18.5 && finalBmi < 25){
						var bmiResult = "Healthy"
					}
					if(finalBmi > 25){
						var bmiResult = "Overweight"
					}
				}
				else{
					var bmiResult = "Please Fill in everything correctly";
				}
				document.getElementById('wpmr_admission_bmi_result').value = bmiResult;
			}
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

	public function wpmr_admission_medical_meta_box( $post ) { ?>
		<div class="row">
			<div class="col-md-6 form-group">
				<label class="wpmr-label" for="wpmr_admission_ailment"><?php _e( "Ailment:", 'wpmr' ); ?></label>
				<input class="form-control" type="text" name="wpmr_admission_ailment" id="wpmr_admission_ailment" value="<?php echo esc_attr( get_post_meta( $post->ID, 'wpmr_admission_ailment', true ) ); ?>" size="30" />
			</div>
			<div class="col-md-6 form-group">
				<label class="wpmr-label" for="wpmr_admission_notes"><?php _e( "Admission Notes:", 'wpmr' ); ?></label>
				<textarea class="form-control" type="text" name="wpmr_admission_notes" id="wpmr_admission_notes" size="30" ><?php echo esc_attr( get_post_meta( $post->ID, 'wpmr_admission_notes', true ) ); ?></textarea>
			</div>
		</div>
		<?php
	}

	function wpmr_admission_vital_meta_box( $post ) { ?>
		<div class="row">
			<div class="col-md-6 form-group">
				<label class="wpmr-label" for="wpmr_admission_height"><?php _e( "Height in cm:", 'wpmr' ); ?></label>
				<input onkeyup="calculateBmi()" class="form-control" type="text" name="wpmr_admission_height" id="wpmr_admission_height" value="<?php echo esc_attr( get_post_meta( $post->ID, 'wpmr_admission_height', true ) ); ?>" size="30" />
			</div>
			<div class="col-md-6 form-group">
				<label class="wpmr-label" for="wpmr_admission_weight"><?php _e( "Weight in KG:", 'wpmr' ); ?></label>
				<input onkeyup="calculateBmi()" class="form-control" type="text" name="wpmr_admission_weight" id="wpmr_admission_weight" value="<?php echo esc_attr( get_post_meta( $post->ID, 'wpmr_admission_weight', true ) ); ?>" size="30" />
			</div>
			<div class="col-md-6 form-group">
				<label class="wpmr-label" for="wpmr_admission_bmi"><?php _e( "BMI:", 'wpmr' ); ?></label>
				<input readonly class="form-control" type="text" name="wpmr_admission_bmi" id="wpmr_admission_bmi" value="<?php echo esc_attr( get_post_meta( $post->ID, 'wpmr_admission_bmi', true ) ); ?>" size="30" />
			</div>
			<div class="col-md-6 form-group">
				<label class="wpmr-label" for="wpmr_admission_bmi_result"><?php _e( "BMI Result:", 'wpmr' ); ?></label>
				<input readonly class="form-control" type="text" name="wpmr_admission_bmi_result" id="wpmr_admission_bmi_result" value="<?php echo esc_attr( get_post_meta( $post->ID, 'wpmr_admission_bmi_result', true ) ); ?>" size="30" />
			</div>
			<div class="col-md-6 form-group">
				<label class="wpmr-label" for="wpmr_admission_temperature"><?php _e( "Temperature:", 'wpmr' ); ?></label>
				<input class="form-control" type="text" name="wpmr_admission_temperature" id="wpmr_admission_temperature" value="<?php echo esc_attr( get_post_meta( $post->ID, 'wpmr_admission_temperature', true ) ); ?>" size="30" />
			</div>
			<div class="col-md-6 form-group">
				<label class="wpmr-label" for="wpmr_admission_pulse"><?php _e( "Pulse:", 'wpmr' ); ?></label>
				<input class="form-control" type="text" name="wpmr_admission_pulse" id="wpmr_admission_pulse" value="<?php echo esc_attr( get_post_meta( $post->ID, 'wpmr_admission_pulse', true ) ); ?>" size="30" />
			</div>
			<div class="col-md-6 form-group">
				<label class="wpmr-label" for="wpmr_admission_respiratory"><?php _e( "Respiratory rate:", 'wpmr' ); ?></label>
				<input class="form-control" type="text" name="wpmr_admission_respiratory" id="wpmr_admission_respiratory" value="<?php echo esc_attr( get_post_meta( $post->ID, 'wpmr_admission_respiratory', true ) ); ?>" size="30" />
			</div>
			<div class="col-md-6 form-group">
				<label class="wpmr-label" for="wpmr_admission_pressure"><?php _e( "Blood pressure:", 'wpmr' ); ?></label>
				<input class="form-control" type="text" name="wpmr_admission_pressure" id="wpmr_admission_pressure" value="<?php echo esc_attr( get_post_meta( $post->ID, 'wpmr_admission_pressure', true ) ); ?>" size="30" />
			</div>
		</div>
		<?php
	}

	/* Save the meta box's post metadata. */
	public function wpmr_save_admission_meta( $post_id, $post ) {

		/* Verify the nonce before proceeding. */
		if ( !isset( $_POST['wpmr_admission_meta_nonce'] ) || !wp_verify_nonce( $_POST['wpmr_admission_meta_nonce'], basename( __FILE__ ) ) )
		return $post_id;
	
		/* Get the post type object. */
		$post_type = get_post_type_object( $post->post_type );
	
		/* Check if the current user has permission to edit the post. */
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

		$meta_data = [
			'wpmr_admission_date',
			'wpmr_admission_discharge_date',
			'wpmr_admission_patient',
			'wpmr_admission_ailment',
			'wpmr_admission_notes',
			'wpmr_admission_episode',
			'wpmr_admission_height',
			'wpmr_admission_weight',
			'wpmr_admission_bmi',
			'wpmr_admission_bmi_result',
			'wpmr_admission_temperature',
			'wpmr_admission_pulse',
			'wpmr_admission_respiratory',
			'wpmr_admission_pressure',
		];

		foreach ( $meta_data as $data ) {
			/* Get the posted data and sanitize it for use as an HTML class. */
			$new_meta_value = ( isset( $_POST [$data ] ) ? sanitize_text_field( $_POST[ $data ] ) : '' );
				
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
		remove_meta_box( 'commentsdiv','admissions','normal' );
		remove_meta_box( 'commentstatusdiv','admissions','normal' );
	}

	function wpmr_enqueue_date_picker(){
		wp_enqueue_script(
			'field-date', 
			get_template_directory_uri() . '/admin/field-date.js', 
			array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ),
			time(),
			true
		);  
	
		wp_enqueue_style( 'jquery-ui-datepicker' );
	}

	public function wpmr_enter_title( $input ) {
		global $post_type;
		if( is_admin() && 'Enter title here' == $input && 'admissions' == $post_type )
			return 'Enter Admission\'s Name';
		return $input;
	}

	public function wpmr_patient_episodes_ajax() {
		if( ! isset( $_GET['patientId'] ) )
			return false;
		$patientId = wp_unslash( $_GET['patientId'] );
		$args = array(
			'posts_per_page'	 => -1,
			'post_type'			 => 'episodes',
			'meta_key'			 => 'wpmr_episode_patient',
			'meta_value'		 => $patientId
		);
		$query = new WP_Query( $args );
		$posts = $query->posts;
		echo wp_json_encode( $posts );
		wp_die();
	}
}