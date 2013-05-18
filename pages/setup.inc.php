<?php
$page = rex_request('page', 'string');
$subpage = rex_request('subpage', 'string');
$chapter = rex_request('chapter', 'string');
$func = rex_request('func', 'string');

$htaccessRoot = $REX['FRONTEND_PATH'] . '/.htaccess';
$backupPathRoot = $REX['INCLUDE_PATH'] . '/addons/rexseo42/backup/';

if ($func == "do_copy") {
	// first backup files
	$htaccessBackupFile = '_htaccess_' . date('Ymd_His');
	$doCopy = true;
	$htaccessFileExists = false;
	$copySuccessful = false;

	if (file_exists($htaccessRoot)) {
		$htaccessFileExists = true;

		if (copy($htaccessRoot, $backupPathRoot . $htaccessBackupFile)) {
			$doCopy = true;
		} else {
			rex_warning($I18N->msg('rexseo42_setup_file_backup_failed', $htaccessRoot));
			$doCopy = false;
		} 
	}

	// then copy if backup was successful
	if ($doCopy) {
		$sourceFile = $REX['INCLUDE_PATH'] . '/addons/rexseo42/install/_htaccess';

		if (copy($sourceFile, $htaccessRoot)) {
			$copySuccessful = true;
			$msg = $I18N->msg('rexseo42_setup_file_copy_successful');
	
			if ($htaccessFileExists) {
				$msg .= ' ' . $I18N->msg('rexseo42_setup_backup_successful');
			}

			echo rex_info($msg);
		} else {
			echo rex_warning($I18N->msg('rexseo42_setup_file_copy_failed'));	
		}
	} else {
		echo rex_warning($I18N->msg('rexseo42_setup_backup_failed'));
	}

	if ($copySuccessful && (rex_request('www_redirect', 'int') == 1 || rex_request('modify_rewritebase', 'int') == 1)) {
		$content = rex_get_file_contents($htaccessRoot);

		// this is for non-ww to www redirect
		if (rex_request('www_redirect', 'int') == 1) {
			$wwwRedirect1 = '#RewriteCond %{HTTP_HOST} ^[^.]+\.[^.]+$';
			$wwwRedirect2 = '#RewriteRule ^(.*)$ http://www.%{HTTP_HOST}/$1 [L,R=301]';
	
			$content = str_replace($wwwRedirect1, ltrim($wwwRedirect1, '#'), $content);
			$content = str_replace($wwwRedirect2, ltrim($wwwRedirect2, '#'), $content);
		}

		// this is for subdir installations  
		if (rex_request('modify_rewritebase', 'int') == 1) {
			$rewriteBase = 'RewriteBase /';

			$content = str_replace($rewriteBase,$rewriteBase . rexseo42::getServerSubDir(), $content);
		}

		if (rex_put_file_contents($htaccessRoot, $content) > 0) {
			//echo rex_info($I18N->msg('rexseo42_setup_htaccess_patch_ok'));
		} else {
			echo rex_warning($I18N->msg('rexseo42_setup_htaccess_patch_failed'));
		}
	}
} elseif ($func == "apply_settings") {
	$server = str_replace("\\'", "'", rex_post('server', 'string'));
	$servername  = str_replace("\\'", "'", rex_post('servername', 'string'));

	$masterFile = $REX['INCLUDE_PATH'] . '/master.inc.php';
	$content = rex_get_file_contents($masterFile);

	$search = array('\\"', "'", '$');
	$destroy = array('"', "\\'", '\\$');
	$replace = array(
		'search' => array(
			"@(REX\['SERVER'\].?\=.?).*$@m",
			"@(REX\['SERVERNAME'\].?\=.?).*$@m"
		),
		'replace' => array(
			"$1'".str_replace($search, $destroy, $server) . "';",
			"$1'".str_replace($search, $destroy, $servername) . "';"
		)
	);

	$content = preg_replace($replace['search'], $replace['replace'], $content);

	if (rex_put_file_contents($masterFile, $content) > 0) {
		echo rex_info($I18N->msg('rexseo42_setup_settings_saved'));

		$REX['SERVER'] = stripslashes($server);
		$REX['SERVERNAME'] = stripslashes($servername);

		// reinit because of subdir check in step 2
		rexseo42::init();
	} else {
		echo rex_warning($I18N->msg('rexseo42_setup_settings_save_failed'));
	}
}
?>

<div class="rex-addon-output">
	<h2 class="rex-hl2"><?php echo $I18N->msg('rexseo42_setup_step1'); ?></h2>
	<div class="rex-area-content">
		<p class="info-msg"><?php echo $I18N->msg('rexseo42_setup_step1_msg1'); ?></p>
		<form action="index.php" method="post" id="settings-form">
			<p class="rex-form-col-a first-textfield">
				<label for="servername"><?php echo $I18N->msg('rexseo42_setup_website_name'); ?></label>
				<input name="servername" id="servername" type="text" class="rex-form-text" value="<?php echo htmlspecialchars($REX['SERVERNAME']); ?>" />
			</p>

			<p class="rex-form-col-a">
				<label for="server"><?php echo $I18N->msg('rexseo42_setup_website_url'); ?></label>
				<input name="server" id="server" type="text" class="rex-form-text" value="<?php echo htmlspecialchars($REX['SERVER']); ?>" />
				<?php if (rexseo42_utils::detectSubDir()) { echo '<span class="subdir-hint">' . $I18N->msg('rexseo42_setup_subdir_hint') . '</span>'; } ?>
			</p>

			<input type="hidden" name="page" value="rexseo42" />
			<input type="hidden" name="subpage" value="setup" />
			<input type="hidden" name="func" value="apply_settings" />
			<div class="rex-form-row">
				<p class="button"><input type="submit" class="rex-form-submit" name="sendit" value="<?php echo $I18N->msg('rexseo42_setup_step1_button'); ?>" /></p>
			</div>
		</form>
	</div>
