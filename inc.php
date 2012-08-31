<?php
include('config.php');

function timezoneFromLocation($latitude, $longitude) {
	global $db;

	$result = FALSE;

	// First check for cities within 100km
	$timezones = $db->prepare("SELECT timezone FROM timezone WHERE ST_DWithin(ST_GeographyFromText('SRID=4326;POINT(" . $longitude . ' ' . $latitude . ")'), location, 100000, false) order by st_distance(ST_GeographyFromText('SRID=4326;POINT(" . $longitude . ' ' . $latitude . ")'), location) LIMIT 1");
	$timezones->execute();

	while($tz = $timezones->fetch()) {
		$result = $tz['timezone'];
	}

	if($result)
		return $result;

	// If nothing was found that close, check again within 6000km
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
