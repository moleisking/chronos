<?php
 /*
 * Plugin Name: Chronos
 * Plugin URI: https://www.espeaky.com
 * Description: A wordpress plugin that adds the ability to book appointments with users by user id.
 * Author: Scott Johnston
 * Author URI: https://www.linkedin.com/in/scott8johnston/
 * Version: 1.0.0
 * License: GPLv2 or later
 */

 /**
  * @author Scott Johnston
  * @license https://www.gnu.org/licenses/gpl-3.0.html
  * @package Chronos
  * @version 1.0.0
 */

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

class Chronos {	

	public function __construct(){		
		register_activation_hook(__FILE__, array($this, 'plugin_activate')); 
		register_deactivation_hook(__FILE__, array($this, 'plugin_deactivate')); 
	}		

	public function plugin_activate(){
		flush_rewrite_rules();	
		Chronos::create_table();		
	}

	public function plugin_deactivate(){
		flush_rewrite_rules();		
	}

	private static function create_table(){		

		//Setup database access
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		//Create appointment table
		$createAppointments = "CREATE TABLE IF NOT EXISTS ".$wpdb->base_prefix."appointments (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			consumerId bigint NOT NULL,	
			data json NULL,		
			datetime bigint NOT NULL,	
			note varchar(255) NULL,		
			productId bigint NULL,		
			providerId bigint NOT NULL,	
			orderId bigint NULL,	
			timezone varchar(20) NOT NULL,	
			timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			type varchar(10) NOT NULL,
			PRIMARY KEY  (id)
		  ) ".$charset_collate.";";
		dbDelta($createAppointments);

		//Create timeslot table		
		$createTimeslots = "CREATE TABLE IF NOT EXISTS ".$wpdb->base_prefix."timeslots (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,	
			data json NULL,	
			end bigint NOT NULL,
			start bigint NOT NULL,	
			timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			timezone varchar(20) NOT NULL,
			type varchar(10) NOT NULL,
			userId bigint NOT NULL,	
			PRIMARY KEY  (id)
		  ) ".$charset_collate.";";
		dbDelta($createTimeslots);			
	}	
}

include(plugin_dir_path(__FILE__) . 'chronos-admin.php');

include(plugin_dir_path(__FILE__) . 'chronos-api.php');

include(plugin_dir_path(__FILE__) . 'chronos-shortcode.php');

include(plugin_dir_path(__FILE__) . 'chronos-widget.php');

$chronos = new Chronos;
?>