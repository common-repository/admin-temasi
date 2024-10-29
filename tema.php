<?php
/*
Plugin Name: Admin Teması - Eticoxs
Plugin URI: http://eticoxs.com
Description: Wordpress yönetim çubuğunu, menü, oturum açma, altbilgi, simge ve renkleri değiştirme
Version: 1.0.5
Author: Barış AKTAŞ
Author URI: www.eticoxs.com
Text Domain: tema
Domain Path: /languages
*/

class tema {

	private  $menus
			,$submenus
			,$settings
			,$settings_name = 'wp_tema_option'
			,$page_name = 'Admin Teması - Eticoxs'
			,$plugin_url = ''
			,$plugin_path = ''
			;

	function __construct() {
		// ini_set('error_reporting', E_ALL);
		// add to menu and load basic css		
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_init', array( $this, 'process_settings_import' ) );
		add_action( 'admin_init', array( $this, 'process_settings_export' ) );

		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		// get it work!
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'admin_print_footer_scripts', array( $this, 'admin_footer_scripts' ) );
		add_action( 'admin_bar_menu', array( $this, 'admin_bar'), 999 );
		add_filter( 'parent_file', array( $this, 'admin_menu' ) );
		add_filter( 'admin_footer_text', array( $this, 'admin_footer' ) );
		add_filter( 'update_footer', array( $this, 'admin_footer' ), 999 );
		add_action( 'login_enqueue_scripts', array( $this, 'login_style' ), 10 );
		add_action( 'login_enqueue_scripts', array( $this, 'login_script' ), 1 );
		// remove the google webfont
		add_filter( 'gettext_with_context', array( $this, 'disable_open_sans' ), 888, 4 );
		// register_deactivation_hook( __FILE__, array($this, "deactivation"));
		$this->plugin_url  = plugins_url('',__FILE__).'/';
		$this->plugin_path = plugin_dir_path( __FILE__ );
	}

	// add plugin to settings menu
	function add_menu() {
		//print_r(get_option( $this->settings_name ));
		$active = true;
		if ( is_multisite() ) {
			$this->settings = get_blog_option(1, $this->settings_name );
			if(get_current_blog_id() != 1){
				if($this->get_setting('network') == true){
					$active = false;
				}else{
					$this->settings = get_option( $this->settings_name );
				}
			}
		}else{
			$this->settings = get_option( $this->settings_name );
		}
		if($active){
			$page = add_submenu_page( 'options-general.php', $this->page_name, $this->page_name, 'manage_options', 'tema-admin', array( $this, 'settings' ) ); 
			add_action('load-'.$page, array( $this, 'admin_help' ));
		}
	}

	// register
	function register_settings() {
		register_setting( 'tema-admin-group', $this->settings_name );
		add_filter( 'pre_update_option_'.$this->settings_name, array($this, 'update_variables'), 10, 2 );
	}

	// get setting
	function get_setting($arg){
		$ptr = $this->settings;
	    foreach (func_get_args() as $arg) {
	        if (!is_array($ptr) || !is_scalar($arg) || !isset($ptr[$arg])) {
	            return NULL;
	        }
	        $ptr = $ptr[$arg];
	    }
	    return $ptr;
	}

	// settings
	function settings() {
		global $nav;
		global $subnav;
		?>
		<div class="wrap">
			<h2><?php echo $this->page_name; ?></h2>
			<div class="row clearfix">
				<form method="post" id="form" action="options.php">
				<?php settings_fields( 'tema-admin-group' ); ?>
				<div class="col col-4">
					<h3 class="m-b"><span>Colors</span></h3>
					<p class="no-m-t text-sm">
						<label>
							<input name="<?php echo $this->settings_name; ?>[use-default-color]" type="checkbox" <?php if ($this->get_setting('use-default-color') == true) echo 'checked="checked" '; ?>> 
							Use default color
						</label>
					</p>
					<div class="box">
						<h4 class="active"><span>Default colors</span></h4>
						<div class="box-body b-t hide show">
							<div class="color-selector">
								<div>
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="-120 -120 240 240" width="70" height="70">
										<g>
											<path fill="#594f8d" d="M7.34788079488412e-15,-120A120,120 0 0,1 103.92304845413264,59.99999999999998L59.38459911664722,34.28571428571427A68.57142857142857,68.57142857142857 0 0,0 4.198789025648068e-15,-68.57142857142857Z" style="stroke: rgb(255, 255, 255);"></path>
											<path fill="#fbb82a" d="M103.92304845413264,59.99999999999998A120,120 0 0,1 -103.92304845413261,60.00000000000004L-59.38459911664721,34.285714285714306A68.57142857142857,68.57142857142857 0 0,0 59.38459911664722,34.28571428571427Z" style="stroke: rgb(255, 255, 255);"></path>
											<path fill="#3cc5f1" d="M-103.92304845413261,60.00000000000004A120,120 0 0,1 -2.2043642384652355e-14,-120L-1.2596367076944203e-14,-68.57142857142857A68.57142857142857,68.57142857142857 0 0,0 -59.38459911664721,34.285714285714306Z" style="stroke: rgb(255, 255, 255);"></path>
										</g>
									</svg>
									<p>
										<label>
											<input name="<?php echo $this->settings_name; ?>[default-color]" type="radio" value="purple" <?php if ($this->get_setting('default-color') == 'purple') echo 'checked="checked" '; ?>> 
											Purple
										</label>
									</p>
								</div>
								<div>
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="-120 -120 240 240" width="70" height="70">
										<g>
											<path fill="#222222" d="M7.34788079488412e-15,-120A120,120 0 0,1 103.92304845413264,59.99999999999998L59.38459911664722,34.28571428571427A68.57142857142857,68.57142857142857 0 0,0 4.198789025648068e-15,-68.57142857142857Z" style="stroke: rgb(255, 255, 255);"></path>
											<path fill="#f1f1f1" d="M103.92304845413264,59.99999999999998A120,120 0 0,1 -103.92304845413261,60.00000000000004L-59.38459911664721,34.285714285714306A68.57142857142857,68.57142857142857 0 0,0 59.38459911664722,34.28571428571427Z" style="stroke: rgb(255, 255, 255);"></path>
											<path fill="#c62727" d="M-103.92304845413261,60.00000000000004A120,120 0 0,1 -2.2043642384652355e-14,-120L-1.2596367076944203e-14,-68.57142857142857A68.57142857142857,68.57142857142857 0 0,0 -59.38459911664721,34.285714285714306Z" style="stroke: rgb(255, 255, 255);"></path>
										</g>
									</svg>
									<p>
										<label>
											<input name="<?php echo $this->settings_name; ?>[default-color]" type="radio" value="black" <?php if ($this->get_setting('default-color') == 'black') echo 'checked="checked" '; ?>> 
											Black
										</label>
									</p>
								</div>
								<div>
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="-120 -120 240 240" width="70" height="70">
										<g>
											<path fill="#fafafa" d="M7.34788079488412e-15,-120A120,120 0 0,1 103.92304845413264,59.99999999999998L59.38459911664722,34.28571428571427A68.57142857142857,68.57142857142857 0 0,0 4.198789025648068e-15,-68.57142857142857Z" style="stroke: rgb(255, 255, 255);"></path>
											<path fill="#3cc5f1" d="M103.92304845413264,59.99999999999998A120,120 0 0,1 -103.92304845413261,60.00000000000004L-59.38459911664721,34.285714285714306A68.57142857142857,68.57142857142857 0 0,0 59.38459911664722,34.28571428571427Z" style="stroke: rgb(255, 255, 255);"></path>
											<path fill="#f1f1f1" d="M-103.92304845413261,60.00000000000004A120,120 0 0,1 -2.2043642384652355e-14,-120L-1.2596367076944203e-14,-68.57142857142857A68.57142857142857,68.57142857142857 0 0,0 -59.38459911664721,34.285714285714306Z" style="stroke: rgb(255, 255, 255);"></path>
										</g>
									</svg>
									<p>
										<label>
											<input name="<?php echo $this->settings_name; ?>[default-color]" type="radio" value="white" <?php if ($this->get_setting('default-color') == 'white') echo 'checked="checked" '; ?>> 
											White
										</label>
									</p>
								</div>
							</div>
						</div>
					</div>
					<div class="box">
						<h4 class="b-b"><span>Global</span></h4>
						<div class="box-body b-b hide">
							<div class="color-picker">
								<p>
									<label>Base color
										<input name="<?php echo $this->settings_name; ?>[color][base-color]" value="<?php esc_html_e( $this->get_setting('color', 'base-color')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>
								<p>
									<label>Text color
										<input name="<?php echo $this->settings_name; ?>[color][text-color]" value="<?php esc_html_e( $this->get_setting('color', 'text-color')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>
								<p>
									<label>Icon color
										<input name="<?php echo $this->settings_name; ?>[color][icon-color]" value="<?php esc_html_e( $this->get_setting('color', 'icon-color')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>

								<p>
									<label>Highlight color
										<input name="<?php echo $this->settings_name; ?>[color][highlight-color]" value="<?php esc_html_e( $this->get_setting('color', 'highlight-color')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>

								<p>
									<label>Notification color
										<input name="<?php echo $this->settings_name; ?>[color][notification-color]" value="<?php esc_html_e( $this->get_setting('color','notification-color')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>


								<p>
									<label>Body background
										<input name="<?php echo $this->settings_name; ?>[color][body-background]" value="<?php esc_html_e( $this->get_setting('color','body-background')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>

								<p>
									<label>Link color
										<input name="<?php echo $this->settings_name; ?>[color][link]" value="<?php esc_html_e( $this->get_setting('color','link')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>

								<p>
									<label>Form checked
										<input name="<?php echo $this->settings_name; ?>[color][form-checked]" value="<?php esc_html_e( $this->get_setting('color','form-checked')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>

								<p>
									<label>Button color
										<input name="<?php echo $this->settings_name; ?>[color][button-color]" value="<?php esc_html_e( $this->get_setting('color','button-color')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>

								<p>
									<label>Secondary button color
										<input name="<?php echo $this->settings_name; ?>[color][button-secondary]" value="<?php esc_html_e( $this->get_setting('color','button-secondary')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>

								<p>
									<label>Secondary button text
										<input name="<?php echo $this->settings_name; ?>[color][button-secondary-text]" value="<?php esc_html_e( $this->get_setting('color','button-secondary-text')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>
							</div>
						</div>
						<h4 class="b-b active"><span>Menu</span></h4>
						<div class="box-body b-b hide">
							<div class="color-picker">
								<p>
									<label>Menu text color
										<input name="<?php echo $this->settings_name; ?>[color][menu-text]" value="<?php esc_html_e( $this->get_setting('color','menu-text')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>
								<p>
									<label>Menu icon color
										<input name="<?php echo $this->settings_name; ?>[color][menu-icon]" value="<?php esc_html_e( $this->get_setting('color','menu-icon')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>
								<p>
									<label>Menu background
										<input name="<?php echo $this->settings_name; ?>[color][menu-background]" value="<?php esc_html_e( $this->get_setting('color','menu-background')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>

								<p>
									<label>Menu highlight text color
										<input name="<?php echo $this->settings_name; ?>[color][menu-highlight-text]" value="<?php esc_html_e( $this->get_setting('color','menu-highlight-text')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>
								<p>
									<label>Menu highlight icon color
										<input name="<?php echo $this->settings_name; ?>[color][menu-highlight-icon]" value="<?php esc_html_e( $this->get_setting('color','menu-highlight-icon')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>
								<p>
									<label>Menu highlight background
										<input name="<?php echo $this->settings_name; ?>[color][menu-highlight-background]" value="<?php esc_html_e( $this->get_setting('color','menu-highlight-background')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>

								<p>
									<label>Menu current text color
										<input name="<?php echo $this->settings_name; ?>[color][menu-current-text]" value="<?php esc_html_e( $this->get_setting('color','menu-current-text')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>
								<p>
									<label>Menu current icon color
										<input name="<?php echo $this->settings_name; ?>[color][menu-current-icon]" value="<?php esc_html_e( $this->get_setting('color','menu-current-icon')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>
								<p>
									<label>Menu current background
										<input name="<?php echo $this->settings_name; ?>[color][menu-current-background]" value="<?php esc_html_e( $this->get_setting('color','menu-current-background')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>

								<p>
									<label>Submenu text color
										<input name="<?php echo $this->settings_name; ?>[color][menu-submenu-text]" value="<?php esc_html_e( $this->get_setting('color','menu-submenu-text')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>
								<p>
									<label>Submenu focus text color
										<input name="<?php echo $this->settings_name; ?>[color][menu-submenu-focus-text]" value="<?php esc_html_e( $this->get_setting('color','menu-submenu-focus-text')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>
								<p>
									<label>Submenu current text color
										<input name="<?php echo $this->settings_name; ?>[color][menu-submenu-current-text]" value="<?php esc_html_e( $this->get_setting('color','menu-submenu-current-text')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>
								<p>
									<label>Submenu background
										<input name="<?php echo $this->settings_name; ?>[color][menu-submenu-background]" value="<?php esc_html_e( $this->get_setting('color','menu-submenu-background')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>

								<p>
									<label>Bubble text color
										<input name="<?php echo $this->settings_name; ?>[color][menu-bubble-text]" value="<?php esc_html_e( $this->get_setting('color','menu-bubble-text')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>
								<p>
									<label>Bubble background
										<input name="<?php echo $this->settings_name; ?>[color][menu-bubble-background]" value="<?php esc_html_e( $this->get_setting('color','menu-bubble-background')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>
								<p>
									<label>Bubble current text color
										<input name="<?php echo $this->settings_name; ?>[color][menu-bubble-current-text]" value="<?php esc_html_e( $this->get_setting('color','menu-bubble-current-text')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>
								<p>
									<label>Bubble current background
										<input name="<?php echo $this->settings_name; ?>[color][menu-bubble-current-background]" value="<?php esc_html_e( $this->get_setting('color','menu-bubble-current-background')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>

								<p>
									<label>Collapse text color
										<input name="<?php echo $this->settings_name; ?>[color][menu-collapse-text]" value="<?php esc_html_e( $this->get_setting('color','menu-collapse-text')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>
								<p>
									<label>Collapse icon color
										<input name="<?php echo $this->settings_name; ?>[color][menu-collapse-icon]" value="<?php esc_html_e( $this->get_setting('color','menu-collapse-icon')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>
								<p>
									<label>Collapse focus text color
										<input name="<?php echo $this->settings_name; ?>[color][menu-collapse-focus-text]" value="<?php esc_html_e( $this->get_setting('color','menu-collapse-focus-text')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>
								<p>
									<label>Collapse focus icon color
										<input name="<?php echo $this->settings_name; ?>[color][menu-collapse-focus-icon]" value="<?php esc_html_e( $this->get_setting('color','menu-collapse-focus-icon')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>
							</div>
						</div>
						<h4><span>Bar</span></h4>
						<div class="box-body b-t hide">
							<div class="color-picker">
								<p>
									<label>Bar text color
										<input name="<?php echo $this->settings_name; ?>[bar][menu-text]" value="<?php esc_html_e( $this->get_setting('bar','menu-text')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>
								<p>
									<label>Bar icon color
										<input name="<?php echo $this->settings_name; ?>[bar][menu-icon]" value="<?php esc_html_e( $this->get_setting('bar','menu-icon')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>
								<p>
									<label>Bar highlight icon color
										<input name="<?php echo $this->settings_name; ?>[bar][menu-highlight-icon]" value="<?php esc_html_e( $this->get_setting('bar','menu-highlight-icon')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>
								<p>
									<label>Bar background
										<input name="<?php echo $this->settings_name; ?>[bar][menu-background]" value="<?php esc_html_e( $this->get_setting('bar','menu-background')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>
								<p>
									<label>Bar submemu text
										<input name="<?php echo $this->settings_name; ?>[bar][menu-submenu-text]" value="<?php esc_html_e( $this->get_setting('bar','menu-submenu-text')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>
								<p>
									<label>Bar submemu focus text
										<input name="<?php echo $this->settings_name; ?>[bar][menu-submenu-focus-text]" value="<?php esc_html_e( $this->get_setting('bar','menu-submenu-focus-text')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>
								<p>
									<label>Bar submenu background
										<input name="<?php echo $this->settings_name; ?>[bar][menu-submenu-background]" value="<?php esc_html_e( $this->get_setting('bar','menu-submenu-background')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>
								<p>
									<label>Avatar frame
										<input name="<?php echo $this->settings_name; ?>[bar][adminbar-avatar-frame]" value="<?php esc_html_e( $this->get_setting('bar','adminbar-avatar-frame')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>
								<p>
									<label>Input background
										<input name="<?php echo $this->settings_name; ?>[bar][adminbar-input-background]" value="<?php esc_html_e( $this->get_setting('bar','adminbar-input-background')); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
									</label>
								</p>
							</div>
						</div>
					</div>
					<h3 class="m-b"><span>Others</span></h3>
					<p class="no-m-t text-sm">Login page, footer and others</p>
					<div class="box">
						<h4 class="b-b"><span>Login page</span></h4>
						<div class="box-body b-b hide">
							<p>
								<label>Logo image <br>
									<input name="<?php echo $this->settings_name; ?>[login_logo]" value="<?php esc_html_e( $this->get_setting('login_logo') ); ?>" type="text">
									<button type="button" class="button-secondary upload-btn">Upload</button>
								</label>
							</p>
							<p>
								<label>Background image <br>
									<input name="<?php echo $this->settings_name; ?>[login_bg_img]" value="<?php esc_html_e( $this->get_setting('login_bg_img') ); ?>" type="text">
									<button type="button" class="button-secondary upload-btn">Upload</button>
								</label>
							</p>
							<p>
								<label>
									Background color<br>
									<input name="<?php echo $this->settings_name; ?>[login_bg_color]" value="<?php esc_html_e( $this->get_setting('login_bg_color') ); ?>" type="text" class="widefat color-field" placeholder="#f1f1f1">
								</label>
							</p>
							<p>
								<label>Custom css
									<textarea name="<?php echo $this->settings_name; ?>[login_css]" class="widefat" rows="4" placeHolder="a{color: #888}"><?php esc_html_e( $this->get_setting('login_css') ); ?></textarea>
								</label>
							</p>
						</div>
						<h4><span>Footer</span></h4>
						<div class="box-body b-t hide">
							<p>
								<label>Text
									<input name="<?php echo $this->settings_name; ?>[footer_text]" value="<?php esc_html_e( $this->get_setting('footer_text') ); ?>" type="text" class="widefat">
								</label>
							</p>
							<p>
								<label>
									<input name="<?php echo $this->settings_name; ?>[footer_text_hide]" type="checkbox" <?php if ($this->get_setting('footer_text_hide') == true) echo 'checked="checked" '; ?>> 
									Hide 'Text'
								</label>
							</p>
							<p>
								<label>Version
									<input name="<?php echo $this->settings_name; ?>[footer_version]" value="<?php esc_html_e( $this->get_setting('footer_version') ); ?>" type="text" class="widefat">
								</label>
							</p>
							<p>
								<label>
									<input name="<?php echo $this->settings_name; ?>[footer_version_hide]" type="checkbox" <?php if ($this->get_setting('footer_version_hide') == true) echo 'checked="checked" '; ?>> 
									Hide 'Version'
								</label>
							</p>
						</div>
						<?php if ( is_multisite() && get_current_blog_id() == 1 ) { ?>
						<h4 class="b-t"><span>Network</span></h4>
						<div class="box-body b-t hide">
							<p>
								<label>
									<input name="<?php echo $this->settings_name; ?>[network]" type="checkbox" <?php if ($this->get_setting('network') == true) echo 'checked="checked" '; ?>> 
									Disable on sub sites
								</label>
							</p>
						</div>
						<?php } ?>
						<h4 class="b-t"><span>Custom css</span></h4>
						<div class="box-body b-t hide">
							<p>
								<label>
									<textarea name="<?php echo $this->settings_name; ?>[admin_css]" class="widefat" rows="4" placeHolder="a{color: #888}"><?php esc_html_e( $this->get_setting('admin_css') ); ?></textarea>
								</label>
							</p>
						</div>
						<h4 class="b-t"><span>Custom js</span></h4>
						<div class="box-body b-t hide">
							<p>
								<label>
									<textarea name="<?php echo $this->settings_name; ?>[admin_js]" class="widefat" rows="4" placeHolder="alert(1);"><?php esc_html_e( $this->get_setting('admin_js') ); ?></textarea>
								</label>
							</p>
						</div>
					</div>
				</div>
				<div class="col col-8">
					<h3 class="m-b"><span>Bar</span></h3>
					<p class="no-m-t text-sm">Change the admin bar on the top</p>

					<div class="row clearfix">
						<div class="col col-6">
							<div class="box">
								<h4><span>Logo &amp; name</span></h4>
								<div class="box-body b-t hide">
									<p>
										<label>
											logo image<br>
											<input name="<?php echo $this->settings_name; ?>[bar_logo]" type="text" value="<?php esc_html_e( $this->get_setting('bar_logo') ); ?>">
											<button type="button" class="button-secondary upload-btn">Upload</button>
										</label>
									</p>
									<p>
										<label>
											Link
											<input name="<?php echo $this->settings_name; ?>[bar_name_link]" type="text" value="<?php esc_html_e( $this->get_setting('bar_name_link') ); ?>" class="widefat">
										</label>
									</p>
									<p>
										<label>
											Name
											<input name="<?php echo $this->settings_name; ?>[bar_name]" type="text" value="<?php esc_html_e( $this->get_setting('bar_name') ); ?>" class="widefat">
										</label>
									</p>
									<p>
										<label>
											<input name="<?php echo $this->settings_name; ?>[bar_name_hide]" type="checkbox" <?php if ( $this->get_setting('bar_name_hide') == true ) echo 'checked="checked" '; ?>> 
											Hide 'Name'
										</label>
									</p>
								</div>
							</div>
							<div class="box">
								<h4><span>Quick links</span></h4>
								<div class="box-body b-t hide">
									<p>
										<fieldset>
											<label>
												<input name="<?php echo $this->settings_name; ?>[bar_updates_hide]" type="checkbox" <?php if ($this->get_setting('bar_updates_hide') == true) echo 'checked="checked" '; ?>> 
												Remove 'Updates'
											</label>
											<br>
											<label>
												<input name="<?php echo $this->settings_name; ?>[bar_comments_hide]" type="checkbox" <?php if ($this->get_setting('bar_comments_hide') == true) echo 'checked="checked" '; ?>> 
												Remove 'Comments'
											</label>
											<br>
											<label>
												<input name="<?php echo $this->settings_name; ?>[bar_new_hide]" type="checkbox" <?php if ($this->get_setting('bar_new_hide') == true) echo 'checked="checked" '; ?>> 
												Remove 'New'
											</label>
										</fieldset>
									</p>
								</div>
							</div>
							<h3 class="m-b"><span>Menu</span></h3>
							<p class="no-m-t  text-sm">Change the menu on the left.</p>
							<p>
								<label style="margin-right:20px">
									<input name="<?php echo $this->settings_name; ?>[menu_collapse]" type="checkbox" <?php if ($this->get_setting('menu_collapse') == true) echo 'checked="checked" '; ?>> 
									Collapse menu 
								</label>
								<label>
									<input name="<?php echo $this->settings_name; ?>[menu_collapse_hide]" type="checkbox" <?php if ($this->get_setting('menu_collapse_hide') == true) echo 'checked="checked" '; ?>> 
									Hide collapse link
								</label>
							</p>
							<div class="clearfix admin-menus">
									<?php
										foreach ($this->menus as $k=>$v){
											$id = $this->get_slug($v);
											if($id[0] != NULL){
												$title = isset( $nav[$id[0]]['title'] ) && $nav[$id[0]]['title'] != '' ? $nav[$id[0]]['title'] : NULL;
												$icon  = isset( $nav[$id[0]]['icon'] ) && $nav[$id[0]]['icon'] != '' ? $nav[$id[0]]['icon'] : NULL;
												$hide  = isset( $nav[$id[0]]['hide'] ) && $nav[$id[0]]['hide'] != '' ? $nav[$id[0]]['hide'] : NULL;
												$index = isset( $nav[$id[0]]['index'] ) && $nav[$id[0]]['index'] != '' ? $nav[$id[0]]['index'] : $v[10];
									?>
									<div class="box bg admin-menu-item">

										<h4 <?php if($id[1] == NULL){ echo 'class="separator"'; }?> >
											<?php if($id[1]){ ?>
											<i id="<?php echo 'icon-'.$k; ?>" class="<?php echo esc_attr( $icon ? $icon : $v[6] ); ?>" data-target="#dropdown" data-toggle="dropdown"></i>
											<input name="<?php echo $this->settings_name.'[menu]['.$id[0].'][icon]'; ?>" value="<?php echo esc_attr( $icon ); ?>" type="text" hidden>
											<span class="pull-right text-muted <?php if ( $hide ) echo 'text-l-t'; ?>">
												<?php if($title) echo $id[1]; ?>
											</span>
											<span><?php echo $title ? $title : $id[1]; ?></span>
											<?php } ?>
										</h4>

										<div class="box-body b-t hide">
											<input name="<?php echo $this->settings_name.'[menu]['.$id[0].'][index]'; ?>" value="<?php esc_html_e( $index ); ?>" type="text" hidden>
											<?php if($id[1]){ ?>
											<p>
												<label>
													Title:
													<input name="<?php echo $this->settings_name.'[menu]['.$id[0].'][title]'; ?>" value="<?php esc_html_e( $title ); ?>" type="text" class="widefat">
												</label>
											</p>
											<?php } ?>
											<p>
												<label>
													<input name="<?php echo $this->settings_name.'[menu]['.$id[0].'][hide]'; ?>" <?php if ($hide) echo 'checked="checked" '; ?> type="checkbox"> 
													Remove from menu
												</label>
											</p>
											<?php
												if(isset($this->submenus[$v[2]])){
											?>
											<p class="toggle">
												<a href="#admin" class="c-p">Submenu</a>										
											</p>
											<?php } ?>
											<div class="hide admin-menus">
												<?php
													$sub = isset($this->submenus[$v[2]]) ? $this->submenus[$v[2]] : array() ;
													foreach ($sub as $k){
														$sid = $this->get_slug($k);
														if($sid[0] != NULL){
															$title = isset( $subnav[$sid[0]]['title'] ) && $subnav[$sid[0]]['title'] != '' ? $subnav[$sid[0]]['title'] : NULL;
															$hide  = isset( $subnav[$sid[0]]['hide'] )  && $subnav[$sid[0]]['hide'] != '' ? TRUE : FALSE;
															$index = isset( $subnav[$sid[0]]['index'] ) && $subnav[$sid[0]]['index'] != '' ? $subnav[$sid[0]]['index'] : $v[10];
												?>
												<div class="box admin-menu-item">
													<h4 class="sm">
														<span class="pull-right text-muted <?php if ( $hide ) echo 'text-l-t'; ?>">
															<?php if($title) echo $sid[1]; ?>
														</span>
														<span><?php echo $title ? $title : $sid[1]; ?></span>
													</h4>
													<div class="box-body b-t hide">
														<input name="<?php echo $this->settings_name.'[submenu]['.$sid[0].'][index]'; ?>" value="<?php esc_html_e( $index ); ?>" type="text" hidden>
														<p>
															<label>
																Title:
																<input name="<?php echo $this->settings_name.'[submenu]['.$sid[0].'][title]'; ?>" value="<?php esc_html_e( $title ); ?>" type="text" class="widefat">
															</label>
														</p>
														<p>
															<label>
																<input name="<?php echo $this->settings_name.'[submenu]['.$sid[0].'][hide]'; ?>" <?php if ( $hide ) echo 'checked="checked" '; ?> type="checkbox"> 
																Remove from menu
															</label>
														</p>
													</div>
												</div>
												<?php } }?>
											</div>
										</div>
									</div>
									<?php
										}
									} ?>
									<div id="dropdown" class="dropdown box">
										<div class="box-body" id="tab-iconlist">
											<div class="clearfix">
												<ul class="subsubsub">
													<li><a href="#tab-dashicons" class="current">Dashicons <span class="count">(230)</span></a> | </li>
													<li><a href="#tab-glyphicons">Glyphicons <span class="count">(263)</span></a></li>
												</ul>
											</div>
											<div class="iconlist clearfix" id="tab-dashicons">
												<!-- admin menu -->
												<div title="menu" class="dashicons-menu"></div>
												<div title="site" class="dashicons-admin-site"></div>
												<div title="dashboard" class="dashicons-dashboard"></div>
												<div title="post" class="dashicons-admin-post"></div>
												<div title="media" class="dashicons-admin-media"></div>
												<div title="links" class="dashicons-admin-links"></div>
												<div title="page" class="dashicons-admin-page"></div>
												<div title="comments" class="dashicons-admin-comments"></div>
												<div title="appearance" class="dashicons-admin-appearance"></div>
												<div title="plugins" class="dashicons-admin-plugins"></div>
												<div title="users" class="dashicons-admin-users"></div>
												<div title="tools" class="dashicons-admin-tools"></div>
												<div title="settings" class="dashicons-admin-settings"></div>
												<div title="network" class="dashicons-admin-network"></div>
												<div title="home" class="dashicons-admin-home"></div>
												<div title="generic" class="dashicons-admin-generic"></div>
												<div title="collapse" class="dashicons-admin-collapse"></div>
												<div title="filter" class="dashicons-filter"></div>
												<div title="customizer" class="dashicons-admin-customizer"></div>
												<div title="multisite" class="dashicons-admin-multisite"></div>
												<!-- welcome screen -->
												<div title="write blog" class="dashicons-welcome-write-blog"></div>
												<!--<div title="" class="dashicons-welcome-edit-page"></div> Duplicate -->
												<div title="add page" class="dashicons-welcome-add-page"></div>
												<div title="view site" class="dashicons-welcome-view-site"></div>
												<div title="widgets and menus" class="dashicons-welcome-widgets-menus"></div>
												<div title="comments" class="dashicons-welcome-comments"></div>
												<div title="learn more" class="dashicons-welcome-learn-more"></div>

												<!-- post formats -->
												<!--<div title="" class="dashicons-format-standard"></div> Duplicate -->
												<div title="aside" class="dashicons-format-aside"></div>
												<div title="image" class="dashicons-format-image"></div>
												<div title="gallery" class="dashicons-format-gallery"></div>
												<div title="video" class="dashicons-format-video"></div>
												<div title="status" class="dashicons-format-status"></div>
												<div title="quote" class="dashicons-format-quote"></div>
												<!--<div title="links" class="dashicons-format-links"></div> Duplicate -->
												<div title="chat" class="dashicons-format-chat"></div>
												<div title="audio" class="dashicons-format-audio"></div>
												<div title="camera" class="dashicons-camera"></div>
												<div title="images (alt)" class="dashicons-images-alt"></div>
												<div title="images (alt 2)" class="dashicons-images-alt2"></div>
												<div title="video (alt)" class="dashicons-video-alt"></div>
												<div title="video (alt 2)" class="dashicons-video-alt2"></div>
												<div title="video (alt 3)" class="dashicons-video-alt3"></div>

												<!-- media -->
												<div title="archive" class="dashicons-media-archive"></div>
												<div title="audio" class="dashicons-media-audio"></div>
												<div title="code" class="dashicons-media-code"></div>
												<div title="default" class="dashicons-media-default"></div>
												<div title="document" class="dashicons-media-document"></div>
												<div title="interactive" class="dashicons-media-interactive"></div>
												<div title="spreadsheet" class="dashicons-media-spreadsheet"></div>
												<div title="text" class="dashicons-media-text"></div>
												<div title="video" class="dashicons-media-video"></div>
												<div title="audio playlist" class="dashicons-playlist-audio"></div>
												<div title="video playlist" class="dashicons-playlist-video"></div>
												<div title="play player" class="dashicons-controls-play"></div>
												<div title="player pause" class="dashicons-controls-pause"></div>
												<div title="player forward" class="dashicons-controls-forward"></div>
												<div title="player skip forward" class="dashicons-controls-skipforward"></div>
												<div title="player back" class="dashicons-controls-back"></div>
												<div title="player skip back" class="dashicons-controls-skipback"></div>
												<div title="player repeat" class="dashicons-controls-repeat"></div>
												<div title="player volume on" class="dashicons-controls-volumeon"></div>
												<div title="player volume off" class="dashicons-controls-volumeoff"></div>

												<!-- image editing -->
												<div title="crop" class="dashicons-image-crop"></div>
												<div title="rotate" class="dashicons-image-rotate"></div>
												<div title="rotate left" class="dashicons-image-rotate-left"></div>
												<div title="rotate right" class="dashicons-image-rotate-right"></div>
												<div title="flip vertical" class="dashicons-image-flip-vertical"></div>
												<div title="flip horizontal" class="dashicons-image-flip-horizontal"></div>
												<div title="filter" class="dashicons-image-filter"></div>
												<div title="undo" class="dashicons-undo"></div>
												<div title="redo" class="dashicons-redo"></div>

												<!-- tinymce -->
												<div title="bold" class="dashicons-editor-bold"></div>
												<div title="italic" class="dashicons-editor-italic"></div>
												<div title="ul" class="dashicons-editor-ul"></div>
												<div title="ol" class="dashicons-editor-ol"></div>
												<div title="quote" class="dashicons-editor-quote"></div>
												<div title="alignleft" class="dashicons-editor-alignleft"></div>
												<div title="aligncenter" class="dashicons-editor-aligncenter"></div>
												<div title="alignright" class="dashicons-editor-alignright"></div>
												<div title="insertmore" class="dashicons-editor-insertmore"></div>
												<div title="spellcheck" class="dashicons-editor-spellcheck"></div>
												<!-- <div title="" class="dashicons-editor-distractionfree"></div> Duplicate -->
												<div title="expand" class="dashicons-editor-expand"></div>
												<div title="contract" class="dashicons-editor-contract"></div>
												<div title="kitchen sink" class="dashicons-editor-kitchensink"></div>
												<div title="underline" class="dashicons-editor-underline"></div>
												<div title="justify" class="dashicons-editor-justify"></div>
												<div title="textcolor" class="dashicons-editor-textcolor"></div>
												<div title="paste" class="dashicons-editor-paste-word"></div>
												<div title="paste" class="dashicons-editor-paste-text"></div>
												<div title="remove formatting" class="dashicons-editor-removeformatting"></div>
												<div title="video" class="dashicons-editor-video"></div>
												<div title="custom character" class="dashicons-editor-customchar"></div>
												<div title="outdent" class="dashicons-editor-outdent"></div>
												<div title="indent" class="dashicons-editor-indent"></div>
												<div title="help" class="dashicons-editor-help"></div>
												<div title="strikethrough" class="dashicons-editor-strikethrough"></div>
												<div title="unlink" class="dashicons-editor-unlink"></div>
												<div title="rtl" class="dashicons-editor-rtl"></div>
												<div title="break" class="dashicons-editor-break"></div>
												<div title="code" class="dashicons-editor-code"></div>
												<div title="paragraph" class="dashicons-editor-paragraph"></div>
												<div title="table" class="dashicons-editor-table"></div>
												<!-- posts -->
												<div title="align left" class="dashicons-align-left"></div>
												<div title="align right" class="dashicons-align-right"></div>
												<div title="align center" class="dashicons-align-center"></div>
												<div title="align none" class="dashicons-align-none"></div>
												<div title="lock" class="dashicons-lock"></div>
												<div title="unlock" class="dashicons-unlock"></div>
												<div title="calendar" class="dashicons-calendar"></div>
												<div title="calendar" class="dashicons-calendar-alt"></div>
												<div title="visibility" class="dashicons-visibility"></div>
												<div title="hidden" class="dashicons-hidden"></div>
												<div title="post status" class="dashicons-post-status"></div>
												<div title="edit pencil" class="dashicons-edit"></div>
												<div title="trash remove delete" class="dashicons-trash"></div>
												<div title="sticky" class="dashicons-sticky"></div>
												<!-- sorting -->
												<div title="external" class="dashicons-external"></div>
												<div title="arrow-up" class="dashicons-arrow-up"></div>
												<div title="arrow-down" class="dashicons-arrow-down"></div>
												<div title="arrow-right" class="dashicons-arrow-right"></div>
												<div title="arrow-left" class="dashicons-arrow-left"></div>
												<div title="arrow-up" class="dashicons-arrow-up-alt"></div>
												<div title="arrow-down" class="dashicons-arrow-down-alt"></div>
												<div title="arrow-right" class="dashicons-arrow-right-alt"></div>
												<div title="arrow-left" class="dashicons-arrow-left-alt"></div>
												<div title="arrow-up" class="dashicons-arrow-up-alt2"></div>
												<div title="arrow-down" class="dashicons-arrow-down-alt2"></div>
												<div title="arrow-right" class="dashicons-arrow-right-alt2"></div>
												<div title="arrow-left" class="dashicons-arrow-left-alt2"></div>
												<div title="sort" class="dashicons-sort"></div>
												<div title="left right" class="dashicons-leftright"></div>
												<div title="randomize shuffle" class="dashicons-randomize"></div>
												<div title="list view" class="dashicons-list-view"></div>
												<div title="exerpt view" class="dashicons-exerpt-view"></div>
												<div title="grid view" class="dashicons-grid-view"></div>

												<!-- social -->
												<div title="share" class="dashicons-share"></div>
												<div title="share" class="dashicons-share-alt"></div>
												<div title="share" class="dashicons-share-alt2"></div>
												<div title="twitter social" class="dashicons-twitter"></div>
												<div title="rss" class="dashicons-rss"></div>
												<div title="email" class="dashicons-email"></div>
												<div title="email" class="dashicons-email-alt"></div>
												<div title="facebook social" class="dashicons-facebook"></div>
												<div title="facebook social" class="dashicons-facebook-alt"></div>
												<div title="googleplus social" class="dashicons-googleplus"></div>
												<div title="networking social" class="dashicons-networking"></div>

												<!-- WPorg specific icons: Jobs, Profiles, WordCamps -->
												<div title="hammer development" class="dashicons-hammer"></div>
												<div title="art design" class="dashicons-art"></div>
												<div title="migrate migration" class="dashicons-migrate"></div>
												<div title="performance" class="dashicons-performance"></div>
												<div title="universal access accessibility" class="dashicons-universal-access"></div>
												<div title="universal access accessibility" class="dashicons-universal-access-alt"></div>
												<div title="tickets" class="dashicons-tickets"></div>
												<div title="nametag" class="dashicons-nametag"></div>
												<div title="clipboard" class="dashicons-clipboard"></div>
												<div title="heart" class="dashicons-heart"></div>
												<div title="megaphone" class="dashicons-megaphone"></div>
												<div title="schedule" class="dashicons-schedule"></div>

												<!-- internal/products -->
												<div title="wordpress" class="dashicons-wordpress"></div>
												<div title="wordpress" class="dashicons-wordpress-alt"></div>
												<div title="press this" class="dashicons-pressthis"></div>
												<div title="update" class="dashicons-update"></div>
												<div title="screenoptions" class="dashicons-screenoptions"></div>
												<div title="info" class="dashicons-info"></div>
												<div title="cart shopping" class="dashicons-cart"></div>
												<div title="feedback form" class="dashicons-feedback"></div>
												<div title="cloud" class="dashicons-cloud"></div>
												<div title="translation language" class="dashicons-translation"></div>

												<!-- taxonomies -->
												<div title="tag" class="dashicons-tag"></div>
												<div title="category" class="dashicons-category"></div>

												<!-- widgets -->
												<div title="archive" class="dashicons-archive"></div>
												<div title="tagcloud" class="dashicons-tagcloud"></div>
												<div title="text" class="dashicons-text"></div>

												<!-- alerts/notifications/flags -->
												<div title="yes check checkmark" class="dashicons-yes"></div>
												<div title="no x" class="dashicons-no"></div>
												<div title="no x" class="dashicons-no-alt"></div>
												<div title="plus add increase" class="dashicons-plus"></div>
												<div title="plus add increase" class="dashicons-plus-alt"></div>
												<div title="minus decrease" class="dashicons-minus"></div>
												<div title="dismiss" class="dashicons-dismiss"></div>
												<div title="marker" class="dashicons-marker"></div>
												<div title="filled star" class="dashicons-star-filled"></div>
												<div title="half star" class="dashicons-star-half"></div>
												<div title="empty star" class="dashicons-star-empty"></div>
												<div title="flag" class="dashicons-flag"></div>
												<div title="warning" class="dashicons-warning"></div>

												<!-- misc/cpt -->
												<div title="location pin" class="dashicons-location"></div>
												<div title="location" class="dashicons-location-alt"></div>
												<div title="vault safe" class="dashicons-vault"></div>
												<div title="shield" class="dashicons-shield"></div>
												<div title="shield" class="dashicons-shield-alt"></div>
												<div title="sos help" class="dashicons-sos"></div>
												<div title="search" class="dashicons-search"></div>
												<div title="slides" class="dashicons-slides"></div>
												<div title="analytics" class="dashicons-analytics"></div>
												<div title="pie chart" class="dashicons-chart-pie"></div>
												<div title="bar chart" class="dashicons-chart-bar"></div>
												<div title="line chart" class="dashicons-chart-line"></div>
												<div title="area chart" class="dashicons-chart-area"></div>
												<div title="groups" class="dashicons-groups"></div>
												<div title="businessman" class="dashicons-businessman"></div>
												<div title="id" class="dashicons-id"></div>
												<div title="id" class="dashicons-id-alt"></div>
												<div title="products" class="dashicons-products"></div>
												<div title="awards" class="dashicons-awards"></div>
												<div title="forms" class="dashicons-forms"></div>
												<div title="testimonial" class="dashicons-testimonial"></div>
												<div title="portfolio" class="dashicons-portfolio"></div>
												<div title="book" class="dashicons-book"></div>
												<div title="book" class="dashicons-book-alt"></div>
												<div title="download" class="dashicons-download"></div>
												<div title="upload" class="dashicons-upload"></div>
												<div title="backup" class="dashicons-backup"></div>
												<div title="clock" class="dashicons-clock"></div>
												<div title="lightbulb" class="dashicons-lightbulb"></div>
												<div title="microphone mic" class="dashicons-microphone"></div>
												<div title="desktop monitor" class="dashicons-desktop"></div>
												<div title="tablet ipad" class="dashicons-tablet"></div>
												<div title="smartphone iphone" class="dashicons-smartphone"></div>
												<div title="phone" class="dashicons-phone"></div>
												<div title="index card" class="dashicons-index-card"></div>
												<div title="carrot food vendor" class="dashicons-carrot"></div>
												<div title="building" class="dashicons-building"></div>
												<div title="store" class="dashicons-store"></div>
												<div title="album" class="dashicons-album"></div>
												<div title="palm tree" class="dashicons-palmtree"></div>
												<div title="tickets (alt)" class="dashicons-tickets-alt"></div>
												<div title="money" class="dashicons-money"></div>
												<div title="smiley smile" class="dashicons-smiley"></div>
												<div title="thumbs up" class="dashicons-thumbs-up"></div>
												<div title="thumbs down" class="dashicons-thumbs-down"></div>
												<div title="layout" class="dashicons-layout"></div>
											</div>
											<div class="iconlist clearfix" id="tab-glyphicons" >
												<div class="dashicons-glyphicon-asterisk"></div> 
												<div class="dashicons-glyphicon-plus"></div> 
												<div class="dashicons-glyphicon-euro"></div> 
												<div class="dashicons-glyphicon-eur"></div> 
												<div class="dashicons-glyphicon-minus"></div> 
												<div class="dashicons-glyphicon-cloud"></div> 
												<div class="dashicons-glyphicon-envelope"></div> 
												<div class="dashicons-glyphicon-pencil"></div> 
												<div class="dashicons-glyphicon-glass"></div> 
												<div class="dashicons-glyphicon-music"></div> 
												<div class="dashicons-glyphicon-search"></div> 
												<div class="dashicons-glyphicon-heart"></div> 
												<div class="dashicons-glyphicon-star"></div> 
												<div class="dashicons-glyphicon-star-empty"></div> 
												<div class="dashicons-glyphicon-user"></div> 
												<div class="dashicons-glyphicon-film"></div> 
												<div class="dashicons-glyphicon-th-large"></div> 
												<div class="dashicons-glyphicon-th"></div> 
												<div class="dashicons-glyphicon-th-list"></div> 
												<div class="dashicons-glyphicon-ok"></div> 
												<div class="dashicons-glyphicon-remove"></div> 
												<div class="dashicons-glyphicon-zoom-in"></div> 
												<div class="dashicons-glyphicon-zoom-out"></div> 
												<div class="dashicons-glyphicon-off"></div> 
												<div class="dashicons-glyphicon-signal"></div> 
												<div class="dashicons-glyphicon-cog"></div> 
												<div class="dashicons-glyphicon-trash"></div> 
												<div class="dashicons-glyphicon-home"></div> 
												<div class="dashicons-glyphicon-file"></div> 
												<div class="dashicons-glyphicon-time"></div> 
												<div class="dashicons-glyphicon-road"></div> 
												<div class="dashicons-glyphicon-download-alt"></div> 
												<div class="dashicons-glyphicon-download"></div> 
												<div class="dashicons-glyphicon-upload"></div> 
												<div class="dashicons-glyphicon-inbox"></div> 
												<div class="dashicons-glyphicon-play-circle"></div> 
												<div class="dashicons-glyphicon-repeat"></div> 
												<div class="dashicons-glyphicon-refresh"></div> 
												<div class="dashicons-glyphicon-list-alt"></div> 
												<div class="dashicons-glyphicon-lock"></div> 
												<div class="dashicons-glyphicon-flag"></div> 
												<div class="dashicons-glyphicon-headphones"></div> 
												<div class="dashicons-glyphicon-volume-off"></div> 
												<div class="dashicons-glyphicon-volume-down"></div> 
												<div class="dashicons-glyphicon-volume-up"></div> 
												<div class="dashicons-glyphicon-qrcode"></div> 
												<div class="dashicons-glyphicon-barcode"></div> 
												<div class="dashicons-glyphicon-tag"></div> 
												<div class="dashicons-glyphicon-tags"></div> 
												<div class="dashicons-glyphicon-book"></div> 
												<div class="dashicons-glyphicon-bookmark"></div> 
												<div class="dashicons-glyphicon-print"></div> 
												<div class="dashicons-glyphicon-camera"></div> 
												<div class="dashicons-glyphicon-font"></div> 
												<div class="dashicons-glyphicon-bold"></div> 
												<div class="dashicons-glyphicon-italic"></div> 
												<div class="dashicons-glyphicon-text-height"></div> 
												<div class="dashicons-glyphicon-text-width"></div> 
												<div class="dashicons-glyphicon-align-left"></div> 
												<div class="dashicons-glyphicon-align-center"></div> 
												<div class="dashicons-glyphicon-align-right"></div> 
												<div class="dashicons-glyphicon-align-justify"></div> 
												<div class="dashicons-glyphicon-list"></div> 
												<div class="dashicons-glyphicon-indent-left"></div> 
												<div class="dashicons-glyphicon-indent-right"></div> 
												<div class="dashicons-glyphicon-facetime-video"></div> 
												<div class="dashicons-glyphicon-picture"></div> 
												<div class="dashicons-glyphicon-map-marker"></div> 
												<div class="dashicons-glyphicon-adjust"></div> 
												<div class="dashicons-glyphicon-tint"></div> 
												<div class="dashicons-glyphicon-edit"></div> 
												<div class="dashicons-glyphicon-share"></div> 
												<div class="dashicons-glyphicon-check"></div> 
												<div class="dashicons-glyphicon-move"></div> 
												<div class="dashicons-glyphicon-step-backward"></div> 
												<div class="dashicons-glyphicon-fast-backward"></div> 
												<div class="dashicons-glyphicon-backward"></div> 
												<div class="dashicons-glyphicon-play"></div> 
												<div class="dashicons-glyphicon-pause"></div> 
												<div class="dashicons-glyphicon-stop"></div> 
												<div class="dashicons-glyphicon-forward"></div> 
												<div class="dashicons-glyphicon-fast-forward"></div> 
												<div class="dashicons-glyphicon-step-forward"></div> 
												<div class="dashicons-glyphicon-eject"></div> 
												<div class="dashicons-glyphicon-chevron-left"></div> 
												<div class="dashicons-glyphicon-chevron-right"></div> 
												<div class="dashicons-glyphicon-plus-sign"></div> 
												<div class="dashicons-glyphicon-minus-sign"></div> 
												<div class="dashicons-glyphicon-remove-sign"></div> 
												<div class="dashicons-glyphicon-ok-sign"></div> 
												<div class="dashicons-glyphicon-question-sign"></div> 
												<div class="dashicons-glyphicon-info-sign"></div> 
												<div class="dashicons-glyphicon-screenshot"></div> 
												<div class="dashicons-glyphicon-remove-circle"></div> 
												<div class="dashicons-glyphicon-ok-circle"></div> 
												<div class="dashicons-glyphicon-ban-circle"></div> 
												<div class="dashicons-glyphicon-arrow-left"></div> 
												<div class="dashicons-glyphicon-arrow-right"></div> 
												<div class="dashicons-glyphicon-arrow-up"></div> 
												<div class="dashicons-glyphicon-arrow-down"></div> 
												<div class="dashicons-glyphicon-share-alt"></div> 
												<div class="dashicons-glyphicon-resize-full"></div> 
												<div class="dashicons-glyphicon-resize-small"></div> 
												<div class="dashicons-glyphicon-exclamation-sign"></div> 
												<div class="dashicons-glyphicon-gift"></div> 
												<div class="dashicons-glyphicon-leaf"></div> 
												<div class="dashicons-glyphicon-fire"></div> 
												<div class="dashicons-glyphicon-eye-open"></div> 
												<div class="dashicons-glyphicon-eye-close"></div> 
												<div class="dashicons-glyphicon-warning-sign"></div> 
												<div class="dashicons-glyphicon-plane"></div> 
												<div class="dashicons-glyphicon-calendar"></div> 
												<div class="dashicons-glyphicon-random"></div> 
												<div class="dashicons-glyphicon-comment"></div> 
												<div class="dashicons-glyphicon-magnet"></div> 
												<div class="dashicons-glyphicon-chevron-up"></div> 
												<div class="dashicons-glyphicon-chevron-down"></div> 
												<div class="dashicons-glyphicon-retweet"></div> 
												<div class="dashicons-glyphicon-shopping-cart"></div> 
												<div class="dashicons-glyphicon-folder-close"></div> 
												<div class="dashicons-glyphicon-folder-open"></div> 
												<div class="dashicons-glyphicon-resize-vertical"></div> 
												<div class="dashicons-glyphicon-resize-horizontal"></div> 
												<div class="dashicons-glyphicon-hdd"></div> 
												<div class="dashicons-glyphicon-bullhorn"></div> 
												<div class="dashicons-glyphicon-bell"></div> 
												<div class="dashicons-glyphicon-certificate"></div> 
												<div class="dashicons-glyphicon-thumbs-up"></div> 
												<div class="dashicons-glyphicon-thumbs-down"></div> 
												<div class="dashicons-glyphicon-hand-right"></div> 
												<div class="dashicons-glyphicon-hand-left"></div> 
												<div class="dashicons-glyphicon-hand-up"></div> 
												<div class="dashicons-glyphicon-hand-down"></div> 
												<div class="dashicons-glyphicon-circle-arrow-right"></div> 
												<div class="dashicons-glyphicon-circle-arrow-left"></div> 
												<div class="dashicons-glyphicon-circle-arrow-up"></div> 
												<div class="dashicons-glyphicon-circle-arrow-down"></div> 
												<div class="dashicons-glyphicon-globe"></div> 
												<div class="dashicons-glyphicon-wrench"></div> 
												<div class="dashicons-glyphicon-tasks"></div> 
												<div class="dashicons-glyphicon-filter"></div> 
												<div class="dashicons-glyphicon-briefcase"></div> 
												<div class="dashicons-glyphicon-fullscreen"></div> 
												<div class="dashicons-glyphicon-dashboard"></div> 
												<div class="dashicons-glyphicon-paperclip"></div> 
												<div class="dashicons-glyphicon-heart-empty"></div> 
												<div class="dashicons-glyphicon-link"></div> 
												<div class="dashicons-glyphicon-phone"></div> 
												<div class="dashicons-glyphicon-pushpin"></div> 
												<div class="dashicons-glyphicon-usd"></div> 
												<div class="dashicons-glyphicon-gbp"></div> 
												<div class="dashicons-glyphicon-sort"></div> 
												<div class="dashicons-glyphicon-sort-by-alphabet"></div> 
												<div class="dashicons-glyphicon-sort-by-alphabet-alt"></div> 
												<div class="dashicons-glyphicon-sort-by-order"></div> 
												<div class="dashicons-glyphicon-sort-by-order-alt"></div> 
												<div class="dashicons-glyphicon-sort-by-attributes"></div> 
												<div class="dashicons-glyphicon-sort-by-attributes-alt"></div> 
												<div class="dashicons-glyphicon-unchecked"></div> 
												<div class="dashicons-glyphicon-expand"></div> 
												<div class="dashicons-glyphicon-collapse-down"></div> 
												<div class="dashicons-glyphicon-collapse-up"></div> 
												<div class="dashicons-glyphicon-log-in"></div> 
												<div class="dashicons-glyphicon-flash"></div> 
												<div class="dashicons-glyphicon-log-out"></div> 
												<div class="dashicons-glyphicon-new-window"></div> 
												<div class="dashicons-glyphicon-record"></div> 
												<div class="dashicons-glyphicon-save"></div> 
												<div class="dashicons-glyphicon-open"></div> 
												<div class="dashicons-glyphicon-saved"></div> 
												<div class="dashicons-glyphicon-import"></div> 
												<div class="dashicons-glyphicon-export"></div> 
												<div class="dashicons-glyphicon-send"></div> 
												<div class="dashicons-glyphicon-floppy-disk"></div> 
												<div class="dashicons-glyphicon-floppy-saved"></div> 
												<div class="dashicons-glyphicon-floppy-remove"></div> 
												<div class="dashicons-glyphicon-floppy-save"></div> 
												<div class="dashicons-glyphicon-floppy-open"></div> 
												<div class="dashicons-glyphicon-credit-card"></div> 
												<div class="dashicons-glyphicon-transfer"></div> 
												<div class="dashicons-glyphicon-cutlery"></div> 
												<div class="dashicons-glyphicon-header"></div> 
												<div class="dashicons-glyphicon-compressed"></div> 
												<div class="dashicons-glyphicon-earphone"></div> 
												<div class="dashicons-glyphicon-phone-alt"></div> 
												<div class="dashicons-glyphicon-tower"></div> 
												<div class="dashicons-glyphicon-stats"></div> 
												<div class="dashicons-glyphicon-sd-video"></div> 
												<div class="dashicons-glyphicon-hd-video"></div> 
												<div class="dashicons-glyphicon-subtitles"></div> 
												<div class="dashicons-glyphicon-sound-stereo"></div> 
												<div class="dashicons-glyphicon-sound-dolby"></div> 
												<div class="dashicons-glyphicon-sound-5-1"></div> 
												<div class="dashicons-glyphicon-sound-6-1"></div> 
												<div class="dashicons-glyphicon-sound-7-1"></div> 
												<div class="dashicons-glyphicon-copyright-mark"></div> 
												<div class="dashicons-glyphicon-registration-mark"></div> 
												<div class="dashicons-glyphicon-cloud-download"></div> 
												<div class="dashicons-glyphicon-cloud-upload"></div> 
												<div class="dashicons-glyphicon-tree-conifer"></div> 
												<div class="dashicons-glyphicon-tree-deciduous"></div> 
												<div class="dashicons-glyphicon-cd"></div> 
												<div class="dashicons-glyphicon-save-file"></div> 
												<div class="dashicons-glyphicon-open-file"></div> 
												<div class="dashicons-glyphicon-level-up"></div> 
												<div class="dashicons-glyphicon-copy"></div> 
												<div class="dashicons-glyphicon-paste"></div> 
												<div class="dashicons-glyphicon-alert"></div> 
												<div class="dashicons-glyphicon-equalizer"></div> 
												<div class="dashicons-glyphicon-king"></div> 
												<div class="dashicons-glyphicon-queen"></div> 
												<div class="dashicons-glyphicon-pawn"></div> 
												<div class="dashicons-glyphicon-bishop"></div> 
												<div class="dashicons-glyphicon-knight"></div> 
												<div class="dashicons-glyphicon-baby-formula"></div> 
												<div class="dashicons-glyphicon-tent"></div> 
												<div class="dashicons-glyphicon-blackboard"></div> 
												<div class="dashicons-glyphicon-bed"></div> 
												<div class="dashicons-glyphicon-apple"></div> 
												<div class="dashicons-glyphicon-erase"></div> 
												<div class="dashicons-glyphicon-hourglass"></div> 
												<div class="dashicons-glyphicon-lamp"></div> 
												<div class="dashicons-glyphicon-duplicate"></div> 
												<div class="dashicons-glyphicon-piggy-bank"></div> 
												<div class="dashicons-glyphicon-scissors"></div> 
												<div class="dashicons-glyphicon-bitcoin"></div> 
												<div class="dashicons-glyphicon-btc"></div> 
												<div class="dashicons-glyphicon-xbt"></div> 
												<div class="dashicons-glyphicon-yen"></div> 
												<div class="dashicons-glyphicon-jpy"></div> 
												<div class="dashicons-glyphicon-ruble"></div> 
												<div class="dashicons-glyphicon-rub"></div> 
												<div class="dashicons-glyphicon-scale"></div> 
												<div class="dashicons-glyphicon-ice-lolly"></div> 
												<div class="dashicons-glyphicon-ice-lolly-tasted"></div> 
												<div class="dashicons-glyphicon-education"></div> 
												<div class="dashicons-glyphicon-option-horizontal"></div> 
												<div class="dashicons-glyphicon-option-vertical"></div> 
												<div class="dashicons-glyphicon-menu-hamburger"></div> 
												<div class="dashicons-glyphicon-modal-window"></div> 
												<div class="dashicons-glyphicon-oil"></div> 
												<div class="dashicons-glyphicon-grain"></div> 
												<div class="dashicons-glyphicon-sunglasses"></div> 
												<div class="dashicons-glyphicon-text-size"></div> 
												<div class="dashicons-glyphicon-text-color"></div> 
												<div class="dashicons-glyphicon-text-background"></div> 
												<div class="dashicons-glyphicon-object-align-top"></div> 
												<div class="dashicons-glyphicon-object-align-bottom"></div> 
												<div class="dashicons-glyphicon-object-align-horizontal"></div> 
												<div class="dashicons-glyphicon-object-align-left"></div> 
												<div class="dashicons-glyphicon-object-align-vertical"></div> 
												<div class="dashicons-glyphicon-object-align-right"></div> 
												<div class="dashicons-glyphicon-triangle-right"></div> 
												<div class="dashicons-glyphicon-triangle-left"></div> 
												<div class="dashicons-glyphicon-triangle-bottom"></div> 
												<div class="dashicons-glyphicon-triangle-top"></div> 
												<div class="dashicons-glyphicon-console"></div> 
												<div class="dashicons-glyphicon-superscript"></div> 
												<div class="dashicons-glyphicon-subscript"></div> 
												<div class="dashicons-glyphicon-menu-left"></div> 
												<div class="dashicons-glyphicon-menu-right"></div> 
												<div class="dashicons-glyphicon-menu-down"></div> 
												<div class="dashicons-glyphicon-menu-up"></div> 
											</div>
										</div>
									</div>
							</div>
						</div>
						<div class="col col-4">
							<!-- import / export -->
							<div class="box">
								<div class="box-body">
									<p>
										<input type="submit" class="button button-primary button-block button-lg m-b" value="<?php _e('Save Changes') ?>" />
									</p>
									</form>
									<form method="post" enctype="multipart/form-data">
										<p>
											<input type="file" name="import_file"/>
										</p>
										<p>
											<input type="hidden" name="setting_action" value="import_settings" />
											<?php wp_nonce_field( 'setting_import_nonce', 'setting_import_nonce' ); ?>
											<?php submit_button( __( 'Import theme' ), 'button-block', 'submit', false ); ?>
										</p>
									</form>
									<form method="post">
										<p><input type="hidden" name="setting_action" value="export_settings" /></p>
										<p>
											<?php wp_nonce_field( 'setting_export_nonce', 'setting_export_nonce' ); ?>
											<?php submit_button( __( 'Export theme' ), 'button-block', 'submit', false ); ?>
										</p>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			(function ($) {
				 // toggle
				$(document).on('click', '.box > h3, .box > h4, .toggle', function(){
					$(this).toggleClass('active');
					$(this).next( ".hide" ).toggleClass('show');
				});

				jQuery(document).ready(function($){
				    // menu sortable
					$('.admin-menus').sortable({
						items: '.admin-menu-item',
						cursor: 'move',
						containment: 'parent',
						placeholder: 'box box-placeholder'
					});

					// color
					$('.color-field').wpColorPicker();

					// uploader
					$('.upload-btn').click(function(e) {
				        e.preventDefault();
				        var that = $(this);
				        var image = wp.media({ 
				            title: 'Upload Image',
				            multiple: false
				        }).open()
				        .on('select', function(e){
				            var uploaded_image = image.state().get('selection').first();
				            var image_url = uploaded_image.toJSON().url;
				            that.prev().val(image_url);
				        });
				    });
				});

				$('.admin-menus').on( "sortout", function( event, ui ) {
					ui.item.parent().find('.admin-menu-item').each(function(){
						var item = $(this).find('> div > input');
						if( item.val() != '' ){
							item.val( $(this).index() );
							item.attr('data-sort', $(this).index());
						}
					});
				});

				// tab
				$('#tab-glyphicons').hide();
				$(document).on('click', '#tab-iconlist ul a', function(e){
					e.stopPropagation();
					e.preventDefault();
					var c = $('#tab-iconlist');
					c.find('.iconlist').hide();
					c.find('a.current').removeClass('current');
					$(this).addClass('current');
					$( $(this).attr('href') ).show();
					//console.log($( $(this).attr('href') ).find('div').length)
				});

				// icons dropdown
				var select_icon;
				$('#dropdown').on('show.bs.dropdown', function (e) {
				  var  t = $('#dropdown')
				  	  ,i = $(e.relatedTarget)
				  	  ,p = $( '#'+i.attr('id') ).parent().parent().position()
				  	  ;
				  select_icon = $( '#'+i.attr('id') );
				  $('div', '.iconlist').each(function(){
				  	$(this).removeClass('active');
				  	if($(this).hasClass( i.attr('class') )){
				  		$(this).addClass('active');
				  	}
				  });
				  t.css('top', p.top+42);
				})

				// select icon
				$(document).on('click', '.iconlist div', function(e){
					var c = $(this).attr('class');
					select_icon.attr('class', c);
					select_icon.next().val(c);
				});

			})(jQuery);
		</script>
		<?php
	}

	// scripts
	function admin_scripts() {
		wp_enqueue_media();
		wp_enqueue_script(array('jquery-ui-sortable'));
		wp_enqueue_style( 'wp-color-picker');
		wp_enqueue_script('wp-color-picker');

		wp_enqueue_script( 'dropdown', $this->plugin_url.( "js/dropdown.js" ), array());

		wp_register_style( 'glyphicons', $this->plugin_url.( "css/glyphicons.css" ), array());
		wp_enqueue_style(  'glyphicons' );

		wp_register_style( 'admin', $this->plugin_url.( "scss/scss.php/admin.scss" ), array());
		wp_enqueue_style(  'admin' );

		wp_register_style( 'font', $this->plugin_url.( "css/font.css" ), array());
		wp_enqueue_style(  'font' );

		wp_register_style( 'style', $this->plugin_url.( "scss/scss.php/admin.scss" ), array());
		wp_enqueue_style(  'style' );

		wp_register_style( 'menu', $this->plugin_url.( "scss/scss.php/_admin_menu.scss" ), array());
		wp_enqueue_style(  'menu' );

		wp_register_style( 'bar', $this->plugin_url.( "scss/scss.php/_admin_bar.scss" ), array());
		wp_enqueue_style(  'bar' );
	}

	// admin menu
	function admin_menu() {
		global $menu;
		global $submenu;
		global $nav;
		global $subnav;
		$nav    = $this->get_setting('menu');
		$subnav = $this->get_setting('submenu');

		$i = 0;
		foreach ($menu as $k=>&$v){
			$i++;
			$v[10] = $i;
		}

		foreach ($submenu as $k=>&$v){
			$i = 0;
			foreach ($v as $key=>&$val){
				$i++;
				$val[10] = $i;
			}
			usort($v, array($this, 'sort_submenu'));
		}
		
		usort($menu, array($this, 'sort_menu'));

		$this->menus = array_merge(array(), $menu === NULL ? array() : $menu);
		$this->submenus = array_merge(array(), $submenu === NULL ? array() : $submenu);

		// update menu
		end( $menu );
		
		foreach ($menu as $k=>&$v){
			$id = $this->get_slug($v);
			if($id[0] != NULL && isset( $nav[$id[0]] )){
				// hide
				if( isset($nav[$id[0]]['hide']) && $nav[$id[0]]['hide'] ){
					unset($menu[$k]);
				}else{
					// title
					if( isset($nav[$id[0]]['title']) && $nav[$id[0]]['title'] != ''){
						$v[0] = $nav[$id[0]]['title']. ( isset($id[2]) ? ' <span '.$id[2] : '' );
					}

					// icon
					if( isset( $nav[$id[0]]['icon'] ) &&  $nav[$id[0]]['icon'] != ''){
						$v[6] = $nav[$id[0]]['icon'];
					}

					// update the submenu
					if( isset($submenu[$v[2]]) ){
						foreach ($submenu[$v[2]] as $key=>&$val){
							$sid = $this->get_slug($val);

							if($sid[0] != NULL && isset( $subnav[$sid[0]]['title'] ) && $subnav[$sid[0]]['title'] !=''){
								$val[0] = $subnav[$sid[0]]['title']. ( isset($sid[2]) ? ' <span '.$sid[2] : '' );
							}
							if( isset($subnav[$sid[0]]['hide']) && $subnav[$sid[0]]['hide'] != ''){						
								unset( $submenu[$v[2]][$key] );
							}
						}
					}
				}
			}
		}
	}

	// sort menu
	function sort_menu($a, $b) {
		global $nav;
		$i = isset( $nav[$this->get_slug($a)[0]]['index'] ) && $nav[$this->get_slug($a)[0]]['index'] != '' ? $nav[$this->get_slug($a)[0]]['index'] : $a[10];
		$j = isset( $nav[$this->get_slug($b)[0]]['index'] ) && $nav[$this->get_slug($b)[0]]['index'] != '' ? $nav[$this->get_slug($b)[0]]['index'] : $b[10];
	    
	    if ($i == $j) {
	        return 0;
	    }
	    return ($i < $j) ? -1 : 1;
	}

	// sort submenu
	function sort_submenu($a, $b) {
		global $subnav;
		$i = isset( $subnav[$this->get_slug($a)[0]]['index'] ) && $subnav[$this->get_slug($a)[0]]['index'] != '' ? $subnav[$this->get_slug($a)[0]]['index'] : $a[10];
		$j = isset( $subnav[$this->get_slug($b)[0]]['index'] ) && $subnav[$this->get_slug($b)[0]]['index'] != '' ? $subnav[$this->get_slug($b)[0]]['index'] : $b[10];
	    
	    if ($i == $j) {
	        return 0;
	    }
	    return ($i < $j) ? -1 : 1;
	}

	// get id
	function get_slug($s){
		$c = explode(' <span', $s[0]);
		return array(strtolower( str_replace( ' ', '_', $s[2] )), $c[0], isset($c[1]) ? $c[1] : NULL) ;
	}

	// admin bar
	function admin_bar(){
		global $wp_admin_bar;

		$all_toolbar_nodes = $wp_admin_bar->get_nodes();
		$site = array();
		foreach ( $all_toolbar_nodes as $key=>$node ) {
			$args = $node;
			if($args->id == "site-name"){
				$logo = $this->get_setting('bar_logo') ? sprintf('<img src="%s">', $this->get_setting('bar_logo')) : '';
				$hide = $this->get_setting('bar_name_hide') ? "hide" : "";
				$name = $this->get_setting('bar_name') ? $this->get_setting('bar_name') : $args->title;
				$args->title = sprintf('%s <span class="%s">%s</span>', $logo, $hide, $name);				
				$this->get_setting('bar_name_link') && ($args->href = $this->get_setting('bar_name_link'));
			}
			if($args->id == "my-sites"){
				$site = $node;
			}
			// update the Toolbar node
			$wp_admin_bar->add_node( $args );
		}
		// remove the wordpress logo
		$wp_admin_bar->remove_node( 'wp-logo' );
		$wp_admin_bar->remove_node( 'view-site' );

		$wp_admin_bar->remove_node( 'my-sites' );
		$wp_admin_bar->add_node( $site );

		if($this->get_setting('bar_updates_hide')){
				$wp_admin_bar->remove_node('updates');
		}
		if($this->get_setting('bar_comments_hide')){
				$wp_admin_bar->remove_node('comments');
		}
		if($this->get_setting('bar_new_hide')){
				$wp_admin_bar->remove_node('new-content');
		}
		if($this->get_setting('bar_new_hide')){
				$wp_admin_bar->remove_node('new-content');
		}
	}

	// admin footer
	function admin_footer( $default ){
		if(  strpos($default, 'wordpress') === false ){
			if( $this->get_setting('footer_version_hide') ){
				return '';
			}
			if( $this->get_setting('footer_version') ){
				return $this->get_setting('footer_version');
			}
		}else{
			if( $this->get_setting('footer_text_hide') ){
				return '';
			}
			if( $this->get_setting('footer_text') ){
				return $this->get_setting('footer_text');
			}
		}
		return $default;
	}

	// menu folder
	function admin_footer_scripts() {

		if( $this->get_setting('menu_collapse') ) {
		?>
				<script type="text/javascript">
					jQuery(document).ready(function(){
						!jQuery(".folded").length && jQuery("#collapse-menu").trigger("click");
					});
				</script>
		<?php
		}

		if( $this->get_setting('menu_collapse_hide') ) {
		?>
				<script type="text/javascript">
					jQuery(document).ready(function(){
						jQuery("#collapse-menu").hide();
					});
				</script>
		<?php
		}
		echo '<script type="text/javascript">'.$this->get_setting('admin_js').'</script>';
		echo '<style type="text/css">'.$this->get_setting('admin_css').'</style>';
	}

	// save to variables
	function update_variables( $new_value, $old_value ) {
		$file = $this->plugin_path.'/scss/scss/_variables_menu.scss';
		$file_ = $this->plugin_path.'/scss/scss/_variables_bar.scss';
		if( isset($new_value['use-default-color']) ){
			$file__ = $this->plugin_path.'/scss/scss/_variables_menu_'.$new_value['default-color'].'.scss';
			$file___ = $this->plugin_path.'/scss/scss/_variables_bar_white.scss';
			if(file_exists($file__)){
			    file_put_contents($file, file_get_contents($file__), FILE_TEXT );
			}
			if(file_exists($file___)){
			    file_put_contents($file_, file_get_contents($file___), FILE_TEXT );
			}
		}else{
			// menu
		    $output = "";
		    foreach ( $new_value['color'] as $variable => $vvalue ) {
		    	if($vvalue != ''){
		        	$output .= '$' . $variable . ': ' . $vvalue . ';' . PHP_EOL;
		        }
		    }
		    file_put_contents($file, $output, FILE_TEXT );

		  	// bar
			$output = "";
		    foreach ( $new_value['bar'] as $variable => $vvalue ) {
		    	if($vvalue != ''){
		        	$output .= '$' . $variable . ': ' . $vvalue . ';' . PHP_EOL;
		        }
		    }
		    file_put_contents($file_, $output, FILE_TEXT );
		}
		return $new_value;
	}

	// login
	function login_style() {
		add_filter( 'login_headerurl', array( $this, 'login_headerurl' ) );
		add_filter( 'login_headertitle', array( $this, 'login_headertitle' ) );

		$this->settings = get_option( $this->settings_name );

		?>
		<link rel='stylesheet' href='<?php echo $this->plugin_url.( "scss/scss.php/login.scss" ); ?>' type='text/css' media='all' />
		<style type="text/css">
		<?php
		if( $this->get_setting('login_logo') ){
		?>
	      body.login div#login h1 {
	        background-image: url(<?php echo $this->get_setting('login_logo'); ?>);
	        background-position: center top;
	        background-repeat: no-repeat;
	        background-size: contain;
	      }
	      body.login div#login h1 a {
	        background-image: none;
	      }
	    <?php
		}
		if( $this->get_setting('login_bg_color') ){
		?>
		  html {
	        background: <?php echo $this->get_setting('login_bg_color'); ?>;
	      }
	      body{
	      	background: transparent;
	      }
		<?php 
		}
		if( $this->get_setting('login_bg_img') ){
		?>
		  html {
	        background-image: url(<?php echo $this->get_setting('login_bg_img'); ?>);
	        background-size: cover;
	        background-position: center center;
	      }
	      body{
	      	background: transparent;
	      }
		<?php 
		}
		
		?>
		</style>
		<style type="text/css">
			<?php
				echo $this->get_setting('login_css');
			?>
		</style>
		<?php
	}

	function login_script() {
		wp_enqueue_script( 'form', $this->plugin_url.( "js/form.js" ), array('jquery'));
	}

	function login_headerurl() {
		return esc_url( trailingslashit( get_bloginfo( 'url' ) ) );
	}

	function login_headertitle() {
		return esc_attr( get_bloginfo( 'name' ) );
	}

	// disable the google webfonts api
	function disable_open_sans( $translations, $text, $context, $domain ) {
		if ( 'Open Sans font: on or off' == $context && 'on' == $text ) {
			$translations = 'off';
		}
		return $translations;
	}

	// deactivation
	function deactivation() {
		delete_option( $this->settings_name );
	}

	/**
	 * Process a settings export to a json file
	 */
	function process_settings_export() {
		if( empty( $_POST['setting_action'] ) || 'export_settings' != $_POST['setting_action'] )
			return;
		if( ! wp_verify_nonce( $_POST['setting_export_nonce'], 'setting_export_nonce' ) )
			return;
		if( ! current_user_can( 'manage_options' ) )
			return;
		$settings = get_option( $this->settings_name );
		ignore_user_abort( true );
		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=tema-admin-settings-export-' . date( 'm-d-Y' ) . '.json' );
		header( "Expires: 0" );
		echo json_encode( $settings );
		exit;
	}

	/**
	 * Process a settings import from a json file
	 */
	function process_settings_import() {
		if( empty( $_POST['setting_action'] ) || 'import_settings' != $_POST['setting_action'] )
			return;
		if( ! wp_verify_nonce( $_POST['setting_import_nonce'], 'setting_import_nonce' ) )
			return;
		if( ! current_user_can( 'manage_options' ) )
			return;

		$import_file = $_FILES['import_file']['tmp_name'];
		if( empty( $import_file ) ) {
			wp_die( __( 'Please upload a file to import' ) );
		}
		// Retrieve the settings from the file and convert the json object to an array.
		$settings = (array) json_decode( file_get_contents( $import_file ) );
		update_option( $this->settings_name, $settings );
		wp_safe_redirect( admin_url( 'options-general.php?page=tema-admin' ) ); exit;
	}

	// help
	function admin_help() {
		$current_screen = get_current_screen();

		// Overview
		$current_screen->add_help_tab(
			array(
				'id'		=> 'overview',
				'title'		=> __( 'Overview', 'AT' ),
				'content'	=>
					'<p><strong>' . __( 'Admin Theme by flatfull.com', 'AT' ) . '</strong></p>' .
					'<p>' . __( 'Admin Theme changes your wordpress admin appearance', 'AT' ) . '</p>' .
					'<p>' . __( 'Have fun.', 'AT' ) . '</p>',
			)
		);

		// Help Sidebar
		$current_screen->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'AT' ) . '</strong></p>' .
			'<p><a href="http://flatfull.com/" target="_blank">'     . __( 'FAQ',     'AT' ) . '</a></p>' .
			'<p></p>'
		);
	}

}

new tema;
