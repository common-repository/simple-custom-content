<?php
/*
	Plugin Name: Simple Custom Content
	Plugin URI: https://perishablepress.com/simple-custom-content/
	Description: Easily add custom content to your WP Posts and RSS Feeds.
	Tags: content, custom content, feeds, posts, rss
	Author: Jeff Starr
	Author URI: https://plugin-planet.com/
	Donate link: https://monzillamedia.com/donate.html
	Contributors: specialk
	Requires at least: 4.6
	Tested up to: 6.7
	Stable tag: 20241010
	Version:    20241010
	Requires PHP: 5.6.20
	Text Domain: simple-custom-content
	Domain Path: /languages
	License: GPL v2 or later
*/

/*
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 
	2 of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	with this program. If not, visit: https://www.gnu.org/licenses/
	
	Copyright 2024 Monzilla Media. All rights reserved.
*/

if (!defined('ABSPATH')) die();


$simple_custom_content_version_wp = '4.6';
$simple_custom_content_version    = '20241010';
$simple_custom_content_options    = get_option('scs_options');
$simple_custom_content_path       = plugin_basename(__FILE__); // simple-custom-content/simple-custom-content.php


function simple_custom_content_i18n_init() {
	
	global $simple_custom_content_path;
	
	load_plugin_textdomain('simple-custom-content', false, dirname($simple_custom_content_path) .'/languages/');
	
}
add_action('init', 'simple_custom_content_i18n_init');


function simple_custom_content_require_wp_version() {
	
	global $simple_custom_content_version_wp, $simple_custom_content_path;
	
	if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
		
		$wp_version  = get_bloginfo('version');
		$plugin_data = get_plugin_data(__FILE__, false);
		
		if (version_compare($wp_version, $simple_custom_content_version_wp, '<')) {
			
			if (is_plugin_active($simple_custom_content_path)) {
				
				deactivate_plugins($simple_custom_content_path);
				
				$msg  = '<p><strong>'. $plugin_data['Name'] .'</strong> '. esc_html__('requires WordPress ', 'simple-custom-content') . $simple_custom_content_version_wp . esc_html__(' or higher, and has been deactivated!', 'simple-custom-content') .'<br />';
				$msg .= esc_html__('Please upgrade WordPress and try again. Return to the', 'simple-custom-content') .' <a href="'. admin_url() .'">'. esc_html__('WordPress Admin area', 'simple-custom-content') .'</a>.</p>';
				
				wp_die($msg);
				
			}
			
		}
		
	}
	
}
add_action('admin_init', 'simple_custom_content_require_wp_version');


function simple_custom_content_footer_text($text) {
	
	$screen_id = simple_custom_content_get_current_screen_id();
	
	$ids = array('settings_page_simple-custom-content/simple-custom-content');
	
	if ($screen_id && apply_filters('simple_custom_content_admin_footer_text', in_array($screen_id, $ids))) {
		
		$text = __('Like this plugin? Give it a', 'simple-custom-content');
		
		$text .= ' <a target="_blank" rel="noopener noreferrer" href="https://wordpress.org/support/plugin/simple-custom-content/reviews/?rate=5#new-post">';
		
		$text .= __('★★★★★ rating&nbsp;&raquo;', 'simple-custom-content') .'</a>';
		
	}
	
	return $text;
	
}
add_filter('admin_footer_text', 'simple_custom_content_footer_text', 10, 1);


function simple_custom_content_get_current_screen_id() {
	
	if (!function_exists('get_current_screen')) require_once ABSPATH .'/wp-admin/includes/screen.php';
	
	$screen = get_current_screen();
	
	if ($screen && property_exists($screen, 'id')) return $screen->id;
	
	return false;
	
}


// custom content in all feeds
function simple_custom_content_feeds($content) {
	
	$options  = get_option('scs_options');
	$custom   = $options['scs_all_feeds'];
	$location = $options['scs_location_feeds'];
	
	$custom = simple_custom_content_replace_shortcodes($custom);
	
	if (is_feed()) {
		
		if ($location == 'scs_feed_after') {
			
			return $content . $custom;
			
		} elseif ($location == 'scs_feed_before') {
			
			return $custom . $content;
			
		} elseif ($location == 'scs_feed_both') {
			
			return $custom . $content . $custom;
			
		} else {
			
			return $content;
			
		}
		
	} else {
		
		return $content;
		
	}
	
}

if (isset($simple_custom_content_options['scs_location_feeds']) && $simple_custom_content_options['scs_location_feeds'] !== 'scs_feed_none') {
	
	if (isset($simple_custom_content_options['scs_enable_excerpts_feeds']) && $simple_custom_content_options['scs_enable_excerpts_feeds']) {
		
		add_filter('the_excerpt_rss', 'simple_custom_content_feeds');
		
	} else {
		
		add_filter('the_content', 'simple_custom_content_feeds');
		
	}
}


