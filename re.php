<?php
$url = "http://open.denglu.cc/receiver?" . $_SERVER['QUERY_STRING'];
header('Location: ' . $url);