<?php
/**
 * 七类上传类
 */

class Qiniu{

    private $bucket;
    private $accessKey;
    private $secretKey;

    function __construct(){
        Yaf_Loader::import('qiniu/io.php');
        Yaf_Loader::import('qiniu/rs.php');
        Yaf_Loader::import('qiniu/config.php');

        $this->bucket    = $bucket;
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
    }

    public function upload($key, $source){
        Qiniu_SetKeys($this->accessKey, $this->secretKey);
        $putPolicy = new Qiniu_RS_PutPolicy($this->bucket);
        $upToken = $putPolicy->Token(null);
        $putExtra = new Qiniu_PutExtra();
        $putExtra->Crc32 = 1;

        return Qiniu_PutFile($upToken, $key, $source, $putExtra);
    }
   
}