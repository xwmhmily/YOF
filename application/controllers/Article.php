<?php

class ArticleController extends BasicController {
  
  private $m_article;

  private function init(){
    $userID = $this->getSession('userID');
    $this->m_article = $this->load('Article');
  }

  public function indexAction(){
    echo 'Article index'; die;
  }

  public function addAction(){

  }

  public function addActAction(){
    $m['title']   = $this->getPost('title');
    $m['content'] = $this->getPost('content');
    $m['userID']  = $this->getSession('userID');
    $m['addTime'] = CUR_TIMESTAMP;

    $articleID = $this->m_article->Insert($m);

    if($articleID){
      $this->redirect('/user/profile');
    }else{
      jsAlert('发布文章失败, 请重试');
      $this->redirect('/article/add');
    }
  }

  public function editAction(){
    // URL 中带的参数用 getQuery
    $articleID = $this->getQuery('articleID');
    $buffer['article'] = $this->m_article->SelectByID('', $articleID);

    $this->getView()->assign($buffer);
  }

  public function editActAction(){
    // POST 过来的用 getPost
    $articleID = $this->getPost('articleID');
    $m['title']   = $this->getPost('title');
    $m['content'] = $this->getPost('content');

    $code = $this->m_article->UpdateById($m, $articleID);

    if($code){
      $this->redirect('/user/profile');
    }else{
      jsAlert('编辑文章失败, 请重试');
      $this->redirect('/article/edit?articleID='.$articleID);
    }
  }

  public function delAction(){
    $articleID = $this->getQuery('articleID');
    $code = $this->m_article->DeleteById($articleID);

    if(!$code){
      jsAlert('删除文章失败, 请重试');
    }

    $this->redirect('/user/profile');
  }

  // 测试URL 路由 [伪静态]
  public function detailAction(){
    $articleID = $this->get('articleID');
    $buffer['article'] = $this->m_article->SelectByID('', $articleID);

    pr($buffer['article']); die;
  }

}
