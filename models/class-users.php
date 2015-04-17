<?php

namespace jpwp\models;

defined('ABSPATH') or die('No direct script access allowed');

class Users {

	/**
	 * Maximum login attempts
	 * @since v0.0.1
	 * @var int
	 */
	private $max_login_attempts;

	public function __construct() {
		$this->max_login_attempts = (int) get_option('max-login-attemps', -1);
	}

	/**
	 * Inicio de sesion de usuarios
	 * @since v0.0.1
	 */
	public function user_login($submit) {
		$jpwp = get_instance();

		$user_id = username_exists($submit['user_login']);
		if (empty($user_id)) {
			$jpwp->error->add('user_failed', 'Usuario o contraseña incorrectos');
			return $jpwp->error;
		}

		$user_blocked = (bool) $this->is_user_blocked($user_id);
		if ($user_blocked) {
			$jpwp->error->add('user_blocked', 'Disculpa, usuario bloqueado');
			return $jpwp->error;
		}

		$user = wp_signon($submit, false);

		if (is_wp_error($user)) {
			$this->add_user_attempt($user_id);

			$user_blocked = (bool) $this->is_user_blocked($user_id);

			if ($user_blocked) {
				$error = new WP_Error('user_blocked', 'Disculpa, usuario bloqueado');
				return $error;
			} else {
				return $user;
			}
		} else {
			$this->clear_user_attempt($user_id);
			$response[] = array(
					'code' => 'user_signon_success',
					'message' => 'Has iniciado sesión exitosamente',
			);
			return $response;
		}
	}

	public function user_register($userdata) {
		$user_id = wp_insert_user($userdata);

		if (is_wp_error($user_id)) {
			return $user_id;
		} else {
			add_user_meta($user_id, 'show_admin_bar_front', 'false');
			$response[] = array(
					'code' => 'user_register_success',
					'message' => 'Registro exitoso',
					'user_id' => $user_id,
			);
			return $response;
		}
	}

	/**
	 * Verifica si el usuario esta bloqueado
	 * @param int|string $user_id
	 * @return boolean
	 * @since v0.0.1
	 */
	private function is_user_blocked($user_id) {

		if (!is_int($user_id)) {
			$user_id = (bool) username_exists($user_id);
		}

		if ($user_id == 0) {
			return FALSE;
		}

		$user_blocked = (bool) get_user_meta($user_id, 'user_blocked', FALSE);

		if ($this->max_login_attempts < 0) return FALSE;

		if ($user_blocked) return TRUE;

		$user_attemps = get_user_meta($user_id, 'login_attempts', TRUE);

		if ($user_attemps > $this->max_login_attempts) {
			$this->block_user($user_id);
			$user_blocked = TRUE;
		}
		return $user_blocked;
	}

	/**
	 * Bloquear usuarios
	 * @param int $user_id
	 * @since v0.0.1
	 */
	private function block_user($user_id) {
		add_user_meta($user_id, 'user_blocked', TRUE, TRUE);
	}

	/**
	 * Agregar intentos fallidos al contador de usuarios
	 * @param int $user_id
	 * @since v0.0.1
	 */
	private function add_user_attempt($user_id) {
		$login_attempts = (int) get_user_meta($user_id, 'login_attempts', TRUE);
		$login_attempts++;
		update_user_meta($user_id, 'login_attempts', $login_attempts);
	}

	/**
	 * Desbloquear usuarios y borrar intentos fallidos
	 * @param int $user_id
	 * @since v0.0.1
	 */
	private function clear_user_attempt($user_id) {
		update_user_meta($user_id, 'login_attempts', 0);
	}

	public function update_user_pass($current_pass, $new_pass) {

		$jpwp = get_instance();

		if (!is_user_logged_in()) {
			$jpwp->error->user_not_logged(__FUNCTION__);
			return $jpwp->error;
		}

		$user_id = get_current_user_id();
		$current_user = get_user_by('id', $user_id);

		$valid_pass = wp_check_password($current_pass, $current_user->get('user_pass'), $user_id);

		if (!$valid_pass) {
			$jpwp->error->add('bad_user_pass', __('Current pass invalid', 'jpwp'));
			return $jpwp->error;
		}

		wp_set_password($new_pass, $user_id);

		$data[] = array(
				'code' => 'success_update',
				'message' => 'Contraseña actualizada exitosamente'
		);
		return $data;
	}

}
