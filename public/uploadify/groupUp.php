<?php
/**
 * File: uploadify.php
 * Functionality: Uploadify 的上传处理文件
 * Author: Nic XIE
 * Date: 2013-07-01
 */

define('BASE_PATH', $_SERVER['DOCUMENT_ROOT']);
include BASE_PATH.'/common.php';
Helper::import('File');

// Destination
$targetFolder = getParam('destination');
$id = getParam('id');
$is_insert_db = getParam('is_insert_db');
if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = UPLOAD_PATH. '/'. $targetFolder;   // 保存文件的目标文件夹
	
	if(!file_exists($targetPath)){
		@createRDir($targetPath);
	}
	
	// Validate the file type
	$fileTypes = array('jpg','jpeg','gif','png');
	$fileParts = pathinfo($_FILES['Filedata']['name']);

    $name  = uniqid(). '.' . $fileParts['extension'];
    $targetFile = $targetPath. '/' . $name;
	if (in_array($fileParts['extension'], $fileTypes)) {
		move_uploaded_file($tempFile, $targetFile);
        echo  $name;
	} else {
		echo 'Error when uploading file !2';
	}
	
}

?>