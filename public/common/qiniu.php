<?php

$avatar = $_FILES['avatar'];

if($avatar['name']){
    Yaf_Loader::import('L_Upload.class.php');
    
    $dir = TMP_PATH.'/';
            
    $fileName = date('YmdHis');
    $upload = new L_Upload($avatar, $dir);

    $code = $upload->upload($fileName);

    if($code == 1){
		Yaf_Loader::import('L_Qiniu.class.php');

		$img = $fileName . '.' . $upload->extension;
        $key = time().'.'.$upload->extension;
        $source = $dir.$img;

        $l_qiniu = new L_Qiniu();
        list($ret, $err) = $l_qiniu->upload($key, $source);

        if($err !== NULL){
        	// Handle this error
        }else{
        	// Your code here ....
        	unlink($source);
        }
    }