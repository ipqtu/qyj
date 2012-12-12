<?php
class Url {
	
	private $url, $scheme, $host, $query, $query_data_array = array (), $last_url;
	
	private $method;
	
	public function __construct($method) {
		$this->method = abs ( intval ( $method ) ) % 3;
		$this->scheme = $this->is_ssl () ? 'https://' : 'http://';
		$this->host = $_SERVER ['HTTP_HOST'];
		$this->url = $this->scheme . $_SERVER ['HTTP_HOST'] . $_SERVER ['REQUEST_URI'];
		$this->deal_url ();
	}
	
	public function mk_url($urlData) {
		$methodFunName = array ('normal', 'fake', 'html' );
		$funName = "mk_" . $methodFunName [$this->method] . "_url";
		return $this->$funName ( $urlData );
	}
	
	private function deal_url() {
		$methodFunName = array ('normal', 'fake', 'html' );
		$funName = "deal_" . $methodFunName [$this->method] . "_url";
		$this->$funName ();
	}
	
	public function is_ssl() {
		if (isset ( $_SERVER ['HTTPS'] )) {
			if ('on' == strtolower ( $_SERVER ['HTTPS'] ))
				return true;
			if ('1' == $_SERVER ['HTTPS'])
				return true;
		} elseif (isset ( $_SERVER ['SERVER_PORT'] ) && ('443' == $_SERVER ['SERVER_PORT'])) {
			return true;
		}
		return false;
	}
	
	private function mk_normal_url($urlData) {
		$query_url = "";
		preg_match ( '|(.*\.php)|i', $this->url, $matches );
		if (empty ( $matches )) {
			$query_url .= $this->scheme . $this->host . "/?";
		} else {
			$query_url .= $matches [1] . '?';
		}
		foreach ( $urlData as $value ) {
			(empty ( $value )) || $query_url .= $value . "&";
		}
		return substr ( $query_url, 0, - 1 );
	}
	
	private function deal_normal_url() {
		preg_match ( '|\?(.*)|i', $_SERVER ['REQUEST_URI'], $matches );
		if (empty ( $matches )) {
			$this->query = "";
			$this->query_data_array = array ();
		} else {
			$this->query = $matches [1];
			$this->query_data_array = explode ( '&', $this->query );
		}
	}
	
	private function mk_fake_url($urlData) {
		$query_url = "";
		preg_match ( '|(.*\.php)|i', $this->url, $matches );
		if (empty ( $matches )) {
			$query_url .= $this->scheme . $this->host . "/index.php";
		} else {
			$query_url .= $matches [1];
		}
		foreach ( $urlData as $value ) {
			(empty ( $value )) || $query_url .= "/" . $value;
		}
		return $query_url;
	}
	
	private function deal_fake_url() {
		preg_match ( '|.php/(.*)|i', $_SERVER ['REQUEST_URI'], $matches );
		if (empty ( $matches )) {
			$this->query = "";
			$this->query_data_array = array ();
		} else {
			$this->query = $matches [1];
			$this->query_data_array = explode ( '/', $this->query );
		}
	}
	
	private function mk_html_url($urlData) {
		$html_name = array_pop ( $urlData );
		$query_url = "";
		foreach ( $urlData as $value ) {
			($value === '') || $query_url .= $value . "/";
		}
		if ($html_name === '')
			return $this->scheme . $this->host . "/" . $query_url;
		return $this->scheme . $this->host . "/" . $query_url . $html_name . '.html';
	}
	
	private function deal_html_url() {
		$this->path = "";
		preg_match ( '|^/(.*)\.html|i', $_SERVER ['REQUEST_URI'], $matches );
		empty ( $matches ) && preg_match ( '|^/(.*)|i', $_SERVER ['REQUEST_URI'], $matches );
		$matches [1] = preg_replace ( '/([\w]*\.php\/)/', "", $matches [1] );
		$this->query = $matches [1];
		$this->query_data_array = explode ( '/', $this->query );
		$last_data = array_pop ( $this->query_data_array );
		($last_data == "") || $this->query_data_array [] = $last_data;
	}
	
	public function get_url() {
		return $this->url;
	}
	
	public function get_query() {
		return $this->query;
	}
	
	public function get_query_data() {
		return $this->query_data_array;
	}
	
	public function get_site_url() {
		return $this->scheme . $this->host;
	}
	
	public function get_last_url() {
		return isset ( $_COOKIE ['last_url'] ) ? $_COOKIE ['last_url'] : $this->get_site_url ();
	}
}