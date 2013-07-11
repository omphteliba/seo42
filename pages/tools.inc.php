<?php
require($REX['INCLUDE_PATH'] . '/addons/rexseo42/classes/class.rexseo42_tool.inc.php');
require($REX['INCLUDE_PATH'] . '/addons/rexseo42/classes/class.rexseo42_tool_manager.inc.php');

$func = rex_request('func', 'string');

if ($func == 'export_data') {
	$content = '';

	$sql = rex_sql::factory();
	//$sql->debugsql = 1;
	$seoData = $sql->getArray('SELECT * FROM '. $REX['TABLE_PREFIX'] .'article WHERE status = 1');

	$content .= 'ID;Artikelname;Titel;Beschreibung;Keywords' . "\r\n";

	for ($i = 0; $i < count($seoData); $i++) {
		$content .= $seoData[$i]['id'] . ';' . $seoData[$i]['name'] . ';' . $seoData[$i]['seo_title'] . ';' . $seoData[$i]['seo_description'] . ';' . $seoData[$i]['seo_keywords'] . "\r\n";
	}

	header("Content-type: text/plain");
	header("Content-Disposition: attachment; filename=seo_export_" . rexseo42::getServer() . ".csv");

	ob_clean();
	
	echo $content;	

	exit;
}
?>

<div class="rex-addon-output">
	<h2 class="rex-hl2">SEO Export Tool</h2>
	<div class="rex-area-content">
		<p>Exportiert entsprechende SEO Daten in eine CSV-Datei.<br />Nur Artikel die online sind werden berücksichtigt.</p>
		<form action="index.php" method="post">		
			<p class="button">
				<input type="submit" class="rex-form-submit" name="sendit" value="Export" />
			</p>
			<input type="hidden" name="page" value="rexseo42" />
			<input type="hidden" name="subpage" value="tools" />
			<input type="hidden" name="func" value="export_data" />
		</form>
	</div>
</div>

<?php
$toolManager = new rexseo42_tool_manager();

$tool = new rexseo42_tool($I18N->msg('rexseo42_tool1'), $I18N->msg('rexseo42_tool1_desc', rexseo42::getServerWithSubDir()), 'http://www.google.com/search?q=site:' . rexseo42::getServerWithSubDir());
$toolManager->addTool($tool);

$tool = new rexseo42_tool($I18N->msg('rexseo42_tool3'), $I18N->msg('rexseo42_tool3_desc'), 'http://www.google.com/webmasters/tools/');
$toolManager->addTool($tool);

$tool = new rexseo42_tool($I18N->msg('rexseo42_tool2'), $I18N->msg('rexseo42_tool2_desc'), 'http://www.google.com/webmasters/tools/submit-url');
$toolManager->addTool($tool);

$tool = new rexseo42_tool($I18N->msg('rexseo42_tool4'), $I18N->msg('rexseo42_tool4_desc'), 'http://www.gaijin.at/olsgprank.php');
$toolManager->addTool($tool);

$tool = new rexseo42_tool($I18N->msg('rexseo42_tool6'), $I18N->msg('rexseo42_tool6_desc'), 'http://www.seitwert.de/#quick');
$toolManager->addTool($tool);

$tool = new rexseo42_tool($I18N->msg('rexseo42_tool8'), $I18N->msg('rexseo42_tool8_desc'), 'http://www.seomofo.com/snippet-optimizer.html');
$toolManager->addTool($tool);

$toolManager->printToolList($I18N->msg('rexseo42_tools_caption'));
?>

<style type="text/css">
table.rex-table th {
	font-size: 1.2em;
}

table.rex-table td {
	padding: 11px 5px;
}

table.rex-table td p {
	margin-top: 6px;
	color: #32353A;
}

table.rex-table td p.url {
	color: grey;
}

.rex-table td a,
.rex-table td span {
	font-weight: bold;
}

p.button {
	margin: 10px 0 0 0;
}
</style>

