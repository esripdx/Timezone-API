<?php
include('config.php');

function timezoneFromLocation($latitude, $longitude) {
	global $db;

	$result = FALSE;

	$kmInDeg = LatLngDist(array($latitude,$longitude),array($latitude,$longitude+1));
	$degInKm = 1 / $kmInDeg;
	$radiusSearch = 100; // First check for cities within 100km
	$radiusDeg = $radiusSearch * $degInKm;
	$sq[0] = array($latitude-$radiusDeg,$longitude+$radiusDeg); // upper left
	$sq[1] = array($latitude+$radiusDeg,$longitude+$radiusDeg); // top right
	$sq[2] = array($latitude+$radiusDeg,$longitude-$radiusDeg); // правый нижний
	$sq[3] = array($latitude-$radiusDeg,$longitude-$radiusDeg); // bottom right
	$sq[4] = $sq[0]; // closable polygon
	$areaSearch=$radiusDeg * $radiusDeg;
	$query=sprintf('SELECT timezone, 6371 * acos( cos( radians(%1$s) ) * cos( radians( X(tzc.coordinates) ) ) * cos( radians( Y(tzc.coordinates) ) - radians(%2$s) ) + sin( radians(%1$s) ) * sin( radians( X(tzc.coordinates) ) ) ) as distance
						FROM `timezone`
						WHERE MBRWithin(tzc.coordinates, GeomFromText(\'Polygon((%4$s %5$s, %6$s %7$s,%8$s %9$s, %10$s %11$s, %12$s %13$s))\'))
						HAVING distance <= %3$s
						ORDER BY distance LIMIT 1',
			$latitude, $longitude, $areaSearch, $sq[0][0], $sq[0][1], $sq[1][0], $sq[1][1], $sq[2][0], $sq[2][1], $sq[3][0], $sq[3][1], $sq[4][0],  $sq[4][1]);

	// First check for cities within 100km
	$timezones = $db->prepare($query);
	$timezones->execute();

	while($tz = $timezones->fetch()) {
		$result = $tz['timezone'];
	}

	if($result)
		return $result;

	$radiusSearch = 6000; // If nothing was found that close, check again within 6000km
	$query=sprintf('SELECT timezone, 6371 * acos( cos( radians(%1$s) ) * cos( radians( X(tzc.coordinates) ) ) * cos( radians( Y(tzc.coordinates) ) - radians(%2$s) ) + sin( radians(%1$s) ) * sin( radians( X(tzc.coordinates) ) ) ) as distance
						FROM `timezone`
						WHERE MBRWithin(tzc.coordinates, GeomFromText(\'Polygon((%4$s %5$s, %6$s %7$s,%8$s %9$s, %10$s %11$s, %12$s %13$s))\'))
						HAVING distance <= %3$s
						ORDER BY distance LIMIT 1',
			$latitude, $longitude, $areaSearch, $sq[0][0], $sq[0][1], $sq[1][0], $sq[1][1], $sq[2][0], $sq[2][1], $sq[3][0], $sq[3][1], $sq[4][0],  $sq[4][1]);


	$timezones = $db->prepare($query);
	$timezones->execute();
	while($tz = $timezones->fetch()) {
		$result = $tz['timezone'];
	}

	return $result;
}

function LatLngDist($p, $q) {
		$R = 6371; // Earth radius in km

		$dLat = (($q[0] - $p[0]) * pi() / 180);
		$dLon = (($q[1] - $p[1]) * pi() / 180);
		$a = sin($dLat / 2) * sin($dLat / 2) +
			cos($p[0] * pi() / 180) * cos($q[0] * pi() / 180) *
			sin($dLon / 2) * sin($dLon / 2);
		$c = 2 * atan2(sqrt($a), sqrt(1 - $a));

		return $R * $c;
}

function get($k) {
	return array_key_exists($k, $_GET) ? $_GET[$k] : FALSE;
}
