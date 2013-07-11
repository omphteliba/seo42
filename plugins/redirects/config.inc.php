<?php

$REX['ADDON']['page']['themes'] = 'Redirects';
$REX['ADDON']['version']['themes'] = '1.0.0';
$REX['ADDON']['author']['themes'] = 'RexDude';
$REX['ADDON']['supportpage']['themes'] = 'forum.redaxo.de';

// includes
//require($REX['INCLUDE_PATH'] . '/addons/rexseo42/plugins/redirects/settings.inc.php');
require_once($REX['INCLUDE_PATH'] . '/addons/rexseo42/plugins/redirects/classes/class.rex_redirects_utils.inc.php');

if ($REX['REDAXO']) {
	// add lang file
	$I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/rexseo42/plugins/redirects/lang/');
} else {
	// do the redirect
	rex_redirects_utils::redirect();
}