// custom content in all posts
function simple_custom_content_posts($content) {
	
	$options       = get_option('scs_options');
	
	$custom        = isset($options['scs_all_posts'])      ? $options['scs_all_posts']      : '';
	$location      = isset($options['scs_location_posts']) ? $options['scs_location_posts'] : 'scs_post_none';
	$posts_only    = isset($options['scs_posts_only'])     ? $options['scs_posts_only']     : 0;
	$include_pages = isset($options['scs_include_pages'])  ? $options['scs_include_pages']  : 0;
	$exclude_ids   = isset($options['exclude_ids'])        ? $options['exclude_ids']        : '';
	
	$exclude_ids = array_map('trim', explode(',', $exclude_ids));
	
	foreach ($exclude_ids as $id) {
		
		if ($id == get_the_ID()) $custom = '';
		
	}
	
	if (is_feed()) {
		
		$custom = '';
		
	} elseif (is_page()) {
		
		if (!$include_pages) $custom = '';
		
	} else {
		
		if (!is_singular('post') && $posts_only) $custom = '';
		
	}
	
	$custom = simple_custom_content_replace_shortcodes($custom);
	
	if ($location === 'scs_post_after') {
		
		$content = $content . $custom;
		
	} elseif ($location === 'scs_post_before') {
		
		$content = $custom . $content;
		
	} elseif ($location === 'scs_post_both') {
		
		$content = $custom . $content . $custom;
		
	}
	
	return $content;
	
}

if (isset($simple_custom_content_options['scs_location_posts']) && $simple_custom_content_options['scs_location_posts'] !== 'scs_post_none') {
	
	if (isset($simple_custom_content_options['scs_enable_excerpts']) && $simple_custom_content_options['scs_enable_excerpts']) {
		
		add_filter('the_excerpt', 'simple_custom_content_posts');
		
	} else {
		
		$priority = apply_filters('simple_custom_content_priority', 10);
		
		add_filter('the_content', 'simple_custom_content_posts', $priority);
		
	}
	
}


// SCS alt shortcode
function simple_custom_content_alt_shortcode() {
	
	$options = get_option('scs_options');
	
	$output = isset($options['scs_alt_shortcode']) ? $options['scs_alt_shortcode'] : '';
	
	$output = simple_custom_content_replace_shortcodes($output);
	
	return $output;
	
}
add_shortcode('scs_alt', 'simple_custom_content_alt_shortcode');


// SCS both shortcode
function simple_custom_content_both_shortcode() {
	
	$options = get_option('scs_options');
	
	$output = isset($options['scs_both_shortcode']) ? $options['scs_both_shortcode'] : '';
	
	$output = simple_custom_content_replace_shortcodes($output);
	
	return $output;
	
}
add_shortcode('scs_both', 'simple_custom_content_both_shortcode');


// SCS feed shortcode
function simple_custom_content_feed_shortcode() {
	
	$options = get_option('scs_options');
	
	$output = isset($options['scs_feed_shortcode']) ? $options['scs_feed_shortcode'] : '';
	
	$output = simple_custom_content_replace_shortcodes($output);
	
	if (is_feed()) return $output;
		
	return '';
	
}
add_shortcode('scs_feed', 'simple_custom_content_feed_shortcode');


// SCS post shortcode
function simple_custom_content_post_shortcode() {
	
	$options = get_option('scs_options');
	
	$output = isset($options['scs_post_shortcode']) ? $options['scs_post_shortcode'] : '';
	
	$output = simple_custom_content_replace_shortcodes($output);
	
	if (!is_feed()) return $output;
	
	return '';
	
}
add_shortcode('scs_post', 'simple_custom_content_post_shortcode');


function simple_custom_content_replace_shortcodes($string) {
	
	if ($string) {
		
		$patterns = array();
		$patterns[0] = "/%%id%%/";
		$patterns[1] = "/%%date%%/";
		$patterns[2] = "/%%title%%/";
		$patterns[3] = "/%%author%%/";
		$patterns[4] = "/%%permalink%%/";
		$patterns[5] = "/%%year%%/";
		
		$replacements = array();
		$replacements[0] = get_the_ID();
		$replacements[1] = get_the_date();
		$replacements[2] = get_the_title();
		$replacements[3] = get_the_author();
		$replacements[4] = get_the_permalink();
		$replacements[5] = date('Y');
		
		$string = preg_replace($patterns, $replacements, $string);
		
	}
	
	return apply_filters('simple_custom_content_replace_shortcodes', $string);
	
}


function simple_custom_content_plugin_action_links($links, $file) {
	
	global $simple_custom_content_path;
	
	if ($file === $simple_custom_content_path && current_user_can('manage_options')) {
		
		$href = admin_url('options-general.php?page=simple-custom-content/simple-custom-content.php');
		
		$scs_link = '<a href="'. $href .'" title="'. esc_attr__('Visit Plugin Settings', 'simple-custom-content') .'">'. esc_html__('Settings', 'simple-custom-content') .'</a>';
		
		array_unshift($links, $scs_link);
		
	}
	
	return $links;
	
}
add_filter('plugin_action_links', 'simple_custom_content_plugin_action_links', 10, 2);


