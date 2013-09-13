<?php
include('inc.php');

$db = new PDO(PDO_DSN, PDO_USER, PDO_PASS, array(PDO::ATTR_PERSISTENT => false));


$query = $db->prepare('CREATE TABLE `timezone` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `location` geometry NOT NULL,
  `timezone` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  SPATIAL KEY `location` (`location`)
)ENGINE=MyISAM');
$query->execute();

$data = file('tz_cities.txt');

foreach($data as $line) {
	if(preg_match('/([0-9\.-]+) ([0-9\.-]+) (.+)/', $line, $match)) {
		$latitude = $match[1];
		$longitude = $match[2];
		$timezone = trim($match[3]);

		echo $latitude . ', ' . $longitude . "\t" . $timezone . "\n";
		$query = $db->prepare('INSERT INTO timezone (location, timezone) VALUES ( GeomFromText(\'POINT(' . $longitude . ' ' . $latitude . ')\'), \'' . $timezone . '\')');
		$query->execute();
	}
}

