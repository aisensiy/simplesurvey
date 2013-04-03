<?php

class Auth extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	function denglu() {
		$this->load->library('denglu');
		if(!empty($_GET['token'])){
			try {
				$userInfo = $this->denglu->getUserInfoByToken($_GET['token']);
				$store = array_intersect_key($userInfo, array_flip(Bind::$allowed));
				$count = Bind::first(array(
					'select' => 'count(*) as count',
					'conditions' => array('mediaUserID = ? AND mediaID = ?', $store['mediaUserID'], $store['mediaID'])
				));

				if($count->count == 1) {
					$bind = Bind::find(array(
						'conditions' => array('mediaUserID = ? AND mediaID = ?', $store['mediaUserID'], $store['mediaID']),
						'include' => array('user')
					));
					$bind->update_attributes($store);
					User::login($bind->user->id);
				} else {
					$user = User::create(array(
						'nickname' => $store['screenName']
					));
					$store = array_merge($store, array('user_id' => $user->id));
					$bind = Bind::create($store);
					User::login($user->id);
				}
				redirect('/');
            } 	
            catch(DengluException $e) {
              	echo 'error!';
            }
        }
	}

	function create() {
		$user = User::create(array(
			'email' => $this->input->post('email'),
			'password' => $this->input->post('password'),
			'nickname' => $this->input->post('nickname')
		));
		if($user->is_valid()) {
			User::login($user->id);
			$result = array('success' => TRUE);
		} else {
			$result = array('success' => FALSE, 'msg' => implode(", ", $user->errors->full_messages(array('email' => '邮箱', 'nickname' => '昵称'))));
		}
		echo json_encode($result);
	}

	function read() {
		$user = User::validate_login($this->input->post('email'), $this->input->post('password'));
		if($user) {
			$result = array('success' => TRUE);
		} else {
			$result = array('success' => FALSE);
		}
		echo json_encode($result);
	}

	function is_login() {
		$result = array();
		if($this->user) {
			$result = array(
				'success' => TRUE,
				'data' => array(
					'nickname' => $this->user->nickname,
					'email' => $this->user->email,
					'id' => $this->user->id,
					'src' => $this->user->src
				)
			);
		}
		else
			$result = array(
				'success' => FALSE
			);
		echo json_encode($result);
	}

	function logout() {
		User::logout();
		redirect('/');
	}

	//pages
	function login() {
		$this->load->view('auth/login.php');
	}

	function signup() {
		$this->load->view('auth/signup.php');	
	}
}