function simple_custom_content_plugin_row_meta($links, $file) {
	
	global $simple_custom_content_path;
	
	if ($file === $simple_custom_content_path) {
		
		$home_href  = 'https://perishablepress.com/simple-custom-content/';
		$home_title = esc_attr__('Plugin Homepage', 'simple-custom-content');
		$home_text  = esc_html__('Homepage', 'simple-custom-content');
		
		$links[] = '<a target="_blank" rel="noopener noreferrer" href="'. $home_href .'" title="'. $home_title .'">'. $home_text .'</a>';
		
		$rate_href  = 'https://wordpress.org/support/plugin/simple-custom-content/reviews/?rate=5#new-post';
		$rate_title = esc_html__('Give us a 5-star rating at WordPress.org', 'simple-custom-content');
		$rate_text  = esc_html__('Rate this plugin', 'simple-custom-content') .'&nbsp;&raquo;';
		
		$links[] = '<a target="_blank" rel="noopener noreferrer" href="'. $rate_href .'" title="'. $rate_title .'">'. $rate_text .'</a>';
		
	}
	
	return $links;
	
}
add_filter('plugin_row_meta', 'simple_custom_content_plugin_row_meta', 10, 2);


function simple_custom_content_delete_plugin_options() {
	
	delete_option('scs_options');
	
}

if (isset($simple_custom_content_options['default_options']) && $simple_custom_content_options['default_options'] == 1) {
	
	register_uninstall_hook(__FILE__, 'simple_custom_content_delete_plugin_options');
	
}


function simple_custom_content_add_defaults() {
	
	$tmp = get_option('scs_options');
	
	if ((isset($tmp['default_options']) && $tmp['default_options'] == '1') || (!is_array($tmp))) {
		
		$arr = array(
			
			'scs_all_feeds'             => '<p>This text/markup is included in all RSS Feeds.</p>',
			'scs_all_posts'             => '<p>This text/markup is included in all WP Posts.</p>',
			'scs_enable_excerpts'       => 0,
			'scs_enable_excerpts_feeds' => 0,
			'scs_location_feeds'        => 'scs_feed_none',
			'scs_location_posts'        => 'scs_post_none',
			'scs_alt_shortcode'         => '<p>This text/markup is displayed wherever you put the [scs_alt] shortcode.</p>',
			'scs_both_shortcode'        => '<p>This text/markup is displayed in RSS Feeds and WP Posts when you include [scs_both] in your post(s).</p>',
			'scs_feed_shortcode'        => '<p>This text/markup is displayed in RSS Feeds when you include the [scs_feed] shortcode in your post(s).</p>',
			'scs_post_shortcode'        => '<p>This text/markup is displayed in WP Posts when you include the [scs_post] shortcode in your post(s).</p>',
			'default_options'           => 0,
			'scs_posts_only'            => 0,
			'scs_include_pages'         => 0,
			'exclude_ids'               => '',
			
		);
		
		update_option('scs_options', $arr);
		
	}
	
}
register_activation_hook (__FILE__, 'simple_custom_content_add_defaults');


$simple_custom_content_location_feeds = array(
	
	'scs_feed_after' => array(
		'value' => 'scs_feed_after',
		'label' => 'Include custom content at the end of each feed item',
	),
	
	'scs_feed_before' => array(
		'value' => 'scs_feed_before',
		'label' => 'Include custom content at the beginning of each feed item',
	),
	
	'scs_feed_both' => array(
		'value' => 'scs_feed_both',
		'label' => 'Include custom content at the beginning and end of each feed item',
	),
	
	'scs_feed_none' => array(
		'value' => 'scs_feed_none',
		'label' => 'Do not include custom content in any feed items',
	),
	
);

$simple_custom_content_location_posts = array(
	
	'scs_post_after' => array(
		'value' => 'scs_post_after',
		'label' => 'Include custom content at the end of each post',
	),
	
	'scs_post_before' => array(
		'value' => 'scs_post_before',
		'label' => 'Include custom content at the beginning of each post',
	),
	
	'scs_post_both' => array(
		'value' => 'scs_post_both',
		'label' => 'Include custom content at the beginning and end of each post',
	),
	
	'scs_post_none' => array(
		'value' => 'scs_post_none',
		'label' => 'Do not include custom content in any posts',
	),
	
);


function simple_custom_content_init() {
	
	register_setting('scs_plugin_options', 'scs_options', 'simple_custom_content_validate_options');
	
}
add_action ('admin_init', 'simple_custom_content_init');


