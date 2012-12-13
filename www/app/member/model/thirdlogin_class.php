<?php

class Thirdlogin {
	
	private $db = "";
	
	private $method = "";
	
	private $binding_id = "";
	
	private $user_id = "";
	
	private $return_url = "";
	
	private $login_url = "";
	
	private $third_user_info = "";
	
	private $support_third_login_name = array ('renren', 'qq' );
	
	//renren
	private $renren_api_key = "c67eac8a281a4a12ab3bcbc5c5aebe11";
	private $renren_secret_key = "34128a9dd4424232a139040dc656ebdd";
	private $renren_scope = "publish_share+read_user_album+read_user_photo+publish_feed+publish_share";
	//qq
	private $qq_app_id = "100261125";
	private $qq_app_key = "9296e3cd5041c89c4d6c89aab9439cc8";
	private $qq_scope = "get_user_info,add_share,list_album,add_album,upload_pic,add_topic,add_one_blog,add_weibo";
	
	public function __construct($return_url, $method, $login_url) {
		$this->return_url = $return_url;
		$this->login_url = $login_url;
		in_array ( $method, $this->support_third_login_name ) || Display::display_404_error ();
		$this->method = $method;
		$this->db = Mysql::get_object ();
	}
	
	public function login() {
		$function_name = $this->method . "_login";
		return $this->$function_name ();
	}
	
	public function check_binding() {
		$function_name = $this->method . "_whether_binding";
		return $this->$function_name ();
	}
	
	public function get_user_id() {
		return $this->user_id;
	}
	
	public function get_binding_id() {
		return $this->binding_id;
	}
	
	public function get_binding_type() {
		return $this->method;
	}
	
	public function get_binding_user_info($binding_id) {
		$function_name = $this->method . "_user_info";
		return $this->$function_name ( $binding_id );
	}
	
	public function get_third_user_url() {
		$function_name = $this->method . "_user_url";
		return $this->$function_name ();
	}
	public function binding_user($user_id, $binding_id) {
		$function_name = $this->method . "_binding_user";
		return $this->$function_name ( $user_id, $binding_id );
	}
	
	//=========================renren_login======================================
	private function renren_login() {
		session_start ();
		if (empty ( $_GET )) {
			$_SESSION ['state'] = md5 ( uniqid ( rand (), TRUE ) );
			$url = "https://graph.renren.com/oauth/authorize?client_id={$this->renren_api_key}&redirect_uri={$this->return_url}&response_type=code&scope={$this->renren_scope}&state={$_SESSION ['state']}";
			Display::load_url ( $url );
		} else {
			isset ( $_GET ['code'] ) || Display::load_url ( $this->login_url );
			if ($_GET ['state'] == $_SESSION ['state']) {
				$url = "http://graph.renren.com/oauth/token?grant_type=authorization_code&client_id={$this->renren_api_key}&redirect_uri={$this->return_url}&client_secret={$this->renren_secret_key}&code={$_GET['code']}";
				require_once LIB_URL.'class_snoopy.php';
				$snoopy = new Snoopy();
				$snoopy->fetch($url);
				$response = $snoopy->results;
				require_once LIB_URL . 'class_json.php';
				$json = new Services_JSON ();
				$params = $json->decode ( $response );
				if (isset ( $params->error )) {
					return false;
				}
				$this->third_user_info = $params;
				return true;
			}
			return false;
		}
	}
	
