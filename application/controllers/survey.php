<?php

class Survey extends MY_Controller {
	function __construct() {
		parent::__construct();
	}
	
	function get($id) {
		try {
			$entity = Entity::find($id, array('include' => array('entity_tags' => array('tag'))));	
		}
		catch(ActiveRecord\RecordNotFound $e) {
			echo '{}';
			return;
		}
		$survey = array(
			'id'=> $entity->id,
			'title'=> $entity->title,
			'description'=> $entity->description,
			'questions' => json_decode($entity->content, TRUE)
		);

		$json = array(
			'survey' => $survey,
			'tags' => $entity->row_to_array(),
			'submited' => $this->_has_submited($id)
		);
		echo json_encode($json);
	}
	
	function save() {
		if(!$this->user) {
			echo json_encode(array('login' => FALSE));
			return;
		}

		$entity = new Entity;
		$json = json_decode($this->input->post('json'), TRUE);
		$tags = json_decode($this->input->post('tags'), TRUE);
		$tags = $tags ? $tags : array();
		$c = 1;
		for($i=0; $i<sizeof($json['questions']); $i++) {
			$json['questions'][$i]['id'] = $c++;
			if(!empty($json['questions'][$i]['questions']) && is_array($json['questions'][$i]['questions'])) 
				for($j=0; $j<sizeof($json['questions'][$i]['questions']); $j++)
					$json['questions'][$i]['questions'][$j][1] = $c++;
		}
		if(intval($json['id']) > 0) {
			$entity = Entity::find($json['id']);
		}
		$entity->title = $json['title'];
		$entity->description = $json['description'];
		$entity->content = json_encode($json['questions']);
		$entity->user_id = $this->user->id;
		if($entity->is_invalid()) {
			echo json_encode(array(
				'success' => FALSE
			));
			return;
		}
		
		$entity->save();
		if(intval($json['id']) > 0) EntityTag::del_tags($entity->id);
		EntityTag::add_tags($entity->id, $tags);
		//print_r($json);
		echo json_encode(array(
			'success' => $entity->id
		));
	}

	function update_status($id=FALSE) {
		if(!$this->user) {
			echo json_encode(array('success' => FALSE));
			return;
		}
		$val = $this->input->post('val');
		if($val === FALSE || $id === FALSE) {
			echo json_encode(array('success' => FALSE));
			return;
		}
		try {
			$entity = Entity::find($id);
			if($entity->user->id == $this->user->id) {
				$entity->status = $val;
				$entity->save();
				echo json_encode(array('success' => TRUE));
			} else {
				echo json_encode(array('success' => FALSE, 'msg' => '没有权限'));
			}
		} 
		catch(ActiveRecord\RecordNotFound $e) {
			echo json_encode(array('success' => FALSE));
			return;
		}
	}

	function answer($id) {
		$success = TRUE;
		if($this->_has_submited($id)) $success = FALSE;
		try {
			$entity = Entity::find($id);
		}
		catch(ActiveRecord\RecordNotFound $e) {
			$success = FALSE;
		}
		if($entity->status != 1) {
			$success = FALSE;
		}
		if(!$success) {
			echo json_encode(array('success' => FALSE));
			return;
		}
		$answer = Answer::create(array(
			'entity_id' => $id,
			'content' => $this->input->get('json')
		));
		if($answer->is_valid()) {
			$this->_noduplicate_strategy($id);
			echo json_encode(
				array('success' => TRUE, 'msg' => "save answer {$answer->id}")
			);
		}
		else 
			echo json_encode(
				array('success' => FALSE)
			);
	}

	function message() {
		$this->load->view('message.html');
	}

	function delete($id) {
		$success = TRUE;
		$entity = FALSE;
		if($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->user) {
			$success = FALSE;
		}
		try {
			$entity = Entity::find($id);
		}
		catch(ActiveRecord\RecordNotFound $e) {
			$success = FALSE;
		}
		if($success && ($entity && $this->user->id != $entity->user_id))
			$success = FALSE;
		if($success) {
			Answer::table()->delete(array('entity_id' => $entity->id));
			$entity->delete();
			echo json_encode(array('success' => TRUE));
		} else {
			echo json_encode(array('success' => FALSE));
		}
	}
	
	//pages
	function create() {
		if(!$this->user) redirect('/login');
		$this->load->view('survey/new.php');
	}
	function edit($id) {
		if(!$this->user) {
			redirect('/login');
			return;
		}
		try {
			$entity = Entity::find($id);
		}
		catch(ActiveRecord\RecordNotFound $e) {
			show_404();
			return;
		}
		if($entity->status != 0) {
			show_error('只有未发布的问卷才可以编辑');
			return;
		}
		$this->load->view('survey/edit.php');
	}
	function show($id) {
		try {
			$entity = Entity::find($id);
		}
		catch(ActiveRecord\RecordNotFound $e) {
			show_404();
		}
		$data = array();
		if($entity->status == 0) {
			show_error('只有发布的问卷才能这么做');
			return;
		}
		if($this->_has_submited($id)) {
			$data['error'] = '你已经填写过这个问卷了';
		}
		$this->load->view('survey/show.php', $data);
	}

	function _noduplicate_strategy($id) {
		$this->input->set_cookie(array(
			'name' => "sv_$id",
			'value' => time(),
			'expire' => '86500'
		));
	}

	function _has_submited($id) {
		if($this->input->cookie("sv_$id")) return true;
		return false;
	}
	
}