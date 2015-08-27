<?php

$avatar = $_FILES['avatar'];

if($avatar['name']){
    $dir = TMP_PATH.'/';
            
    $fileName = date('YmdHis');
    $upload = new Upload($avatar, $dir);

    $code = $upload->upload($fileName);

    if($code == 1){
		$img = $fileName . '.' . $upload->extension;
        $key = time().'.'.$upload->extension;
        $source = $dir.$img;

        $l_qiniu = new Qiniu();
        list($ret, $err) = $l_qiniu->upload($key, $source);

        if($err !== NULL){
        	// Handle this error
        }else{
        	// Your code here ....
        	unlink($source);
        }
    }