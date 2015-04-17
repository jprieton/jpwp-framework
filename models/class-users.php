<?php

namespace jpwp\models;

defined('ABSPATH') or die('No direct script access allowed');

class Users {

	/**
	 * Maximum login attempts
	 * @var int
	 */
	private $max_login_attempts;

	public function __construct() {
		$this->max_login_attempts = (int) get_option('max-login-attemps', -1);
	}

	/**
	 * Inicio de sesion de usuarios
	 * JSON
	 */
	public function user_login($submit) {
		global $jpwp;
		$jpwp instanceof jpwp;

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

	/**
	 * Verifica si el usuario esta bloqueado
	 * @param int|string $user_id
	 * @return boolean
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
	 */
	private function block_user($user_id) {
		add_user_meta($user_id, 'user_blocked', TRUE, TRUE);
	}

	/**
	 * Agregar intentos fallidos al contador de usuarios
	 * @param int $user_id
	 */
	private function add_user_attempt($user_id) {
		$login_attempts = (int) get_user_meta($user_id, 'login_attempts', TRUE);
		$login_attempts++;
		update_user_meta($user_id, 'login_attempts', $login_attempts);
	}

	/**
	 * Desbloquear usuarios y borrar intentos fallidos
	 * @param int $user_id
	 */
	private function clear_user_attempt($user_id) {
		update_user_meta($user_id, 'login_attempts', 0);
	}

}
