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
define('ALLOW_SUBDOMAINS', true); // allow pictures to be on a subdomain of allowed host

if(DEBUG == true) {
	error_reporting(E_ALL);
	ini_set('display_error', 1);
}

$allowed_domains = array(
	'flickr.com'
);

$source = $_GET['src'];
$uri_components = parse_url($_GET['src']);
$width = isset($_GET['w']) ? abs(intval($_GET['w'])) : 0;
$height = isset($_GET['h']) ? abs(intval($_GET['h'])) : 0;
$zc = isset($_GET['zc']) ? intval($_GET['zc']) : 0;

if(isset($uri_components['host']) && !in_array($uri_components['host'], $allowed_domains)) {
	$bad_domain = true;
	
	if(ALLOW_SUBDOMAINS === true)
		foreach($allowed_domains as $domain)
			if(preg_match('/\.' . str_replace('.', '\.', $domain) . '$/i', $uri_components['host'])) {
				$bad_domain = false;
				break;
			}
			
	if($bad_domain)
		trigger_error('Host not allowed: ' . $uri_components['host'], E_USER_ERROR);
}

if(isset($uri_components['host']) || file_exists($source))
	$image_handle = fopen($source, 'rb');
else if(file_exists($_SERVER['DOCUMENT_ROOT'] . $source))
	$image_handle = fopen($_SERVER['DOCUMENT_ROOT'] .$source, 'rb');
else
	trigger_error('Image file (src) not found', E_USER_ERROR);
	
$image = new Imagick();
$image->readimagefile($image_handle);

if($zc)
	try {
		$image->cropthumbnailimage($width, $height);
	} catch (ImagickException $e) {
		trigger_error($e->getMessage(), E_USER_ERROR);
	}
else
	try {
		$image->thumbnailimage($width, $height, true);	
	} catch(ImagickException $e) {
		trigger_error($e->getMessage(), E_USER_ERROR);
	}

$cache_time = 157680000;
header('Cache-Control: max-age=' . $cache_time . ',must-revalidate');
header('Expires: ' . gmdate("D, d M Y H:i:s", time() + $cache_time));

header('Content-Type: image/' . $image->getImageFormat());
echo $image;
