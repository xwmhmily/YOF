<?php
/**
 *  Yar client
 *  Functionality: 演示如何调用 Yar Server
 *  Remark: 参数以数组形式传过去!
 */

class YarClientController extends BasicController {
  
  private $userURL;
  private $articleURL;

  private function init(){
    $userID = $this->getSession('userID');
    
    // Article 和 User 的URL
    $this->userURL    = SERVER_DOMAIN.'/yarServer/user';
    $this->articleURL = SERVER_DOMAIN.'/yarServer/article';
  }

  // Call yar article list
  public function articleAction(){
    $p['status'] = 1;

    if(ENV == 'DEV'){
      $p['userID'] = 9;
    }else{
      $p['userID'] = 5;
    }

    $rep = $this->yarRequest($this->articleURL, 'index', $p);

    $buffer['articles'] = $rep['articles'];
    $this->getView()->assign($buffer);
  }
  
  // Call yar article detail
  public function articleDetailAction(){
    $p['articleID'] = 3;
    $rep = $this->yarRequest($this->articleURL, 'detail', $p);

    $buffer['article'] = $rep['article'];
    $this->getView()->assign($buffer);
  }

  // Call yar user list
  public function userAction(){
    $users = $this->yarRequest($this->userURL, 'index');
    pr($users); die;
  }
  
  // Call yar user detail
  public function userDetailAction(){
    if(ENV == 'DEV'){
      $p['userID'] = 9;
    }else{
      $p['userID'] = 5;
    }

    $user = $this->yarRequest($this->userURL, 'detail', $p);
    pr($user); die;
  }

  // 并发调用列表
  public function concurrentAction(){
    $buffer = array();

    // 指定不同的回调函数, 好区分数据
    function userCallback($retval, $callinfo){
      $GLOBALS['buffer']['users'] = json_decode($retval, TRUE);
    }

    function articleCallback($retval, $callinfo){
      $GLOBALS['buffer']['articles'] = json_decode($retval, TRUE);
    }

    $this->yarConcurrentRequest($this->userURL, 'index', $p, 'userCallback');
    
    // 给 article 的 index 传参数
    $p['status'] = 1;

    if(ENV == 'DEV'){
      $p['userID'] = 9;
    }else{
      $p['userID'] = 5;
    }
    $this->yarConcurrentRequest($this->articleURL, 'index', $p, 'articleCallback');
    $this->yarLoop();

    pr($GLOBALS['buffer']); die;
  }

  // 并发调用详细
  public function concurrentDetailAction(){
    $buffer = array();

    function userCallback($retval, $callinfo){
      $GLOBALS['buffer']['user'] = json_decode($retval, TRUE);
    }

    function articleCallback($retval, $callinfo){
      $GLOBALS['buffer']['article'] = json_decode($retval, TRUE);
    }

    if(ENV == 'DEV'){
      $p['userID'] = 9;
    }else{
      $p['userID'] = 5;
    }

    $this->yarConcurrentRequest($this->userURL, 'detail', $p, 'userCallback');

    $a['articleID'] = 3;  
    $this->yarConcurrentRequest($this->articleURL, 'detail', $a, 'articleCallback');
    $this->yarLoop();

    pr($GLOBALS['buffer']); die;
  }

}
