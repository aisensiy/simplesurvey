<?php

class Result extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this->ROW_PER_PAGE = 50;
		$this->LIMIT = 20;
	}

	function index($id=0) {
		if(!$id) {
			show_404();
		}

		$this->entity_id = $id;
		$this->filter = $this->input->get('filter') ? json_decode($this->input->get('filter'), TRUE) : array();
		$entity = Entity::find($id, array('include' => 'answers'));
		$this->questions = json_decode($entity->content, TRUE);
		$this->answers = $entity->answers;
		$wrapper = array(
			'title' => $entity->title,
			'result' => array()
		);
		$wrapper = array(
			'title' => $entity->title,
			'result' => $this->_statistic()
		);
		echo json_encode($wrapper);
	}

	function text($id, $page=0) {
		if(!$this->user) redirect('/login');

		$index = $this->input->get('index');
		if(!$index) {
			show_error('出错了唉');
			return;
		}
		
		$result = $this->_get_text($id, $page, $index);
		
		$this->load->view('survey/text-list.php', $result);
	}

	function show($id) {
		if(!$this->user) redirect('/login');
		try {
			$entity = Entity::find($id);
		} catch(ActiveRecord\RecordNotFound $e) {
			show_404();
			return;
		}
		if($this->user->id != $entity->user_id) {
			show_404();
		}
		$this->load->view('survey/result.php');
	}

	

	function _get_text($id, $page, $index) {

		// 处理分页
		$num = Answer::first(array(
			'select' => 'count(*) as count',
			'conditions' => array('entity_id = ?', $id)
		));

		$config = $this->paginate(array(
			'base_url' => "/index.php/result/$id/",
			'total_rows' => $num->count,
			'per_page' => $this->ROW_PER_PAGE
		));

		$this->pagination->initialize($config); 
		
		//获取问卷结果
		$answers = Answer::all(array(
			'limit' => $this->ROW_PER_PAGE, 
			'offset' => $page, 
			'conditions' => array('entity_id = ?', $id),
			'order' => 'created_at desc'
		));

		//找到指定的问题
		$entity = Entity::find($id);
		foreach(json_decode($entity->content, TRUE) as $question) {
			if('field' . $question['id'] == $index) {
				if($question['type'] != 'text') {
					return FALSE;
				}
				$content = $question['content'];
				break;
			}
			if(strpos($question['type'], 'choice')) {
				foreach($question['choices'] as $choice) {
					if($choice['other'] && 'other' . $question['id'] == $index) {
						$content = $question['content'].' => '.$choice['content'];
						break;
					}
				}
			}
		}

		if(!isset($content)) {
			show_404();
			return;			
		}
		//拿到所有的结果
		$result = array();
		foreach($answers as $answer) {
			$json = json_decode($answer->content, TRUE);
			if(isset($json[$index]))
				$result[] = $json[$index];
		}

		return array(
			'url' => site_url('/result/show/' . $id),
			'content' => $content, 
			'results' => $result, 
			'paginate' => $this->pagination->create_links()
		);
	}
	function _statistic() {
		
		$result = array();
		//init
		foreach($this->questions as $index => $question) {
			$id = $this->entity_id;
			if($question['type'] == 'text') {
				$name = 'field' . $question['id'];
				$result[$name] = array(
					'type' => 'text',
					'content' => $question['content'],
					'url' => site_url("/result/text/$id?index=$name"),
					'results' => array()
				);
			} else if(strpos($question['type'], 'choice') !== FALSE) {
				$name = 'field' . $question['id'];
				$result[$name] = array(
					'content' => $question['content'],
					'results' => array()
				);
				$q_res = array();
				$choices = $question['choices'];
				foreach($question['choices'] as $choice) {
					$q_res[] = array(
						'content' => $choice['content'],
						'count' => 0
					);
					if($choice['other']) {
						$result['other' . ($index + 1)] = array(
							'type' => 'other',
							'content' => $question['content'].' => '.$choice['content'],
							'url' => site_url("/result/text/$id?index=other" . ($index + 1)),
							'results' => array()
						);
					}
				}
				$result[$name]['results'] = $q_res;
			} else if(strpos($question['type'], 'dropdown') !== FALSE) {
				$name = 'field' . $question['id'];
				$result[$name] = array(
					'content' => $question['content'],
					'results' => array()
				);
				$q_res = array();
				foreach($question['choices'] as $choice)
					$q_res[] = array(
						'content' => $choice,
						'count' => 0
					);
				$result[$name]['results'] = $q_res;
			} else if(strpos($question['type'], 'matrix') !== FALSE) {
				$subquestion = $question['questions'];
				$choices = $question['vals'];
				foreach($question['questions'] as $subq) {
					$name = 'field' . $subq[1];
					$result[$name] = array(
						'content' => $subq[0],
						'results' => array()
					);
					$sub_res = array();
					foreach($choices as $choice) 
						$sub_res[] = array(
							'content' => $choice,
							'count' => 0
						);
					$result[$name]['results'] = $sub_res;
				}
			}
		}

		//counter
		foreach ($this->answers as $a) {
			if($a->is_invalid()) {
				continue;
			}
			$answer = json_decode($a->content, TRUE);
			$valid = TRUE;
			foreach($answer as $field => $reply) {
				if(!$this->_check($field, $reply)) {
					$valid = FALSE;
					break;
				}
			}
			foreach($this->filter as $k => $v) {
				if(!isset($answer[$k])) {
					$valid = FALSE;
					break;
				}
			}
			if(!$valid) continue;
			foreach($answer as $field => $reply) {
				if(isset($result[$field]['type']) && 
					preg_match('/^(text)|(other)$/i', $result[$field]['type'])) {
					if(sizeof($result[$field]['results']) < $this->LIMIT)
						$result[$field]['results'][] = $reply;
					continue;	
				}	
				$reply = (array)$reply;
				foreach($reply as $id) {
					$result[$field]['results'][$id-1]['count']++;
				}
			}
		}
		
		return $result;
	}

	function _check($field, $i) {
		$n = sizeof($this->filter);
		//如果没有filter 或者 这个问题没有做为filter
		if(!$n || !isset($this->filter[$field])) return TRUE;
		if(is_array($i)) {
			$valid = FALSE;
			foreach($i as $j) {
				if(in_array($j, $this->filter[$field])) return TRUE;
			}
		}
		else {
			if(in_array($i, $this->filter[$field])) return TRUE;
		}
		return FALSE;
	}
}