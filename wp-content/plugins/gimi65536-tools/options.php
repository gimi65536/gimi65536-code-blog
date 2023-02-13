<?php
// Settings for options and option pages
function g6tools_sanitize_toolapi_address(string $input){
	$input = trim($input);
	$l = strlen($input);
	if($l == 0){
		return '/';
	}else{
		if(substr($input, -1) == '/'){
			return $input;
		}
		return $input . '/';
	}
}

function g6tools_settings_init(){
	register_setting('g6tools_general', 'g6tools_toolapi_address', array(
		'type' => 'string',
		'default' => 'http://tool/',
		'sanitize_callback' => 'g6tools_sanitize_toolapi_address'
	));
	add_settings_section(
		'g6tools_general_section', # Section name
		__("General Settings", 'gimi65536-tools'),
		'g6tools_general_section_callback',
		'g6tools_setting_page'
	);
	add_settings_field(
		'g6tools_toolapi_address_field', # Field name
		__('Tool API Address', 'gimi65536-tools'),
		'g6tools_toolapi_address_field_callback',
		'g6tools_setting_page',
		'g6tools_general_section'
	);
}
add_action('admin_init', 'g6tools_settings_init');

function g6tools_general_section_callback() {
	echo '<p style="display: none;">G6tools General Section Introduction.</p>';
}

function g6tools_toolapi_address_field_callback(){
	$address = get_option('g6tools_toolapi_address');
	?>
	<input type="text" name="g6tools_toolapi_address" value="<?php echo isset( $address ) ? esc_attr( $address ) : 'http://tool/'; ?>">
	<?php
}

function g6tools_options_page_html(){
	?>
	<div class="wrap">
		<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields('g6tools_general'); // import nonces
			do_settings_sections('g6tools_setting_page');
			submit_button(__('Update', 'gimi65536-tools'));
			?>
		</form>
		<button id="g6tools-reload">Reload</button><output id="g6tools-reload-result"></output>
	</div>
	<script>
		jQuery("#g6tools-reload").click(function(e){
			jQuery("#g6tools-reload-result").text('');
			form = new FormData();
			form.append('action', 'gimi65536_tool_reload');
			form.append('nonce', '<?php echo wp_create_nonce('g6tool_reload_tool_nonce'); ?>');
			jQuery.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				type: 'POST',
				data: form,
				contentType: false,
				processData: false,
				cache: false,
				success: function(data){
					jQuery("#g6tools-reload-result").text(data);
				},
				error: function(data){
					jQuery("#g6tools-reload-result").text('Server Error.');
				}
			});
		});
	</script>
	<?php
}
function g6tools_options_page(){
	add_menu_page(
		__("gimi65536's Tools", 'gimi65536-tools'), # Settings page title
		"g6tools", # Name shown in the side bar
		'manage_options',
		'gimi65536-tools',
		'g6tools_options_page_html'
	);
}
add_action('admin_menu', 'g6tools_options_page');
?>