<?php

// Settings for shortcode
function g6_validate_toolname($name){
	return preg_match('/^[\w_\$]+$/', $name) === 1;
}

function g6form_shortcode($atts = [], $content = null, $tag = ''){
	if(is_null($content)){
		return '';
	}

	$wporg_atts = shortcode_atts(
		array(
			'tool' => '',
		), $atts, $tag
	);
	$tool = $wporg_atts['tool'];
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

	$script = <<<END
	<form method="post" class="g6form g6form-$tool" id="$formid">
	<fieldset>
	<input type="checkbox" id="coding" name="interest[]" value="coding" />
	<label for="coding">コーディング</label>
	<input type="checkbox" id="music" name="interest[]" value="music" />
	<label for="music">音楽</label>
	</fieldset>
		$content
		<div class="g6form-submit g6form-submit-$tool">
			<button type='submit'>$trans[submit]</button>
		</div>
		<input type="hidden" name="action" value="gimi65536_tool_invoke">
		<input type="hidden" name="nonce" value="$nonce">
		<input type="hidden" name="tool" value="$tool">
		<div class="g6form-result g6form-result-$tool">
			<pre id="g6form-result-$tool-$formid">$trans[here_show_results]</pre>
		</div>
	</form>
	<script>
		jQuery("#$formid").submit(function(e){
			e.preventDefault();
			let form = new FormData(document.getElementById('$formid'));
			jQuery.ajax({
				url: "$ajaxurl",
				type: 'POST',
				/* application/x-www-form-urlencoded
				data: ({action: "gimi65536_tool_invoke", nonce: "123", tool: "aa"}),
				*/
				/* multipart/form-data */
				data: form,
				contentType: false,
				processData: false,
				cache: false,
				success: function(data){
					jQuery("#g6form-result-$tool-$formid").text(data);
				},
				error: function(data){
					jQuery("#g6form-result-$tool-$formid").text(g6tools_js_l10n['exe_error']);
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
	$nonce = $_POST['nonce'];
	$tool = $_POST['tool'];
	if(!wp_verify_nonce($nonce, "g6tool_nonce_$tool")){
		echo __("Validation failed!", 'gimi65536-tools');
		wp_die();
	}
	unset($_POST['action']);
	unset($_POST['nonce']);
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
?>