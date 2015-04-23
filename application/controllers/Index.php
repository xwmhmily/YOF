<?php

class IndexController extends BasicController {

	public function init(){
        $userID = $this->getSession('userID');

        if($userID){
    	   define('USER_ID', $userID);
        }
	}

	public function indexAction() {
        $m_article = $this->load('Article');
        $userID = $this->getSession('userID');

        if($userID){
            $buffer['username'] = $this->getSession('username');

            // User Aritcles
            $where = array('userID' => USER_ID);
            $total = $m_article->Where($where)->Total();

            $page = $this->get('page');
            $page = $page ? $page : 1;

            $size  = 10;
            $pages = ceil($total/$size);
            $order = array('addTime' => 'DESC');
            $start = ($page-1)*$size;
            $limit = $start.','.$size;

            $url = '/';
            $buffer['pageNav'] = generatePageLink($page, $pages, $url, $total);
            $buffer['articles'] = $m_article->Where($where)->Order($order)->Limit($limit)->Select();
        }

        $this->getView()->assign($buffer);
  	}
  	
}
