<?php

class UserController extends BasicController {

	private $m_user;

	private function init(){
		Yaf_Registry::get('adminPlugin')->checkLogin();
		
        $this->m_user  = $this->load('User');
        $this->homeUrl = '/admin/user';
	}

	public function indexAction(){
		$total = $this->m_user->Total();

		$page = $this->get('page');
		$size = 10;
		$pages = ceil($total/$size);
		$order = array('id' => 'DESC');
		$limit = $this->getLimit();

		$buffer['pageNav'] = generatePageLink($page, $pages, $this->homeUrl, $total);
		$buffer['users'] = $this->m_user->Order($order)->Limit($limit)->Select();

		$this->getView()->assign($buffer);
	}

	
}