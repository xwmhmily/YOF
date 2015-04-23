<?php

class ArticleController extends BasicController {

	private $m_article;

	private function init(){
		Yaf_Registry::get('adminPlugin')->checkLogin();

		$this->m_article = $this->load('Article');
		$this->homeUrl = '/admin/article';
	}

	public function indexAction(){
		$total = $this->m_article->Total();

		$page = $this->get('page');
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
		$articleID = $this->get('articleID');
		$m['status'] = 1;

		$code = $this->m_article->UpdateByID($m, $articleID);
		jsRedirect($this->homeUrl);
	}

	public function delAction(){
		$articleID = $this->get('articleID');
		$code = $this->m_article->DeleteByID($articleID);
		jsRedirect($this->homeUrl);
	}
	
}