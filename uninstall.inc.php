<?php
if ($REX["ADDON"]["rexseo42"]["settings"]['drop_dbfields_on_uninstall']) {
	$sql = new rex_sql();
	//$sql->debugsql = true;
	$sql->setQuery('ALTER TABLE `' . $REX['TABLE_PREFIX'] . 'article` DROP `seo_title`, DROP `seo_description`, DROP `seo_keywords`, DROP `seo_custom_url`, DROP `seo_canonical_url`, DROP `seo_noindex`, DROP `seo_ignore_prefix`, DROP `seo_title_attribute`, DROP `seo_alt_attribute`, DROP `seo_link_text`');
} else {
	echo rex_info($I18N->msg('rexseo42_uninstall_dbfields_not_removed'));
}

rex_generateAll();

$REX['ADDON']['install']['rexseo42'] = 0;
?>
