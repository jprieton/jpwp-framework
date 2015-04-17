<?php

add_action('wp_ajax_nopriv_user_login', function () {

	$jpwp = get_instance();

	$verify_nonce = (bool) $jpwp->input->verify_wpnonce('user_login');

	if (!$verify_nonce) {
		$jpwp->error->method_not_supported(__FUNCTION__);
		wp_send_json_error($jpwp->error);
	}

	$submit = array(
			'user_login'    => $jpwp->input->post('user_login'),
			'user_password' => $jpwp->input->post('user_password'),
			'remember'      => $jpwp->input->post('remember')
	);
	
	$jpwp->load->model('users');
	$response = $jpwp->users->user_login($submit);

	if (is_wp_error($response)) {
		wp_send_json_error($response);
	} else {
		wp_send_json_success($response);
	}
}, 5);
