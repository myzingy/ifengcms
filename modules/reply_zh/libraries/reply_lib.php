<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class reply_lib
{
	var $site;		//站点信息
	var $error;		//错误信息
	var $option;	//微信配置参数
	function reply_lib(){
		// Get CI Instance
		$this->CI = &get_instance();
		$this->CI->load->model('reply_model');
		include FCPATH."/modules/reply/libraries/wechat.class.php";
		
	}
	function error($str='clear'){
		$error=$this->error;	
		$this->error=$str=='clear'?'':$str;
		return $error;
	}
	function init($site=array()){
		$this->site=$site?$site:array(
			'token'=>'ifeng_zhonghan',
			'appid'=>'wx7a0977703527e2ae',
			'appsecret'=>'f3dd8af6968035decaf358630aff22d9'
		);
		$this->site=(object)$this->site;
		$this->setWechatOption();
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
	function apply(){
		
		if(!$this->option['token']){
			$this->weObj->text($this->error())->reply();
		}else{
			$flag=$this->weObj->valid(true);
			if(is_string($flag)){
				//首次验证
				die($flag);
			}elseif($flag){
				
				$type = $this->weObj->getRev()->getRevType();
				switch($type) {
		    		case Wechat::MSGTYPE_TEXT:
		    		case Wechat::MSGTYPE_EVENT:
						
						$data=$this->weObj->getRevData();
						if($data['Event']=='subscribe'){
							//$this->CI->reply_model->delUnUser($data['FromUserName']);
							$this->upBindPhoneInfo($data['FromUserName'],true);
							$msg=$this->CI->reply_model->msgData();
							$msg['subscribe']=$msg['subscribe']?$msg['subscribe']:"欢迎您关注凤凰网中韩交流频道！";
							$data=array('type'=>'text','data'=>$msg['subscribe']);
							break;
						}
						if($data['Event']=='unsubscribe'){
							//$this->CI->reply_model->addUnUser($data['FromUserName']);
							$this->upBindPhoneInfo($data['FromUserName'],false);
							break;
						}
						$key=($type==Wechat::MSGTYPE_TEXT)?$data['Content']:$data['EventKey'];
						if('ddebug'==$key){
							$ddebug=var_export(
								array(
									'GET'=>$_GET,
									'POST'=>$data,
									'URL'=>$_SERVER['REQUEST_URI']
								),true
							);
							$this->weObj->text($ddebug)->reply();
							return true;
						}
		    			$data=$this->keyRoute($key,$data['FromUserName']);
		    			break;
		    		case Wechat::MSGTYPE_IMAGE:
						$msg=$this->CI->reply_model->msgData();
						$msg['keyword']=$msg['keyword']?$msg['keyword']:'您的消息已经送达，小编会在工作时间内及时回复，感谢您的关注。';
		    			$data=array('type'=>'text','data'=>$msg['keyword']);
		    			break;
		    		default:
						$msg=$this->CI->reply_model->msgData();
						$msg['keyword']=$msg['keyword']?$msg['keyword']:'您的消息已经送达，小编会在工作时间内及时回复，感谢您的关注。';
		    			$data=array('type'=>'text','data'=>$msg['keyword']);
			    }
				if($data['type']=='list'){
					$this->weObj->news($data['data'])->reply();
				}else{
					$this->weObj->text($data['data'])->reply();
				}
			}else{
				$this->weObj->text('错误的URL，请登录 '.$_SERVER['SERVER_NAME'].' 重新绑定公众号[:S]')->reply();
			}
		}
	}
	//关键字路由
	function keyRoute($key='',$openid=''){
		
		$key=trim($key);
		$flag=preg_match("/([a-z]+)[ ]?([0-9|]+)/i", $key,$match);
		if($flag){
			$fun='remoteServer'.strtoupper($match[1]);
			if(method_exists($this,$fun)){
				$data=$this->$fun(trim($match[2]),$openid);
				return $data;
			}
		}
		//手机号,绑定用户
		// $isPhone=preg_match("/^1[0-9]{10}$/", $key,$match);
		// if($isPhone){
			// $data=$this->bindPhoneOpenid($key,$openid);
			// return $data;
		// }
		//公共平台关键字
		//$data=$this->getKeyForWechat($key);
		//if($data) return $data;
		//站内关键字
		$data=array('type'=>'text','data'=>'您的消息已经送达，小编会在工作时间内及时回复，感谢您的关注。');
		//$data=$this->getKeyForSite($key,$openid);
		return $data;
	}
	//投票系统
	function remoteServerTP($key,$openid){
		return $this->remoteServerPP($key,$openid);
	}
	//新投票系统
	function remoteServerPP($key,$openid){
		$data=array('type'=>'text','data'=>'太困了，投票系统睡着了(～ o ～)~zZ，请你稍后再试');
		//向投票系统发起POST请求
		$this->CI->load->library('open');
		$url=site_url('vote_zh/ifengvote');
		$param=array(
			'postid'=>$key,
			'openid'=>$openid,
			'ip'=>$this->CI->input->ip_address()
		);
		$str=$this->CI->open->http_post($url,$param);
		if($str){
			$info=json_decode($str,true);
			if($info['status']!=0){
				$data['data']=$info['error'];
			}else{
				$data['data']=$info['msg'];
			}
		}
		//构造投票结果
		return $data;
	}
	//搜索站点关键字
	function getKeyForSite($key='',$openid=''){
		$menu=array('?','menu','help','？');
		if(in_array($key, $menu)!==false){
			return array('type'=>'text','data'=>"请直接提交你的问题,小编会尽快答复你\n");
		}elseif(($key+0)>=1 && ($key+0)<=9999){//站点栏目
			return array('type'=>'text','data'=>"请直接提交你的问题,小编会尽快答复你\n");
		}
		//查找站点关键字
		$this->CI->load->module_library('article','articlelib');
		$res=$this->CI->article_model->getArticleForKeyword($key);
		if($res){
			$info=array();
			foreach ($res->result() as $key => $r) {
				array_push($info,array(
			  		'Title'=>$r->title,
			  		'Description'=>$r->title,
			  		'PicUrl'=>base_url().$r->src,
			  		'Url'=>$this->CI->article_model->getUrl($r->id,$r->type,$r->url)
			  	));
			}
			return array('type'=>'list','data'=>$info);
		}
		$msg=$this->CI->reply_model->msgData();
		$msg['keyword']=$msg['keyword']?$msg['keyword']:'您的消息已经送达，小编会在工作时间内及时回复，感谢您的关注。';
		return array('type'=>'text','data'=>$msg['keyword']);
	}
	//搜索公众平台关键字
	function getKeyForWechat($key=''){
		//查找站点关键字
		$this->CI->load->module_library('article','articlelib');
		$res=$this->CI->article_model->getArticleForWXkeyword($key);
		if($res){
			$info=array();
			foreach ($res->result() as $key => $r) {
				array_push($info,array(
			  		'Title'=>$r->subject,
			  		'Description'=>$r->content,
			  		'PicUrl'=>base_url().$r->src,
			  		'Url'=>$r->url
			  	));
			}
			if(count($info)==1){
				if(!$info[0]['PicUrl']){
					return array('type'=>'text','data'=>$info[0]['Description']."\n".$info[0]['Url']);
				}
			}
			return array('type'=>'list','data'=>$info);
		}
		return false;
	}
}