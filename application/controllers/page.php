<?php

class Page extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	function index($page=0) {
		$entities = Entity::all(
			array(
				'limit' => $this->ROW_PER_PAGE, 'offset' => $page, 
				'conditions' => array('status = ?', 1),
				'order' => 'created_at desc'
			)
		);
		$num = Entity::first(array(
			'select' => 'count(*) as count',
			'conditions' => array('status = ?', 1)
		));
		$config = $this->paginate(array(
			'base_url' => '/index.php/page/index/',
			'total_rows' => $num->count,
			'per_page' => $this->ROW_PER_PAGE
		));
		$this->pagination->initialize($config); 
		$this->load->view('home.php', array('entities' => $entities, 'paginate' => $this->pagination->create_links()));
	}
	
	function mysurvey($page=0) {
		if(!$this->user)
			redirect('/login');	
		else
			$this->_survey_list(
				array('conditions' => array('user_id = ?', $this->user->id)), 
				'/index.php/page/mysurvey/', 
				$page,
				TRUE
			);
	}

	function tag($id=0, $page=0) {
		if(!$id) {
			show_404();
			return;
		}
		try {
			$tag = Tag::find($id);
		} 
		catch(ActiveRecord\RecordNotFound $e) {
			show_404();
			return;
		}
		$this->_survey_list(
			array(
				'joins' => 'inner join entity_tag e on(entities.id = e.entity_id)',
				'conditions' => "e.tag_id = $id"
			),
			"/page/tag/$id/",
			$page
		);
		/*
		$num = Entity::first(array(
			'select' => 'count(*) as count',
			'conditions' => array('user_id = ?', $this->user->id)
		));
		$config = $this->paginate(array(
			'base_url' => '/index.php/page/mysurvey/',
			'total_rows' => $num->count,
			'per_page' => $this->ROW_PER_PAGE
		));
		$this->pagination->initialize($config); 
		
		$entities = Entity::all(array(
			'limit' => $this->ROW_PER_PAGE, 
			'offset' => $page, 
			'conditions' => array('user_id = ?', $this->user->id),
			'order' => 'created_at desc'
		));
		$this->load->view('survey-list.php', array('entities' => $entities, 'paginate' => $this->pagination->create_links()));			
		*/
	}

	function _survey_list($query, $url, $start, $login=FALSE) {
		$count = array_merge(array('select' => 'count(*) as count'), $query);
		$num = Entity::first($count);
		$config = $this->paginate(array(
			'base_url' => $url,
			'total_rows' => $num->count,
			'per_page' => $this->ROW_PER_PAGE
		));
		$this->pagination->initialize($config); 
		
		$select = array_merge(
			array(
				'limit' => $this->ROW_PER_PAGE, 
				'offset' => $start, 
				'order' => 'created_at desc',
				'include' => array('entity_tags' => array('tag'))
			),
			$query
		);
		$entities = Entity::all($select);
		$this->load->view('survey-list.php', array('login' => $login, 'entities' => $entities, 'paginate' => $this->pagination->create_links()));	
	}

}