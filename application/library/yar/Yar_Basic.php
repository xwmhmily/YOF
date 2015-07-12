<?php
/**
 *  Yar 提供服务类的基类
 */
class Yar_Basic {

	protected function verifySign(){
		$request = Yaf_Dispatcher::getInstance()->getRequest();

		$sign      = $request->get('sign');
	    $i['time'] = $request->get('time');

	    //Only valid in 30 seconds
      	if(CUR_TIMESTAMP - $i['time'] > 30){
        	$rep['code']  = 1001;
        	$rep['error'] = 'error sign';

        	Helper::response($rep);
      	}

	    $newSign = Helper::generateSign($i);

	    if(strtolower($newSign) != $sign){
	      $rep['code']  = 1001;
	      $rep['error'] = 'error sign';

	      Helper::response($rep);
	    }
	}

	protected function response($data){
		return json_encode($data, JSON_UNESCAPED_UNICODE);
	}

	protected function error($error){
		switch($error){
	      case 'ERR_MISSING':
	        $rep['code'] = 1002;
	        $rep['error'] = 'misssing parameters';
	      break;

	      case 'ERR_NO_DATA':
	        $rep['code']  = 9998;
	        $rep['error'] = 'no data';
	      break;

	      case 'ERR_UNKNOWN':
	        $rep['code']  = 9999;
	        $rep['error'] = 'unknown error';
	      break;

	      case 'ERR_DUPLICATED':
	        $rep['code']  = 1110;
	        $rep['error'] = 'operation duplicated';
	      break;

	      case 'ERR_FAIL_UPLOAD_TO_CLOUD':
	        $rep['code'] = 1008;
	        $rep['error'] = 'failed to upload to cloud';
	      break;

	      case 'ERR_FAIL_UPLOAD_TO_TMP':
	        $rep['code'] = 1007;
	        $rep['error'] = 'failed to upload to tmp';
	      break;

	      case 'ERR_NO_FILE_RECEIVED':
	        $rep['code'] = 1009;
	        $rep['error'] = 'empty file received';
	      break;
	    }

	    return json_encode($rep);
	}

}