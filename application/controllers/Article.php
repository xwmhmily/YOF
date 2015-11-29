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
    $m['content'] = $this->getPost('editorValue', FALSE); // DO NOT FILTER
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
    $articleID = $this->getPost('articleID');
    $m['title']   = $this->getPost('title');
    $m['content'] = $this->getPost('editorValue', FALSE); // DO NOT FILTER

    $code = $this->m_article->UpdateByID($m, $articleID);

    if($code){
      $this->redirect('/user/profile');
    }else{
      jsAlert('编辑文章失败, 请重试');
      $this->redirect('/article/edit?articleID='.$articleID);
    }
  }

  public function delAction(){
    $articleID = $this->getQuery('articleID');
    $code = $this->m_article->DeleteByID($articleID);

    if(!$code){
      jsAlert('删除文章失败, 请重试');
    }

    $this->redirect('/user/profile');
  }

  // 测试URL 路由 [伪静态]
  public function detailAction(){
    $categoryID = $this->getParam('categoryID');
    $articleID  = $this->getParam('articleID');

    $buffer['article'] = $this->m_article->SelectByID('', $articleID);

    $this->getView()->assign($buffer);
  }

}
