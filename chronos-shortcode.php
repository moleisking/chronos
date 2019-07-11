<?php
 /**
  * @author Scott Johnston
  * @license https://www.gnu.org/licenses/gpl-3.0.html
  * @package Chronos
  * @version 1.0.0
 */

//defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );
class ChronosShortcode{

	public function __construct(){		
		add_action('init', array($this,'registerChronosShortcodes')); 				
		add_action('wp_enqueue_scripts', array($this,'__script_and_style'));
		add_action('wp_ajax_post_appointment_create', array($this,'post_appointment_create'));	
		add_action('wp_ajax_post_timeslot_create', array($this,'post_timeslot_create'));	
		add_action('wp_ajax_post_timeslot_delete', array($this,'post_timeslot_delete'));
	}

	public function __script_and_style(){
		wp_register_script('chronosScript', plugins_url( '/js/chronos.js', __FILE__ ), array('jquery','jquery-form'), '1.0', true);
		wp_enqueue_script('chronosScript');
		wp_localize_script('chronosScript','ajax_object',array( 'ajax_url' => admin_url("admin-ajax.php")));

		wp_register_style('chronosStyle', plugins_url( '/css/chronos.css', __FILE__ ), array(), '1.0',	'all');
		wp_enqueue_style('chronosStyle');

		wp_register_style('w3Style', plugins_url( '/css/w3.css', __FILE__ ), array(), '1.0',	'all');
		wp_enqueue_style('w3Style');
	}

	public function registerChronosShortcodes( $atts ) {		
		add_shortcode( 'chronos_timeslots', array($this ,'shortcode_timeslots' ) );	
		add_shortcode( 'chronos_appointments', array($this ,'shortcode_appointments' ) );	
		add_shortcode( 'chronos_appointment_create', array($this ,'shortcode_appointment_create' ) );
	}		

	public function error_dialog(){
		echo "<script>alert('Only one review allowed per user.')</script>";
	}	

	public function post_appointment_create(){	

		if  (is_user_logged_in() && isset($_POST['datetime']) && 
				isset($_POST['providerId']) && isset($_POST['uniqueId']) ) {	
			
			//Collect post information
			$consumerId = get_current_user_id();
			$data =  filter_var ($_POST['data'], FILTER_SANITIZE_SPECIAL_CHARS);
			$datetime =  filter_var ($_POST['datetime'.$uniqueId], FILTER_SANITIZE_NUMBER_INT);	
			$note = filter_var ($_POST['note'] , FILTER_SANITIZE_STRING);			
			$orderId =  filter_var ($_POST['orderId'], FILTER_SANITIZE_NUMBER_INT);	
			$productId =  filter_var ($_POST['productId'], FILTER_SANITIZE_NUMBER_INT);							
			$providerId =  filter_var ($_POST['providerId'], FILTER_SANITIZE_NUMBER_INT);	
			$timezone =  filter_var ($_POST['timezone'], FILTER_SANITIZE_STRING);	
			$type =  filter_var ($_POST['type'], FILTER_SANITIZE_STRING);
			$uniqueId =  filter_var ($_POST['uniqueId'], FILTER_SANITIZE_STRING);			
						
			//Write to database
			if ($senderId != $receiverId) {
										
				global $wpdb;
				$wpdb->insert( $wpdb->base_prefix.'appointments', 
					array( 	
						'consumerId' => $consumerId,
						'data' => $data,	
						'datetime' => $datetime,
						'productId' => $productId,	
						'orderId' => $orderId,
						'providerId' => $providerId,	
						'timezone' => $timezone,	
						'type' => $type,						
						'note' =>  $note
					) 
				);					

			} else {
				$this->error_dialog();
			}			
		} else {
			echo "<script>alert('User not logged in or missing user id.')</script>";			
		}
	}

