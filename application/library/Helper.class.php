<?php
/**
 * File: Helper.class.php
 * Functionality: Controller, library, function loader and raiseError
 * Author: 大眼猫
 * Date: 2013-5-8
 * ----------------- DO NOT MODIFY THIS FILE UNLESS YOU FULLY UNDERSTAND ! ------------------
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
		$file     = ucfirst($file);
		$function = 'F_'.$file;
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
	 * Load Component
	 * @return componment
	 */
	public static function loadComponment($c){
		$componment = 'Com_'.ucfirst($c);

		$hash = md5($componment);
		if(self::$obj[$hash]){
			return self::$obj[$hash];
		}

		$file = APP_PATH .'/application/componment/' . $componment .'.php';
		
		if(file_exists($file)){
			Yaf_Loader::import($file);
		} else {
			$traceInfo = debug_backtrace();
			$error = 'Componment '.$componment.' NOT FOUND !';
			self::raiseError($traceInfo, $error);
		}

		try{
			self::$obj[$hash] = new $componment();
			return self::$obj[$hash];
		} catch(Exception $error) {
			$traceInfo = debug_backtrace();
			$error = 'Load Componment '.$componment.' FAILED !';
			self::raiseError($traceInfo, $error);
		}
	}

	
	/**
	 * Load model
	 * <br />After loading a model, the new instance will be added into $obj immediately,
	 * <br />which is used to make sure that the same model is only loaded once per page !
	 *
	 * @param string => model to be loaded
	 * @return new instance of $model or raiseError on failure !
	 */
	public static function load($model) {
		$path = '';

		//新增分组功能
		if(strpos($model, '/') !== FALSE){
			list($category, $model) = explode('/', $model);
			$path = '/'. $category;
		}
		
		$hash = md5($path . $model);

		if(self::$obj[$hash] && is_object(self::$obj[$hash])) {
			return self::$obj[$hash];
		}

		$file = MODEL_PATH .$path .'/M_'.$model.'.php';
		
		if(!file_exists($file)) {
			// 加载默认模型, 减少没啥通用方法的模型
			$default = TRUE;
			$table   = strtolower($model);
			$model   = 'M_Default';
			$file    = MODEL_PATH.'/'.$model.'.php';
		}

		// 不知道是不是 Yaf_Loader 的 bug, Windows 下有可能报重定义
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
	 * Raise error and halt if it is under UAT
	 *
	 * @param string debug back trace info
	 * @param string error to display
	 * @param string error SQL statement
	 * @return null
	 */
	public static function raiseError($traceInfo, $error, $sql = '') {
        $errorMsg = "<h2 style='color:red'>Error occured !</h2>";
        $errorMsg .= '<h3>' . $error . '</h3>';
        if ($sql) {
            $errorMsg .= 'SQL: ' . $sql . '<br /><br />';
        }

        $errorMsg .= 'The following table shows trace info: <table border=1 width=100%>';
        $errorMsg .= "<tr style='text-align:center;color:red;background-color:yellow'>";
        $errorMsg .= '<th>NO</th><th>File</th><th>Line</th><th>Function</th></tr>';

        $i = 1;
        foreach ($traceInfo as $v) {
            $errorMsg .= '<tr height=40>';
            $errorMsg .= '<td align="center">' . $i . '</td>';
            $errorMsg .= '<td>' . $v['file'] . '</td>';
            $errorMsg .= '<td align="center">&nbsp;' . $v['line'] . '</td>';
            $errorMsg .= '<td align="center">&nbsp;' . $v['function'] . '()</td>';
            $errorMsg .= '</tr>';
            $i++;
        }

        $errorMsg .= '</table>';
        $errorMsg .= '<h2>Please check and correct it, then try again ! Good Luck !</h2><hr />';
        unset($traceInfo, $v, $sql, $i);

        if(ENV == 'DEV'){
            die($errorMsg);
        }else{
            // PRODUCTION: 500 Error
            header('HTTP/1.1 500 Internal Server Error');

            $html = '<html>
                <head><title>500 Internal Server Error</title></head>
                <body bgcolor="white">
                <center><h1>500 Internal Server Error</h1></center>
                <hr>
                </body>
            </html>';
            die($html);
        }
	}

}
