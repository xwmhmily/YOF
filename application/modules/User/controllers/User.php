<?php

class UserController extends BasicController {

	private $m_user;

	private function init(){
		$this->m_user = $this->load('user');
		$userID = $this->getSession('userID');

		if($userID){
			define('USER_ID', $userID);
		}
	}

	public function indexAction(){
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

            $url = '/user/user';
            $buffer['pageNav'] = generatePageLink($page, $pages, $url, $total);
            $buffer['articles'] = $m_article->Where($where)->Order($order)->Limit($limit)->Select();
        }else{
        	$this->redirect('/');
        }

        $this->getView()->assign($buffer);
	}

	// Login
	public function loginAction(){
		
	}

	public function loginActAction(){
		$username = $this->getPost('username');
		$password = $this->getPost('password');

		$field = array('id');
		$where = array('username' => $username, 'password' => $password);
		$data  = $this->m_user->Field($field)->Where($where)->SelectOne();
		$userID = $data['id'];

		if($userID){
			// Set to session
			$this->setSession('userID', $userID);
			$this->setSession('username', $username);

			$this->redirect('/user/user'); // 会令 jsAlert失效
		}else{
			jsAlert('登录失败, 请检查用户名和密码');
			jsRedirect('/user/user/login');
		}
	}

	// Register
	public function registerAction(){

	}

	public function registerActAction(){
		$m['username'] = $this->getPost('username');
		$m['password'] = $this->getPost('password');
		
		$userID = $this->m_user->Insert($m);
		if(!$userID){
			$msg = '注册失败,请重试';
			$url = '/user/user/register';
		}else{
			$msg = '注册成功,请登录';
			$url = '/user/user/login';
		}

		jsAlert($msg);
		jsRedirect($url);
	}

	// Logout
	public function logoutAction(){
		$this->unsetSession('userID');
		$this->unsetSession('username');

		$this->redirect('/');
	}

	// Profile
	public function profileAction(){
		$buffer['user'] = $this->m_user->SelectByID('', USER_ID);

		$provinceID = $buffer['user']['provinceID'];
		$cityID = $buffer['user']['cityID'];
		$regionID = $buffer['user']['regionID'];

		$buffer['cityElement'] = Helper::loadComponment('City')->generateCityElement($provinceID, $cityID, $regionID, 1);
		$this->getView()->assign($buffer);
	}

	public function profileActAction(){
		$m['realname']   = $this->getPost('realname');
		$m['provinceID'] = $this->getPost('areaProvince');
		$m['cityID']     = $this->getPost('areaCity');
		$m['regionID']   = $this->getPost('areaRegion');

		$m['province'] = $this->load('Province')->getProvinceNameByID($m['provinceID']);
		$m['city']     = $this->load('City')->getCityNameByID($m['cityID']);
		if($m['regionID']){
			$m['region'] = $this->load('Region')->getRegionNameByID($m['regionID']);
		}
		
		$code = $this->m_user->UpdateByID($m, USER_ID);
		if(FALSE === $code){
			jsAlert('编辑个人信息失败, 请重试');
			jsRedirect('/user/user/profile');
		}

		$this->redirect('/user/user');
	}
}