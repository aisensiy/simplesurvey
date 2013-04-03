<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
function casttoclass($class, $object)
{
	return unserialize(preg_replace('/^O:\d+:"[^"]++"/', 'O:' . strlen($class) . ':"' . $class . '"', serialize($object)));
}