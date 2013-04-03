<?php 
class Question extends ActiveRecord\Model {
	static $table_name = 'question';
	static $belongs_to = array(
		array('survey', 'class_name' => 'Survey_model'),
		array('parent', 'class_name' => 'Question')
	);
	static $has_many = array(
		array('choices', 'foreign_key' => 'question_id', 'order' => 'no asc'),
		array('questions', 'foreign_key' => 'parent_id', 'class_name' => 'Question', 'order' => 'no asc') 
	);
	
	public function _save($json) {
		$this->survey_id = $json['survey_id'];
		$this->no = $json['no'];
		$this->content = $json['content'];
		$this->type = $json['type'];
		if(!empty($json['parent_id'])) $this->parent_id = $json['parent_id'];
		$this->save();
	}
	
	public static function factory($type) {
		$name = explode('-', $type);
		if(sizeof($name) == 1) return ucfirst($name[0]) . 'Question';
		else if(sizeof($name) == 2) return ucfirst($name[1]) . 'Question';
	}
	
	function _to_json() {
		return json_decode($this->to_json(array('except' => array('created_at', 'updated_at', 'lastmodifiedby', 'no', 'parent_id'))), TRUE);
	}
}

