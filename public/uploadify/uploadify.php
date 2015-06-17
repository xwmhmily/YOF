<?php
/**
 * File: uploadify.php
 * Functionality: Uploadify 的上传处理文件
 * Author: Nic XIE
 * Date: 2013-07-01
 */

define('BASE_PATH', $_SERVER['DOCUMENT_ROOT']);

// Destination
$targetFolder = $_POST['destination'];

if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = BASE_PATH. '/'. $targetFolder;
	
	// Validate the file type
	$fileTypes = array('jpg', 'jpeg', 'gif', 'png');
	$fileParts = pathinfo($_FILES['Filedata']['name']);

    $name  = uniqid(). '.' . $fileParts['extension'];
    $targetFile = $targetPath. '/' . $name;
	if (in_array(strtolower($fileParts['extension']), $fileTypes)) {
		move_uploaded_file($tempFile, $targetFile);

		echo '/'. $targetFolder .'/'. $name;
	} else {
		echo 'Error when uploading file 2 !';
	}
	
}