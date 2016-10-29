<?php
	class REST {

		public $_allow = array();
		public $_content_type = "application/json;charset=UTF-8";
		public $_request = array();

		private $_method = "";
		private $_code = 200;

		public function __construct(){
		}

		public function get_referer(){
			return $_SERVER['HTTP_REFERER'];
		}

		public function response($data,$status){
			$this->_code = ($status)?$status:200;
			$this->set_headers();
			echo $data;
			exit;
		}
		private function get_status_message(){
			$status = array(
						200 => 'OK',
						201 => 'Created',
						204 => 'No Content',
						404 => 'Not Found',
						406 => 'Not Acceptable');
			return ($status[$this->_code])?$status[$this->_code]:$status[500];
		}

		public function get_request_method(){
			return $_SERVER['REQUEST_METHOD'];
		}

		private function set_headers(){
			header("HTTP/1.1 ".$this->_code." ".$this->get_status_message());
			header("Content-Type:".$this->_content_type);
			header("Access-Control-Allow-Origin:*");
			header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
			header("Access-Control-Allow-Headers: X-Requested-With");
		}
	}
?>
