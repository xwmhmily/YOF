<?php
/**
 * File: C_Basic.php
 * Functionality: Basic Controller
 * Author: 大眼猫
 * Date: 2015-5-8
 */

class BasicController extends Yaf_Controller_Abstract {

  protected $homeUrl;
  
  public function get($key, $filter = TRUE){
    if($filter){
      return filterStr($this->getRequest()->get($key));
    }else{
      return $this->getRequest()->get($key);
    }
  }

  public function getPost($key, $filter = TRUE){
    if($filter){
      return filterStr($this->getRequest()->getPost($key));
    }else{
      return $this->getRequest()->getPost($key);
    }
  }

  public function getParam($key, $filter = TRUE){
    if($this->getRequest()->isGet()){
      if($filter){
        return filterStr($this->getRequest()->get($key));
      }else{
        return $this->getRequest()->get($key);
      }
    }else{
      if($filter){
        return filterStr($this->getRequest()->getPost($key));
      }else{
        return $this->getRequest()->getPost($key);
      }
    }
  }

  public function getQuery($key, $filter = TRUE){
    if($filter){
      return filterStr($this->getRequest()->getQuery($key));
    }else{
      return $this->getRequest()->getQuery($key);
    }
  }

  public function getSession($key){
    return Yaf_Session::getInstance()->__get($key);
  }

  public function setSession($key, $val){
    return Yaf_Session::getInstance()->__set($key, $val);
  }

  public function unsetSession($key){
    return Yaf_Session::getInstance()->__unset($key);
  }

  // Clear cookie
  public function clearCookie($key){
    setCookie($key, '');
  }

  /**
   * Set COOKIE
   */
  public function setCookie($key, $value, $expire = 3600, $path = '/', $domain = '', $httpOnly = FALSE){
    setCookie($key, $value, CUR_TIMESTAMP + $expire, $path, $domain, $httpOnly);
  }

  /**
   * 获取cookie
   */
  public function getCookie($key){
    return trim($_COOKIE[$key]);
  }

  // Go home
  public function goHome(){
    jsRedirect($this->homeUrl);
  }

  // Show error
  public function showError($error, $tpl, $die = TRUE){
    $buffer['error'] = $error;
    $this->display($tpl, $buffer);

    if($die){
      die;
    }
  }

  // Get limit
  public function getLimit($size = 10){
    $page = $this->get('page');
    if(!$page){
      $page = $this->getPost('page');
    }

    $page = $page ? $page : 1;

    $start = ($page-1)*$size;
    $limit = $start.','.$size;

    return $limit;
  }

  // Load model
  public function load($model){
    return Helper::load($model);
  }

  /**
   * Verify API sign
   */
  public function verifySign(){
    $sign = $this->getRequest()->getPost('sign');
    $i['time'] = $this->getRequest()->getPost('time');

    // Only valid in 30 seconds
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

  /**
   *  API Response
   */
  public function response($error){
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

    Helper::response($rep);
  }

}
