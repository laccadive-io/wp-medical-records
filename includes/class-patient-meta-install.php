<?php

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

class WPMR_Meta {

    public static function run() {
        
    }

    /**
     * Installs table for badgemeta
     *
     */
    public static function patient_meta_install() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'wpmr_patientmeta';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // see wpdb_get_schema() in https://github.com/WordPress/WordPress/blob/master/wp-admin/includes/schema.php
        $max_index_length = 191;
        
        $install_query = "CREATE TABLE $table_name (
            meta_id bigint(20) unsigned NOT NULL auto_increment,
            patient_id bigint(20) unsigned NOT NULL default '0',
            meta_key varchar(255) default NULL,
            meta_value longtext,
            PRIMARY KEY  (meta_id),
            KEY patient (patient_id),
            KEY meta_key (meta_key($max_index_length))
        ) $charset_collate;";
        
        dbDelta( $install_query );
    }



    /**
     * Integrates patient_meta table with $wpdb
     *
     */
    public static function patient_meta_integrate_wpdb() {
        global $wpdb;
        
        $wpdb->patient_meta = $wpdb->prefix . 'wpmr_patientmeta';
        $wpdb->tables[] = 'wpmr_patientmeta';
        
        return;
    }
}