</div>

<div class="rex-addon-output">
	<h2 class="rex-hl2"><?php echo $I18N->msg('rexseo42_setup_step2'); ?></h2>
	<div class="rex-area-content">
		<p><?php echo $I18N->msg('rexseo42_setup_step2_msg1'); ?></p>
		<form action="index.php" method="post">
			<p class="no-bottom-margin" id="codeline">
				<code>/rexseo42/install/_htaccess</code> &nbsp;<?php echo $I18N->msg('rexseo42_setup_to'); ?>&nbsp; <code>/.htaccess</code>
			</p>
			
			<?php if (rexseo42::getServerSubDir() != '') { ?>
			<p class="rex-form-checkbox rex-form-label-right"> 
				<input type="checkbox" value="1" id="modify_rewritebase" name="modify_rewritebase" checked="checked" />
				<label for="modify_rewritebase"><?php echo $I18N->msg('rexseo42_setup_rewritebase', rexseo42::getServerSubDir()); ?></label>
			</p>
			<?php } ?>

			<p class="rex-form-checkbox rex-form-label-right"> 
				<input type="checkbox" value="1" id="www_redirect" name="www_redirect" />
				<label for="www_redirect"><?php echo $I18N->msg('rexseo42_setup_www_redirect_checkbox'); ?></label>
			</p>

			<input type="hidden" name="page" value="rexseo42" />
			<input type="hidden" name="subpage" value="setup" />
			<input type="hidden" name="func" value="do_copy" />
			<div class="rex-form-row">
				<p class="button"><input type="submit" class="rex-form-submit" name="sendit" id="copy-file-submit" value="<?php echo $I18N->msg('rexseo42_setup_step2_button'); ?>" /></p>
			</div>
		</form>
	</div>
</div>

<?php
$codeExample = '<head>
	<title><?php echo rexseo42::getTitle(); ?></title>
	<meta name="description" content="<?php echo rexseo42::getDescription(); ?>" />
	<meta name="keywords" content="<?php echo rexseo42::getKeywords(); ?>" />
	<meta name="robots" content="<?php echo rexseo42::getRobotRules();?>" />
	<link rel="canonical" href="<?php echo rexseo42::getCanonicalUrl(); ?>" />
</head>';
?>

<div class="rex-addon-output">
	<h2 class="rex-hl2"><?php echo $I18N->msg('rexseo42_setup_step3'); ?></h2>
	<div class="rex-area-content">
		<p class="info-msg"><?php echo $I18N->msg('rexseo42_setup_step3_msg1'); ?></p>
		<div id="code-example"><?php rex_highlight_string($codeExample); ?></div>
		<p class="info-msg no-bottom-margin"><?php echo $I18N->msg('rexseo42_setup_codeexamples'); ?></p>
	</div>
</div>

<style type="text/css">
#rex-page-rexseo42 span.subdir-hint {
	margin-left: 165px;
	display: block;
}

#rex-page-rexseo42 .rex-code {
    word-wrap: break-word;
}

#rex-page-rexseo42 .info-msg {
	margin-bottom: 10px;
}

#rex-page-rexseo42 .no-bottom-margin {
	margin-bottom: 0px;
	margin-top: 7px;
}

#rex-page-rexseo42 .button {
	float: right; 
	margin-bottom: 10px; 
	margin-right: 5px;
	
}

#rex-page-rexseo42 p.rex-form-col-a.first-textfield {
	margin-bottom: 3px;
}

#rex-page-rexseo42 p.rex-form-col-a label {
	width: 160px;
	display: inline-block;
	margin-bottom: 10px;
}

#rex-page-rexseo42 p.rex-form-col-a input.rex-form-text {
	width: 320px;
}

#rex-page-rexseo42 p.rex-form-checkbox input {
	position: relative;
	top: 3px;
}

#rex-page-rexseo42 #modify_rewritebase {
	margin-top: 10px;
}

#rex-page-rexseo42 #www_redirect {
    margin-top: 8px;
}
</style>

<script type="text/javascript">
var rewriteBaseMsgShown = false;

jQuery(document).ready( function() {
	jQuery('#settings-form').submit(function() {
		var pat = /^https?:\/\//i;
		var serverString = jQuery('#server').val();
		var slashPosAfterDomain = serverString.indexOf("/", 8); // https:// = 8

		if (pat.test(serverString) && slashPosAfterDomain !== -1 && (serverString.charAt(serverString.length - 1) == '/')) {
			return true;
		}

		alert('<?php echo $I18N->msg('rexseo42_setup_url_alert'); ?>');

		return false;
	});

	<?php if (file_exists($htaccessRoot)) { ?>
	jQuery('#copy-file-submit').click(function(e) {
		if (!confirm("<?php echo $I18N->msg('rexseo42_setup_htaccess_alert'); ?>")) {
			e.preventDefault();
		}
	});
	<?php } ?>

	jQuery('#modify_rewritebase').click(function(e) {
		if (!jQuery('#modify_rewritebase').is(':checked') && !rewriteBaseMsgShown) {
			rewriteBaseMsgShown = true;
			alert("<?php echo $I18N->msg('rexseo42_setup_rewritebase_alert'); ?>\r\n\r\nRewriteBase /<?php echo rexseo42::getServerSubDir(); ?>");
		}
	});
});
</script>

