<?php

function g6form_number_shortcode($atts = []){
	$atts = shortcode_atts(
		array(
			'name' => '',
			'min' => '0',
			'max' => '',
			'optional' => '',
			'value' => '0',
		), $atts
	);
	$name = $atts['name'];
	$min = empty($atts['min']) ? '' : " min=\"{$atts['min']}\"";
	$max = empty($atts['max']) ? '' : " max=\"{$atts['max']}\"";
	$required = empty($atts['optional']) ? ' required=""' : '';
	$value = empty($atts['value']) ? '' : " value=\"{$atts['value']}\"";
	return <<<END
	<input name="$name" type="number"$min$max$required$value>
	END;
}

function g6tools_formfield_shortcodes_init(){
	add_shortcode('g6form_number', 'g6form_number_shortcode');
}
add_action('init', 'g6tools_formfield_shortcodes_init');