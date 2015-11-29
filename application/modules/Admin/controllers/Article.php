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
		$pages = ceil($total/10);
		$order = array('id' => 'DESC');
		$limit = $this->getLimit();

		$buffer['pageNav'] = generatePageLink($page, $pages, $this->homeUrl, $total);
		$buffer['articles'] = $this->m_article->Order($order)->Limit($limit)->Select();

		$this->getView()->assign($buffer);
	}

	public function verifyAction(){
		$articleID = $this->get('articleID');
		$m['status'] = 1;

		$code = $this->m_article->UpdateByID($m, $articleID);
		$this->goHome();
	}

	public function staticAction(){
		jsAlert('该菜单功能没有实现,只用于演示多个子菜单如何添加');
		jsRedirect('/admin/article');
	}
	
}