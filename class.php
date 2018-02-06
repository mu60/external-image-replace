<?php

class external_image_replace_get_posts {
	function org_posts() {
		$args = array(
			"posts_per_page" => -1,
			"post_type" => "post",
			"post_status" => "any",
		);
		$org_posts = get_posts($args);
		return $org_posts;
	}
	function match_post($post_id) {
		$single_post = get_post($post_id);
		if(!$single_post) {
			return false;
		}
		// imgタグを見つける
		preg_match_all("/<img([^>]*)>/", $single_post->post_content, $match_img, PREG_PATTERN_ORDER);
		if(count($match_img[1]) == 0) {
			return false;
		}
		$img_src = array();
		foreach($match_img[1] as $single_img) {
			// src属性を見つけて、値だけ抜き出す
			preg_match('/src="([^"]*)"/', $single_img, $match_src);
			if(strpos($match_src[1], (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"]) !== false) {
				continue;
			}
			$text = file_get_contents(plugin_dir_path( __FILE__ ) . "ignore_url.config");
			$ignore_urls = explode("\n", $text);
			$ignore_urls = array_map("trim", $ignore_urls);
			$ignore_urls = array_filter($ignore_urls, "strlen");
			$ignore_urls = array_values($ignore_urls);
			$match_flag = false;
			foreach($ignore_urls as $ignore_url) {
				if(strpos($match_src[1], $ignore_url) !== false) {
					$match_flag = true;
					continue;
				}
			}
			if(!$match_flag) {
				array_push($img_src, $match_src[1]);
			}
		}
		if(count($img_src) > 0) {
			$match_post = array(
				"post_id" => $single_post->ID,
				"post_title" => $single_post->post_title,
				"post_content" => $single_post->post_content,
				"img_src" => $img_src,
			);
			return $match_post;
		} else {
			return false;
		}
	}
	function match_posts() {
		$match_posts = array();
		foreach($this->org_posts() as $org_post) {
			$match_post = $this->match_post($org_post->ID);
			if($match_post) {
				array_push($match_posts, $match_post);
			}
		}
		return $match_posts;
	}
	function replace_post() {
		$message = __("Post ID", "external-image-replace") . " " . filter_input(INPUT_POST, "select_id");
		$error_message = "<span class='error_message'>".__("Error!", "external-image-replace")."</span> " . $message;
		$select_post = $this->match_post(filter_input(INPUT_POST, "select_id"));
		if(!$select_post) {
			return $error_message . " " . __("is not found.", "external-image-replace");
		}
		$wp_upload_dir = wp_upload_dir();
		$post_content = $select_post["post_content"];
		foreach($select_post["img_src"] as $img_src) {
			$parse_url = parse_url($img_src);
			$parse_url = $parse_url["scheme"] . "://" . $parse_url["host"] . $parse_url["path"];
			$path_parts = pathinfo($parse_url);
			$uploaddir_img = $wp_upload_dir["path"] . "/" . $path_parts["basename"];
			$uploadurl_img = $wp_upload_dir["url"] . "/" . $path_parts["basename"];
			$flag = @file_get_contents($parse_url);
			if(!$flag) {
				return $error_message . " " . __("The download destination image does not exist.", "external-image-replace") . " " . $img_src;
			}
			$flag = copy($parse_url, $uploaddir_img);
			if(!$flag) {
				return $error_message . " " . __("Failed to download images.", "external-image-replace") . " " . $img_src;
			}
			$filetype = wp_check_filetype($path_parts["basename"], null );
			$attachment = array(
				"post_mime_type" => $filetype["type"],
				"post_title" => sanitize_file_name($path_parts["basename"]),
				"post_content" => "",
				"post_status" => "inherit",
			);
			$attach_id = wp_insert_attachment( $attachment, $uploadurl_img, $select_post["post_id"] );
			if($attach_id == 0) {
				unlink($uploaddir_img);
				return $error_message . " " . __("The image could not be registered in the media library.", "external-image-replace") . " " . $img_src;
			}
			// wp_generate_attachment_metadata() の実行に必要なので下記ファイルを含める。
			require_once( ABSPATH . "wp-admin/includes/image.php" );
			// 添付ファイルのメタデータを生成し、データベースを更新。
			$attach_data = wp_generate_attachment_metadata( $attach_id, $uploaddir_img );
			if(!$attach_data) {
				wp_delete_attachment( $attach_id, true );
				return $error_message . " " . __("Failed to generate thumbnails of images registered in the media library.", "external-image-replace") . " " . $img_src;
			}
			$flag = wp_update_attachment_metadata( $attach_id, $attach_data );
			if(!$flag) {
				wp_delete_attachment( $attach_id, true );
				return $error_message . " " . __("Failed to register thumbnails of images registered in the media library.", "external-image-replace") . " " . $img_src;
			}
			$attach_url = wp_get_attachment_url($attach_id);
			$post_content_temp = str_replace($img_src, $attach_url, $post_content);
			$my_post = array(
				"ID" => $select_post["post_id"],
				"post_content" => $post_content_temp,
			);
			$update_flag = wp_update_post($my_post);
			if($update_flag == 0) {
				wp_delete_attachment( $attach_id, true );
				return $error_message . " " . __("Failed to update the article body.", "external-image-replace") . " " . $img_src;
			}
			$post_content = $post_content_temp;
		}
		return $message . " " . __("Processing is completed.", "external-image-replace");
	}
}
