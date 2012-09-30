<?php
include('inc.php');

if(preg_match('|^/timezone/([0-9\.-]+)/([0-9\.-]+)|', $_SERVER['REQUEST_URI'], $match) || (get('latitude') && get('longitude'))) {
	$db = new PDO(PDO_DSN, PDO_USER, PDO_PASS, array(PDO::ATTR_PERSISTENT => TRUE));

	$latitude = get('latitude') ?: $match[1];
	$longitude = get('longitude') ?: $match[2];

	$timezone = timezoneFromLocation($latitude, $longitude);

	if($timezone) {
		$tz = new DateTimeZone($timezone);
		$now = new DateTime(date('c'));
		$now->setTimeZone($tz);
	
		respond(array(
			'timezone' => $timezone,
			'offset' => $now->format('P'),
			'seconds' => (int)$now->format('Z')
		));
	} else {
		respond(array(
			'error' => 'no_data'
		));
	}

} else {
	header('HTTP/1.1 404 Not Found');
	respond(array(
		'error' => 'not_found'
	));
}

function respond($data) {
	header('Content-type: application/json');
	if(preg_match('/callback=([^\&]+)/', $_SERVER['REQUEST_URI'], $match) || get('callback')) {
		$cb = get('callback') ?: $match[1];
		echo $cb . '(';
		echo json_encode($data);
		echo ')';
	} else {
		echo json_encode($data);
	}
}