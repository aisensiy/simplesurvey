<?php 
class Choice extends ActiveRecord\Model {
	static $table_name = 'choice';
	static $belongs_to = array(
		array('question')
	);
}