	public function post_appointment_delete(){		

		if  (is_user_logged_in() && isset($_POST['id']) && 				
				isset($_POST['uniqueId']) ) {	
			
			//Collect post information
			$id =  filter_var ($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
			$userId = get_current_user_id();	
						
			//Write to database
			if ($id && $userId) {
										
				global $wpdb;
				$wpdb->delete( $wpdb->base_prefix.'appointments', 
					array( 	
						'ID' => $id,	
						'consumerId' => $userId	
					) 
				);					 

			} else {
				$this->error_dialog();
			}			
		} else {
			echo "<script>alert('User not logged in or missing user id.')</script>";			
		}
	}	

	public function post_timeslot_create(){		

		if  (is_user_logged_in() && isset($_POST['end']) && 
				isset($_POST['start']) && isset($_POST['type']) && 
					isset($_POST['uniqueId']) ) {	
			
			//Collect post information
			$data =  isset($_POST['data']) ? filter_var ($_POST['data'], FILTER_SANITIZE_SPECIAL_CHARS , array('options' => array('default' => NULL)) ) : NULL;				
			$end =  filter_var ($_POST['end'], FILTER_SANITIZE_NUMBER_INT);						
			$start =  filter_var ($_POST['start'], FILTER_SANITIZE_NUMBER_INT);	
			$timezone =  filter_var ($_POST['timezone'], FILTER_SANITIZE_STRING);	
			$type =  filter_var ($_POST['type'], FILTER_SANITIZE_STRING);
			$uniqueId =  filter_var ($_POST['uniqueId'], FILTER_SANITIZE_STRING);	
			$userId = get_current_user_id();	
						
			//Write to database
			if ($end && $start && $userId) {
										
				global $wpdb;
				$wpdb->insert( $wpdb->base_prefix.'timeslots', 
					array( 	
						'data' => $data,	
						'end' => $end,												
						'start' => $start,
						'timezone' => $timezone,	
						'type' => $type,	
						'userId' => $userId	
					) 
				);					 

			} else {
				$this->error_dialog();
			}			
		} else {
			echo "<script>alert('User not logged in or missing user id.')</script>";			
		}
	}	

	public function post_timeslot_delete(){		

		if  (is_user_logged_in() && isset($_POST['id']) && 				
				isset($_POST['uniqueId']) ) {	
			
			//Collect post information
			$id =  filter_var ($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
			$userId = get_current_user_id();	
						
			//Write to database
			if ($id && $userId) {
										
				global $wpdb;
				$wpdb->delete( $wpdb->base_prefix.'timeslots', 
					array( 	
						'ID' => $id,	
						'userId' => $userId	
					) 
				);	
				
			} else {
				$this->error_dialog();
			}			
		} else {
			echo "<script>alert('User not logged in or missing user id.')</script>";			
		}
	}	
	
	public function shortcode_appointments( $atts ) {		

		//Get options
		$appointment_cancel_limit = !empty(get_option('appointment_cancel_limit')) ? get_option('appointment_cancel_limit') : 1440; 
		$appointment_count_limit = !empty(get_option('appointment_count_limit')) ? get_option('appointment_count_limit') : 20; 
		$appointment_range_limit = !empty(get_option('appointment_range_limit')) ? get_option('appointment_range_limit') : 43200 ;
		
		//Extract lowercase only parameters from shortcode	
		$atts = shortcode_atts( array(
			'user_id' => null	
		), $atts, 'chronos_appointments' );
		$providerId = filter_var($atts['user_id'], FILTER_SANITIZE_NUMBER_INT); 
	
		//Query
		global $wpdb;
		$select = "SELECT * FROM ".$wpdb->base_prefix."appointments". 
			" WHERE consumerId =".$providerId." OR providerId =".$providerId.
			" ORDER BY timestamp ASC".
			" LIMIT 100";		
		$results = $wpdb->get_results($select);

		//Build HTML		
		$html .= "<div id='divAppointments' class='divAppointments w3-container'><br>";
			
		if (is_user_logged_in())	{

			// Appointment list
			if (sizeof($results) > 0){

				foreach ($results as $result) {	

					//Generate uniqueId id for frmAppointmentDelete
					$uniqueId = uniqid("StrPrefix");		

					$html .= "<div  id='divAppointment' class='divAppointment w3-cell-row'>".
						"<input type='datetime-local' id='datetime' name='datetime' class='w3-cell'>".$result->datetime."</div>".
						"<form id='frmAppointmentDelete".$uniqueId."' name='frmAppointmentDelete".$uniqueId."' class='frmAppointmentDelete' method='post' action='".admin_url('admin-ajax.php')."'>".
							"<input type='hidden' id='id' name='id' value='".$result->id."'>".
							"<input type='hidden' id='uniqueId' name='uniqueId' value='".$uniqueId."'>".
							"<input type='hidden' id='clientId' name='clientId' value='".$result->clientId."'>".
							"<input type='hidden' id='providerId' name='providerId' value='".$result->providerId."'>".
							"<input type='hidden' id='datetime".$uniqueId."' name='datetime".$uniqueId."' value='".$result->datetime."'>".
							"<input type='hidden' id='timezone".$uniqueId."' name='timezone".$uniqueId."' value='".$result->timezone."'>".
							"<input type='hidden' name='action' value='post_appointment_delete'>".	
						"</form>".
						//Post delete appointment
						"<div id='divAppointmentDelete' name='divAppointmentDelete' class='w3-cell'>".
							"<button id='btnAppointmentDelete' name='btnAppointmentDelete' type='submit' width='100%' form='frmAppointmentDelete".$uniqueId."'>Delete</button>".
						"</div>".
					"</div>";	
				}

			} else {
				$html .= "<div id='divAppointments'>Empty</div>";
			}												
		}						
				
		$html .= "</div>";		
   		
		return $html;
	}	

	public function shortcode_appointment_create( $atts ) {		

		//Get options
		$appointment_cancel_limit = !empty(get_option('appointment_cancel_limit')) ? get_option('appointment_cancel_limit') : 1440; 
		$appointment_count_limit = !empty(get_option('appointment_count_limit')) ? get_option('appointment_count_limit') : 20; 
		$appointment_range_limit = !empty(get_option('appointment_range_limit')) ? get_option('appointment_range_limit') : 43200 ;
		
		//Extract lowercase only parameters from shortcode	
		$atts = shortcode_atts( array(
			'user_id' => null	
		), $atts, 'chronos_appointments' );
		$providerId = filter_var($atts['user_id'], FILTER_SANITIZE_NUMBER_INT); 
	
		//Query
		global $wpdb;
		$select = "SELECT * FROM ".$wpdb->base_prefix."appointments". 
			" WHERE consumerId =".$userId." OR providerId =".$userId.
			" ORDER BY timestamp ASC".
			" LIMIT 100";		
		$results = $wpdb->get_results($select);

		//Build HTML		
		$html .= "<div id='divAppointments' class='divAppointments w3-container'><br>";
			
		if (is_user_logged_in())	{

			// Appointment list
			if (sizeof($results) > 0){

				foreach ($results as $result) {	

					//Generate uniqueId id for frmAppointmentDelete
					$uniqueId = uniqid("StrPrefix");		

					$html .= "<div  id='divAppointment' class='divAppointment w3-cell-row'>".
						"<input type='datetime-local' id='datetime' name='datetime' class='w3-cell'>".$result->datetime."</div>".
						"<form id='frmAppointmentDelete".$uniqueId."' name='frmAppointmentDelete".$uniqueId."' class='frmAppointmentDelete' method='post' action='".admin_url('admin-ajax.php')."'>".
							"<input type='hidden' id='id' name='id' value='".$result->id."'>".
							"<input type='hidden' id='uniqueId' name='uniqueId' value='".$uniqueId."'>".
							"<input type='hidden' id='clientId' name='clientId' value='".$result->clientId."'>".
							"<input type='hidden' id='providerId' name='providerId' value='".$result->providerId."'>".
							"<input type='hidden' id='datetime".$uniqueId."' name='datetime".$uniqueId."' value='".$result->datetime."'>".
							"<input type='hidden' id='timezone".$uniqueId."' name='timezone".$uniqueId."' value='".$result->timezone."'>".
							"<input type='hidden' name='action' value='post_appointment_delete'>".	
						"</form>".
						//Post delete appointment
						"<div id='divAppointmentDelete' name='divAppointmentDelete' class='w3-cell'>".
							"<button id='btnAppointmentDelete' name='btnAppointmentDelete' type='submit' width='100%' form='frmAppointmentDelete".$uniqueId."'>Delete</button>".
						"</div>".
					"</div>";	
				}

			} else {
				$html .= "<div id='divAppointments'>Empty</div>";
			}

			//Generate uniqueId id for frmAppointmentCreate
			$uniqueId = uniqid("StrPrefix");	

			//Create Appointment form
			$html .= "<div  id='divAppointment' class='w3-cell-row'>".
				"<div id='datetime' name='datetime' class='w3-cell'>".$datetime."</div>".
				"<form id='frmAppointmentCreate".$uniqueId."' name='frmAppointmentCreate".$uniqueId."' class='frmAppointmentCreate' method='post' action='".admin_url('admin-ajax.php')."'>".
					"<input type='hidden' id='uniqueId' name='uniqueId' value='".$uniqueId."'>".					
					"<input type='hidden' id='providerId' name='clientId' value='".$providerId."'>".
					"<input type='datetime-local' id='datetime".$uniqueId."' name='datetime".$uniqueId."' value='".$datetime."'>".
					"<input type='hidden' id='timezone".$uniqueId."' name='timezone".$uniqueId."' value='".$timezone."'>".
					"<input type='hidden' name='action' value='post_appointment_create'>".	
				"</form>".
				//Post create appointment
				"<div id='divAppointmentCreate' name='divAppointmentCreate' class='w3-cell'>".
					"<button id='btnAppointmentCreate' name='btnAppointmentCreate' type='submit' width='100%' form='frmAppointmentCreate".$uniqueId."'>Create</button>".
				"</div>".
			"</div>";										
		}						
				
		$html .= "</div>";		
   		
		return $html;
	}	

	public function shortcode_timeslots( $atts ) {		

		//Get options
		$timeslot_buffer = !empty(get_option('timeslot_buffer')) ? get_option('timeslot_buffer') : 1440; 
		$timeslot_interval = !empty(get_option('timeslot_interval')) ? get_option('timeslot_interval') : 60; 
		$timeslot_field_name = !empty(get_option('timeslot_field_name')) ? get_option('timeslot_field_name') : 'custom_field_timeslot' ;
		$utc_field_visible = !empty(get_option('utc_field_visible')) ? get_option('utc_field_visible') : false;  		

		//Extract lowercase only parameters from shortcode	
		$atts = shortcode_atts( array(
			'user_id' => null	
		), $atts, 'chronos_timeslots' );
		$userId = filter_var($atts['user_id'], FILTER_SANITIZE_NUMBER_INT); 		

		//Query
		global $wpdb;
		$select = "SELECT * FROM ".$wpdb->base_prefix."timeslots". 
			" WHERE userId =".$userId. 
			" ORDER BY timestamp ASC".
			" LIMIT 100";		
		$results = $wpdb->get_results($select);
		
		//Build HTML		
		$html .= "<div id='divTimeslots' class='divTimeslots w3-container'>";
			
		if (is_user_logged_in())	{

			// Timeslot list
			if (sizeof($results) > 0){

				foreach ($results as $result) {	

					//Generate uniqueId id for frmTimeslotDelete
					$uniqueId = uniqid("StrPrefix");

					$start = ( strlen($result->start) > 3 ) ? substr($result->start, 0, 2).":".substr($result->start, 2, 2) : "0".substr($result->start, 0, 1).":".substr($result->start, 1, 2);
					$end = ( strlen($result->end) > 3 ) ? substr($result->end, 0, 2).":".substr($result->end, 2, 2) : "0".substr($result->end, 0, 1).":".substr($result->end, 1, 2);

					$html .= "<div  id='divTimeslot' class='divTimeslot w3-cell-row'>".									
						"<form id='frmTimeslotDelete".$uniqueId."' name='frmTimeslotDelete".$uniqueId."' class='frmTimeslotDelete' method='post' action='".admin_url('admin-ajax.php')."'>".							
							"<select id='type' name='type' value='".$result->type."'>".
								"<option value='monday'>Monday</option>".
								"<option value='tuesday'>Tuesday</option>".
								"<option value='wednesday'>Wednesday</option>".
								"<option value='thursday'>Thursday</option>".
								"<option value='friday'>Friday</option>".
								"<option value='saturday'>Saturday</option>".
								"<option value='sunday'>Sunday</option>".
							"</select>".							
							"<input id='start' name='start' type='time' value='".$start."'> ".
							"<input id='end' name='end' type='time' value='".$end."'> ".	
							"<input id='id' name='id' type='hidden' value='".$result->id."'>".	
							"<input id='uniqueId' name='uniqueId' type='hidden' value='".$uniqueId."'>".
							"<input id='userId' name='userId' type='hidden' value='".$result->userId."'>".							
							"<input id='timezone' name='timezone' type='hidden' value='".$result->timezone."'>".
							"<input name='action' type='hidden' value='post_timeslot_delete'>".	
						"</form>".						
						"<button id='btnTimeslotDelete' name='btnTimeslotDelete' class='btnTimeslot' type='submit' form='frmTimeslotDelete".$uniqueId."'>Delete</button>".					
					"</div>";	
				}

			} else {
				$html .= "<div id='divTimeslots'>Empty</div>";
			}

			//Generate uniqueId id for frmTimeslotCreate
			$uniqueId = uniqid("StrPrefix");

			//Create Timeslot form
			$html .= "<div  id='divTimeslot' class='divTimeslot w3-row'>".				
				"<form id='frmTimeslotCreate".$uniqueId."' name='frmTimeslotCreate".$uniqueId."' class='frmTimeslotCreate' method='post' action='".admin_url('admin-ajax.php')."'>".
					"<select id='type' name='type' value='monday'>".
						"<option value='monday'>Monday</option>".
						"<option value='tuesday'>Tuesday</option>".
						"<option value='wednesday'>Wednesday</option>".
						"<option value='thursday'>Thursday</option>".
						"<option value='friday'>Friday</option>".
						"<option value='saturday'>Saturday</option>".
						"<option value='sunday'>Sunday</option>".
					"</select>".					
					"<input id='start' name='start' type='time' value='09:00'>".
					"<input id='end' name='end' type='time' value='21:00'>".		
					"<input id='uniqueId' name='uniqueId' type='hidden' value='".$uniqueId."'>".									
					"<input id='timezone' name='timezone' type='hidden' value='".get_option('timezone_string')."'>".
					"<input name='action' type='hidden' value='post_timeslot_create'>".	
				"</form>".				
				"<button id='btnTimeslotCreate' name='btnTimeslotCreate' class='btnTimeslotCreate' type='submit' form='frmTimeslotCreate".$uniqueId."'>Create</button>".		
			"</div>";										
		}						
				
		$html .= "</div>";			
   		
		return $html;
	}	
		
}	

$chronosShortcode = new ChronosShortcode();
?>