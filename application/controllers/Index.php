<?php

class IndexController extends Yaf_Controller_Abstract {

	private $session = null;
	private $request = null;

	public function init(){
		$this->session = Yaf_Session::getInstance();  // SESSION 不需要 start
		$this->request = $this->getRequest();

      	$userID = $this->session->__get('userID');
      	if($userID){
        	define('USER_ID', $userID);
      	}
	}

	public function indexAction() {
		$m_article = Helper::load('Article');
		
      	$userID = $this->session->__get('userID');
		if($userID){
			$buffer['username'] = $this->session->__get('username');

			// User Aritcles
			$where = array('userID' => USER_ID);
			$total = $m_article->Where($where)->Total();

			$page = $this->request->get('page');
			$page = $page ? $page : 1;

			$size  = 10;
			$pages = ceil($total/$size);
			$order = array('addTime' => 'DESC');
			$start = ($page-1)*$size;
			$limit = $start.','.$size;

			$url = '/';
			$buffer['pageNav'] = generatePageLink($page, $pages, $url, $total);
			$buffer['articles'] = $m_article->Where($where)->Order($order)->Limit($limit)->Select();
		}

		$this->getView()->assign($buffer);
  	}
  	
}
