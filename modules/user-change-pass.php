<?php

/**
 * Cambio de contraseñas de usuarios (AJAX)
 */
add_action('wp_ajax_user_change_pass', function () {

	$jpwp = get_instance();

	$verify_nonce = (bool) $jpwp->input->verify_wpnonce('user_change_pass');

	if (!$verify_nonce) {
		$jpwp->error->method_not_supported(__FUNCTION__);
		wp_send_json_error($jpwp->error);
	}
			$current_pass  = $jpwp->input->post('current_pass');
			$new_pass      = $jpwp->input->post('new_pass');
			$verify_pass   = $jpwp->input->post('verify_pass');

	if (empty($current_pass)) {
		$jpwp->error->add('bad_user_pass', __('Current pass invalid', 'jpwp'));
		wp_send_json_error($jpwp->error);
	}

	if (empty($new_pass) || $new_pass != $verify_pass) {
		$jpwp->error->add('bad_user_pass', __('New password empty or invalid', 'jpwp'));
		wp_send_json_error($jpwp->error);
	}

	$jpwp->load->model('users');
	$response = $jpwp->users->update_user_pass($current_pass, $new_pass);

	if (is_wp_error($response)) {
		wp_send_json_error($response);
	} else {
		wp_send_json_success($response);
	}
},10);
