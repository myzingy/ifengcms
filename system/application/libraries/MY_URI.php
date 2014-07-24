<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
class MY_URI extends CI_URI {
	function _filter_uri($str) {
		if ($str != '' AND $this -> config -> item('permitted_uri_chars') != '') {
			$str = urlencode($str);
			//   if ( ! preg_match("|^[".preg_quote($this->config->item('permitted_uri_chars'))."]+$|i", $str))
			if (!preg_match("|^[" . ($this -> config -> item('permitted_uri_chars')) . "]+$|i", rawurlencode($str))) {
				exit('The URI you submitted has disallowed characters.');
			}
			$str = urldecode($str);
		}
		return $str;
	}
	function getParamsArr($key=''){
		$arr=array();
		for($i=1;$i<count($this->rsegments);$i+=2){
			$arr[$this->rsegments[$i]]=$this->rsegments[$i+1];
		}
		return $arr;
	}
}