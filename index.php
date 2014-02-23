<?php

require('Lib/Migrate.php');

// Migrate the file
$migrate = new Migrate();
$migrate->setFile('Data/BrightCove.xml');
$migrate->setOutputType(Migrate::KALTURA);
$migrate->setLimit(3);
$migrate->formatXml = true;
$migrate->setOutputFileName('Data/Kaltura.xml');
$migrate->go();

?>