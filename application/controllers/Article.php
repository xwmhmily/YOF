<?php

class ArticleController extends Yaf_Controller_Abstract {
  
  private $m_article = null;
  private $request = null;
  private $session = null;

  private function init(){
    $this->request = $this->getRequest();
    $this->session = Yaf_Session::getInstance();

    $this->m_article = Helper::load('Article');
  }

  public function indexAction(){
    echo 'Article index'; die;
  }

  public function addAction(){

  }

  public function addActAction(){
    $m['title']   = $this->request->getPost('title');
    $m['content'] = $this->request->getPost('content');
    $m['userID']  = $this->session->__get('userID');
    $m['addTime'] = CUR_TIMESTAMP;

    $articleID = $this->m_article->Insert($m);

    if($articleID){
      $this->redirect('/');
    }else{
      jsAlert('发布文章失败, 请重试');
      $this->redirect('/article/add');
    }
  }

  public function editAction(){
    // URL 中带的参数用 getQuery
    $articleID = $this->request->getQuery('articleID');
    $buffer['article'] = $this->m_article->SelectByID('', $articleID);

    $this->getView()->assign($buffer);
  }

  public function editActAction(){
    // POST 过来的用 getPost
    $articleID = $this->request->getPost('articleID');
    $m['title']   = $this->request->getPost('title');
    $m['content'] = $this->request->getPost('content');

    $code = $this->m_article->UpdateById($m, $articleID);

    if($code){
      $this->redirect('/');
    }else{
      jsAlert('编辑文章失败, 请重试');
      $this->redirect('/article/edit?articleID='.$articleID);
    }
  }

  public function delAction(){
    $articleID = $this->request->getQuery('articleID');
    $code = $this->m_article->DeleteById($articleID);

    if(!$code){
      jsAlert('删除文章失败, 请重试');
    }

    $this->redirect('/');
  }

  // 测试URL 路由 [伪静态]
  public function detailAction(){
    $articleID = $this->request->get('articleID');
    $buffer['article'] = $this->m_article->SelectByID('', $articleID);

    pr($buffer['article']); die;
  }

}
