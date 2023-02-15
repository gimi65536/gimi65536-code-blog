<?php

// Settings for shortcode
function g6_validate_toolname($name){
	return preg_match('/^[\w_\$]+$/', $name) === 1;
}

function g6form_shortcode($atts = [], $content = null, $tag = ''){
	if(is_null($content)){
		return '';
	}

	$atts = shortcode_atts(
		array(
			'tool' => '',
		), $atts, $tag
	);
	$tool = $atts['tool'];
	if(!g6_validate_toolname($tool)){
		return '';
	}

	$formid = wp_generate_password(8, false); # This is not a real password, just a random string.
	$ajaxurl = admin_url('admin-ajax.php');
	$nonce = wp_create_nonce("g6tool_nonce_$tool");

	$trans = array(
		"submit" => esc_html__("Submit", 'gimi65536-tools'),
		"here_show_results" => esc_html__("Results will be shown here...", 'gimi65536-tools')
	);
	$trans_js = array(
		"exe_error" => __("Execution error!", 'gimi65536-tools')
	);
	if(!wp_script_is("g6tools-js-l10n")){
		wp_register_script("g6tools-js-l10n", '');
		wp_enqueue_script("g6tools-js-l10n");
		wp_add_inline_script("g6tools-js-l10n", 'const g6tools_js_l10n = ' . json_encode($trans_js) . ';');
	}

	$content = do_shortcode($content);

	$script = <<<END
	<form method="post" class="g6form g6form-$tool" id="$formid">
		<div class="g6form-content">
		$content
		</div>
		<div class="g6form-submit g6form-submit-$tool">
			<button type='submit'>$trans[submit]</button>
		</div>
		<input type="hidden" name="action" value="gimi65536_tool_invoke">
		<input type="hidden" name="_ajax_nonce" value="$nonce">
		<input type="hidden" name="tool" value="$tool">
		<output class="g6form-result g6form-result-$tool" aria-live="polite" role="log">
			<pre id="g6form-result-$tool-$formid">$trans[here_show_results]</pre>
		</output>
	</form>
	<script>
		jQuery("#$formid").submit(function(e){
			e.preventDefault();
			let form = new FormData(document.getElementById('$formid'));
			jQuery.ajax({
				url: "$ajaxurl",
				type: 'POST',
				/* application/x-www-form-urlencoded
				data: ({action: "gimi65536_tool_invoke", _ajax_nonce: "123", tool: "aa"}),
				*/
				/* multipart/form-data */
				data: form,
				contentType: false,
				processData: false,
				cache: false,
				success: function(data){
					// To fit any other thing in <pre> block
					for(node of jQuery("#g6form-result-$tool-$formid").contents()){
						if(node.nodeType == 3){
							node.textContent = data;
							break;
						}
					}
				},
				error: function(data){
					for(node of jQuery("#g6form-result-$tool-$formid").contents()){
						if(node.nodeType == 3){
							node.textContent = g6tools_js_l10n['exe_error'];
							break;
						}
					}
				}
			});
		});
	</script>
	END;
	return $script;
}

function g6tools_shortcodes_init(){
	add_shortcode('g6form', 'g6form_shortcode');
}
add_action('init', 'g6tools_shortcodes_init');

function g6tools_invoke(){
	$nonce = $_POST['_ajax_nonce'];
	$tool = $_POST['tool'];
	if(!wp_verify_nonce($nonce, "g6tool_nonce_$tool")){
		echo __("Validation failed!", 'gimi65536-tools');
		wp_die();
	}
	unset($_POST['action']);
	unset($_POST['_ajax_nonce']);
	unset($_POST['tool']);

	$address = get_option('g6tools_toolapi_address');
	$curl = curl_init($address . $tool);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($_POST));
	curl_exec($curl);
	curl_close($curl);

	wp_die();
}
add_action("wp_ajax_gimi65536_tool_invoke", "g6tools_invoke");
add_action("wp_ajax_nopriv_gimi65536_tool_invoke", "g6tools_invoke");

function g6tools_reload(){
	$nonce = $_POST['_ajax_nonce'];
	if(!current_user_can("update_plugins") || !wp_verify_nonce($nonce, "g6tool_reload_tool_nonce")){
		echo __("Validation failed!", 'gimi65536-tools');
		wp_die();
	}

	$address = get_option('g6tools_toolapi_address');
	$curl = curl_init($address);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_exec($curl);
	curl_close($curl);

	wp_die();
}
add_action("wp_ajax_gimi65536_tool_reload", "g6tools_reload");
?>