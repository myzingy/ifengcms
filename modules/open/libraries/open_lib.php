<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class open_lib
{
	var $statusCode=array(
		-1=>'登录超时，请重新登录。'
		,0=>'正常'
		,10000=>'一般性错误'
		,10001=>'接口未定义'
		,10002=>'非法Token'
	);
	var $AppID='16da8c490cf88da1c78f6f3875a8dc72';
	var $AppSecret='01126e1ca8ede9228ce0e658c0672c40';
	function open_lib(){
		// Get CI Instance
		$this->CI = &get_instance();
		$this->CI->load->model('open_model');
		$this->CI->load->library('open');
	}
	public function status(&$arr,$code,$str=''){
		$error=$this->statusCode[$code];
		if($error){
			$arr['status']=$code;
			$arr['error']=$str?$str:$error;
		}else{
			$arr['status']=10000;
			$arr['error']=$code;	
		}
		return $arr;
	}
	//检查是否合法请求
	public function check(){
		parse_str($_SERVER['QUERY_STRING'], $_GET);
		$AppSecret=$this->checkAppid($_GET['appid']);
		if($AppSecret && $_GET['siteid']>0){
			return $this->CI->open->checkSignature($AppSecret);
		}
		return false;
	}
	//检查appid
	public function checkAppid($appid=''){
		$_data=array(
			'bd6f6e8269e53e2f96971bd17bd05818'=>'d5efe0340251eed330b6a710b430fbd1',//测试
			'0b9981148224b721c149a883020ff3af'=>'80a1ac3392ac0082fd5ae59146dff40e',//微社力
			'8089f941e27eecf453eb933fbcf09bbe'=>'256e0626e67941e28364afeee13d7276',//商城
			'3e71cf39d469f9f2e1ea6bc0a85e8bc3'=>'ae52c139134b20ea68462743ed8b35cf',//统计系统
			
		);
		return $_data[$appid];
	}
	//构造统计代码
	public function create_total_code($arr){
		$this->CI->load->module_library('oauth','oauth_lib');
		$arr['siteid']=$arr['siteid']?$arr['siteid']:1;
		$arr['type']=$arr['type']?$arr['type']:'article';
		if(strstr($_SERVER['HTTP_HOST'],'192.168')){
			$url='http://'.$_SERVER['HTTP_HOST']."/fighter/";
		}else{
			$url="http://fighter.wisheli.com/";	
		}
		$url.='index.php/stat/index?from=cms';
		$cookie=$this->CI->oauth_lib->getWechatCookie();
		$weuser=array(
			'openid'=>$cookie['openid']?$cookie['openid']:'',
			'nickname'=>$cookie['nickname']?$cookie['nickname']:'',
			'sex'=>$cookie['sex']?$cookie['sex']:'',
			'language'=>$cookie['language']?$cookie['language']:'',
			'city'=>$cookie['city']?$cookie['city']:'',
			'province'=>$cookie['province']?$cookie['province']:'',
			'country'=>$cookie['country']?$cookie['country']:'',
			'headimgurl'=>$cookie['headimgurl']?$cookie['headimgurl']:'',
		);
		$weuser=json_encode($weuser);
$code=<<<ENDCODE
<script type="text/javascript">
	var wsl_stat={
		conf:{
			 siteid:'{$arr['siteid']}',
			 appid:'{$this->AppID}',
			 type:'{$arr['type']}',
			 pkid:'{$arr[pkid]}',
			 wechat:{$weuser}
		}
	 }
</script>
<script type="text/javascript" src="{$url}"></script>
ENDCODE;
		return $code;
	}
}