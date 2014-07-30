<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class api_lib
{
	var $user=array(
		'siteid'=>1,
		'appid'=>'bd6f6e8269e53e2f96971bd17bd05818',
		'fighterID'=>'9b2bd5f1a36f243c',
	);
	var $statusCode=array(
		-1=>'登录超时，请重新登录。'
		,0=>'正常'
		,10000=>'一般性错误'
		,10001=>'接口未定义'
		,10002=>'非法Token'
	);
	var $limit=15;
	function __construct(){
		// Get CI Instance
		$this->CI = &get_instance();
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
	//文件上传
	function uploadImage(){
		$formName	= 'files';
		if($_FILES[$formName] && $_FILES[$formName]['error'][0] == 0){
			if (!in_array($_FILES[$formName]['type'][0], array('image/jpeg','image/pjpeg','image/x-png','image/png','image/gif'))){
				$this->status($info, 10000,'只允许上传jpg,gif,png格式图片!');
				return $info;
			}		
			$name = $_FILES[$formName]['name'][0];
			$filename = md5($name).substr($name, strrpos($name, '.'));
			
			$this->CI->load->module_helper('baebcs','baebcs');
			$baidu_bcs=baidu_bcs();
			$url=bsc_upload($baidu_bcs,$_FILES[$formName]['tmp_name'][0],$this->user['siteid'].'/'.$filename);
				
			if(!$url){
				$this->status($info, 10000,'上传文件失败，请重新上传');
			}else{
				$info= array('files'=>array('name'=>$url));	
				$this->status($info, 0);
			}
			return $info;
		}
		$this->status($info, 10000,'请选择上传文件!');
		return $info;
	}
	#################################################
	# 手机端处理逻辑
	#################################################
	//检查微信用户状态
	function checkWechat(){
		$info=array(
			'status'=>-1,
			'url'=>'http://121.199.26.107:9999/auth.php?redirect_uri='.base64_encode(site_url('fighter/setUserCookie')).'&scope=snsapi_base&state=123'
		);
		$this->CI->load->module_library('fighter','fighter_lib');
		$cookie=$this->CI->fighter_lib->getFighterCookie();
		if($cookie['siteid']>0 && $cookie['appid']){
			$cookie['fighterID']=$cookie['fighterID']?$cookie['fighterID']:$this->user['fighterID'];
			$this->user=$cookie;
			//检查用户状态
			$status=$this->CI->fighter_lib->getFighterStatus();
			//[-1,0,1] 空记录，正常，待审核
			if($status==-1){
				$info['url']='http://121.199.26.107:9999/auth.php?redirect_uri='.base64_encode(site_url('fighter/setUserCookie')).'&scope=snsapi_base&state=123';
			}elseif($status==1){
				$info['status']=0;	
				$info['url']=base_url().'/mobile/battleplane/reg.html';
			}else{
				$info['status']=0;	
				$info['url']=base_url().'/mobile/battleplane/';
			}
		}
		$info['status']=0;	
		return $info;
	}
	#################################################
	# CMS 系统接口
	#################################################
	//点赞
	
	//点评
	
	//打赏
	
	//发布动态
	
	
}