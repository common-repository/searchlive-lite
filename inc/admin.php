<?php
namespace Searchlive;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {exit; }

class SearchliveAdmin{

	public function __construct()
	{
		add_action('admin_init', array($this, 'addOptions'));
		add_action('admin_menu', array($this, 'addAdminMenu'));
		add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
	}
	
	public function enqueueScripts(){
		
		// SCRIPTS
		wp_register_script('searchlive-admin', SEARCHLIVE_ASSETS_URL . 'js/admin.js',['jquery'], SEARCHLIVE_PLUGIN_NAME, true);
		wp_enqueue_script( 'searchlive-admin' );
		
		
		// STYLES
		wp_enqueue_style( 'searchlive-admin', SEARCHLIVE_ASSETS_URL . 'css/admin.css', array(), SEARCHLIVE_PLUGIN_NAME, 'all' );
	
	}
	
	public static function addOptions(){
		// General Options
		register_setting('searchlive_options', 'searchlive_window_trigger', array('default' => ''));
	}
	
	public function addAdminMenu()
    {		
		add_submenu_page( 'options-general.php', 'Searchlive', 'Searchlive', 'manage_options', 'searchlive', array($this, 'settingsPage'), 7 );
    }
	
	public function settingsPage(){
	?>
		<div class="searchlive_settings_page">
			<form method="post" action="" class="searchlive_general_options">
				
				<?php settings_fields('searchlive_options'); do_settings_sections('searchlive_options'); ?>
			
				<div class="slive_title"><h1><?php echo esc_html__('Searchlive Settings', SEARCHLIVE_TEXT_DOMAIN); ?></h1></div>
				
				<div class="slive_section">
					<div class="slive_field">
						<div class="field_title"><label for="searchlive_window_trigger"><?php esc_html_e('Trigger Search Window', SEARCHLIVE_TEXT_DOMAIN); ?></label></div>
						<div class="field_content"><input id="searchlive_window_trigger" name="searchlive_window_trigger" type="text" value="<?php echo get_option('searchlive_window_trigger', ''); ?>" /></div>
						<div class="field_desc">
							<p><?php esc_html_e('Enter "id" or "class" of a trigger which opens search window when you click on it. If you want to use multiple triggers, please, enter their "id" or "class" and separate them with commas. Don\'t forget to use ( . ) before class name or ( # ) before id name.', SEARCHLIVE_TEXT_DOMAIN); ?></p>
						</div>
					</div>

					<div class="slive_save_btn">
						<a class="save_link button button-primary" href="#">
							<span class="save_text"><?php esc_html_e('Save Changes', SEARCHLIVE_TEXT_DOMAIN);?></span>
						</a>
					</div>
				</div>
				
				
			</form>
		</div>

	<?php
	}

}
new SearchliveAdmin;