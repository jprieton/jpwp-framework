<?php

add_action('wp_ajax_profile_picture', function() {
	$jpwp = get_instance();
	$verify_wpnonce = $jpwp->input->verify_wpnonce('profile_picture');

	if (!$verify_wpnonce) {
		$jpwp->error->method_not_supported(__FUNCTION__);
		wp_send_json_error($jpwp->error);
	}

	$jpwp->load->model('media');

	/* @var $jpwp->media jpwp\models\Media */
	$media = & $jpwp->media;

	$args = [
			'width' => 500,
			'height' => 500,
			'crop' => TRUE,
			'rename' => TRUE,
			'prefix' => 'usrpic',
	];
	$attachemt_id = $media->add_image('profile_image', $args);

	if (is_wp_error($attachemt_id)) {
		wp_send_json_error($attachemt_id);
	}

	$user_id = get_current_user_id();
	$current_profile_image = get_user_meta($user_id, 'profile_image', TRUE);

	if (!empty($current_profile_image)) {
		wp_delete_attachment($current_profile_image, TRUE);
	}
	update_user_meta($user_id, 'profile_image', $attachemt_id);


	$response[] = array(
			'code' => 'user_profile_image',
			'message' => __('User profile imagen updated', 'jpwp')
	);
	wp_send_json_success($response);
});
