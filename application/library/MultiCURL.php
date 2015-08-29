<?php
/*
 * Multi CURL class
 * 该类转自 http://www.cnblogs.com/wc1217/archive/2012/03/08/2387565.html
 * 但有小修改
 */

class MultiCURL {

    private $curl_handle;
    private $url_list = array();
    
    function __construct($seconds = 60){
        set_time_limit($seconds);
    }

    /*
     * 设置网址
     * @list 数组
     */
    public function setUrlList($list){
        $this->url_list = $list;
    }

    /*
     * 执行
     * @return array
     */
    public function exec(){
        $mh = curl_multi_init();
        foreach($this->url_list as $i => $url){
            $conn[$i] = curl_init($url);

		    curl_setopt($conn[$i], CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt($conn[$i], CURLOPT_HEADER, 0);
		    curl_setopt($conn[$i], CURLOPT_NOBODY, 0);
		    curl_setopt($conn[$i], CURLOPT_FOLLOWLOCATION, 0);
		    curl_setopt($conn[$i], CURLOPT_TIMEOUT, 30);

            curl_multi_add_handle($mh, $conn[$i]);
        }

        $active = FALSE;

        do{
            $mrc = curl_multi_exec($mh, $active);
        }while($mrc == CURLM_CALL_MULTI_PERFORM);

        while($active && $mrc == CURLM_OK){
            //if(curl_multi_select($mh) != -1){
                do{
                    $mrc = curl_multi_exec($mh,$active);
                }while($mrc == CURLM_CALL_MULTI_PERFORM);
            //}
        }

        $result = array();
        foreach($this->url_list as $i => $url){
            $result[$i] = curl_multi_getcontent($conn[$i]);
            curl_close($conn[$i]);
            curl_multi_remove_handle($mh,$conn[$i]);  
        }

        curl_multi_close($mh);
        return $result;
    }
}