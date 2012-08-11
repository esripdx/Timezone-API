<?php
include('inc.php');

$db = new PDO(PDO_DSN, PDO_USER, PDO_PASS, array(PDO::ATTR_PERSISTENT => false));

$timezone = timezoneFromLocation($argv[1], $argv[2]);

if($timezone) {	
	$tz = new DateTimeZone($timezone);
	$now = new DateTime(date('c'));
	$now->setTimeZone($tz);
	
	echo "timezone: " . $timezone . "\n";
	echo "offset:   " .  $now->format('P') . "\n";
	echo "seconds:  " . (int)$now->format('Z') . "\n";
} else {
	echo "No data found for " . $argv[1] . ", " . $argv[2] . "\n";
}
