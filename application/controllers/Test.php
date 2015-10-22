<?php

class TestController extends BasicController {

	public function init(){
        $userID = $this->getSession('userID');
	}

	// 测试事务 【不支持跨库的事务】
	public function transactionAction(){
		$m_user = $this->load('User');
		$m['username'] = 'dymdym';
		$m['password'] = 123456789;

		$m_user->beginTransaction();
		$userID = $m_user->Insert($m);

		$a['userID']  = 5;
		$a['title']   = 'test transaction';
		$a['content'] = 'bbb';
		$a['addTime'] = CUR_TIMESTAMP;

		$articleID = $this->load('Article')->Insert($a); 

		if($userID && $articleID){
			echo '__Commit__'; br();
			$m_user->Commit();
		}else{
			echo '__Rollback__'; br();
			$m_user->Rollback();
		}

		echo '__DONE__'; die;
	}

	// 测试拼接 SQL
	public function indexAction() {
		$field = array('id', 'userID', 'title', 'status');

		$userID = $this->getSession('userID');
		$m_article = $this->load('Article');

		// $field = array('id', 'author', 'title');
		// $where = array('userID' => $userID, 'status' => 1);
		// $data = $m_article->Field($field)
		// 				  ->Where($where)
		// 				  ->ORR()
		// 				  ->Where('id', '>', 30)
		// 				  ->Select();

		// pr($data); die;

		// 测试 UpdateOne
		$m['status'] = 1;
		$where = array('userID' => $userID);
		//$rows  = $m_article->Update($m);
		//$rows  = $m_article->Where(1)->Update($m);
		//$rows  = $m_article->Where($where)->UpdateOne($m);
		//$rows  = $m_article->Between('id', 1, 30)->Update($m);
		// echo $rows; die;

		// 测试 DeleteOne
		// $rows  = $m_article->Where($where)
		// 					->Order(array('id' => 'DESC'))
		// 					->Limit(10)
		// 					->Delete();

		// $rows  = $m_article->Where($where)
		// 					->Order(array('id' => 'DESC'))
		// 					->DeleteOne();
		// echo $rows; die;


		// 测试 ORR
		$where2 = array('status' => 1);
		$total  = $m_article->Where($where)
							->ORR()
							->Where($where2)
							->ORR()
							->Where('id', '>', 1)
							->Where('id', '<=', 30)
							->Total();
		echo '第一种 ORR => '. $total; br();

		// $data   = $m_article->Field($field)
		// 					->Where($where)
		// 					->ORR()
		// 					->Where($where2)
		// 					->ORR()
		// 					->Where('id', '>', 1)
		// 					->Where('id', '<=', 30)
		// 					->Order('id')
		// 					->Order(array('userID' => 'DESC'))
		// 					->Limit("10")
		// 					->SelectOne();
		// pr($data); die;

		$m['status'] = 1;
		$data   = $m_article->Where($where)
							->ORR()
							->Where($where2)
							->ORR()
							->Where('id', '>', 1)
							->Where('id', '<=', 30)
							->Order(array('id' => 'DESC'))
							->Limit(1)
							->Update($m);
		pr($data); die;

		// $data   = $m_article->Where($where)
		// 					->ORR()
		// 					->Where($where2)
		// 					->ORR()
		// 					->Where('id', '>', 1)
		// 					->Where('id', '<=', 30)
		// 					->Delete();


		// 测试第一种 where => 用数组来表示 =, 可以是多个
		$where = array('userID' => $userID);
		$where2 = array('status' => 1);
		$total = $m_article->Where($where)->Where($where2)->Total();
		echo '第一种 => '. $total; br();

		//$data = $m_article->Field($field)
							// ->Where($where)
							// ->Where($where2)->Select();
		//pr($data); die;

		// 第一种还可以这样简写, 多个数组合并, 一个 Where 搞定
		$where3 = array('userID' => $userID, 'status' => 1);
		$total = $m_article->Where($where3)->Total();
		echo '第一种简写 => '.  $total; br();

		// $data = $m_article->Field($field)
		// 				  ->Where($where3)->Select();
		// pr($data); die;

		// $m['status'] = 0;
		// $data = $m_article->Where($where3)->Update($m);
		// pr($data); die;

		// 第二种 where => 数组和字符串混合, 多次调用 Where()
		$where = '`userID` != "'.$userID.'"';
		$where2 = '`id` > "1"';
		$where3 = array('status' => 1);
		$total2 = $m_article->Where($where)
							->Where($where2)
							->Where($where3)->Total();
		echo '第二种 => '.$total2; br();

		// $data = $m_article->Field($field)
		//  				  ->Where($where)
		//  				  ->Where($where2)
		//  				  ->Where($where3)->Select();
		// pr($data); die;

		// $m['status'] = 1;
		// $data = $m_article->Where($where)
		//  				  ->Where($where2)
		//  				  ->Where($where3)->Update($m);
		// pr($data); die;

		// $data = $m_article->Where($where)
		//  				  ->Where($where2)
		//  				  ->Where($where3)->Delete();

		// 第二种还能这么写
		$total2 = $m_article->Where('userID', '!=', $userID)
							->Where('id', '>', 1)
							->Where('status', '=', 1)->Total();
		echo '第二种的另一种写法 => '.$total2; br();

		// $data = $m_article->Field($field)
		// 					->Where('userID', '!=', $userID)
		// 					->Where('id', '>', 1)
		// 					->Where('status', '=', 1)->Select();
		// pr($data); die;

		// 第三种 where => 数组和多个参数
		$where = array('userID' => $userID);
		$total3 = $m_article->Where($where)
							->Where('id', '<=', 31)
							->Where('title', 'NOT LIKE', '第')->Total();
		echo '第三种 => '.$total3; br();

		$data = $m_article->Field($field)
							->Where($where)
							->Where('id', '<=', 31)
							->Where('title', 'NOT LIKE', '第')
							->Select();
		//pr($data);

		// $m['status'] = 1;
		// $data = $m_article->Where($where)
		// 				->Where('id', '<=', 31)
		// 				->Where('title', 'NOT LIKE', '第')
		// 				->Update($m);
		// pr($data); die;

		// $data = $m_article->Where($where)
		// 				->Where('id', '<=', 31)
		// 				->Where('title', 'NOT LIKE', '第')
		// 				->Delete();
		// pr($data); die;

		// 第四种: 特殊的用字符串
		$where = '`userID` IS NOT NULL';
		$total4 = $m_article->Where($where)->Total();
		echo '第四种 => '.$total4; br();

		// 第五种: 只调用 Between, 无 Where
		$total6 = $m_article->Between('id', 1, 10)->Total();
		echo '第五种 => '.$total6; br();

		// $data = $m_article->Field($field)
		// 				  ->Between('id', 1, 10)->Select();
		// pr($data); die;

		// $m['status'] = 1;
		//$data = $m_article->Between('id', 1, 10)->Update($m);
		// pr($data); die;

		// $data = $m_article->Between('id', 1, 10)
		// 					->Limit(10)
		// 					->Delete();
		// pr($data); die;

		// 第六种: 多次调用 Between + where
		$total6 = $m_article->Where('id', '<=', 100)
							->Between('id', 1, 1000)
							->Where('status', '=', 1)
							->Between('id', 1, 99)
							->Where(array('userID' => $userID))
							->Total();
		echo '第六种 => '.$total6; br();

		// $data = $m_article->Field($field)
		// 					->Where('id', '<=', 100)
		// 					->Between('id', 1, 1000)
		// 					->Where('status', '=', 1)
		// 					->Between('id', 1, 99)
		// 					->Where(array('userID' => $userID))
		// 					->Order(array('id' => 'DESC'))
		// 					->Select();
		// pr($data); die;

		// $m['status'] = 1;
		// $data = $m_article->Where('id', '<=', 100)
		// 					->Between('id', 1, 1000)
		// 					->Where('status', '=', 1)
		// 					->Between('id', 1, 99)
		// 					->Where(array('userID' => $userID))
		// 					->Update($m);
		// pr($data); die;

		// $data = $m_article->Where('id', '<=', 100)
		// 					->Between('id', 1, 1000)
		// 					->Where('status', '=', 1)
		// 					->Between('id', 1, 99)
		// 					->Where(array('userID' => $userID))
		// 					->Delete();

		// 第七种: 多次调用 Between 和 where, orderby 各种组合
		$where = array('userID' => $userID, 'status' => 1);
		$inArray = array(1,2,3,4,5);

		$total5 = $m_article->Where($where)
							->Where('title', 'NOT LIKE', '第')
							->Where('id', 'IN', $inArray)
							->Where('id', 'NOT IN', '111,112,113')
							->Where('id', '>=', 1)
							->Where('id <= "31"')
							->Between('id', 1, 1001)
							->Between('id', 1, 99)->Total();
		echo '第七种 => '.$total5; br();

		// 第七种: 多次调用 Between 和 Where, ORR, Orderby, Limit, Select 各种组合
		// $field = array('id', 'userID', 'status', 'title', 'addTime');
		// $where = array('userID' => $userID, 'status' => 1);
		$idArray = array(1,2,3,4,5);
		// $data = $m_article->Field($field)
		// 					->Where($where)
		// 					// OR
		// 					->ORR()
		// 					->Where('title', 'NOT LIKE', '第')
		// 					// IN 传数组
		// 					->Where('id', 'IN', $idArray)
		// 					->ORR()
		// 					// IN 传字符串
		// 					->Where('id', 'NOT IN', '111,112,113')
		// 					->Where('id', '>=', 1)
		// 					->Between('id', 1, 1001)
		// 					->Where('id <= "31"')
		// 					->Between('id', 1, 99)
		// 					->Order(array('status' => 'DESC'))
		// 					->Order(array('id' => 'ASC'))
		// 					->Limit(10, 20)
		// 					->Select();
		// 					//->SelectOne();
		// pr($data); die;

		// $data = $m_article->Field($field)
		// 					->Where($where)
		// 					// OR
		// 					->ORR()
		// 					->Where('title', 'NOT LIKE', '第')
		// 					// IN 传数组
		// 					->Where('id', 'IN', $idArray)
		// 					->ORR()
		// 					// IN 传字符串
		// 					->Where('id', 'NOT IN', '111,112,113')
		// 					->Where('id', '>=', 1)
		// 					->Between('id', 1, 1001)
		// 					->Where('id <= "31"')
		// 					->Between('id', 1, 99)
		// 					->Limit(10,20)
		// 					->Delete();

		// Error : Truncated incorrect DOUBLE value: '111,112,113'
		// $m['status'] = 0;
		// $data = $m_article->Where($where)
		// 					->ORR()
		// 					->Where('title', 'NOT LIKE', '第')
		// 					//->Where('id', 'IN', $inArray)
		// 					->ORR()
		// 					//->Where('id', 'NOT IN', '111,112,113')
		// 					->Where('id', '>=', 1)
		// 					->Between('id', 1, 1001)
		// 					->Where('id <= "31"')
		// 					->Between('id', 1, 99)
		// 					->Limit(20)
		// 					->Update($m);
		// pr($data); die;

		// $data = $m_article->Where($where)
		// 					->ORR()
		// 					->Where('title', 'NOT LIKE', '第')
		// 					//->Where('id', 'IN', $inArray)
		// 					->ORR()
		// 					//->Where('id', 'NOT IN', '111,112,113')
		// 					->Where('id', '>=', 1)
		// 					->Between('id', 1, 1001)
		// 					->Where('id <= "31"')
		// 					->Between('id', 1, 99)
		// 					->Limit(10)
		// 					->Delete();

		// $m['status'] = 1;
		// $articleID = 31;
		// $code = $m_article->UpdateByID($m, $articleID);
		// echo $code; br();
		
		die;
    }

}
