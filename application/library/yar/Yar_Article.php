<?php

class Yar_Article extends Yar_Basic {

	private $m_article;

	function __construct(){
		// 验证签名
		$this->verifySign();
		$this->m_article = Helper::load('Article');
	}

	// 参数不能以$p 的数组形式传不过来, 要拆开来
	public function index($status = '', $userID = ''){
		$where = 1;

		if($status){
	 		$where = array('status' => $status);
	 	}

	 	if($userID){
	 		$where['userID'] = $userID;
	 	}

		$articles = $this->m_article->Where($where)->Select();

		$rep['code'] = 1;
		$rep['articles'] = $articles;

		// 返回数据用 response, 见 Yar_Basic.php
		return $this->response($rep);
	}

	public function detail($articleID){
		if(!$articleID){
			// 报错用 error, 见 Yar_Basic.php
			return $this->error('ERR_MISSING');
		}

		$article = $this->m_article->SelectByID('', $articleID);

		$rep['code'] = 1;
		$rep['article'] = $article;
		return $this->response($rep);
	}

}