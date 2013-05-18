<?php

if (isset($REX['USER']) && !$REX['USER']->isAdmin() && $REX['USER']->hasPerm('rexseo42[tools_only]')) {
	$startSubpage = 'tools';
} else {
	$startSubpage = 'welcome';
}

$myself = rex_request('page', 'string');
$subpage = rex_request('subpage', 'string') != '' ? rex_request('subpage', 'string') : $startSubpage;
$chapter = rex_request('chapter', 'string');
$func = rex_request('func', 'string');
$section_id = rex_request('section_id', 'string');
$section_class = rex_request('section_class', 'string');
$highlight = rex_request('highlight', 'string');
$myroot = $REX['INCLUDE_PATH'] . '/addons/' . $myself;

// includes
require_once($myroot.'/classes/class.rexseo_rewrite.inc.php');

// layout top
require($REX['INCLUDE_PATH'] . '/layout/top.php');

// title
rex_title($REX['ADDON']['name'][$myself] . ' <span style="font-size:14px; color:silver;">' . $REX['ADDON']['version'][$myself] . '</span>', $REX['ADDON'][$myself]['SUBPAGES']);

// subpages
switch($subpage){
	case '':
		$subpage = $startSubpage;
	case 'welcome':
	case 'options':
	case 'tools':
	case 'setup':
	case 'help':
		$local_path = '/addons/' . $myself . '/pages/';
		break;
	default:
		$local_path = '/addons/' . $myself . '/plugins/' . $subpage . '/';
}

require $REX['INCLUDE_PATH'] . $local_path . $subpage . '.inc.php';

// layout bottom
require $REX['INCLUDE_PATH'] . '/layout/bottom.php';

