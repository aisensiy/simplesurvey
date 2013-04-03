<?php

class MY_Controller extends CI_Controller {

	var $user = FALSE;
	protected $ROW_PER_PAGE = 10;

	function __construct() {
		parent::__construct();
		$this->_check_user();
	}

	protected function paginate($customize) {
		$this->load->library('pagination');
		$config['full_tag_open'] = '<div class="pagination"><ul>';
		$config['full_tag_close'] = '</ul></div>';
		$config['first_link'] = false;
		$config['last_link'] = false;
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['prev_link'] = '&larr;';
		$config['prev_tag_open'] = '<li class="prev">';
		$config['prev_tag_close'] = '</li>';
		$config['next_link'] = '&rarr;';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		$config['cur_tag_open'] =  '<li class="active"><a href="#">';
		$config['cur_tag_close'] = '</a></li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config = array_merge($config, $customize);
		return $config;
	}

	protected function _check_user() {
		$this->user = $this->session->userdata('user_id') ? User::find($this->session->userdata('user_id')) : FALSE;
	}

}