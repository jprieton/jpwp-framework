<?php

add_action('wp_ajax_nopriv_user_register', function () {

	$jpwp = get_instance();

	$verify_nonce = (bool) $jpwp->input->verify_wpnonce('user_register');

	if (!$verify_nonce) {
		$jpwp->error->method_not_supported(__FUNCTION__);
		wp_send_json_error($jpwp->error);
	}

	$submit = array(
			'user_login'    => $jpwp->input->post('user_email'),
			'user_pass'     => $jpwp->input->post('user_password'),
			'user_email'    => $jpwp->input->post('user_email')
	);

	if (!is_email($submit['user_email'])) {
		$jpwp->error->add('bad_user_email', 'El email suministrado es inválido');
		wp_send_json_error($jpwp->error);
	}

	$jpwp->load->model('users');
	$response = $jpwp->users->user_register($submit);

	do_action('after_user_register', $response);

	if (is_wp_error($response)) {
		wp_send_json_error($response);
	} else {
		wp_send_json_success($response);
	}
},5);
