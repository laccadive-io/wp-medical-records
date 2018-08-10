<?php 

/**
 * Adds metadata field to a Patient Meta.
 *
 * @param int    $patient_meta_id   Patient Meta ID.
 * @param string $meta_key   Metadata name.
 * @param mixed  $meta_value Metadata value.
 * @param bool   $unique     Optional, default is false. Whether the same key should not be added.
 * @return int|false Meta ID on success, false on failure.
 */
function add_patient_meta($patient_id, $meta_key, $meta_value, $unique = false) {
    return add_metadata('patient', $patient_id, $meta_key, $meta_value, $unique);
}

/**
 * Removes metadata matching criteria from a patient.
 *
 * You can match based on the key, or key and value. Removing based on key and
 * value, will keep from removing duplicate metadata with the same key. It also
 * allows removing all metadata matching key, if needed.
 *
 * @param int    $patient_id    Patient ID
 * @param string $meta_key   Metadata name.
 * @param mixed  $meta_value Optional. Metadata value.
 * @return bool True on success, false on failure.
 */
function delete_patient_meta($patient_id, $meta_key, $meta_value = '') {
    return delete_metadata('patient', $patient_id, $meta_key, $meta_value);
}
/**
 * Retrieve meta field for a Patient.
 *
 * @param int    $cf_case_id Patient ID.
 * @param string $key     Optional. The meta key to retrieve. By default, returns data for all keys.
 * @param bool   $single  Whether to return a single value.
 * @return mixed Will be an array if $single is false. Will be value of meta data field if $single is true.
 */
function get_patient_meta($patient_id, $key = '', $single = false) {
    return get_metadata('patient', $patient_id, $key, $single);
}
/**
 * Update Patient meta field based on Patient ID.
 *
 * Use the $prev_value parameter to differentiate between meta fields with the
 * same key and Patient ID.
 *
 * If the meta field for the user does not exist, it will be added.
 *
 * @param int    $patient_id   Patient ID.
 * @param string $meta_key   Metadata key.
 * @param mixed  $meta_value Metadata value.
 * @param mixed  $prev_value Optional. Previous value to check before removing.
 * @return int|bool Meta ID if the key didn't exist, true on successful update, false on failure.
 */
function update_patient_meta($patient_id, $meta_key, $meta_value, $prev_value = '') {
    return update_metadata('patient', $patient_id, $meta_key, $meta_value, $prev_value);
}

/**
 * Update Patient meta field based on Patient ID.
 *
 * Use the $prev_value parameter to differentiate between meta fields with the
 * same key and CF Case ID.
 *
 * If the meta field for the user does not exist, it will be added.
 *
 * @param string $meta_key   Metadata key.
 * @return int Meta key. If the key didn't exist, null on failure.
 */
function get_patient_meta_value_by_meta_key($meta_key) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wpmr_patientmeta';
    $query = "SELECT meta_value FROM $table_name WHERE meta_key = '$meta_key'";
    $patient_meta = $wpdb->get_var($query);
    return $patient_meta;
}

// call when a user is deleted
// add_action( 'trashed_post', 'wpmr_delete_patientmeta' );
// function wpmr_delete_patientmeta($patient_id) {
    
// 	delete_patient_meta($patient_id, $meta_key);
// }