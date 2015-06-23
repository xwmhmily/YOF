<?php

require_once $_SERVER['DOCUMENT_ROOT']."/qiniu/io.php";
require_once $_SERVER['DOCUMENT_ROOT']."/qiniu/rs.php";

$bucket = "super-bin";
$key1 = date('Y-m-d').'/'.'topic/'.$_FILES['upfile']['name'];
$accessKey = 'StwlnEcDkpqblLFpkgxDWUU7IsrlBmDA2XuwCryv';
$secretKey = '1APnavqP6fYTOtOVwUk-zuoD89WtUZ1OqgjqOpR_';

Qiniu_SetKeys($accessKey, $secretKey);
$putPolicy = new Qiniu_RS_PutPolicy($bucket);
$upToken = $putPolicy->Token(null);

list($ret, $err) = Qiniu_PutFile($upToken, $key1, $_FILES['upfile']['tmp_name'], null);
if ($err === null) {
	$url = $key1;
		echo json_encode( array(
			'url'=>$url,
			'title'=>'',
			'original'=>'',
			'state'=>'SUCCESS'
		)
	);
} else {
	echo json_encode( array(
		'state'=>$err
	) );
}