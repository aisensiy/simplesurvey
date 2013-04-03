<?php
class Survey_model extends ActiveRecord\Model {
	static $table_name = 'survey';
	static $has_many = array(
		array('questions', 'foreign_key' => 'survey_id', 'order' => 'no asc')
	);
	static $belongs_to = array(
		array('users', 'class_name' => 'User')
	);
	
	static $validates_presence_of = array(
		array('title')
	);
	static $validates_size_of = array(
		array('title', 'within' => array(1, 80)),
		array('description', 'within' => array(1, 500))
	);
	
	function _to_json() {
		$json = array(
			'id' => $this->id,
			'title' => $this->title,
			'decription' => $this->description
		);
		$questions = array();
		foreach($this->questions as $question) {
			$o = casttoclass(Question::factory($question->type), $question);
			$questions[] = $o->_to_json();
		}
		$json['questions'] = $questions;
		return $json; 
	}
}
