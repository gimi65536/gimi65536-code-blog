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
	$min = $atts['min'] == '' ? '' : " min=\"{$atts['min']}\"";
	$max = $atts['max'] == '' ? '' : " max=\"{$atts['max']}\"";
	$required = $atts['optional'] == '' ? ' required=""' : '';
	$value = $atts['value'] = '' ? '' : " value=\"{$atts['value']}\"";
	return <<<END
	<input name="$name" type="number"$min$max$required$value>
	END;
}

function g6tools_formfield_shortcodes_init(){
	add_shortcode('g6form_number', 'g6form_number_shortcode');
}
add_action('init', 'g6tools_formfield_shortcodes_init');