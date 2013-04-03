<?php

class Answer extends ActiveRecord\Model {
	static $belongs_to = array(
		array('entity')
	);

	function validate() {
		if(!$this->content_valid()) {
			$this->errors->add('提交内容', '与问卷不匹配');
		}
	}

	private function _valid_logic($rule, $field, $value) {
		//如果某个field不存在则失败
		//如果某个 field value 不在合法范围则失败
		//如果某个 single field 结果为数组则失败
		if(strpos($field, 'field') !== FALSE && 
			preg_match('/(matrix)|(choice)|(dropdown)/i', $rule['type'])) {
			
			if(is_array($value)) {
				if(preg_match('/single/', $field)) return FALSE;
				else {
					foreach($value as $v) 
						if(!(intval($value) >= $rule['range'][0] &&
							 intval($value) <= $rule['range'][1]))
							return FALSE;
				}
			}
			else if(
			  !(intval($value) >= $rule['range'][0] &&
				intval($value) <= $rule['range'][1])) {
				return FALSE;
			}
		}
		else if(strpos($field, 'other') !== FALSE || 
			(strpos($field, 'field') !== FALSE && preg_match('/text/i', $rule['type']))) {
			if(!is_scalar($value))
				return FALSE;
		}

		else 
			return FALSE;

		return TRUE;
	}
	private function content_valid() {
		$answer = json_decode($this->content, TRUE);
		if(!is_array($answer) || sizeof($answer) == 0) return false;
		try {
			$entity = $this->entity;
		}
		catch(ActiveRecord\RecordNotFound $e) {
			return FALSE;
		}
		$questions = json_decode($entity->content, TRUE);
		$question_map = array();
		foreach($questions as $question) {
			if(strpos($question['type'], 'matrix') !== FALSE) {
				$choices = $question['vals'];
				$range = array(1, sizeof($choices));
				foreach($question['questions'] as $subq) {
					$name = 'field' . $subq[1];
					$question_map[$name] = array(
						'range' => $range,
						'type'  => $question['type']
					);
				}
			} else if(strpos($question['type'], 'dropdown') !== FALSE) {
				$range = array(1, sizeof($question['choices']));
				$name = 'field' . $question['id'];
				$question_map[$name] = $range;
				$question_map[$name] = array(
					'range' => $range,
					'type'  => $question['type']
				);
			} else if(strpos($question['type'], 'choice') !== FALSE) {
				$name = 'field' . $question['id'];
				$range = array(1, sizeof($question['choices']));
				$question_map[$name] = array(
					'range' => $range,
					'type'  => $question['type']
				);
				foreach($question['choices'] as $choice) {
					if(isset($choice['other']))
						$question_map['other' . $question['id']] = array(
							'range' => 'text',
							'type'  => $question['type']
						);
				}
			} else if(strpos($question['type'], 'text') !== FALSE) {
				$name = 'field' . $question['id'];
				$question_map[$name] = array(
					'range' => 'text',
					'type'  => $question['type']
				);
			}
		}
		//print_r($question_map);
		foreach($answer as $field => $value) {
			if(!isset($question_map[$field])) {
				return FALSE;
			}
			else if(!$this->_valid_logic($question_map[$field], $field, $value)) {
				//print_r($question_map[$field]);
				//print_r($field);
				return FALSE;
			}
		}
		return TRUE;
	}
}
