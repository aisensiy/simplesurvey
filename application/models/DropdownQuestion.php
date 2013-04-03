<?php

class DropdownQuestion extends Question {
	function _save($json) {
		parent::_save($json);
		for($i=0, $n=sizeof($json['choices']); $i<$n; $i++) {
			$c = $json['choices'][$i];
			$choice = new Choice(array(
				'content' => $c,
				'question_id' => $this->id,
				'no' => $i + 1
			));
			$choice->save();
		}
	}
	
	function _to_json() {
		$choices = array();
		foreach($this->choices as $choice)
			$choices[] = $choice->content; 
		$json = parent::_to_json();
		$json['choices'] = $choices;
		return $json;
	}
}