	private function renren_whether_binding() {
		$sql = "SELECT * FROM `" . $this->db->prefix . 'renren` WHERE `third_id` = %d';
		$result = $this->db->get_row ( $this->db->prepare ( $sql, $this->third_user_info->user->id ) );
		if (empty ( $result )) {
			$result = $this->db->insert ( $this->db->prefix . 'renren', array ('user_id' => 0, 'third_avatar' => $this->third_user_info->user->avatar [0]->url, 'third_id' => $this->third_user_info->user->id, 'third_name' => $this->third_user_info->user->name, 'refresh_token' => $this->third_user_info->refresh_token, 'access_token' => $this->third_user_info->access_token ) );
			$this->renren_add_feed ( "趣友街", "中国矿业大学兴趣交流平台", "http://www.quyoujie.com", "http://www.quyoujie.com/images/logo.jpg", "我今天加入了趣友街网站,朋友们你们也去看看吧!", $this->third_user_info->access_token );
			$this->binding_id = ($result > 0) ? $this->db->get_insert_id () : 0;
			return false;
		}
		$this->db->update ( $this->db->prefix . 'renren', array ('third_avatar' => $this->third_user_info->user->avatar [0]->url, 'third_name' => $this->third_user_info->user->name, 'refresh_token' => $this->third_user_info->refresh_token, 'access_token' => $this->third_user_info->access_token ), array ('third_id' => $this->third_user_info->user->id ) );
		$this->user_id = $result->user_id;
		$this->binding_id = $result->id;
		if ($this->user_id > 0)
			return true;
		return false;
	}
	
	private function renren_binding_user($user_id, $binding_id) {
		$result = $this->renren_user_info ( $binding_id );
		if (empty ( $result )) {
			return false;
		}
		$this->db->update ( $this->db->prefix . 'renren', array ('user_id' => $user_id ), array ('id' => $result->id ) );
		return true;
	}
	
	private function renren_user_info($binding_id) {
		$sql = "SELECT * FROM `" . $this->db->prefix . 'renren` WHERE `id` = %d';
		return $this->db->get_row ( $this->db->prepare ( $sql, $binding_id ) );
	}
	
	private function renren_user_url() {
		return "http://www.renren.com/" . $this->third_user_info->third_id;
	}
	
	private function renren_add_feed($name, $describe, $url, $image, $message, $access_token) {
		$method = "feed.publishFeed";
		$v = "1.0";
		$params = array ('v' => $v, 'method' => $method, 'name' => $name, 'description' => $describe, 'url' => $url, 'image' => $image, 'message' => $message, 'access_token' => $access_token );
		ksort ( $params );
		reset ( $params );
		foreach ( $params as $k => $v ) {
			$str .= $k . '=' . $v;
		}
		$params['sig'] = md5 ( $str . $this->renren_secret_key );
		$url = 'http://api.renren.com/restserver.do';
		require_once LIB_URL.'class_snoopy.php';
		$snoopy_object = new Snoopy();
		$result = $snoopy_object->submit($url,$params);
	}
	//========================qq_login=============================
	

	public function get_url_contents($url) {
		if (ini_get ( "allow_url_fopen" ) == "1")
			return file_get_contents ( $url );
		
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, TRUE );
		curl_setopt ( $ch, CURLOPT_URL, $url );
		$result = curl_exec ( $ch );
		curl_close ( $ch );
		
