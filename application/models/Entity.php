<?php

class Entity extends ActiveRecord\Model {

	static $has_many = array(
		array('answers', 'order' => 'created_at desc'),
		array('entity_tags'),
		array('tags', 'through' => 'entity_tags')
	);
	static $belongs_to = array(
		array('user')
	);

	function row_to_array() {
		$a = array();
		foreach($this->entity_tags as $et)
			$a[] = $et->tag->content;
		return $a;
	}

	function get_answer_num() {
		$num = Answer::first(
			array(
				'select' => 'count(*) as count',
				'conditions' => "entity_id = {$this->id}"
			)
		);
		return $num->count;
	}
	// validate
	static $validates_presence_of = array(
      	array('title'),
     	array('content'),
     	array('user_id')
    );

    static $validates_size_of = array(
    	array('title', 'within' => array(1,200), 'too_short' => 'too short!'),
    	array('description', 'maximum' => 2000, 'too_long' => 'should be short and sweet')
    );

    function validate() {
    	if(!$this->validate_content())
    		$this->errors->add('content', 'invalid');
    }

    /**
     * 检查允许的属性以及必须有的属性
     */
    protected static function check_attr_in_keys($question, $allowed, $not_null) {
    	foreach($question as $k => $v) 
    		if(!in_array($k, $keys)) return false;
    	foreach($not_null as $k) 
    		if(!isset($question[$k])) return false;
    	return true;
    }

    protected function validate_content() {
    	$default_attrs = static::$default_attrs;
    	$default_not_null_attrs = static::$default_not_null_attrs;
    	$check_attr_in_keys = function ($question, $allowed, $not_null) {
	    	foreach($question as $k => $v) 
	    		if(!in_array($k, $allowed)) return false;
	    	foreach($not_null as $k) 
	    		if(!isset($question[$k])) return false;
	    	return true;
	    };
	    //各种问题的规则
    	$rules = array(
	    	'choice' => function($question) use ($default_attrs, $default_not_null_attrs, $check_attr_in_keys) {

	    		$check_choice = function($choices) use ($check_attr_in_keys) {
	    			$allowed = array('content', 'other');
	    			$not_null = array('content');
	    			foreach($choices as $choice) {
	    				if(!$check_attr_in_keys($choice, $allowed, $not_null)) {
	    					return false;
	    				}
	    			}
	    			return true;
	    		};
	    		$allowed_attrs = array_merge($default_attrs, array('choices', 'other', 'column'));
	    		$not_null_attrs = array_merge($default_not_null_attrs, array('choices'));
	    		if(!$check_attr_in_keys($question, $allowed_attrs, $not_null_attrs)) {
	    			return false;
	    		}

	    		if(!is_array($question['choices']) || 
	    			sizeof($question['choices']) < 2 || 
	    			!$check_choice($question['choices'])) return false;
	    		return true;
	    	},
	    	'dropdown' => function($question) use ($default_attrs, $default_not_null_attrs, $check_attr_in_keys) {
	    		$check_choice = function($choices) {
	    			foreach($choices as $choice) {
	    				if(!is_scalar($choice)) return false;
	    			}
	    			return true;
	    		};
	    		$allowed_attrs = array_merge($default_attrs, array('choices', 'other', 'column'));
	    		$not_null_attrs = array_merge($default_not_null_attrs, array('choices'));
	    		if(!$check_attr_in_keys($question, $allowed_attrs, $not_null_attrs)) return false;
	    		if(!is_array($question['choices']) || 
	    		    sizeof($question['choices']) < 2 || 
	    		    !$check_choice($question['choices'])) return false;	
	    		return true;
	    	},
	    	'matrix' => function($question) use ($default_attrs, $default_not_null_attrs, $check_attr_in_keys) {
	    		$check_question = function($questions) {
	    			foreach($questions as $question)
	    				if(!is_array($question) || 
	    					sizeof($question) != 2 ||
	    					!is_scalar($question[0]) ||
	    					!is_int($question[1]))
	    					return false;
	    			return true;
	    		};

	    		$allowed_attrs = array_merge($default_attrs, array('questions', 'vals'));
	    		$not_null_attrs = array_merge($default_not_null_attrs, array('questions', 'vals'));
	    		if(!$check_attr_in_keys($question, $allowed_attrs, $not_null_attrs)) return false;
	    		if(!is_array($question['vals']) ||
	    			!is_array($question['questions']) ||
	    			!$check_question($question['questions']))
	    			return false;
	    		return true;
	    	},
	    	'text' => function($question) use ($default_attrs, $default_not_null_attrs, $check_attr_in_keys) {
	    		if(!$check_attr_in_keys($question, $default_attrs, $default_not_null_attrs))
	    			return false;
	    		return true;
	    	}
	    );
    	$get_type = function ($type) {
    		preg_match('/^(\w+)-?(\w+)?$/i', $type, $match);
			if(!isset($match[2]))
				return $match[1];
			else
				return $match[2];
    	};
    	//可以json_decode
    	//type 在指定的范围内
    	//每种 type 要符合其格式的规定
    	$questions = json_decode($this->content, TRUE);
    	if(!is_array($questions) || sizeof($questions) == 0) return false;
    	foreach($questions as $question) {
    		if(!isset($question['type'])) {
    			return false;
    		}
    		$type = $question['type'];
    		if(!in_array($type, static::$question_types)) {
    			return false;
    		}
    		$type = $get_type($type);
    		if(!$rules[$type]($question)) {
    			//print_r($question);
    			return false;
    		}
    	}
    	return true;
    }
    //允许的问题类型
    static $question_types = array('multi-choice', 'single-choice', 'multi-dropdown', 'single-dropdown', 'multi-matrix', 'single-matrix', 'text');
    static $default_attrs = array('id', 'type', 'content');
    static $default_not_null_attrs = array('content', 'id', 'type');


}
