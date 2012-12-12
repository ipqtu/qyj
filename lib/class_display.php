<?php
class Display {
	
	static function display_404_error() {
		global $Object_template, $_URL, $action;
		print_r ( $_URL );
		echo 'action:' . $action;
		include_once APP_URL . 'display/error404.php';
		exit ();
	}
	
	//问题？
	static function display_back($message) {
		exit ( '<script type="text/javascript">
					<!--
				alert("' . $message . '");
				window.history.back();
					//-->
				</script>' );
	}
	
	static function load_url($url = "") {
		global $Object_template, $Object_url;
		(empty ( $url )) && $url = $Object_url->get_last_url ();
		header ( "Location: " . $url );
		exit ();
	}
	
	static function display_nologin() {
		global $Object_url;
		$url = $Object_url->mk_url ( array ('member', 'login' ) );
		exit ( '<script type="text/javascript">
					<!--
				alert("你还没有登录,不能进行相关操作,请登录!");
				location.href="' . $url . '";
					//-->
				</script>' );
	}
	
	static function display_system_error($message) {
		$error_str = sprintf ( 'o,my god： %s', $message );
		exit ( $error_str );
	}
	
	static function display_dialog($message, $url = "") {
		global $Object_template, $Object_url;
		(empty ( $url )) && $url = $Object_url->get_last_url ();
		exit ( '<script type="text/javascript">
					<!--
				alert("' . $message . '");
				location.href="' . $url . '";
					//-->
				</script>' );
	}
}

