<?php

namespace jpwp\models;

defined('ABSPATH') or die('No direct script access allowed');

class Media {

	/**
	 * 
	 * @param string $field
	 * @param array $args
	 * @return int
	 */
	public function add_image($field, $args = array()) {

		$defaults = [
				'width' => 1024,
				'height' => 768,
				'crop' => FALSE,
				'rename' => FALSE,
				'prefix' => '',
		];

		$args = wp_parse_args($args, $defaults);

		$dest_w = (int) $args['width'];
		$dest_h = (int) $args['height'];
		$crop = (bool) $args['crop'];
		$rename = (bool) $args['rename'];
		$prefix = (string) $args['prefix'];

		$jpwp = get_instance();
		$overrides = [
				'test_form' => false,
				'mimes' => [
						'jpg' => 'image/jpg',
						'jpeg' => 'image/jpeg',
						'gif' => 'image/gif',
						'png' => 'image/png'
				]
		];
		$image_data = wp_handle_upload($_FILES[$field], $overrides);

		if (isset($image_data['error']) || isset($image_data['upload_error_handler'])) {
			$jpwp->error->add('upload_error', __('Invalid or empty file', 'jpwp'));
			return $jpwp->error;
		}

		$image_string = file_get_contents($image_data['file']);

		$image = imagecreatefromstring($image_string);

		$orig_w = imagesx($image);
		$orig_h = imagesy($image);

		$icr = image_resize_dimensions($orig_w, $orig_h, $dest_w, $dest_h, $crop);

		if (!$icr) {
			$icr = $this->image_resize_dimensions($orig_w, $orig_h);
		}

		$thumb = imagecreatetruecolor($icr[4], $icr[5]);
		try {
			imagecopyresampled($thumb, $image, $icr[0], $icr[1], $icr[2], $icr[3], $icr[4], $icr[5], $icr[6], $icr[7]);

			switch ($image_data['type']) {
				case 'image/bmp': imagewbmp($thumb, $image_data['file']);
					break;
				case 'image/gif': imagegif($thumb, $image_data['file']);
					break;
				case 'image/jpg': imagejpeg($thumb, $image_data['file']);
					break;
				case 'image/png': imagepng($thumb, $image_data['file']);
					break;
			}
		} catch (Exception $e) {
			$jpwp->error->add('upload_error', __('Invalid or empty file', 'jpwp'));
			return $jpwp->error;
		}

		$post_title = preg_replace('/\.[^.]+$/', '', basename($image_data['file']));

		if ($rename) {
			/* @var $prefix string */
			$prefix = empty($prefix) ? '' : $prefix . '-';
			$_new_filename = $prefix . substr(md5(date('dmYhis')), 0, 12) . '.' . (end(explode('/', $image_data['type'])));
			$_old_filename = end(explode('/', $image_data['url']));
			rename($image_data['file'], str_replace($_old_filename, $_new_filename, $image_data['file']));
			$image_data['url'] = str_replace($_old_filename, $_new_filename, $image_data['url']);
			$image_data['file'] = str_replace($_old_filename, $_new_filename, $image_data['file']);
		}

		$attachment = array(
				'post_mime_type' => $image_data['type'],
				'post_title' => $post_title,
				'post_content' => '',
				'post_status' => 'inherit',
				'guid' => $image_data['url']
		);

		if (!function_exists('wp_generate_attachment_metadata')) {
			require_once(ABSPATH . "wp-admin" . '/includes/image.php');
			require_once(ABSPATH . "wp-admin" . '/includes/file.php');
			require_once(ABSPATH . "wp-admin" . '/includes/media.php');
		}
		$wp_upload_dir = wp_upload_dir();
		$filename = str_replace($wp_upload_dir['baseurl'] . '/', '', $image_data['url']);

		$attach_id = wp_insert_attachment($attachment, $filename);
		$attach_data = wp_generate_attachment_metadata($attach_id, $image_data['file']);
		wp_update_attachment_metadata($attach_id, $attach_data);
		return $attach_id;
	}

	/**
	 * 
	 * @param int $orig_w
	 * @param int $orig_h
	 * @return type
	 */
	private function image_resize_dimensions($orig_w, $orig_h) {
		return array($orig_w, $orig_h, 0, 0, $orig_w, $orig_h, $orig_w, $orig_h);
	}

}
