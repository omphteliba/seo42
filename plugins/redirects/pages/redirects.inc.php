<?php
$func = rex_request('func', 'string');
$redirect_id = rex_request('redirect_id', 'int');

// delete redirect (when link clicked from rex list)
if($func == 'delete' && $redirect_id > 0) {
	$sql = rex_sql::factory();
	//  $sql->debugsql = true;
	$sql->setTable($REX['TABLE_PREFIX'] . 'redirects');
	$sql->setWhere('id='. $redirect_id . ' LIMIT 1');

	if ($sql->delete()) {
		echo rex_info($I18N->msg('rexseo42_redirect_deleted'));
	} else {
		echo rex_warning($sql->getErrro());
	}

	rex_redirects_utils::updateCacheFile();
	
	$func = '';
}

// add or edit redirect (after form submit)
rex_register_extension('REX_FORM_SAVED', function ($params) {
	global $REX;

	rex_redirects_utils::updateCacheFile();
	
	// use exit statement, if you want to debug
	return true;
});

// delete redirect (after form submit)
rex_register_extension('REX_FORM_DELETED', function ($params) {
	global $REX;

	rex_redirects_utils::updateCacheFile();

	// use exit statement, if you want to debug
	return true;
});

// output
echo '<div class="rex-addon-output-v2">';

if ($func == '') {
	// rex list
	$query = 'SELECT * FROM ' . $REX['TABLE_PREFIX'] . 'redirects ORDER BY id';

	$list = rex_list::factory($query);
	$list->setNoRowsMessage($I18N->msg('rexseo42_redirect_no_sytles_available'));
	$list->setCaption($I18N->msg('rexseo42_redirect_list_of_redirects'));
	$list->addTableAttribute('summary', $I18N->msg('rexseo42_redirect_list_of_redirects'));
	$list->addTableColumnGroup(array(40, 40, 300, 300, 80));

	$list->setColumnLabel('id', $I18N->msg('rexseo42_redirect_id'));
	$list->setColumnLabel('source_url', $I18N->msg('rexseo42_redirect_source_url'));
	$list->setColumnLabel('target_url', $I18N->msg('rexseo42_redirect_target_url'));

	// icon column
	$thIcon = '<a class="rex-i-element rex-i-generic-add" href="'. $list->getUrl(array('func' => 'add')) .'"><span class="rex-i-element-text">' . $I18N->msg('rexseo42_redirect_create') . '</span></a>';
	$tdIcon = '<span class="rex-i-element rex-i-generic"><span class="rex-i-element-text">###name###</span></span>';
	$list->addColumn($thIcon, $tdIcon, 0, array('<th class="rex-icon">###VALUE###</th>','<td class="rex-icon">###VALUE###</td>'));
	$list->setColumnParams($thIcon, array('func' => 'edit', 'redirect_id' => '###id###'));

	// functions column spans 2 data-columns
	$funcs = $I18N->msg('rexseo42_redirect_functions');
	$list->addColumn($funcs, $I18N->msg('rexseo42_redirect_edit'), -1, array('<th colspan="2">###VALUE###</th>','<td>###VALUE###</td>'));
	$list->setColumnParams($funcs, array('func' => 'edit', 'redirect_id' => $redirect_id, 'redirect_id' => '###id###'));

	$delete = 'deleteCol';
	$list->addColumn($delete, $I18N->msg('rexseo42_redirect_delete'), -1, array('','<td>###VALUE###</td>'));
	$list->setColumnParams($delete, array('redirect_id' => '###id###', 'func' => 'delete'));
	$list->addLinkAttribute($delete, 'onclick', 'return confirm(\'' . $I18N->msg('rexseo42_redirect_delete_confirm') . '\');');

	$list->show();
} elseif ($func == 'add' || $func == 'edit' && $redirect_id > 0) {
	// rex form
	if ($func == 'edit') {
		$formLabel = $I18N->msg('rexseo42_redirect_redirect_edit');
	} elseif ($func == 'add') {
		$formLabel = $I18N->msg('rexseo42_redirect_redirect_add');
	}

	$form = rex_form::factory($REX['TABLE_PREFIX'] . 'redirects', $formLabel, 'id=' . $redirect_id);
	$form->addErrorMessage(REX_FORM_ERROR_VIOLATE_UNIQUE_KEY, $I18N->msg('rexseo42_redirect_redirect_exists'));

	// source url
	$field =& $form->addTextField('source_url'); 
	$field->setLabel($I18N->msg('rexseo42_redirect_source_url'));

	// target url
	$field =& $form->addTextField('target_url'); 
	$field->setLabel($I18N->msg('rexseo42_redirect_target_url'));  

	if ($func == 'edit') {
		$form->addParam('redirect_id', $redirect_id);
	} elseif ($func == 'add') {
		// do nothing
	}

	$form->show();
}

echo '</div>';
?>

