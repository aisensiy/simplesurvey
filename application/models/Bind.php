<?php
class Bind extends ActiveRecord\Model {
	static $belongs_to = array(
		array('user')
	);
	static $allowed = array(
		'gender', 'mediaUserID', 'mediaID', 
		'name', 'screenName', 'profileImageUrl',
		'city', 'homepage', 'description',
		'createTime');
}