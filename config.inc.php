<?php

// register addon
$REX['ADDON']['rxid']['rexseo42'] = '0';
$REX['ADDON']['name']['rexseo42'] = 'REXSEO42';
$REX['ADDON']['version']['rexseo42'] = '1.2.0';
$REX['ADDON']['author']['rexseo42'] = 'Markus Staab, Wolfgang Huttegger, Dave Holloway, Jan Kristinus, jdlx, RexDude';
$REX['ADDON']['supportpage']['rexseo42'] = 'forum.redaxo.de';
$REX['ADDON']['perm']['rexseo42'] = 'rexseo42[]';

// permissions
$REX['PERM'][] = 'rexseo42[]';
$REX['PERM'][] = 'rexseo42[tools_only]';
$REX['EXTPERM'][] = 'rexseo42[seo_default]';
$REX['EXTPERM'][] = 'rexseo42[seo_extended]';

// includes
require($REX['INCLUDE_PATH'] . '/addons/rexseo42/classes/class.rexseo42.inc.php');
require($REX['INCLUDE_PATH'] . '/addons/rexseo42/classes/class.rexseo42_utils.inc.php');
require($REX['INCLUDE_PATH'] . '/addons/rexseo42/settings.dyn.inc.php');
require($REX['INCLUDE_PATH'] . '/addons/rexseo42/settings.advanced.inc.php');
require($REX['INCLUDE_PATH'] . '/addons/rexseo42/settings.lang.inc.php');

// init
if (!$REX['SETUP']) {
	// auto mod rewrite, but not for redaxo system page
	if (!$REX['REDAXO'] || ($REX['REDAXO'] && rex_request('page') != 'specials')) {
		$REX['MOD_REWRITE'] = true;
	}
	
	// init 42
	rex_register_extension('ADDONS_INCLUDED','rexseo42_utils::init', '', REX_EXTENSION_EARLY);
}

if ($REX['REDAXO']) {
	// append lang file
	$I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/rexseo42/lang/');

	// subpages
	if (isset($REX['USER']) && !$REX['USER']->isAdmin() && $REX['USER']->hasPerm('rexseo42[tools_only]')) {
		// add tools page only
		$REX['ADDON']['rexseo42']['SUBPAGES'] = array(
			array('', $I18N->msg('rexseo42_tools'))
		);
	} else {
		// add all pages 
		$REX['ADDON']['rexseo42']['SUBPAGES'] = array(
			array('', $I18N->msg('rexseo42_welcome')),
			array('tools', $I18N->msg('rexseo42_tools')),
			array('options', $I18N->msg('rexseo42_settings')),
			array('setup', $I18N->msg('rexseo42_setup')),
			array('help', $I18N->msg('rexseo42_help'))
		);
	}

	// add css/js files to page header
	if (rex_request('page') == 'rexseo42') {
		rex_register_extension('PAGE_HEADER', 'rexseo42_utils::appendToPageHeader');
	}

	// check for user permissions (admins will have all)
	if (isset($REX['USER']) && ($REX['USER']->isAdmin() || $REX['USER']->hasPerm('rexseo42[seo_default]') || $REX['USER']->hasPerm('rexseo42[seo_extended]') || $REX['USER']->hasPerm('editContentOnly[]'))) {
		// react on one_page_mode option
		if (!$REX['ADDON']['rexseo42']['settings']['one_page_mode'] || ($REX['ADDON']['rexseo42']['settings']['one_page_mode'] && $REX['ARTICLE_ID'] == $REX['START_ARTICLE_ID'])) {
			// add new menu item
			rex_register_extension('PAGE_CONTENT_MENU', 'rexseo42_utils::addSEOPageToPageContentMenu');

			// include seo page
			rex_register_extension('PAGE_CONTENT_OUTPUT', 'rexseo42_utils::addSEOPageToPageContentOutput');
		}
	}

	// for one page mode link to frontend is always "../"
	if ($REX['ADDON']['rexseo42']['settings']['one_page_mode'] && $REX['ARTICLE_ID'] != $REX['START_ARTICLE_ID']) {
		rex_register_extension('PAGE_CONTENT_MENU', 'rexseo42_utils::modifyFrontendLinkInPageContentMenu');
	}

	// check for missing db fields after db import
	if (!$REX['SETUP']) {
		rex_register_extension('A1_AFTER_DB_IMPORT', 'rexseo42_utils::afterDBImport');
	}

	// if clang is added/deleted show message to the user that he should check his lang settings
	rex_register_extension('CLANG_ADDED', 'rexseo42_utils::showMsgAfterClangModified');
	rex_register_extension('CLANG_DELETED', 'rexseo42_utils::showMsgAfterClangModified');
}

