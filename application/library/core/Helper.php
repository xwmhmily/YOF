<?php
/**
 * File: Helper.php
 * Functionality: Model, function loader, raiseError, generateSign, response
 * Author: 大眼猫
 * Date: 2013-5-8
 */

abstract class Helper {

	private static $obj;

	/**
	 * Import function
	 *
	 * @param string file to be imported
	 * @return null
	 */
	public static function import($file) {
		$function = 'F_'.ucfirst($file);
		$f_file   = FUNC_PATH.'/'.$function.'.php';

		if(file_exists($f_file)){
			Yaf_Loader::import($f_file);
			unset($file, $function, $f_file);
		}else{
			$traceInfo = debug_backtrace();
			$error = 'Function '.$file.' NOT FOUND !';
			self::raiseError($traceInfo, $error);
		}
	}
	
	/**
	 * Load model
	 * <br />After loading a model, the new instance will be added into $obj immediately,
	 * <br />which is used to make sure that the same model is only loaded once per request !
	 *
	 * @param string => model to be loaded
	 * @return new instance of $model or raiseError on failure !
	 */
	public static function load($model) {
		$path = '';

		//分组功能
		if(strpos($model, '/') !== FALSE){
			list($category, $model) = explode('/', $model);
			$path = '/'. $category;
		}
		
		$hash = md5($path . $model);

		if(isset(self::$obj[$hash])) {
			return self::$obj[$hash];
		}

		$default = FALSE;
		$file = MODEL_PATH .$path .'/M_'.ucfirst($model).'.php';
		
		if(!file_exists($file)) {
			// 加载默认模型, 减少没啥通用方法的模型
			$default = TRUE;
			$table   = strtolower($model);
			$model   = 'M_Default';
			$file    = MODEL_PATH.'/'.$model.'.php';
		}

		if(PHP_OS == 'Linux'){
			Yaf_Loader::import($file);
		}else{
			require_once $file;
		}

		try{
			if($default){
				self::$obj[$hash] = new $model($table);
			}else{
				$model = 'M_'.$model;
				self::$obj[$hash] = new $model;	
			}
			
			unset($model, $default, $table, $file, $path, $category);
			return self::$obj[$hash];
		}catch(Exception $error) {
			$traceInfo = debug_backtrace();
			$error = 'Load model '.$model.' FAILED !';
			Helper::raiseError($traceInfo, $error);
		}
	}

	/**
     * Generate sign
     * @param array $parameters
     * @return new sign
     */
    public static function generateSign($parameters){
        $signPars = '';
        foreach($parameters as $k => $v) {
            if(isset($v) && 'sign' != $k) {
                $signPars .= $k . '=' . $v . '&';
            }
        }

        $signPars .= 'key='.API_KEY;
        return strtolower(md5($signPars));
    }
	
	
	/**
	 * Response
	 * 
	 * @param string $format : json, xml, jsonp, string
	 * @param array $data: 
	 * @param boolean $die: die if set to true, default is true
	 */
	public static function response($data, $format = 'json', $die = TRUE) {
		switch($format){
			default:
			case 'json':
				$file = FUNC_PATH.'/F_String.php';
				Yaf_Loader::import($file);
				if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"])=="xmlhttprequest"){ 
					$data = JSON($data);
				}else if(isset($_REQUEST['ajax'])){
					$data = JSON($data);
				}else{
					//pr($data); die; // URL 测试打印数组出来
					echo json_encode($data); die;
				}
			break;
			
			case 'jsonp':
				$data = $_GET['jsoncallback'] .'('. json_encode($data) .')';
			break;
			
			case 'string':
			break;
		}

		echo $data;
		
		if($die){
            die;
		}
	}


	/**
	 * Raise error and halt if it is under DEV
	 *
	 * @param string debug back trace info
	 * @param string error to display
	 * @param string error SQL statement
	 * @return null
	 */
	public static function raiseError($trace, $error, $sql = '') {
		// YOF 自定义错误编号
		$errno   = 9999; 
		$errFile = $trace[0]['file'];
		$errLine = $trace[0]['line'];

		// Call yofErrorHandler to show error
		yofErrorHandler($errno, $error, $errFile, $errLine, $sql);
	}

}
