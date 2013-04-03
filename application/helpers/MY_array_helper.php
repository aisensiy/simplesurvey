<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

function object2Array($d) {
	if (is_object($d)) {
		$d = get_object_vars($d);
	}

	if (is_array($d)) {
		return array_map(__FUNCTION__, $d);
	} else {
		return $d;
	}
}

function array2Object($d) {
	if (is_array($d)) {
		/*
		 * Return array converted to object
		 * Using __FUNCTION__ (Magic constant)
		 * for recursive call
		 */
		return (object) array_map(__FUNCTION__, $d);
	} else {
		// Return object
		return $d;
	}
}
