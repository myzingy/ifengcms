<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class oauth extends Public_Controller
{
	/**
	 * Constructor
	 */
	function __construct(){
		parent::__construct();
		// Load the Auth_form_processing class
		$this->load->library('oauth_lib');
		$this->load->helper('cookie');
	}
	function index(){
		if(!is_user()){
			redirect(base_url().'mnew/login.html');
		}
	}
	function login($referer=''){
		if(is_user()){
			//已经登录
			redirect('');
		}
		$cookie=$this->oauth_lib->getWechatCookie();
		
		if($referer=='referer' || ($_SERVER['HTTP_REFERER'] && !$cookie['referer'])){
			$ref_url=$_SERVER['HTTP_REFERER']?$_SERVER['HTTP_REFERER']:site_url();
			$this->oauth_lib->setWechatCookie(array('referer'=>$ref_url));
		}
		if($this->isWechatBrowser()){
			$this->goWeChat();
		}else{
			//跳转到
			redirect(base_url().'mnew/login.html');
		}
	}
	function register(){
		if(is_user()){
			//已经登录
			redirect('');
		}
		if($this->isWechatBrowser()){
			$this->goWeChat();
		}else{
			//跳转到
			redirect(base_url().'mnew/register.html');
		}
	}
	function isWechatBrowser(){
		$this->load->library('user_agent');
		$agent=$this->agent->agent_string();
		return strstr($agent,'MicroMessenger');
	}
	function goWeChat(){
		$cookie=$this->oauth_lib->getWechatCookie();
		if($cookie['openid']){
			//根据 openid 检查电话，
			$res=$this->oauth_model->fetch('UP','*',null,array('openid'=>$cookie['openid']));
			if($res->num_rows()>0){
				$user=$res->row();
			}
			if(!empty($user->phone)){
				//如果用户名是手机号则进行登录
				$this->oauth_lib->set_userlogin($user->user_id);
				//跳转到之前页面
				$cookie=$this->oauth_lib->getWechatCookie();
				$this->oauth_lib->setWechatCookie(array('referer'=>''));
				redirect($cookie['referer']?$cookie['referer']:site_url());
			}else{
				//跳转到注册页面
				redirect(base_url().'mnew/register.html');
			}
		}else{
			$url='http://121.199.26.107:9999/auth.php?redirect_uri='.base64_encode(site_url('oauth/setWechatCookie')).'&scope=snsapi_base&state=123';	
			redirect($url);
		}
		//header("location:".$url);
	}
	function setWechatCookie(){
		$query=$_SERVER['QUERY_STRING'];
		parse_str($query,$param);
		$this->oauth_lib->setWechatCookie($param);
		$this->login();
	}
}