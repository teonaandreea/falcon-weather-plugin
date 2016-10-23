<?php
/*
Plugin Name: Falcon Weather Plugin
Description: Displays weather information for all the cities where Falcon has offices - Copenhagen, New York, Berlin, Budapest.
Version:     1.0
Author:      Teona
License:     GPL2

*/

$plugin_url = WP_PLUGIN_URL . '/falcon-weather';
$options = array();
$display_json = true;


/* Creating the options page*/

function falcon_weather_menu() {
    add_options_page( 'Falcon Weather Plugin', 
                     'Falcon Weather', 
                     'manage_options',
                     'falcon-weather', 
                     'falcon_weather_options_page' );
}

add_action( 'admin_menu', 'falcon_weather_menu' );

/* Creating the settings page and structuring it into one section with one field*/

function falcon_weather_init() {
    add_settings_section( 'section_one', 'How the plugin works', 'section_one_callback', 'falcon-weather' );
    add_settings_field( 'field_one', 'Insert your API key', 'field_one_callback', 'falcon-weather', 'section_one' );
    register_setting( 'falcon_weather_settings_group', 'falcon_weather_setting' );
}

add_action( 'admin_init', 'falcon_weather_init' );

/* Function for the settings section*/

function section_one_callback() {
    echo "<p id='explanation'>This plugin displays weather information from cities where Falcon has offices - Copenhagen, New York, Berlin and Budapest. <br><br>
    It includes a shortcode - [falcon_weather], that must be included on the page you want the information to be displayed.<br> You can customize it, by adding the attributes city_name (Copenhagen, New York, Berlin and Budapest), wind_speed (on/off) and cloud_coverage (on/off).<br> For example [falcon_weather city_name='Copenhagen' cloud_coverage='off']. By default all of the cities and weather information are displayed.<br><br>
    
    In order to access the weather information, Open Weather Map API was used. For the weather information to be retrieved on your website,<br> you need to get your API key by signing up here: <a href='https://home.openweathermap.org/users/sign_up' target='_blank'>https://home.openweathermap.org/users/sign_up</a> <br> and then entering your key in the field below.
    
    </p>";
} 

/* Function for the settings field*/

function field_one_callback() {
    $setting = esc_attr( get_option( 'falcon_weather_setting' ) );
    echo "<input type='text' name='falcon_weather_setting' value='' />";
}

/* Function for the options page*/

function falcon_weather_options_page() {
    ?>

  <!--Creating a form where users will enter their API Key-->
    <div class="wrap">
        <h2>Falcon Weather Settings</h2>

        
        <form name="falcon_weather_settings_form" method="post" action="">
    
        <input type="hidden" name="falcon_weather_form_submitted" value="Y">

        <table class="form-table">
            <tr>
                <td class="row"><label for="falcon_weather_setting"><?php settings_fields( 'falcon_weather_settings_group' ); ?></label></td>
                <td><?php do_settings_sections( 'falcon-weather' ); ?></td>
            </tr>

        </table>

                <?php submit_button($text = 'Submit', $type = 'primary', $name = 'falcon_weather_setting_submit'); ?>
    </form>
    </div>
    <?php
    

   
    /* Storing the values sent by the form in a variable 
    -for later use, in order to get the JSON file from the Open Weather Map API-  */
    
    if( !current_user_can('manage_options')) {
        
        wp_die('You do not have permission to access this page');
    }
    
    global $plugin_url;
    global $options;
    global $display_json;
    
    if (isset($_POST['falcon_weather_form_submitted'])) {
        
        $hidden_field = esc_html($_POST['falcon_weather_form_submitted']);
        
        if($hidden_field == 'Y') {
            
            $falcon_weather_setting = esc_html ($_POST['falcon_weather_setting']);
            
            $falcon_weather_profile = falcon_weather_get_profile($falcon_weather_setting);
            
            $options['falcon_weather_setting'] = $falcon_weather_setting;
            $options ['falcon_weather_profile'] = $falcon_weather_profile;
            $options['last_updated'] = time();
            
            update_option ('falcon_weather', $options);
        }
    }
    
    $options = get_option('falcon_weather');
    if($options != '') {
        
        $falcon_weather_setting = $options['falcon_weather_setting'];
        $falcon_weather_profile = $options['falcon_weather_profile'];
    }

}

function falcon_weather_get_profile($falcon_weather_setting){
    
    $json_feed_url = 'http://api.openweathermap.org/data/2.5/group?id=2618425,5128638,2950159,3054643&units=metric&APPID=' .$falcon_weather_setting;
    $args = array ('timeout' => 120);
    
    $json_feed = wp_remote_get($json_feed_url, $args);
    
    $falcon_weather_profile = json_decode($json_feed['body']);
    
    return $falcon_weather_profile;
    
    
}


function falcon_weather_shortcode($atts, $content = null){
    
    global $post;
    extract(shortcode_atts(array(
        'city_name' => 'NULL',
        'wind_speed' =>'on',
        'cloud_coverage' => 'on'
    
    ),$atts));
    
    if($city_name == 'copenhagen' || $city_name == 'Copenhagen' ) $city_name = 1;
    if($city_name == 'new york'|| $city_name == 'New York') $city_name = 2;
    if($city_name == 'berlin'|| $city_name == 'Berlin') $city_name = 3;
    if($city_name == 'budapest'|| $city_name == 'Budapest') $city_name = 4;
    if($city_name == 'NULL') $city_name = NULL;
    
    $display_city_name = $city_name;
    
    
    if($wind_speed == 'on') $wind_speed = 1;
    if($wind_speed == 'off') $wind_speed = 0;
    
    $display_wind_speed = $wind_speed;
    
    if($cloud_coverage == 'on') $cloud_coverage = 1;
    if($cloud_coverage == 'off') $cloud_coverage = 0;
    
    $display_cloud_coverage = $cloud_coverage;
    
    
    $options = get_option('falcon_weather');
    $falcon_weather_profile = $options['falcon_weather_profile'];
    
    ob_start();
    
    require('front-end.php');
    
    $content = ob_get_clean();
    return $content;
}

add_shortcode('falcon_weather','falcon_weather_shortcode');


function falcon_weather_style(){
    
     wp_enqueue_style('wptreehouse_badges_backend_css', plugins_url ('/falcon-weather/style.css'));
}

add_action ('wp_enqueue_scripts','falcon_weather_style');


?>