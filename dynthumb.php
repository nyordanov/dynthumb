<?php

/**
 * A library similar to timthumb, but using imagick
 * 
 * @author Nikolay Yordanov <me@nyordanov.com> 
 * @link http://nyordanov.com
 * 
 * @param string src
 * @param int w
 * @param int h
 * @param bool zc
 * 
 * @example /dynthumb.php?src=pic.jpg&w=100&h=100&zc=1
 * 
 * @version 1.0
 */

define('DEBUG', true); // set to false in production

if(DEBUG == true) {
	error_reporting(E_ALL);
	ini_set('display_error', 1);
}

$allowed_domains = array(
	'flickr.com'
);

$source = $_GET['src'];
$uri_components = parse_url($_GET['src']);

if(isset($uri_components['host']) && !in_array($uri_components['host'], $allowed_domains))
	trigger_error('Host not allowed: ' . $uri_components['host'], E_USER_ERROR);
