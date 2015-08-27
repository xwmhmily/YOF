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
        
        $config = Yaf_Application::app()->getConfig();

        $this->bucket    = $config['qiniu_bucket'];
        $this->accessKey = $config['qiniu_accessKey'];
        $this->secretKey = $config['qiniu_secretKey'];
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