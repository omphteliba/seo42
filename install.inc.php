<?php
$myself = 'rexseo42';
$myroot = $REX['INCLUDE_PATH'].'/addons/'.$myself;
$error = array();

// append lang file
$I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/rexseo42/lang/');

// check redaxo version
if (version_compare($REX['VERSION'] . '.' . $REX['SUBVERSION'] . '.' . $REX['MINORVERSION'], '4.4.1', '<=')) {
	$error[] = $I18N->msg('rexseo42_install_rex_version');
}

// check for concurrent addons
$disable_addons = array('url_rewrite', 'yrewrite', 'rexseo');

foreach ($disable_addons as $a) {
	if (OOAddon::isInstalled($a) || OOAddon::isAvailable($a)) {
		$error[] = $I18N->msg('rexseo42_install_concurrent') . ' ' . $a;
	}
}

// setup seo db fields
if (count($error) == 0) {
	$sql = new rex_sql();
	//$sql->debugsql = true;
	$sql->setQuery('ALTER TABLE `' . $REX['TABLE_PREFIX'] . 'article` ADD `seo_title` TEXT, ADD `seo_description` TEXT, ADD `seo_keywords` TEXT, ADD `seo_custom_url` TEXT, ADD `seo_canonical_url` TEXT, ADD `seo_noindex` VARCHAR(1), ADD `seo_ignore_prefix` VARCHAR(1), ADD `seo_title_attribute` TEXT, ADD `seo_alt_attribute` TEXT, ADD `seo_menu_title` TEXT');

	// if adding/removing db fields here, check also for uninstall.inc.php and rexseo42_utils::afterDBImport()

	// delete cache
	rex_generateAll();

	// done!
	$REX['ADDON']['install'][$myself] = 1;
} else {
	$REX['ADDON']['installmsg'][$myself] = '<br />'.implode($error,'<br />');
	$REX['ADDON']['install'][$myself] = 0;
}