function simple_custom_content_validate_options($input) {
	
	global $simple_custom_content_location_feeds, $simple_custom_content_location_posts;
	
	// dealing with kses
	global $allowedposttags;
	
	$default_allowedposttags = $allowedposttags; 
	
	$allowed_atts = array(
		
		'align'      => array(),
		'class'      => array(),
		'type'       => array(),
		'id'         => array(),
		'dir'        => array(),
		'lang'       => array(),
		'style'      => array(),
		'xml:lang'   => array(),
		'src'        => array(),
		'alt'        => array(),
		'href'       => array(),
		'rel'        => array(),
		'rev'        => array(),
		'target'     => array(),
		'novalidate' => array(),
		'type'       => array(),
		'value'      => array(),
		'name'       => array(),
		'tabindex'   => array(),
		'action'     => array(),
		'method'     => array(),
		'for'        => array(),
		'width'      => array(),
		'height'     => array(),
		'data'       => array(),
		'title'      => array(),
		
	);
	
	$allowedposttags['form'] = $allowed_atts;
	$allowedposttags['label'] = $allowed_atts;
	$allowedposttags['input'] = $allowed_atts;
	$allowedposttags['textarea'] = $allowed_atts;
	$allowedposttags['script'] = $allowed_atts;
	$allowedposttags['style'] = $allowed_atts;
	$allowedposttags['strong'] = $allowed_atts;
	$allowedposttags['small'] = $allowed_atts;
	$allowedposttags['table'] = $allowed_atts;
	$allowedposttags['span'] = $allowed_atts;
	$allowedposttags['abbr'] = $allowed_atts;
	$allowedposttags['code'] = $allowed_atts;
	$allowedposttags['pre'] = $allowed_atts;
	$allowedposttags['div'] = $allowed_atts;
	$allowedposttags['img'] = $allowed_atts;
	$allowedposttags['h1'] = $allowed_atts;
	$allowedposttags['h2'] = $allowed_atts;
	$allowedposttags['h3'] = $allowed_atts;
	$allowedposttags['h4'] = $allowed_atts;
	$allowedposttags['h5'] = $allowed_atts;
	$allowedposttags['h6'] = $allowed_atts;
	$allowedposttags['ol'] = $allowed_atts;
	$allowedposttags['ul'] = $allowed_atts;
	$allowedposttags['li'] = $allowed_atts;
	$allowedposttags['em'] = $allowed_atts;
	$allowedposttags['hr'] = $allowed_atts;
	$allowedposttags['br'] = $allowed_atts;
	$allowedposttags['tr'] = $allowed_atts;
	$allowedposttags['td'] = $allowed_atts;
	$allowedposttags['p'] = $allowed_atts;
	$allowedposttags['a'] = $allowed_atts;
	$allowedposttags['b'] = $allowed_atts;
	$allowedposttags['i'] = $allowed_atts;
	
	$input['scs_all_feeds'] = wp_kses($input['scs_all_feeds'], $allowedposttags);
	$input['scs_all_posts'] = wp_kses($input['scs_all_posts'], $allowedposttags);
	
	$input['scs_alt_shortcode']  = wp_kses($input['scs_alt_shortcode'],  $allowedposttags);
	$input['scs_both_shortcode'] = wp_kses($input['scs_both_shortcode'], $allowedposttags);
	$input['scs_feed_shortcode'] = wp_kses($input['scs_feed_shortcode'], $allowedposttags);
	$input['scs_post_shortcode'] = wp_kses($input['scs_post_shortcode'], $allowedposttags);
			
	if (!isset($input['scs_location_feeds'])) $input['scs_location_feeds'] = null;
	if (!array_key_exists($input['scs_location_feeds'], $simple_custom_content_location_feeds)) $input['scs_location_feeds'] = null;

	if (!isset($input['scs_location_posts'])) $input['scs_location_posts'] = null;
	if (!array_key_exists($input['scs_location_posts'], $simple_custom_content_location_posts)) $input['scs_location_posts'] = null;

	if (!isset($input['default_options'])) $input['default_options'] = null;
	$input['default_options'] = ($input['default_options'] == 1 ? 1 : 0);

	if (!isset($input['scs_enable_excerpts'])) $input['scs_enable_excerpts'] = null;
	$input['scs_enable_excerpts'] = ($input['scs_enable_excerpts'] == 1 ? 1 : 0);
	
	if (!isset($input['scs_enable_excerpts_feeds'])) $input['scs_enable_excerpts_feeds'] = null;
	$input['scs_enable_excerpts_feeds'] = ($input['scs_enable_excerpts_feeds'] == 1 ? 1 : 0);
	
	if (!isset($input['scs_posts_only'])) $input['scs_posts_only'] = null;
	$input['scs_posts_only'] = ($input['scs_posts_only'] == 1 ? 1 : 0);
	
	if (!isset($input['scs_include_pages'])) $input['scs_include_pages'] = null;
	$input['scs_include_pages'] = ($input['scs_include_pages'] == 1 ? 1 : 0);
	
	$input['exclude_ids'] = sanitize_text_field($input['exclude_ids']);
	
	$allowedposttags = $default_allowedposttags;
	
	return $input;
	
}


