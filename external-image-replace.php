<?php

/*
Plugin Name: External image replace
Plugin URI:
Description: Replace the external image in the posted article with the media library at once.
Author: muromuro
Author URI: https://github.com/mu60
Version: 1.0.8
License: GPLv2 or later
Text Domain: external-image-replace
*/

add_action("plugins_loaded", function () {
	load_plugin_textdomain("external-image-replace");
});

function include_smarty() {
	if(!class_exists("Smarty")) {
		require_once(get_template_directory()."/smarty/Smarty.class.php");
	}
	$smarty = new Smarty();
	$smarty->template_dir = plugin_dir_path( __FILE__ ) . "/templates";
	$smarty->compile_dir = plugin_dir_path( __FILE__ ) . "/templates_c";
	return $smarty;
}

add_action("admin_menu", function () {
	add_management_page(__("External image replace", "external-image-replace"), __("External image replace", "external-image-replace"), "manage_options", "external_image_replace", function () {
		if(!current_user_can( "manage_options")) {
			wp_die( __( "You do not have sufficient permissions to access this page."));
		}
		include_once(plugin_dir_path( __FILE__ )."class.php");
		$img_posts = new external_image_replace_get_posts();
		$smarty = include_smarty();
		if(filter_input(INPUT_POST, "action_button") === "replace") {
			$select_ids = filter_input(INPUT_POST, "post_id", FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
			$args = [
				"select_ids" => $select_ids,
				"post_id" => implode(",", $select_ids),
			];
			foreach($args as $key => $value) {
				$smarty->assign($key, $value);
			}
			$smarty->display("replace.tpl");
		} else {
			$ignore_path = plugin_dir_path( __FILE__ ) . "ignore_url.config";
			if(filter_input(INPUT_POST, "action_button") == "ignore_config") {
				file_put_contents($ignore_path, filter_input(INPUT_POST, "ignore_url"), LOCK_EX);
			}
			$args = [
				"match_count" => count($img_posts->match_posts()),
				"max_count" => count($img_posts->org_posts()),
				"match_posts" => $img_posts->match_posts(),
				"ignore_path" => $ignore_path,
			];
			foreach($args as $key => $value) {
				$smarty->assign($key, $value);
			}
			$smarty->display("search.tpl");
		}
	});
});

add_action("admin_enqueue_scripts", function () {
	global $pagenow;
	if($pagenow == "tools.php" && filter_input(INPUT_GET, "page") === "external_image_replace") {
		$plugin_url = WP_PLUGIN_URL . "/" . str_replace(basename( __FILE__), "", plugin_basename(__FILE__));
		if(filter_input(INPUT_POST, "action_button") === "replace") {
			wp_enqueue_script("external_image_replace", $plugin_url . "assets/replace.js");
			wp_enqueue_style("external_image_replace", $plugin_url . "assets/replace.css");
			wp_localize_script( "external_image_replace", "external_image_replace_ajax", array( "ajax_url" => admin_url("admin-ajax.php")) );
		} else {
			wp_enqueue_script("external_image_replace", $plugin_url . "assets/search.js");
			wp_enqueue_style("external_image_replace", $plugin_url . "assets/search.css");
		}
	}
});

add_action("wp_ajax_external_image_replace" , function () {
	require_once(dirname( __FILE__ ) . "/class.php");
	$img_posts = new external_image_replace_get_posts();
	echo $img_posts->replace_post();
	die(); // ajaxからの実行を終了させる際、これがないと最後に0が出力されてしまう。
});
