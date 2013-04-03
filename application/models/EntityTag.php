<?php

class EntityTag extends ActiveRecord\Model {
	static $table_name = 'entity_tag';
	static $belongs_to = array(
		array('entity'),
		array('tag')
	);

	static function del_tags($entity_id) {
		EntityTag::table()->delete(array('entity_id' => $entity_id));
	}

	static function add_tags($entity_id, $tags) {
		foreach($tags as $tag) {
			
			//echo $tag;
			$row = Tag::find_by_content($tag);
			if(!$row) {
				$row = Tag::create(array('content' => $tag));
			}
			//$row = Tag::create(array('content' => $tag));
			EntityTag::create(array(
				'entity_id' => $entity_id,
				'tag_id' => $row->id
			));
		}
	}
}