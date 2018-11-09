<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class oauth_lib
{
	const COOK_NAME ='WeChatWeiLan';	
	function __construct(){
		// Get CI Instance
		// Get CI Instance
		$this->CI = &get_instance();
		//include FCPATH."/modules/oauth/libraries/wechat.class.php";
		$this->CI->load->helper('cookie');
		$this->CI->load->model('oauth_model');
	}
	//设置微信启动信息
	function setWechatOption($option=array()){
		//$option=$option?$option:$this->site;
		if(!$option){
			$option=array(
				'token'=>$this->site->token,
				'appid'=>$this->site->appid,
				'appsecret'=>$this->site->appsecret,
			);
		}
		$this->option=$option;
		$this->weObj = new Wechat($this->option);
	}
	function getWechatCookie(){
		$info=array();
		$data=get_cookie(oauth_lib::COOK_NAME);
		if($data){
			$info=json_decode($data,true);
		}
		return $info;
	}
	function setWechatCookie($data){
		$old=$this->getWechatCookie();
		foreach($data as $key=>$value){
			$old[$key]=$value;
		}
		//创建记录微信用户
		if($old['openid']){
			$res=$this->CI->oauth_model->fetch('WU','*',null,array('openid'=>$old['openid']));
			$weuser=array(
				'openid'=>$old['openid'],
				'nickname'=>$old['nickname'],
				'sex'=>$old['sex'],
				'language'=>$old['language'],
				'city'=>$old['city'],
				'province'=>$old['province'],
				'country'=>$old['country'],
				'headimgurl'=>$old['headimgurl'],
			);
			if($res->num_rows()>0){
				$this->CI->oauth_model->update('WU',$weuser,array('openid'=>$old['openid']));
			}else{
				$weuser['addtime']=TIME;
				$this->CI->oauth_model->insert('WU',$weuser);
			}
		}
		set_cookie(oauth_lib::COOK_NAME,json_encode($old),86400*360);//存储一年
	}
	function set_userlogin($id)
	{
		//@INFO: This dosn't seem very safe having this exposed to everything.
		// Create Users session data
		$user = $this->CI->oauth_model->fetch('U','*',null,array('id'=>$id));
		$user = $user->row_array();
		$this->CI->session->set_userdata($user);

		if( !$this->CI->session )
		{
			// Could not log user in, something went wrong
			flashMsg('error',$this->CI->lang->line('userlib_login_failed'));

			// Remove autologin value to stop an infinite loop
			delete_cookie('autologin');

			//redirect('auth/login','location');
		}

		// Update users last login time
		$this->CI->oauth_model->update('U',array('last_visit'=>date ("Y-m-d H:i:s")),array('id'=>$id));
	}
}
