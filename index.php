<?php
/**
* ID: scroll_reveal
* Name: Scroll Reveal
* Description: Animazione elementi allo scroll della pagina
* Icon: data:image/svg+xml,%3Csvg width='42px' height='32px' viewBox='2 2 42 32' version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink'%0A%3E%3Crect stroke='none' fill='%23FFCB36' fill-rule='evenodd' x='2' y='2' width='6' height='18' rx='3'%3E%3C/rect%3E%3Crect stroke='none' fill='%23007D97' fill-rule='evenodd' x='26' y='2' width='6' height='32' rx='3'%3E%3C/rect%3E%3Crect stroke='none' fill='%232EAD6D' fill-rule='evenodd' x='14' y='2' width='6' height='8' rx='3'%3E%3C/rect%3E%3Crect stroke='none' fill='%23E31D65' fill-rule='evenodd' x='14' y='16' width='6' height='18' rx='3'%3E%3C/rect%3E%3Crect stroke='none' fill='%23E31D65' fill-rule='evenodd' x='38' y='2' width='6' height='18' rx='3'%3E%3C/rect%3E%3Crect stroke='none' fill='%23FF7C35' fill-rule='evenodd' x='2' y='26' width='6' height='8' rx='3'%3E%3C/rect%3E%3Crect stroke='none' fill='%23553BB8' fill-rule='evenodd' x='38' y='26' width='6' height='8' rx='3'%3E%3C/rect%3E%3C/svg%3E
* Version: 1.1
* 
*/


class bc_reveal {
    private $bc_scroll_reveal_settings_options;

