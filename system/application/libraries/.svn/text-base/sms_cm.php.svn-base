<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class CI_Sms_cm{
	var $url='http://www.68885888.com/ws/BatchSend.aspx?CorpID=251941666&Pwd=cm123321';
	function send($UserNumber,$MessageContent){
		$this->url.="&Mobile={$UserNumber}";
		$this->url.="&Content=".urlencode(iconv('UTF-8', 'GBK', $MessageContent));
		$this->url.="&Cell=";
		$this->url.="&SendTime=";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		$res=curl_exec($ch);
		curl_close($ch);
		return $res;
	}
}
?>