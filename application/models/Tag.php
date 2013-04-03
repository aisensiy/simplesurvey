<?php
class Tag extends ActiveRecord\Model {
	static $has_many = array(
		array('entity_tags'),
		array('entities', 'through' => 'entity_tags', 'order' => 'created_at desc')
	);
}