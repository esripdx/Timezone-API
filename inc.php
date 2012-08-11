<?php
define('PDO_DSN', 'pgsql:dbname=timezone;host=127.0.0.1');
define('PDO_USER', 'loqi');
define('PDO_PASS', 'tinydino');

function timezoneFromLocation($latitude, $longitude) {
	global $db;

	$result = FALSE;

	// First check for cities within 100km
	$timezones = $db->prepare("SELECT timezone FROM timezone WHERE ST_DWithin(ST_GeographyFromText('SRID=4326;POINT(" . $longitude . ' ' . $latitude . ")'), location, 100000, false) order by st_distance(ST_GeographyFromText('SRID=4326;POINT(" . $longitude . ' ' . $latitude . ")'), location) LIMIT 1");
	$timezones->execute();

	while($tz = $timezones->fetch()) {
		$result = $tz['timezone'];
	}

	// $latN = $latitude - 1 % 180;
	// $latS = $latitude + 1 % 180;
	// $lngE = $longitude + 1 % 360;
	// $lngW = $longitude - 1 % 360;

	// $timezones = $db->prepare("SELECT timezone FROM timezone WHERE lat > $latN AND lat <= $latS AND lng > $lngW AND lng < $lngE ORDER BY st_distance(ST_GeographyFromText('SRID=4326;POINT(" . $longitude . ' ' . $latitude . ")'), location) LIMIT 1");
	// $timezones->execute();
	// while($tz = $timezones->fetch()) {
	// 	$result = $tz['timezone'];
	// }

	if($result)
		return $result;

	// If nothing found that close, check again within 6000km
	$timezones = $db->prepare("SELECT timezone FROM timezone WHERE ST_DWithin(ST_GeographyFromText('SRID=4326;POINT(" . $longitude . ' ' . $latitude . ")'), location, 6000000, false) order by st_distance(ST_GeographyFromText('SRID=4326;POINT(" . $longitude . ' ' . $latitude . ")'), location) LIMIT 1");
	$timezones->execute();
	while($tz = $timezones->fetch()) {
		$result = $tz['timezone'];
	}

	return $result;
}

function get($k) {
	return array_key_exists($k, $_GET) ? $_GET[$k] : FALSE;
}