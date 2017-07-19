<?php

/*
 @package playpops for wordpress
 * @author Victor Mendez / Gowri Sankar.R / Vanleurth / AreYouPop
 * Requirements : Flash Player 2.0.2 or higher

	Plugin Name: Areyoupop PlayPops Lite for WordPress
	Plugin URL: http://areyoupop.com/playpops/
	Description: Get More Likes to Your Facebook pages by combining the power of YouTube and Wordpress Posts. Add a video and get more likes
	Version: 2.0.5 lite
	Author: AreYouPop
	Author URI: http://areyoupop.com/

*/

	// --------------------------------------------------
	// --------------------------------------------------
	// Comment this Error Debugging Section When Move to Production
	
	//ini_set('display_errors', true);
	
	// ** Turn off error reporting
	//error_reporting(0);
	
	// Report runtime errors
	//error_reporting(E_ALL | E_NOTICE | E_USER_NOTICE);
	
	// Report all errors
	//error_reporting(E_ALL);
	
	// Same as error_reporting(E_ALL);
	//ini_set("error_reporting", E_ALL);
	
	// Report all errors except E_NOTICE
	//error_reporting(E_ALL & ~E_NOTICE);
	
	// --------------------------------------------------
	// --------------------------------------------------

	if ( ! defined( "PATH_SEPARATOR" ) ) 
	{ 

		if ( strpos( $_ENV[ "OS" ], "Win" ) !== false ) 
			define( "PATH_SEPARATOR", ";" ); 
		else 
			define( "PATH_SEPARATOR", ":" ); 
	}

		
	//echo $path;
	// Determine path
	$path = dirname(__FILE__);
	set_include_path(get_include_path() . PATH_SEPARATOR . $path);
	
	require_once (dirname(__FILE__) . '/classes/settings.php');
	require_once (dirname(__FILE__) . '/classes/playpops_meta.php');
	
	define('PLAYPOPS_PLUGIN_NAME', plugin_basename(__FILE__));
	define('PLAYPOPS_PLUGIN_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);
	define('PLAYPOPS_PLUGIN_URL', plugins_url('/', __FILE__));
	
					
	if (!class_exists('PlayPops')) 
	{

		class PlayPops
		{
	
			// @var PlayPops
			static private $_instance = null;
	
			// Get PlayPops object
			// @return PlayPops
	
			static public function getInstance()
			{
				if (self::$_instance == null) 
				{
					self::$_instance = new PlayPops();
				}
	
				return self::$_instance;
			}
	
			// Construct  ---------------------------------------
			
			private function __construct()
			{
				
				register_activation_hook(PLAYPOPS_PLUGIN_NAME, array(&$this, 'mmPluginActivate'));
				register_deactivation_hook(PLAYPOPS_PLUGIN_NAME, array(&$this, 'mmPluginDeactivate'));
				register_uninstall_hook(PLAYPOPS_PLUGIN_NAME, array(PLAYPOPS_PLUGIN_DIR, 'mmPluginUninstall'));    
				
				//
				if (is_admin())
				{
					// Add the administracion scripts				
					add_action('admin_enqueue_scripts', array(&$this, 'mmScriptsAdmin'));
				}
				else
				{
										
					// Add jquery
					add_action('wp_enqueue_scripts', array(&$this, 'mmScriptsPlaypops'));
					add_action('wp_enqueue_scripts', array(&$this, 'mmStylesPlaypops'));			
					add_action('wp_footer', array(&$this, 'mmPlayPopsBody'));
				}
				
				// Initialize the mce areyoupop buttons
				add_shortcode('playpops', array(&$this, 'mmPlaypopsShortcode'));
				// add_action('admin_print_footer_scripts', array(&$this, 'mmCustomQuicktags'));
				add_action('admin_head', array(&$this, 'mmAddPlaypopsButton'));
			}
	
			// ------------------------------------------------------------
			// Functions
			// ------------------------------------------------------------
			public function mmPlayPopsBody()
			{
				?>
        		<script>
					window.fbAsyncInit = function() {
						FB.init({
						appId      : '682547871766419',
						xfbml      : true,
						version    : 'v2.3'
						});
					};
					
					(function(d, s, id){
						var js, fjs = d.getElementsByTagName(s)[0];
						if (d.getElementById(id)) {return;}
						js = d.createElement(s); js.id = id;
						js.src = "//connect.facebook.net/en_US/sdk.js";
						fjs.parentNode.insertBefore(js, fjs);
					}(document, 'script', 'facebook-jssdk'));
				</script>
        		<!--		
				<script type="text/javascript" src="https://www.youtube.com/iframe_api"></script>
        		-->
            	<?php
			}
			
			// Loading Scripts and Styles
			public function mmScriptsAdmin()
			{
				
				// Enqueue the color picker
				wp_enqueue_style( 'wp-color-picker' );
								
				wp_enqueue_script(
					'PlayPops-admin-script',
					plugin_dir_url(__FILE__) . 'js/playpops-admin-script.js', 
					array( 'wp-color-picker' ), 
					false, 
					true
				);
			}
	
			public function mmStylesPlaypops()
			{
				// Register the style like this for a plugin:
				wp_register_style( 'playpops-style', plugins_url( '/css/playpops-style.css', __FILE__ ), array(), '20150402', 'all' );
				wp_enqueue_style( 'playpops-style' );
			}
			
				
			public function mmScriptsPlaypops()
			{
				
				// load jquery
				wp_enqueue_script('jquery');
				wp_enqueue_script('jquery-ui-core');
								
				// Attach javascript to the bottom of body
				wp_enqueue_script(
					'playpopsbody',  // $handle
					plugin_dir_url(__FILE__) . 'js/playpops_events.js', // $src
					array(), // $deps
					false, // $ver
					true // $in_footer
				);
			}    
	
			// Plugin Activation and Deactivation
			// Activate plugin
			// * @return void
			// 
			
			public function mmPluginActivate()
			{

				$defaultSettings = array();

				$settings = get_option('playpops_general_settings');

				// header color
				if(!isset($settings['playpops_youtube_key']))
					$defaultSettings['playpops_youtube_key'] = "";
					
				// header color
				if(!isset($settings['playpops_header_color']))
					$defaultSettings['playpops_header_color'] = "#6495ED";
				
				// header text
				if(!isset($settings['playpops_header']))
					$defaultSettings['playpops_header'] = "Like Us in Facebook to Stay in Touch with Our Latest Update";
				
				// skip message text
				if(!isset($settings['PlayPops_skip_message']))
					$defaultSettings['PlayPops_skip_message'] = "Skip this Step and Continue Watching";
				
				// Dialog Transparency
				if(!isset($settings['PlayPops_transparency']))
					$defaultSettings['PlayPops_transparency'] = "0.7";
				
				// save options
				update_option('playpops_general_settings', $defaultSettings);       	
			}

			// Deactivate plugin
			// @return void
			public function mmPluginDeactivate()
			{
			}

			// Uninstall plugin
			// @return void
				
			static public function mmPluginUninstall()
			{
			}
			
			public function mmPlaypopsButtons() 
			{
				add_filter( "mce_external_plugins", "mmPlaypopsAddButtons" );
				add_filter( 'mce_buttons', 'mmtuts_register_buttons' );
			}

			public function mmPlaypopsAddButtons( $plugin_array ) 
			{
				$plugin_array['playpops'] = plugin_dir_url(__FILE__) . '/js/mmtuts-plugin.js';
				
				return $plugin_array;
			}

			public function mmtuts_register_buttons( $buttons ) 
			{
				array_push( $buttons, 'dropcap', 'showrecent' ); // dropcap', 'recentposts
				
				return $buttons;
			}
			
			public function mmPlaypopsShortcode($attr, $url) 
			{
			   	$match = array();
				
				// Define the default video dimensions
				extract(shortcode_atts(array(
       				'width' => '640',
       				'height' => '480'
   					), $attr));
				
				// Extract Video ID
				if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) 
				{
					$video_id = $match[1];
					
				}
								
				// Create new string for the embeded video
				$player = '<iframe width="' . $width . '" height="' . $height . '" src="https://www.youtube.com/embed/' . $video_id . '" frameborder="0" allowfullscreen></iframe>';
				
								
				return $player;
				
			}
			
			// Add Quicktags
			/*
			public function mmCustomQuicktags() 
			{

				if ( wp_script_is( 'quicktags' ) ) 
				{
					?>
					<script type="text/javascript">
                    QTags.addButton( 'special_quote', 'quote', '<blockquote class="special-quote">', '</blockquote>', 'b', 'Special Quote', 1 );
                    </script>
	                <?php
                }

			}
			*/
						
			// Add button in toolbar of mce 
			public function mmAddPlaypopsButton() 
			{
    
				global $typenow;
    
				// check user permissions
    			if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) 
				{
    				return;
    			}
    
				// verify the post type
    			if( ! in_array( $typenow, array( 'post', 'page' ) ) )
        			return;
					
				
				
				// check if WYSIWYG is enabled
    			if ( get_user_option('rich_editing') == true) 
				{
					// Add external script to the mce toolbar
					add_filter("mce_external_plugins", array(&$this, "mmAddMceButtonScript"));
					
					// Register mce buttons
					add_filter("mce_buttons", array(&$this, "mmRegisterMceButton"));
				}
			}
			
			// CHANGE THE BUTTON SCRIPT HERE
			public function mmAddMceButtonScript($plugin_array) 
			{
    							
				$plugin_array['playpops_button'] = plugins_url( '/js/playpops-button-mce.js', __FILE__ ); 
    		
				return $plugin_array;
			}
			
			// Register MCE playpops icon
			public function mmRegisterMceButton($buttons) 
			{
			   array_push($buttons, "playpops_button");
			   
			   return $buttons;
			}
		}
		// End Class
	}
	// End if
	
	//instantiate the class
	if (class_exists('PlayPops')) 
	{
		$PlayPops =  PlayPops::getInstance();
	}