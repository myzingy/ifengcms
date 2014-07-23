<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class CI_Sms2014{
	var $pdata=array(
		'cdkey'=>'9SDK-EMY-0999-JCYPK'
		,'password'=>'241481'
		,'smspriority'=>2
		,'addserial'=>TIME
		,'seqid'=>1
	);
	var $url='http://sdk999ws.eucp.b2m.cn:8080/sdkproxy/sendsms.action';
	function send($UserNumber,$MessageContent){
		//send_url=?	
		//POST SpCode=?LoginName=?&Password=?&UserNumber=?&MessageContent=?&SerialNumber=?&f=1
		//$MessageContent=iconv("UTF-8", "GBK", $MessageContent);
		$this->pdata['phone']=$UserNumber;
		$this->pdata['message']=$MessageContent.'【农产品】';
		$postdata=array();
		foreach ($this->pdata as $key => $value) {
			array_push($postdata,$key.'='.$value);
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, implode("&", $postdata));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_exec($ch);
		curl_close($ch);
	}
}
?>