function simple_custom_content_add_options_page() {
	
	add_options_page('Simple Custom Content', 'Custom Content', 'manage_options', __FILE__, 'simple_custom_content_render_form');
	
}
add_action ('admin_menu', 'simple_custom_content_add_options_page');


function simple_custom_content_render_form() {
	
	global $simple_custom_content_version, $simple_custom_content_location_feeds, $simple_custom_content_location_posts; ?>

	<style type="text/css">
		#mm-plugin-options .scs-overview {
			padding: 0 15px 15px 77px;
			background-image: url(<?php echo plugins_url('/simple-custom-content/scc-icon.jpg'); ?>); 
			background-repeat: no-repeat; background-position: 15px 0; background-size: 60px 60px;
			}
		#mm-plugin-options .scs-overview ul { margin: 15px 15px 15px 40px; }
		#mm-plugin-options .scs-overview li { list-style-type: disc; }
		
		#mm-plugin-options .mm-panel-toggle { margin: 5px 0; }
		#mm-plugin-options .mm-credit-info { margin: -10px 0 10px 5px; font-size: 12px; }
		
		#mm-plugin-options #setting-error-settings_updated { margin: 5px 0 15px 0; }
		#mm-plugin-options #setting-error-settings_updated p { margin: 7px 0 6px 0; }
		
		#mm-plugin-options .mm-table-wrap { margin: 15px; }
		#mm-plugin-options .mm-table-wrap td { padding: 5px 10px; vertical-align: middle; }
		#mm-plugin-options .mm-table-wrap .mm-table { padding: 10px 0; }
		#mm-plugin-options .mm-table-wrap .widefat th { padding: 10px 15px; vertical-align: middle; }
		#mm-plugin-options .mm-table-wrap .widefat td { padding: 10px; vertical-align: middle; }
		
		#mm-plugin-options h1 small { line-height: 12px; font-size: 12px; color: #bbb; }
		#mm-plugin-options h2 { margin: 0; padding: 12px 0 12px 15px; font-size: 16px; cursor: pointer; }
		#mm-plugin-options h3 { margin: 20px 15px; font-size: 14px; }
		#mm-plugin-options p { margin-left: 15px; }
		#mm-plugin-options ul { margin: 15px 15px 25px 40px; line-height: 16px; }
		#mm-plugin-options ul + ul { margin-top: 20px; margin-bottom: 20px; }
		#mm-plugin-options li { margin: 8px 0; list-style-type: disc; }
		
		#mm-plugin-options textarea { width: 80%; }
		#mm-plugin-options input[type=text] { width: 60%; }
		#mm-plugin-options li input[type=checkbox] { margin-bottom: -3px; }
		#mm-plugin-options .mm-radio-inputs { margin: 5px 0; }
		#mm-plugin-options .mm-code { 
			display: inline-block; margin: 0 1px; padding: 3px; direction: ltr; unicode-bidi: embed;
			color: #333; background-color: #eaeaea; background-color: rgba(0,0,0,0.07);
			font-size: 13px; font-family: Consolas, Monaco, monospace;
			}
		#mm-plugin-options .mm-item-caption { margin: 3px 0 0 3px; line-height: 17px; font-size: 12px; color: #777; }
		#mm-plugin-options .mm-item-caption code, #mm-plugin-options code { margin: 0; padding: 3px; font-size: 12px; background: #f2f2f2; background-color: rgba(0,0,0,0.05); }
		#mm-plugin-options .mm-item-caption-nomargin { margin: 0; }
		#mm-plugin-options .mm-item-caption-block { display: block; }
		#mm-plugin-options textarea + .mm-item-caption { margin: 0 0 0 3px; }
		
		#mm-plugin-options .scs-select-option { margin: 0 0 30px 15px; }
		#mm-plugin-options .scs-select-option ul { margin-left: 20px; }
		#mm-plugin-options .scs-select-option li { list-style-type: none; }
		#mm-plugin-options .scs-select-option p { margin-left: 0; }
	</style>
	
	<div id="mm-plugin-options" class="wrap">
		<h1><?php esc_html_e('Simple Custom Content', 'simple-custom-content'); ?> <small><?php echo 'v' . $simple_custom_content_version; ?></small></h1>
		<div class="mm-panel-toggle"><a href="<?php get_admin_url() . 'options-general.php?page=simple-custom-content/simple-custom-content.php'; ?>"><?php esc_html_e('Toggle all panels', 'simple-custom-content'); ?></a></div>
		
		<form method="post" action="options.php">
			<?php $options = get_option('scs_options'); settings_fields('scs_plugin_options'); ?>
			<div class="metabox-holder">	
				<div class="meta-box-sortables ui-sortable">
					
					<div id="scs-overview" class="postbox">
						<h2><?php esc_html_e('Overview', 'simple-custom-content'); ?></h2>
						<div class="toggle<?php if (isset($_GET["settings-updated"])) { echo ' default-hidden'; } ?>">
							<div class="scs-overview">
								<p>
									<strong>Simple Custom Content</strong> <?php esc_html_e('makes it easy to add custom content in your posts and feeds.', 'simple-custom-content'); ?>
									<?php esc_html_e('You can add content to all posts, all feeds, or both. You can also use shortcodes to include custom content in specific posts.', 'simple-custom-content'); ?>
								</p>
								<ul>
									<li><a id="scs-custom-content-link" href="#scs-custom-content"><?php esc_html_e('Automatic Custom Content', 'simple-custom-content'); ?></a></li>
									<li><a id="scs-custom-shortcode-link" href="#scs-custom-shortcode"><?php esc_html_e('Post-Specific Custom Content', 'simple-custom-content'); ?></a></li>
									<li><a target="_blank" rel="noopener noreferrer" href="https://wordpress.org/plugins/simple-custom-content/"><?php esc_html_e('Plugin Homepage', 'simple-custom-content'); ?>&nbsp;&raquo;</a></li>
								</ul>
								<p>
									<?php esc_html_e('If you like this plugin, please', 'simple-custom-content'); ?> 
									<a target="_blank" rel="noopener noreferrer" href="https://wordpress.org/support/plugin/simple-custom-content/reviews/?rate=5#new-post" title="<?php esc_attr_e('THANK YOU for your support!', 'simple-custom-content'); ?>"><?php esc_html_e('give it a 5-star rating', 'simple-custom-content'); ?>&nbsp;&raquo;</a>
								</p>
							</div>
						</div>
					</div>
					
					<div id="scs-custom-content" class="postbox">
						<h2><?php esc_html_e('Automatic Custom Content', 'simple-custom-content'); ?></h2>
						<div class="toggle<?php if (!isset($_GET["settings-updated"])) { echo ' default-hidden'; } ?>">
							<p>
								<?php esc_html_e('In this section you can add custom content that will be displayed automatically in all WP Posts and/or RSS Feeds. Check out the', 'simple-custom-content'); ?> 
								<a target="_blank" rel="noopener noreferrer" href="https://wordpress.org/plugins/simple-custom-content/installation/"><?php esc_html_e('plugin documentation', 'simple-custom-content'); ?></a> 
								<?php esc_html_e('for more information.', 'simple-custom-content'); ?>
							</p>
							
							<h3><?php esc_html_e('WP Posts', 'simple-custom-content'); ?></h3>
							<p><label class="description" for="scs_options[scs_all_posts]"><?php esc_html_e('Add some custom content for your posts. You may use text and markup.', 'simple-custom-content'); ?></label></p>
							<p><textarea class="textarea large-text code" cols="68" rows="5" name="scs_options[scs_all_posts]"><?php echo esc_textarea($options['scs_all_posts']); ?></textarea></p>
							<div class="scs-select-option">
								<p><label class="description" for="scs_options[scs_location_posts]"><?php esc_html_e('Where should this custom content be displayed?', 'simple-custom-content'); ?></label></p>
								<ul>
								<?php if (!isset($checked)) $checked = '';
									foreach ($simple_custom_content_location_posts as $option) {
										$radio_setting = $options['scs_location_posts'];
										if ('' != $radio_setting) {
											if ($options['scs_location_posts'] == $option['value']) {
												$checked = "checked=\"checked\"";
											} else {
												$checked = '';
											}
										} ?>
									<li><input type="radio" name="scs_options[scs_location_posts]" value="<?php echo esc_attr($option['value']); ?>" <?php echo $checked; ?> /> <?php echo $option['label']; ?></li>
								<?php } ?>
								</ul>
								<ul>
									<li>
										<input name="scs_options[scs_posts_only]" type="checkbox" value="1" <?php if (isset($options['scs_posts_only'])) { checked('1', $options['scs_posts_only']); } ?> /> 
										<label class="description scs-checklabel" for="scs_options[scs_posts_only]"><?php esc_html_e('Limit custom content to WP Posts (i.e., Post Type = "post")', 'simple-custom-content'); ?></label>
									</li>
									<li>
										<input name="scs_options[scs_include_pages]" type="checkbox" value="1" <?php if (isset($options['scs_include_pages'])) { checked('1', $options['scs_include_pages']); } ?> /> 
										<label class="description scs-checklabel" for="scs_options[scs_include_pages]"><?php esc_html_e('Allow custom content on WP Pages (i.e., Post Type = "page")', 'simple-custom-content'); ?></label>
									</li>
									<li>
										<input name="scs_options[scs_enable_excerpts]" type="checkbox" value="1" <?php if (isset($options['scs_enable_excerpts'])) { checked('1', $options['scs_enable_excerpts']); } ?> /> 
										<label class="description scs-checklabel" for="scs_options[scs_enable_excerpts]"><?php esc_html_e('Display custom content in excerpts instead of content', 'simple-custom-content'); ?></label>
									</li>
								</ul>
							</div>
							
							<h3><?php esc_html_e('RSS Feeds', 'simple-custom-content'); ?></h3>
							<p><label class="description" for="scs_options[scs_all_feeds]"><?php esc_html_e('Add some custom content for your feeds. You may use text and markup.', 'simple-custom-content'); ?></label></p>
							<p><textarea class="textarea large-text code" cols="68" rows="5" name="scs_options[scs_all_feeds]"><?php echo esc_textarea($options['scs_all_feeds']); ?></textarea></p>
							<div class="scs-select-option">
								<p><label class="description" for="scs_options[scs_location_feeds]"><?php esc_html_e('Where should this custom content be displayed?', 'simple-custom-content'); ?></label></p>
								<ul>
								<?php if (!isset($checked)) $checked = '';
									foreach ($simple_custom_content_location_feeds as $option) {
										$radio_setting = $options['scs_location_feeds'];
										if ('' != $radio_setting) {
											if ($options['scs_location_feeds'] == $option['value']) {
												$checked = "checked=\"checked\"";
											} else {
												$checked = '';
											}
										} ?>
									<li><input type="radio" name="scs_options[scs_location_feeds]" value="<?php echo esc_attr($option['value']); ?>" <?php echo $checked; ?> /> <?php echo $option['label']; ?></li>
								<?php } ?>
								</ul>
								<ul>
									<li>
										<input name="scs_options[scs_enable_excerpts_feeds]" type="checkbox" value="1" <?php if (isset($options['scs_enable_excerpts_feeds'])) { checked('1', $options['scs_enable_excerpts_feeds']); } ?> /> 
										<label class="description scs-checklabel" for="scs_options[scs_enable_excerpts_feeds]"><?php esc_html_e('Display custom content only in feed excerpts', 'simple-custom-content'); ?></label>
									</li>
								</ul>
							</div>
							
							<h3><?php esc_html_e('Excluded Post IDs', 'simple-custom-content'); ?></h3>
							<p><?php esc_html_e('Here you can exclude custom content from specific posts. Applies to both WP Posts and RSS Feeds.', 'simple-custom-content'); ?></p>
							<p>
								<input type="text" size="70" maxlength="999" name="scs_options[exclude_ids]" value="<?php if (isset($options['exclude_ids'])) echo esc_attr($options['exclude_ids']); ?>">
								<label class="mm-item-caption mm-item-caption-block" for="scs_options[exclude_ids]"><?php esc_html_e('Enter Post IDs to exclude custom content (separate multiple with comma)', 'simple-custom-content'); ?></label>
							</p>
							
							<p><input type="submit" class="button-primary" value="<?php esc_attr_e('Save Settings', 'simple-custom-content'); ?>" /></p>
						</div>
					</div>
					
					<div id="scs-custom-shortcode" class="postbox">
						<h2><?php esc_html_e('Post-Specific Custom Content', 'simple-custom-content'); ?></h2>
						<div class="toggle<?php if (!isset($_GET["settings-updated"])) { echo ' default-hidden'; } ?>">
							<p>
								<?php esc_html_e('In this section you can define shortcodes to add custom content to individual WP Posts and/or RSS Feeds. Check out the', 'simple-custom-content'); ?> 
								<a target="_blank" rel="noopener noreferrer" href="https://wordpress.org/plugins/simple-custom-content/installation/"><?php esc_html_e('plugin documentation', 'simple-custom-content'); ?></a> <?php esc_html_e('for more information.', 'simple-custom-content'); ?>
							</p>
							
							<h3><label class="description" for="scs_options[scs_post_shortcode]"><?php esc_html_e('Shortcode for specific posts', 'simple-custom-content'); ?></label></h3>
							<p><?php esc_html_e('Add some custom content (text/markup) to display for the', 'simple-custom-content'); ?> <span class="mm-code">[scs_post]</span> <?php esc_html_e('shortcode.', 'simple-custom-content'); ?></p>
							<p><textarea class="textarea large-text code" cols="68" rows="5" name="scs_options[scs_post_shortcode]"><?php echo esc_textarea($options['scs_post_shortcode']); ?></textarea></p>
							
							<h3><label class="description" for="scs_options[scs_feed_shortcode]"><?php esc_html_e('Shortcode for specific feeds', 'simple-custom-content'); ?></label></h3>
							<p><?php esc_html_e('Add some custom content (text/markup) to display for the', 'simple-custom-content'); ?> <span class="mm-code">[scs_feed]</span> <?php esc_html_e('shortcode.', 'simple-custom-content'); ?></p>
							<p><textarea class="textarea large-text code" cols="68" rows="5" name="scs_options[scs_feed_shortcode]"><?php echo esc_textarea($options['scs_feed_shortcode']); ?></textarea></p>
							
							<h3><label class="description" for="scs_options[scs_both_shortcode]"><?php esc_html_e('Shortcode for posts and feeds', 'simple-custom-content'); ?></label></h3>
							<p><?php esc_html_e('Add some custom content (text/markup) to display for the', 'simple-custom-content'); ?> <span class="mm-code">[scs_both]</span> <?php esc_html_e('shortcode.', 'simple-custom-content'); ?></p>
							<p><textarea class="textarea large-text code" cols="68" rows="5" name="scs_options[scs_both_shortcode]"><?php echo esc_textarea($options['scs_both_shortcode']); ?></textarea></p>
							
							<h3><label class="description" for="scs_options[scs_alt_shortcode]"><?php esc_html_e('Bonus shortcode for anywhere', 'simple-custom-content'); ?></label></h3>
							<p><?php esc_html_e('Add some custom content (text/markup) to display for the', 'simple-custom-content'); ?> <span class="mm-code">[scs_alt]</span> <?php esc_html_e('shortcode.', 'simple-custom-content'); ?></p>
							<p><textarea class="textarea large-text code" cols="68" rows="5" name="scs_options[scs_alt_shortcode]"><?php echo esc_textarea($options['scs_alt_shortcode']); ?></textarea></p>
							
							<p><input type="submit" class="button-primary" value="<?php esc_attr_e('Save Settings', 'simple-custom-content'); ?>" /></p>
						</div>
					</div>
					
					<div id="scs-restore-defaults" class="postbox">
						<h2><?php esc_html_e('Restore Defaults', 'simple-custom-content'); ?></h2>
						<div class="toggle default-hidden">
							<p> 
								<input name="scs_options[default_options]" type="checkbox" value="1" id="scs_restore_defaults" <?php if (isset($options['default_options'])) { checked('1', $options['default_options']); } ?> /> 
								<label class="description scs-restore" for="scs_options[default_options]"><?php esc_html_e('Restore default options upon plugin deactivation/reactivation.', 'simple-custom-content'); ?></label>
							</p>
							<p>
								<span class="mm-item-caption mm-item-caption-nomargin">
									<strong><?php esc_html_e('Tip:', 'simple-custom-content'); ?></strong> 
									<?php esc_html_e('leave this option unchecked to remember your settings. Or, to go ahead and restore all default options, check the box, save your settings, and then deactivate/reactivate the plugin.', 'simple-custom-content'); ?>
								</span>
							</p>
							<p><input type="submit" class="button-primary" value="<?php esc_attr_e('Save Settings', 'simple-custom-content'); ?>" /></p>
						</div>
					</div>
					
					<div id="scs-wp-resources" class="postbox">
						<h2><?php esc_html_e('WP Resources', 'simple-custom-content'); ?></h2>
						<div class="toggle<?php if (isset($_GET["settings-updated"])) { echo ' default-hidden'; } ?>">
							<?php require_once('support-panel.php'); ?>
						</div>
					</div>
					
				</div>
			</div>
			
			<div class="mm-credit-info">
				<a target="_blank" rel="noopener noreferrer" href="https://perishablepress.com/simple-custom-content/" title="<?php esc_attr_e('Plugin Homepage', 'simple-custom-content'); ?>">Simple Custom Content</a> <?php esc_html_e('by', 'simple-custom-content'); ?> 
				<a target="_blank" rel="noopener noreferrer" href="https://twitter.com/perishable" title="<?php esc_attr_e('Jeff Starr on Twitter', 'simple-custom-content'); ?>">Jeff Starr</a> @ 
				<a target="_blank" rel="noopener noreferrer" href="https://monzillamedia.com/" title="<?php esc_attr_e('Obsessive Web Design &amp; Development', 'simple-custom-content'); ?>">Monzilla Media</a>
			</div>
			
		</form>
	</div>
	
	<script type="text/javascript">
		// prevent accidents
		if(!jQuery("#scs_restore_defaults").is(":checked")){
			jQuery('#scs_restore_defaults').click(function(event){
				var r = confirm("<?php esc_html_e('Are you sure you want to restore all default options? (this action cannot be undone)', 'simple-custom-content'); ?>");
				if (r == true){  
					jQuery("#scs_restore_defaults").attr('checked', true);
				} else {
					jQuery("#scs_restore_defaults").attr('checked', false);
				}
			});
		}
		// togglez
		jQuery(document).ready(function(){
			jQuery('.mm-panel-toggle a').click(function(){
				jQuery('.toggle').slideToggle(300);
				return false;
			});
			jQuery('.default-hidden').hide();
			jQuery('h2').click(function(){
				jQuery(this).next().slideToggle(300);
			});
			jQuery('#scs-custom-content-link').click(function(){
				jQuery('.toggle').hide();
				jQuery('#scs-custom-content .toggle').slideToggle(300);
				return true;
			});
			jQuery('#scs-custom-shortcode-link').click(function(){
				jQuery('.toggle').hide();
				jQuery('#scs-custom-shortcode .toggle').slideToggle(300);
				return true;
			});
		});
	</script>

<?php }
