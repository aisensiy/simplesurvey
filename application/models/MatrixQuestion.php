<?php

class MatrixQuestion extends Question {
	
	function _save($json) {
		parent::_save($json);
		$check = explode('-', $this->type);
		$check = $check[0];
		$vals = $json['vals'];
		for($i=0, $n=sizeof($json['questions']); $i<$n; $i++) {
			$q = $json['questions'][$i];
			$sub_q = array(
				'parent_id' => $this->id,
				'content' => $q[0],
				'no' => $i+1,
				'type' => 'sub-choice',
				'survey_id' => $this->survey_id,
				'choices' => $vals
			);
			$question = new DropdownQuestion();
			$question->_save($sub_q);
		}
	}
	
	function _to_json() {
		$questions = array();
		$vals = array();
		foreach($this->questions as $question)
			$questions[]  = array($question->content, $question->id);
		$tmp = $this->questions;
		foreach($tmp[0]->choices as $choice) 
			$vals[]  = $choice->content;
		$json = parent::_to_json();
		$json['questions'] = $questions;
		$json['vals'] = $vals;
		return $json;
	}
}
