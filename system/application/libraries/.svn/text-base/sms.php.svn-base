<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class CI_Sms{
	var $username='netwolf103';
	var $password='weilan001';
	function setUrl($url=''){
		$this->password=md5($this->password);
		$url=$url?$url:$this->url;
		$this->url=strtr($url, array('USERNAME'=>$this->username,'PASSWORD'=>$this->password));
		return $this->url;
	}
	function send($UserNumber,$MessageContent){
		$this->url='http://www.smsbao.com/sms?u=USERNAME&p=PASSWORD';
		$this->setUrl();
		$this->url.="&m={$UserNumber}";
		$this->url.="&c=".urlencode($MessageContent);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		@curl_exec($ch);
		curl_close($ch);
	}
	function check(){
		$this->url='http://www.smsbao.com/query?u=USERNAME&p=PASSWORD';
		$this->setUrl();
		$res=file_get_contents($this->url);
		return $res;
	}
}
?>