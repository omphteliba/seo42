<?php

$sql = new rex_sql();
$sql->setQuery('DROP TABLE IF EXISTS `' . $REX['TABLE_PREFIX'] . 'redirects`');

$REX['ADDON']['install']['redirects'] = 0;

