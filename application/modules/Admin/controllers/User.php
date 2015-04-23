<?php

class UserController extends Yaf_Controller_Abstract {

	private $m_user  = null;
	private $request = null;
  	private $session = null;

	private function init(){
            $this->m_user  = Helper::load('User');
            $this->request = $this->getRequest();
            $this->session = Yaf_Session::getInstance();
            include ADMIN_PATH.'/checkAdminLogin.php';
	}

	public function indexAction(){
		$total = $this->m_user->Total();

		$page = $this->request->get('page');
		$page = $page ? $page : 1;

		$size  = 10;
		$pages = ceil($total/$size);
		$order = array('id' => 'DESC');
		$start = ($page-1)*$size;
		$limit = $start.','.$size;

		$url = '/admin/user';
		$buffer['pageNav'] = generatePageLink($page, $pages, $url, $total);

		$buffer['users'] = $this->m_user->Order($order)->Limit($limit)->Select();
		$this->getView()->assign($buffer);
	}

	
}