	public function __construct() {
		$this->bc_scroll_reveal_settings_options = get_option( 'bc_scroll_reveal_settings_option' ); 
		add_action( 'admin_menu', array( $this, 'bc_scroll_reveal_settings_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'bc_scroll_reveal_settings_page_init' ) );
        global $pagenow;
		if(isset($_GET['page'])):
			if($pagenow=='admin.php' && $_GET['page']=='scroll_reveal'){
				add_action('admin_enqueue_scripts', array( $this, '_enqueue_scripts' ));
				add_action('admin_footer-bweb-component_page_scroll_reveal', array( $this, 'admin_js_theme' ));
			}
		endif;
        add_action( 'wp_enqueue_scripts', array( $this, 'load_scrollreveal') );
		add_action( 'enqueue_block_editor_assets', array( $this, '_gutenberg_scripts' ));
	}

	public function bc_scroll_reveal_settings_add_plugin_page() {
		add_submenu_page(
            'bweb-component',
			'Scroll Reveal', // page_title
			'Scroll Reveal', // menu_title
			'manage_options', // capability
			'scroll_reveal', // menu_slug
			array( $this, 'bc_scroll_reveal_settings_create_admin_page' ) // function
		);

	}

	public function bc_scroll_reveal_settings_create_admin_page() {
        ?>

		<div class="wrap">
			
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
				settings_fields( 'bc_scroll_reveal_settings_option_group' );
				?>
					<?php
						do_settings_sections( 'bc_scroll_reveal-settings-scrollreveal' );
						?>
					
					<?php
					submit_button();
				?>
				
			</form>
		</div>
	<?php }

	public function bc_scroll_reveal_settings_page_init() {
		register_setting(
			'bc_scroll_reveal_settings_option_group', // option_group
			'bc_scroll_reveal_settings_option', // option_name
			array( $this, 'bc_scroll_reveal_settings_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'bc_scroll_reveal_settings_scrollreveal_section', // id
			'', // title
			'', // callback
			'bc_scroll_reveal-settings-scrollreveal' // page
		);
		
		
		add_settings_field(
			'item_scrollreveal', // id
			'<a class="add_reveal_button button-secondary"><span class="dashicons dashicons-plus-alt" style="vertical-align: text-top;"></span> Aggiungi</a>', // title
			array( $this, 'item_scrollreveal_callback' ), // callback
			'bc_scroll_reveal-settings-scrollreveal', // page
			'bc_scroll_reveal_settings_scrollreveal_section' // section
		);

		
	}

	public function bc_scroll_reveal_settings_sanitize($input) {
		$sanitary_values = array();
        

		if ( isset( $input['item_scrollreveal'] ) ) {
			$sanitary_values['item_scrollreveal'] = $input['item_scrollreveal'];
		}

		return $sanitary_values;
	}

    
	public function item_scrollreveal_callback(){
		?>

			<div id="cont_reveal">
				<?php
				if(isset($this->bc_scroll_reveal_settings_options['item_scrollreveal'])){
					$item_scrollreveal = $this->bc_scroll_reveal_settings_options['item_scrollreveal'];
					if(isset($item_scrollreveal) && is_array($item_scrollreveal)) {
						foreach($item_scrollreveal as $x => $value ){
							echo '<div class="reveal-item" attr_n="'.$x.'" style="border:1px solid #ccc; padding:20px; display:inline-block; margin:10px;">';
							echo 'class:<br><input style="font-weight: bold;" type="text" class="regular-text item_scrollreveal_class" name="bc_scroll_reveal_settings_option[item_scrollreveal]['.$x.'][class]" value="'.$value['class'].'"/><br><br>';
							echo 'distance:<br><input type="text" class="regular-text item_scrollreveal_distance" name="bc_scroll_reveal_settings_option[item_scrollreveal]['.$x.'][distance]" value="'.$value['distance'].'"/><br><br>';
							echo 'origin:<br>';
							printf(
								'<label><input type="radio" class="item_scrollreveal_origin" name="bc_scroll_reveal_settings_option[item_scrollreveal]['.$x.'][origin]" value="top" %s>top</label> | ',
								( isset( $this->bc_scroll_reveal_settings_options['item_scrollreveal'][$x]['origin'] ) && $this->bc_scroll_reveal_settings_options['item_scrollreveal'][$x]['origin'] === 'top' ) ? 'checked' : ''
							);
							printf(
								'<label><input type="radio" class="item_scrollreveal_origin" name="bc_scroll_reveal_settings_option[item_scrollreveal]['.$x.'][origin]" value="right" %s>right</label> | ',
								( isset( $this->bc_scroll_reveal_settings_options['item_scrollreveal'][$x]['origin'] ) && $this->bc_scroll_reveal_settings_options['item_scrollreveal'][$x]['origin'] === 'right' ) ? 'checked' : ''
							);
							printf(
								'<label><input type="radio" class="item_scrollreveal_origin" name="bc_scroll_reveal_settings_option[item_scrollreveal]['.$x.'][origin]" value="bottom" %s>bottom</label> | ',
								( isset( $this->bc_scroll_reveal_settings_options['item_scrollreveal'][$x]['origin'] ) && $this->bc_scroll_reveal_settings_options['item_scrollreveal'][$x]['origin'] === 'bottom' ) ? 'checked' : ''
							);
							printf(
								'<label><input type="radio" class="item_scrollreveal_origin" name="bc_scroll_reveal_settings_option[item_scrollreveal]['.$x.'][origin]" value="left" %s>left</label><br><br>',
								( isset( $this->bc_scroll_reveal_settings_options['item_scrollreveal'][$x]['origin'] ) && $this->bc_scroll_reveal_settings_options['item_scrollreveal'][$x]['origin'] === 'left' ) ? 'checked' : ''
							);
							echo 'duration:<br><input type="text" class="regular-text item_scrollreveal_duration" name="bc_scroll_reveal_settings_option[item_scrollreveal]['.$x.'][duration]" value="'.$value['duration'].'"/><br><br>';
							echo 'easing:<br><input type="text" class="regular-text item_scrollreveal_easing" name="bc_scroll_reveal_settings_option[item_scrollreveal]['.$x.'][easing]" value="'.$value['easing'].'"/><br><br>';
							echo 'interval:<br><input type="text" class="regular-text item_scrollreveal_interval" name="bc_scroll_reveal_settings_option[item_scrollreveal]['.$x.'][interval]" value="'.$value['interval'].'"/><br><br><br>';
							echo '<a href="#" class="remove_item button-secondary"><span class="dashicons dashicons-trash" style="vertical-align: text-top;"></span> Rimuovi</a><span style="float:right; cursor:pointer;" class="dashicons dashicons-controls-play iconpreview"></span>';
							echo '</div>';
						}
					}

				}
				
				?>
			
			</div>

		<?php
	}

	
	public function _enqueue_scripts($hook){
		
		wp_enqueue_script( 'bc_scroll_reveal-scrollreveal-scripts', plugin_dir_url( DIR_COMPONENT .  '/bweb_component_functions/' ) .'scroll_reveal/assets/scrollreveal.min.js', array( 'jquery' ),'', true );
		wp_enqueue_style( 'bc_scroll_reveal-scrollreveal-adminstyle', plugin_dir_url( DIR_COMPONENT .  '/bweb_component_functions/' ) .'scroll_reveal/assets/style.css' );
		
	}

	public function admin_js_theme($hook){
		?>
		<script>
			jQuery(document).ready(function($) {
				

				$(".add_reveal_button").click(function(e){
					e.preventDefault();
					var x = $('.reveal-item').length;
					if(x>0){
						x = parseInt($('#cont_reveal .reveal-item:last-child').attr('attr_n')) + 1;
					}
					
					var out = '';
					out += '<div class="reveal-item" attr_n="'+x+'" style="border:1px solid #ccc; padding:20px; display:inline-block; margin:10px;">';
					out += 'class:<br><input style="font-weight: bold;" type="text" class="regular-text item_scrollreveal_class" name="bc_scroll_reveal_settings_option[item_scrollreveal]['+x+'][class]" value=".reveal'+x+'"/><br><br>';
					out += 'distance:<br><input type="text" class="regular-text item_scrollreveal_distance" name="bc_scroll_reveal_settings_option[item_scrollreveal]['+x+'][distance]" value="80px"/><br><br>';
					out += 'origin:<br><label><input type="radio" class="item_scrollreveal_origin" name="bc_scroll_reveal_settings_option[item_scrollreveal]['+x+'][origin]" value="top" />top</label> | ';
					out += '<label><input type="radio" class="item_scrollreveal_origin" name="bc_scroll_reveal_settings_option[item_scrollreveal]['+x+'][origin]" value="right" />right</label> | ';
					out += '<label><input type="radio" class="item_scrollreveal_origin" name="bc_scroll_reveal_settings_option[item_scrollreveal]['+x+'][origin]" value="bottom" checked/>bottom</label> | ';
					out += '<label><input type="radio" class="item_scrollreveal_origin" name="bc_scroll_reveal_settings_option[item_scrollreveal]['+x+'][origin]" value="left" />left</label><br><br>';
					out += 'duration:<br><input type="text" class="regular-text item_scrollreveal_duration" name="bc_scroll_reveal_settings_option[item_scrollreveal]['+x+'][duration]" value="1000"/><br><br>';
					out += 'easing:<br><input type="text" class="regular-text item_scrollreveal_easing" name="bc_scroll_reveal_settings_option[item_scrollreveal]['+x+'][easing]" value="cubic-bezier(.215, .61, .355, 1)"/><br><br>';
					out += 'interval:<br><input type="text" class="regular-text item_scrollreveal_interval" name="bc_scroll_reveal_settings_option[item_scrollreveal]['+x+'][interval]" value="500"/><br><br><br>';
					out += '<a href="#" class="remove_item button-secondary"><span class="dashicons dashicons-trash" style="vertical-align: text-top;"></span> Rimuovi</a><span style="float:right; cursor:pointer;" class="dashicons dashicons-controls-play iconpreview"></span>';
					out += '</div>';
					$("#cont_reveal").append(out);
				});

				$("#cont_reveal").on("click",".remove_item", function(e){
					e.preventDefault(); 
					var c = confirm('Confermi la cancellazione?');
					if (c) $(this).parent('.reveal-item').remove();

				});

				$("#cont_reveal").on("click",".iconpreview", function(e){
                    ScrollReveal().destroy();
					var t = $(this).parent('.reveal-item');
					ScrollReveal().reveal(t,{ 
						distance: $('.item_scrollreveal_distance',t).val(),
						origin: $('.item_scrollreveal_origin:checked',t).val(),
						duration: $('.item_scrollreveal_duration',t).val(),
						easing: $('.item_scrollreveal_easing',t).val(),
						interval: $('.item_scrollreveal_interval',t).val(),
						reset: true
					});
					
				});
				
			})
		</script>
		<?php
	}

    function load_scrollreveal(){
        if( isset( $this->bc_scroll_reveal_settings_options['item_scrollreveal'] ) && is_array($this->bc_scroll_reveal_settings_options['item_scrollreveal'])){
            wp_enqueue_script( 'bcTheme-scrollreveal-scripts', plugin_dir_url( DIR_COMPONENT .  '/bweb_component_functions/' ) .'scroll_reveal/assets/scrollreveal.min.js', array( 'jquery' ),'', true );
            $item_scrollreveal = $this->bc_scroll_reveal_settings_options['item_scrollreveal'];
            wp_register_script( 'scrollreveal-scripts', '', array("jquery"), '', true );
            wp_enqueue_script( 'scrollreveal-scripts'  );
            $script_scrollreveal = "";
            foreach($item_scrollreveal as $x => $value ){
                $script_scrollreveal .= "ScrollReveal().reveal('".$value['class']."',{";
                $script_scrollreveal .= "distance: '".$value['distance']."',";
                $script_scrollreveal .= "duration: ".$value['duration'].",";
                $script_scrollreveal .= "origin: '".$value['origin']."',";
                $script_scrollreveal .= "easing: '".$value['easing']."',";
                $script_scrollreveal .= "interval: ".$value['interval'];
                $script_scrollreveal .= "});" . PHP_EOL;
            }
            wp_add_inline_script( 'scrollreveal-scripts',$script_scrollreveal);
        }
    }

	public function _gutenberg_scripts(){
		if(isset($this->bc_scroll_reveal_settings_options['item_scrollreveal'])){
			wp_enqueue_script( 'bc_scroll_reveal-scrollreveal-scripts', plugin_dir_url( DIR_COMPONENT .  '/bweb_component_functions/' ) .'scroll_reveal/assets/scrollreveal.min.js', array( 'jquery' ),'', true );
			wp_enqueue_style( 'bc_scroll_reveal-scrollreveal-adminstyle', plugin_dir_url( DIR_COMPONENT .  '/bweb_component_functions/' ) .'scroll_reveal/assets/style.css' );
	
			$item_scrollreveal = $this->bc_scroll_reveal_settings_options['item_scrollreveal'];
			if(isset($item_scrollreveal) && is_array($item_scrollreveal)) {
				wp_enqueue_script(
					'bc_reveal-editor', 
					plugin_dir_url( DIR_COMPONENT .  '/bweb_component_functions/' ) .'scroll_reveal/assets/editor.js', 
					array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'jquery' ), 
					time(),
					true
				);
				
				wp_localize_script(
					'bc_reveal-editor',
					'bc_reveal_class',
					$item_scrollreveal
				);

			}
		}
	}
	
}
new bc_reveal();