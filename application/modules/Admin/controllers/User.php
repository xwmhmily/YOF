<?php

class UserController extends BasicController {

	private $m_user;

	private function init(){
		Yaf_Registry::get('adminPlugin')->checkLogin();
		
        $this->m_user  = $this->load('User');
	}

	public function indexAction(){
		$total = $this->m_user->Total();

		$page = $this->get('page');
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