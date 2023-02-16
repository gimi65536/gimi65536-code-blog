<?php

/**
 * Plugin Name:       	 gimi65536's Snippets
 * Plugin URI:           https://blog.gimi65536.xyz/
 * Description:       	 A custom plugin used for gimi65536's wordpress blog to store snippets in shortcodes
 * Version:           	 0.0.1
 * Requires at least:    6.0.0
 * Requires PHP:         7.4
 * Author:            	 Wayne Zeng
 * Author URI:        	 https://blog.gimi65536.xyz/
 * Text Domain:       	 gimi65536-snippets
 * Domain Path:          /languages
 */

function g6snippets_load_textdomain(){
	load_plugin_textdomain('gimi65536-snippets', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('init', 'g6snippets_load_textdomain');

function g6snippets_register_post_type(){
	register_post_type(
		'g6snippet',
		array(
			# Most of these labels are useless but it is better than nothing.
			'labels'   => array(
				'name' => __('Snippets', 'gimi65536-snippets'),
				'singular_name' => __('Snippet', 'gimi65536-snippets'),
				'add_new' => __('Add New', 'gimi65536-snippets'),
				'add_new_item' => __('Add New Snippet', 'gimi65536-snippets'),
				'edit_item' => __('Edit Snippet', 'gimi65536-snippets'),
				'new_item' => __('New Snippet', 'gimi65536-snippets'),
				'view_item' => __('View Snippet', 'gimi65536-snippets'),
				'view_items' => __('View Snippets', 'gimi65536-snippets'),
				'search_items' => __('Search Snippets', 'gimi65536-snippets'),
				'not_found' => __('No snippets found', 'gimi65536-snippets'),
				'not_found_in_trash' => __('No snippets found in Trash', 'gimi65536-snippets'),
				'all_items' => __('All Snippets', 'gimi65536-snippets'),
				'archives' => __('Snippet Archives', 'gimi65536-snippets'),
				'attributes' => __('Snippet Attributes', 'gimi65536-snippets'),
				'insert_into_item' => __('Insert into snippet', 'gimi65536-snippets'),
				'uploaded_to_this_item' => __('Uploaded to this snippet', 'gimi65536-snippets'),
				'filter_items_list' => __('Filter snippets list', 'gimi65536-snippets'),
				'items_list_navigation' => __('Snippets list navigation', 'gimi65536-snippets'),
				'items_list' => __('Snippetlist', 'gimi65536-snippets'),
				'item_published' => __('Snippet published.', 'gimi65536-snippets'),
				'item_published_privately' => __('Snippet published privately.', 'gimi65536-snippets'),
				'item_reverted_to_draft' => __('Snippet reverted to draft.', 'gimi65536-snippets'),
				'item_scheduled' => __('Snippet scheduled.', 'gimi65536-snippets'),
				'item_updated' => __('Snippet updated.', 'gimi65536-snippets'),
				'item_link' => __('Snippet Link', 'gimi65536-snippets'),
				'item_link_description' => __('A link to a snippet.', 'gimi65536-snippets')
			),
			'public'   => false,
			'show_ui'  => true,
			'show_in_nav_menus' => true
		)
	);
}
add_action('init', 'g6snippets_register_post_type');

function g6snippets_posts_columns($columns){
	$date = null;
	if(isset($columns['date'])){
		$date = $columns['date'];
		unset($columns['date']);
	}
	$columns['shortcode'] = __('Shortcode', 'gimi65536-snippets');
	if($date){
		$columns['date'] = $date;
	}
	return $columns;
}
function g6snippets_posts_custom_column($column_name, $post_ID){
	if($column_name == 'shortcode'){
		echo <<<END
		<input value="[postlist id=$post_ID]" type="text" size="20" onfocus="this.select();" onclick="this.select();" readonly="readonly" />
		END;
	}
}
add_filter('manage_g6snippet_posts_columns', 'g6snippets_posts_columns');
add_action('manage_g6snippet_posts_custom_column', 'g6snippets_posts_custom_column', 10, 2);

function g6snippets_shortcode($atts = []){
	$atts = shortcode_atts(
		array(
			'id' => '',
			'title' => ''
		), $atts
	);
	$post = null;
	if(!is_numeric($atts['id'])){
		// By title
		$post = get_page_by_title($atts['title'], OBJECT, 'g6snippet');
	}else{
		$id = intval($atts['id']);
		$post = get_post($id);
	}

	if(is_null($post)){
		return __("Snippet is not found.", 'gimi65536-snippets');
	}
	if($post->post_type != "g6snippet"){
		return __("The ID doesn't indicate a snippet.", 'gimi65536-snippets');
	}
	return apply_filters('the_content', $post->post_content);
}
function g6snippets_shortcodes_init(){
	add_shortcode('g6snippet', 'g6snippets_shortcode');
}
add_action('init', 'g6snippets_shortcodes_init');