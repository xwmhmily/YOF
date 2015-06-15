<?php

class ArticleController extends BasicController {

	private $m_article;

	private function init(){
		// Verify sign
		$this->verifySign(); 
		$this->m_article = $this->load('Article');
	}

	public function indexAction(){
		$articles = $this->m_article->Select();
		Helper::response($articles);
	}

	public function detailAction(){
		$articleID = intval($this->getPost('articleID'));
		
		// articleID is required, or we could do nothing !
		if(!$articleID){
			$this->response('ERR_MISSING');
		}

		$article = $this->m_article->SelectByID('', $articleID);

		$rep['code'] = 1;
		$rep['article'] = $article;

		Helper::response($rep);
	}
	
}