<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
require_once('modules/Documents/Documents.php');

global $adb, $current_user, $root_directory;

if(isset($_REQUEST['act']) && $_REQUEST['act'] == 'updateDldCnt')
{
	$res=$adb->pquery('update vtiger_notes set filedownloadcount=filedownloadcount+1 where notesid=?',array($_REQUEST['file_id']));
}

if(isset($_REQUEST['act']) && $_REQUEST['act'] == 'checkFileIntegrityDetailView')
{
		$dbQuery = 'SELECT * FROM vtiger_notes where notesid=?';
		$fileidQuery = 'select attachmentsid from vtiger_seattachmentsrel where crmid=?';
		$result = $adb->pquery($dbQuery,array($_REQUEST['noteid']));
		$fileidResult = $adb->pquery($fileidQuery,array($_REQUEST['noteid']));
		//$activeToinactive_count = 0;

		$file_status = $adb->query_result($result,0,'filestatus');
		$download_type = $adb->query_result($result,0,'filelocationtype');
		$notesid = $adb->query_result($result,0,'notesid');
		$fileid = $adb->query_result($fileidResult,0,'attachmentsid');
		$folderid = $adb->query_result($result,0,'folderid');
		$name = $adb->query_result($result,0,'filename');

		if($download_type == 'I'){
			$saved_filename = $fileid.'_'.$name;
			$pathQuery = $adb->pquery('select path from vtiger_attachments where attachmentsid = ?',array($fileid));
			$filepath = $adb->query_result($pathQuery,0,'path');
		}
		elseif($download_type == 'E'){
			$saved_filename = $name;
		}
		else
			$saved_filename = '';

		if(!fopen($filepath.$saved_filename, 'r'))
		{
			if($file_status == 1)
			{
				$dbQuery1 = 'update vtiger_notes set filestatus = 0 where notesid= ?';
				$result1 = $adb->pquery($dbQuery1,array($notesid));
				echo 'lost_integrity';
			}
			else
				echo 'file_not_available';
		}else {
			echo 'file_available';
		}
}
?>