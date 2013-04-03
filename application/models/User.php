<?php

class User extends ActiveRecord\Model {
	
	static $validates_format_of = array(
      	array('email', 'with' => 
      		'/^([\w\-\.]+@[\w\-\.]+(\.\w+)+)|$/')
    );

    static $validates_uniqueness_of = array(
    	array('email', 'message' => '已经被注册'),
      //array('nickname', 'message' => '已经被使用')
	);

	static $validates_presence_of = array(
      	//array('nickname'),
     	//array('password')
    );

	static $has_one = array(
		array('bind')
	);
	static $has_many = array(
		array('entities')
	);
	function check() {
		$user = User::find_by_email($this->email);
		return $user == FALSE;
	}
	private function hash_password($password) {
		$salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
		$hash = hash('sha256', $salt . $password);

		return $salt . $hash;
	}
	function set_password($password) {
		$this->password = $this->assign_attribute('password', $this->hash_password($password));
	}
	function get_src() {
		if($this->bind) {
			return $this->bind->profileimageurl;
		}
		else if($this->email) {
			$CI =& get_instance();
			$CI->load->spark('gravatar_helper/1.3.0');
			$CI->load->helper('url');
			return Gravatar_helper::from_email($this->email, null, null, site_url('/avatar.jpg'));
		} else {
			return site_url('/avatar.jpg');
		}
	}
	private function validate_password($password) {
		$salt = substr($this->password, 0, 64);
		$hash = substr($this->password, 64, 64);

		$password_hash = hash('sha256', $salt . $password);

		return $password_hash == $hash;
	}

	static function validate_login($email, $password) {
		if(!$email || !$password) return FALSE;
		$user = User::find_by_email($email);

		if($user && $user->validate_password($password)) {
			User::login($user->id);
			return $user;
		}
		else 
			return FALSE;
	}

	static function login($user_id) {
		$CI =& get_instance();
		$CI->session->set_userdata('user_id', $user_id);
	}

	static function logout() {
		$CI =& get_instance();
		$CI->session->sess_destroy();
	}
}
