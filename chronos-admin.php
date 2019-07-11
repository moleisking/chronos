<?php
 /**
  * @author Scott Johnston
  * @license https://www.gnu.org/licenses/gpl-3.0.html
  * @package Chronos
  * @version 1.0.0
 */

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

class ChronosAdmin{		

    private $option_group =  'chronos-config-group';

	public function __construct(){

        add_action( 'admin_menu', array($this,'add_menu') );
        add_action( 'admin_init', array($this, 'register_configure_parameters') );
    }
    
    function register_configure_parameters() {         
       
        register_setting( $this->option_group, 'appointment_cancel_limit', array('integer', 'appointment cancel limit in minutes',null ,false , 1440) ); 
        register_setting( $this->option_group, 'appointment_count_limit', array('integer', 'appointment limit in minutes',null ,false , 20) ); 
        register_setting( $this->option_group, 'appointment_range_limit', array('integer', 'appointment range limit in minutes',null ,false , 43200) ); 
        register_setting( $this->option_group, 'timeslot_buffer', array('integer', 'timeslot buffer in minutes',null ,false , 1440) ); 
        register_setting( $this->option_group, 'timeslot_interval', array('integer', 'timeslot interval in minutes',null ,false , 60) );  
        register_setting( $this->option_group, 'timeslot_field_name', array('string', 'timeslot field name',null ,false , 'custom_field_timeslot') );  
        register_setting( $this->option_group, 'utc_field_visible', array('boolean', 'utc_field_visible',null ,false , '0') ); 
    }    

	function add_menu() {

        $menu_title = 'chronos-info-page';
        $capability = 'manage_options';
		add_menu_page( 'Info', 'Chronos', $capability, $menu_title, array($this, 'add_info_page'), 'dashicons-calendar-alt', 4 );        
        add_submenu_page( $menu_title, 'Chronos Look and Feel', 'Configuration', $capability, 'chronos-configuration-page' , array($this, 'add_configuration_page') );	        				
	}

	public function add_info_page(){
        
        $plugin_data = get_plugin_data( plugin_dir_path(__FILE__).'chronos.php') ;
        echo "<h1>".$plugin_data["Name"]." Info</h1>";       
		echo "<p>".$plugin_data["Description"]."</p>";        
        ?>
        <h2>Checklist</h2>
        <ol>
            <li>By default this plugin uses the user meta field custom_field_timeslot to store the users timeslots.</li>             
        </ol>       
        <h2>Examples</h2>
        <ul>
            <li><code>[chronos_timeslots userId=1]</code></li>  
            <li><code>[chronos_appointments userId=60]</code></li> 
        </ul>        
        <h2>Plugin</h2>
        <ul>        
            <li>Version:<?php echo $plugin_data["Version"];  ?></li> 
            <li>URL: <a href='<?php echo $plugin_data["PluginURI"];  ?>'><?php echo $plugin_data["Name"] ?></a></li>
        </ul>
        <?php 
       
	}
    
    public function add_configuration_page(){	
        ?>

        <h1>Chronos Configure</h1>
            <form method='post' action='options.php'>	
            <?php settings_fields( $this->option_group ); ?>
            <?php do_settings_sections( $this->option_group ); ?>	

                <h2>Appointment rules</h2>

                <h3>Appointment interval</h3>
                <p>
                    <input name="appointment_cancel_limit" type='number' min='1' max='1440' 
                        value="<?php echo (!empty(get_option('appointment_cancel_limit'))) ? get_option('appointment_cancel_limit') : "1440"; ?>" 
                        placeholder = "timeslot interval in minutes"> minutes
                </p>	

                <h3>Appointment count limit</h3>
                <p>
                    <input  name="appointment_count_limit" type='number' min='1' max='525960' 
                        value="<?php echo (!empty(get_option('appointment_count_limit'))) ? get_option('appointment_count_limit') : "20"; ?>" 
                        placeholder = "appointment count limit in minutes"> minutes
                </p>     

                <h3>Appointment range limit</h3>
                <p>
                    <input  name="appointment_range_limit" type='number' min='1' max='525960' 
                        value="<?php echo (!empty(get_option('appointment_range_limit'))) ? get_option('appointment_range_limit') : "43200"; ?>" 
                        placeholder = "appointment range limit in minutes"> minutes
                </p>              

                <h2>Timeslot rules</h2>

                <h3>Timeslot buffer</h3>
                <p>
                    <input  name="timeslot_buffer" type='number' min='1' max='525960' 
                        value="<?php echo (!empty(get_option('timeslot_buffer'))) ? get_option('timeslot_buffer') : "1440"; ?>" 
                        placeholder = "timeslot buffer in minutes"> minutes
                </p>  

                <h3>Timeslot interval</h3>
                <p>
                    <input name="timeslot_interval" type='number' min='1' max='1440' 
                        value="<?php echo (!empty(get_option('timeslot_interval'))) ? get_option('timeslot_interval') : "60"; ?>" 
                        placeholder = "timeslot interval in minutes"> minutes
                </p>	                              

                <h3>UTC field visible</h3>
                <p>UTC Visible <input  name="utc_field_visible" type='checkbox' value='1' 
                    <?php checked( '1', get_option( 'utc_field_visible' ) ); ?> >
                </p>

                <h2>3rd party</h2>

                <h3>User timeslot custom field</h3>               
                <p><input  name="timeslot_field_name" type='text' 
                value="<?php 
                echo (!empty(get_option('timeslot_field_name'))) ? filter_var ( get_option('timeslot_field_name') , FILTER_SANITIZE_EMAIL ) :  "timeslot_field_review"; 
                ?>" placeholder = "timeslot field name"></p>               

                <?php submit_button(); ?>			
            </form>

         <?php
	}
}

$chronosAdmin = new ChronosAdmin;
?>