<?php

class ArticleController extends Yaf_Controller_Abstract {

	private $m_article  = null;
	private $request = null;
	private $session = null;

	private function init(){
		$this->request = $this->getRequest();
		$this->session = Yaf_Session::getInstance();

		$this->m_article = Helper::load('Article');
	}

	public function indexAction(){
		$articles = $this->m_article->Select();
		Helper::response($articles);
	}

	public function detailAction(){
		$articleID = $this->request->getQuery('articleID');
		echo $articleID; die;
	}
	
}