		return $result;
	}
	
	private function qq_login() {
		session_start ();
		if (empty ( $_GET )) {
			$_SESSION ['state'] = md5 ( uniqid ( rand (), TRUE ) );
			$url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=" . $this->qq_app_id . "&redirect_uri=" . urlencode ( $this->return_url ) . "&state=" . $_SESSION ['state'] . "&scope=" . $this->qq_scope;
			Display::load_url ( $url );
		} else {
			isset ( $_GET ['code'] ) || Display::load_url ( $this->login_url );
			if ($_GET ['state'] == $_SESSION ['state']) {
				$url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&" . "client_id=" . $this->qq_app_id . "&redirect_uri=" . urlencode ( $this->return_url ) . "&client_secret=" . $this->qq_app_key . "&code=" . $_GET ["code"];
				$ch = curl_init ();
				//CURLOPT_HTTPHEADER  用来设置http头字段的数组，相当于html的<head></head>中的内容设置
				curl_setopt ( $ch, CURLOPT_HTTPHEADER, array ('Content-Type: application/x-www-form-urlencoded', 'Connection: close', 'Cache-Control: no-cache', 'Accept-Language: zh-cn' ) );
				//CURLOPT_TIMEOUT  响应时间设置
				curl_setopt ( $ch, CURLOPT_TIMEOUT, 20 );
				//CURLOPT_USERAGENT  在HTTP请求中包含一个'User-Agent: '头的字符串(用来设置用户浏览器)
				curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)' );
				//CURLOPT_HEADER  启用时会将头文件的信息作为数据流输出(true,false)
				curl_setopt ( $ch, CURLOPT_HEADER, 0 );
				//CURLOPT_FOLLOWLOCATION  启用时会将服务器服务器返回的'Location: '放在header中递归的返回给服务器，使用CURLOPT_MAXREDIRS可以限定递归返回的数量。
				curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 0 );
				//CURLOPT_RETURNTRANSFER  (这个很重要)将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
				curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
				//CURLOPT_POST 启用时会发送一个常规的POST请求，类型为：application/x-www-form-urlencoded，就像表单提交的一样。
				//curl_setopt ( $ch, CURLOPT_POST, 0 );
				//CURLOPT_URL  需要获取的URL地址，也可以在curl_init()函数中设置
				curl_setopt ( $ch, CURLOPT_URL, $url );
				// CURLOPT_SSL_VERIFYPEER  禁用后cURL将终止从服务端进行验证。使用CURLOPT_CAINFO选项设置证书
				curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
				//CURLOPT_SSL_VERIFYHOST    1 检查服务器SSL证书中是否存在一个公用名(common name)。译者注：公用名(Common Name)一般来讲就是填写你将要申请SSL证书的域名 (domain)或子域名(sub domain)。2 检查公用名是否存在，并且是否与提供的主机名匹配。
				curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, false );
				//CURLOPT_HTTPGET   用get方式获取参数
				curl_setopt ( $ch, CURLOPT_HTTPGET, 1 );
				$res = curl_exec ( $ch );
				curl_close ( $ch );
				echo $res;
				exit ();
				
				//set access token to session
				$_SESSION ["access_token"] = $params ["access_token"];
			}
		}
	}
	
	private function qq_whether_binding() {
		$sql = "SELECT * FROM `" . $this->db->prefix . 'renren` WHERE `third_id` = %d';
		$result = $this->db->get_row ( $this->db->prepare ( $sql, $this->third_user_info->user->id ) );
		if (empty ( $result )) {
			$result = $this->db->insert ( $this->db->prefix . 'renren', array ('user_id' => 0, 'third_avatar' => $this->third_user_info->user->avatar [0]->url, 'third_id' => $this->third_user_info->user->id, 'third_name' => $this->third_user_info->user->name, 'refresh_token' => $this->third_user_info->refresh_token, 'access_token' => $this->third_user_info->access_token ) );
			$this->binding_id = ($result > 0) ? $this->db->get_insert_id () : 0;
			return false;
		}
		$this->db->update ( $this->db->prefix . 'renren', array ('third_avatar' => $this->third_user_info->user->avatar [0]->url, 'third_name' => $this->third_user_info->user->name, 'refresh_token' => $this->third_user_info->refresh_token, 'access_token' => $this->third_user_info->access_token ), array ('third_id' => $this->third_user_info->user->id ) );
		$this->user_id = $result->user_id;
		$this->binding_id = $result->id;
		if ($this->user_id > 0)
			return true;
		return false;
	}
	
	private function qq_binding_user($user_id, $binding_id) {
		$result = $this->renren_user_info ( $binding_id );
		if (empty ( $result )) {
			return false;
		}
		$this->db->update ( $this->db->prefix . 'renren', array ('user_id' => $user_id ), array ('id' => $result->id ) );
		return true;
	}
	
	private function qq_user_info($binding_id) {
		$sql = "SELECT * FROM `" . $this->db->prefix . 'renren` WHERE `id` = %d';
		return $this->db->get_row ( $this->db->prepare ( $sql, $binding_id ) );
	}
	
	private function qq_user_url() {
		return "http://www.renren.com/" . $this->third_user_info->third_id;
	}
}