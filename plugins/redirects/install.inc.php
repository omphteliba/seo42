<?php

$sql = new rex_sql();
//$sql->debugsql=1;
$sql->setQuery("
	CREATE TABLE IF NOT EXISTS `" . $REX['TABLE_PREFIX'] . "redirects` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`source_url` varchar(255) NOT NULL,
	`target_url` varchar(255) NOT NULL,
	PRIMARY KEY (`id`)
);");

$REX['ADDON']['install']['redirects'] = true;

