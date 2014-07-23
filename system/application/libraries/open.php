<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class CI_Open{
	public $HASH = 'wisheli2014@';
	//构造appid、AppSecret
	public function hashString($str=""){
		if(!$str) return $str;
		return md5($str.$this->HASH);
	}
	//构造带有signature的url参数
	public function hashUrlString($arr,$AppSecret){
		$tmpArr = array($AppSecret);
		$signature='';
		foreach($arr as $key=>$val){
	    	array_push($tmpArr,$val);
	    }	
	    sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$signature = sha1( $tmpStr );
		
		$str=http_build_query($arr)."&signature=".$signature;
		return $str;
	}
	//检查Signature
	public function checkSignature($AppSecret){
	    $tmpArr = array($AppSecret);
		$signature='';	
	    foreach($_GET as $key=>$val){
	    	if($key=='signature'){
	    		$signature=$val;
				continue;	
	    	}
	    	array_push($tmpArr,$val);
	    }	
	    sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
	//统计，10s内相同ip算一次访问
	public function total($appid,$type='admin'){
		$ip=MOD::app()->request->userHostAddress;
		$model=t_plugins_total::model()->recently()->find('FAppID=:FAppID AND FIP=:FIP AND FType=:FType'
			, array(':FAppID'=>$appid,':FIP'=>$ip,':FType'=>$type));
		if(time()-$model->FAddTime<10) return false;
		
		$total=new t_plugins_total;
		$total->FAppID=$appid;
		$total->FType=$type;
		$total->FAddTime=time();
		$total->FIP=MOD::app()->request->userHostAddress;
		$total->FSiteID=Mod::app()->request->getParam('siteid',commonModel::getSession('SITEID'));
		$total->FOpenID=Mod::app()->request->getParam('userid','');
		$total->save();
		
		$plugin=t_plugins::model()->findByPk($appid);
		if($type=='public'){
			$plugin->FShowPublicNum+=1;	
		}else{
			$plugin->FShowAdminNum+=1;
		}
		$plugin->save();
		return true;
	}
	//wechat 实例，必须是高级号，进行用户授权
	public function wechat(){
		$options = array(
			'token'=>'tokenaccesskey', //填写你设定的key
			'appid'=>'wxeaabaff57de68f0e', //填写高级调用功能的app id
			'appsecret'=>'59d1e0064292fac25dd1d6b4c98c97a6', //填写高级调用功能的密钥
		);
		return new wechatModel2($options);
	}
	//获取plugin user cookies
	public function cookie($name,$value=""){
		$cookieName='VBoo_Plugin';
		//取得之前的 cookie
		$cookie=Mod::app()->request->getCookies();
		//unset($cookie[$cookieName]);
		$cookie=json_decode($cookie[$cookieName]->value,true);
		if(!$value) return $cookie[$name];
		$cookie[$name]=$value;
		$__cookie = new CHttpCookie($cookieName,json_encode($cookie));
		$__cookie->expire = time()+86400*30;  //有限期30天
		Mod::app()->request->cookies[$cookieName]=$__cookie;
	}
}
?>