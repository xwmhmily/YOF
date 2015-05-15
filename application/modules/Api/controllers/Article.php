<?php

class ArticleController extends BasicController {

	private $m_article;

	private function init(){
		$this->m_article = $this->load('Article');
	}

	public function indexAction(){
		$articles = $this->m_article->Select();
		Helper::response($articles);
	}

	public function detailAction(){
		$articleID = $this->getQuery('articleID');
		echo $articleID; die;
	}
	
}