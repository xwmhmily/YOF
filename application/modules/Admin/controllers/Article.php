<?php

class ArticleController extends Yaf_Controller_Abstract {

	private $m_article = null;
	private $request = null;
  	private $session = null;

	private function init(){
		Yaf_Registry::get('adminPlugin')->checkLogin();

		$this->m_article  = Helper::load('Article');
		$this->request = $this->getRequest();
		$this->session = Yaf_Session::getInstance();

		$this->homeUrl = '/admin/article';
	}

	public function indexAction(){
		$total = $this->m_article->Total();

		$page = $this->request->get('page');
		$page = $page ? $page : 1;

		$size  = 10;
		$pages = ceil($total/$size);
		$order = array('id' => 'DESC');
		$start = ($page-1)*$size;
		$limit = $start.','.$size;

		$buffer['pageNav'] = generatePageLink($page, $pages, $this->homeUrl, $total);

		$buffer['articles'] = $this->m_article->Order($order)->Limit($limit)->Select();
		$this->getView()->assign($buffer);
	}

	public function verifyAction(){
		$articleID = $this->request->get('articleID');
		$m['status'] = 1;

		$code = $this->m_article->UpdateByID($m, $articleID);
		jsRedirect($this->homeUrl);
	}

	public function delAction(){
		$articleID = $this->request->get('articleID');
		$code = $this->m_article->DeleteByID($articleID);
		jsRedirect($this->homeUrl);
	}
	
}