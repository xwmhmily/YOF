<?php

class ProfileController extends BasicController {

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

            $url = '/user/profile';
            $buffer['pageNav'] = generatePageLink($page, $pages, $url, $total);
            $buffer['articles'] = $m_article->Where($where)->Order($order)->Limit($limit)->Select();
        }else{
        	$this->redirect('/');
        }

        // 如果有 xhprof 则开启跟踪功能
        if(function_exists('xhprof_disable')){
	        $data = xhprof_disable();
	        include_once LIB_PATH.'/xhprof_lib/utils/xhprof_lib.php';
	        include_once LIB_PATH.'/xhprof_lib/utils/xhprof_runs.php'; 
	        $objXhprofRun = new XHProfRuns_Default();
	        $run_id = $objXhprofRun->save_run($data, 'xhprof');
	    }

        $buffer['run_id'] = $run_id;
        $this->getView()->assign($buffer);
	}

	// Logout
	public function logoutAction(){
		$this->unsetSession('userID');
		$this->unsetSession('username');

		$this->redirect('/');
	}

	// Profile
	public function editAction(){
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

		// Upload avatar if selected
		if($_FILES['avatar']['name']){
			Helper::import('File');
            Yaf_Loader::import('L_Upload.class.php');

            $fileName = CUR_TIMESTAMP;
            $up = new L_Upload($_FILES['avatar'], UPLOAD_PATH.'/');
            $result = $up->upload($fileName);

            if($result == 1){
            	$m['avatar'] = $fileName.'.'.$up->extension;
            	$this->m_user->UpdateByID($m, USER_ID);
            }else{
            	jsAlert($result);
            }
        }
		
		if(FALSE === $code && $result != 1){
			jsAlert('编辑个人信息失败, 请重试');
		}

		$this->redirect('/user/profile/edit');
	}

	// 二维码
	public function qrcodeAction(){
		$value = $this->get('value', FALSE);
		if($value){
			Yaf_Loader::import('L_Qrcode.class.php');

	    	$savePath .= APP_PATH.'/public/qrcode';

	    	if(!file_exists($savePath)){
	    		Helper::import('File');
	    		createRDir($savePath);
	    	}

	    	$err  = 'L';
			$size = '10';
			// 有 LOGO 的话去掉下一行的注释, 并作为构造函数的第五个参数传入
			//$logo = APP_PATH.'/asset/logo.jpg';

			Helper::import('String');
			$file = getRandom(6, 1).'.png';
	        $qr = $savePath.'/'.$file;

	        $Qrcode = new L_Qrcode($value, $qr, $err, $size);
			$Qrcode->createQr();

			$buffer['qrCode'] = '/qrcode/'.$file;
		}

		$this->getView()->assign($buffer);
	}

	// phpQuery 采集类
	public function crawlAction(){
		$destination = $this->get('destination', FALSE);
		if($destination){
			include LIB_PATH.'/phpQuery/phpQuery.php';

			phpQuery::newDocumentFile($destination);
			$articles = pq('#main_bg .zixunmain .p_lf .p_pad')->find('ul');

			foreach($articles as $article) {
			   	$m['title']   = pq($article)->find('dl dd a')->html();
			   	$m['title']   = addslashes($m['title']);
			   	$m['img']     = pq($article)->find('dl dt a img')->attr('src');

				$final[] = $m;
			}

			$buffer['articles'] = $final;
		}

		$this->getView()->assign($buffer);
	}

	// Http request
	// I'm sure you have better solution ....
	public function httpAction(){
		$url = $this->get('url', FALSE);
		if($url){
			$buffer['content'] = executeHTTPRequest($url, '');
		}

		$this->getView()->assign($buffer);
	}

	// Article list API
	public function apiAction(){
		$url = $this->get('url', FALSE);
		if($url){
			// 此处为了演示和接近实际环境，将 URL 定死
			$url = 'http://yof.mylinuxer.com/api/article';

			// Secure your API with CUR_TIMESTAMP and API_KEY
			$m['time'] = CUR_TIMESTAMP;
			$m['sign'] = Helper::generateSign($m);

			$buffer['content'] = executeHTTPRequest($url, $m); 
		}

		$this->getView()->assign($buffer);
	}

	// Article detail API
	public function apiDetailAction(){
		$articleID = $this->get('articleID');
		$url = $this->get('url', FALSE);
		if($url){
			// 此处为了演示和接近实际环境，将 URL 定死
			$url = 'http://yof.mylinuxer.com/api/article/detail';

			// Secure your API with CUR_TIMESTAMP and API_KEY
			$m['time'] = CUR_TIMESTAMP;
			$m['sign'] = Helper::generateSign($m);
			$m['articleID'] = $articleID;

			$buffer['content'] = executeHTTPRequest($url, $m);
		}

		$this->getView()->assign($buffer);
	}

	// Uploadify
	public function uploadifyAction(){
		
	}

}