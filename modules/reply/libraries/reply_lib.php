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
			'token'=>'snifeng',
			'appid'=>'wxeac5ee619fe202cb',
			'appsecret'=>'41c58f96257c00962b9f2ac2bc0e36a7'
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
							$this->CI->reply_model->delUnUser($data['FromUserName']);
							$msg=$this->CI->reply_model->msgData();
							$msg['subscribe']=$msg['subscribe']?$msg['subscribe']:"欢迎您关注凤凰陕西！\n加入我们，请点击：\nhttp://sn.ifeng.com/shanxizhuanti/jobs/";
							$data=array('type'=>'text','data'=>$msg['subscribe']);
							break;
						}
						if($data['Event']=='unsubscribe'){
							$this->CI->reply_model->addUnUser($data['FromUserName']);
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
						$msg['keyword']=$msg['keyword']?$msg['keyword']:'您的消息已经送达，小编会在工作时间内及时回复，凤凰陕西感谢您的关注。';
		    			$data=array('type'=>'text','data'=>$msg['keyword']);
		    			break;
		    		default:
						$msg=$this->CI->reply_model->msgData();
						$msg['keyword']=$msg['keyword']?$msg['keyword']:'您的消息已经送达，小编会在工作时间内及时回复，凤凰陕西感谢您的关注。';
		    			$data=array('type'=>'text','data'=>$msg['keyword']);
			    }
				if($data['type']=='list'){
					$this->weObj->news($data['data'])->reply();
				}elseif($data['type']=='voice'){
					$this->weObj->voice($data['MediaId'])->reply();
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
		/*
		if($key=='郭晓冬' || $key=='娄烨'){
			$fun='voicePlay';
			if(method_exists($this,$fun)){
				$data=$this->$fun($key,$openid);
				if($data) return $data;
			}
		}
		*/
		$flag=preg_match("/([a-z]+)[ ]?([0-9|]+)/i", $key,$match);
		if($flag){
			$fun='remoteServer'.strtoupper($match[1]);
			if(method_exists($this,$fun)){
				$data=$this->$fun(trim($match[2]),$openid);
				return $data;
			}
		}
		//手机号,查询中奖信息
		$isPhone=preg_match("/^1[0-9]{10}$/", $key,$match);
		if($isPhone){
			$data=$this->getDrawLucker($key);
			return $data;
		}
		//公共平台关键字
		$data=$this->getKeyForWechat($key);
		if($data) return $data;
		//站内关键字
		$data=$this->getKeyForSite($key,$openid);
		return $data;
	}
	//audioPlay
	function voicePlay($name,$openid){
		$sucai=array(
			'郭晓冬'=>'201963242',
			'娄烨'=>'201963223',
		);
		if($sucai[$name]){
			$data=array('type'=>'voice','MediaId'=>$sucai[$name]);
			return $data;
		}
		return false;
	}
	//投票系统
	function remoteServerTP($key,$openid){
		$data=array('type'=>'text','data'=>'太困了，凤凰陕西投票系统睡着了(～ o ～)~zZ，请你稍后再试');
		//向投票系统发起POST请求
		$this->CI->load->library('open');
		if(strstr($_SERVER['HTTP_HOST'],'192.168')){
			$url="http://127.0.0.1/vote/";	
		}else{
			$url="http://121.199.26.107:8083/";	
		}
		$url.="wp-admin/admin-ajax.php?action=ifengvote";
		$param=array(
			'postid'=>$key,
			'openid'=>$openid,
			'ip'=>$this->CI->input->ip_address()
		);
		$str=$this->CI->open->http_post($url,$param);
		//print $str;
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
	//新投票系统
	function remoteServerPP($key,$openid){
		$data=array('type'=>'text','data'=>'太困了，凤凰陕西投票系统睡着了(～ o ～)~zZ，请你稍后再试');
		//向投票系统发起POST请求
		$this->CI->load->library('open');
		$url=site_url('vote/ifengvote');
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
			return array('type'=>'text','data'=>"请直接提交你的问题,小编会尽快答复你\n".anchor("/",'【凤凰陕西】'));
		}elseif(($key+0)>=1 && ($key+0)<=9999){//站点栏目
			return array('type'=>'text','data'=>"请直接提交你的问题,小编会尽快答复你\n".anchor("/",'【凤凰陕西】'));
		}
		//查找站点关键字
		$this->CI->load->module_library('article','articlelib');
		$res=$this->CI->article_model->getArticleForKeyword($key);
		$row=$res?$res->num_rows():0;
		if($row>1){
			if($row==1){
				$r=$res->row();
				if(!$r->src){
					return array('type'=>'text','data'=>"{$r->title}\n{$r->subject}\n<a href='{$r->url}'>点击查看</a>");
				}
			}
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
		$msg['keyword']=$msg['keyword']?$msg['keyword']:'您的消息已经送达，小编会在工作时间内及时回复，凤凰陕西感谢您的关注。';
		return array('type'=>'text','data'=>$msg['keyword']);
	}
	//搜索公众平台关键字
	function getKeyForWechat($key=''){
		/*
		$wxkeys=array(
			'车宝宝 金象 金象 网络 模特 车模 香车 美女 报名',
			'ALS als 海尔 海尔免清洗 免清洗 洗衣机 海尔洗衣机',
			'新闻 【新闻】 召集令 【召集令】 新闻召集令 【新闻召集令】 有奖 【有奖】 奖品 【奖品】',
		);
		$wxkeysdata=array(
			array('type'=>'text','data'=>'点击网址报名：http://hd.wisheli.com/signup/cbb/'),
			array('type'=>'list','data'=>array(array(
			  		'Title'=>'【爱心接力】无水“冰桶挑战” 凤凰陕西携手海尔西安为ALS募捐',
			  		'Description'=>'凤凰陕西携手海尔西安在8月30日—31日，将在西安国美电器北二环店门口摆开全民慈善擂台，发起“老陕冰桶挑战”为陕西的渐冻人互助协会募捐活动。',
			  		'PicUrl'=>'http://mmbiz.qpic.cn/mmbiz/uy58DnvWGPkQRGclec2f101oHjGc7VGUjibR1WoDAEC0QqY7LvURWwoWXvicyYvRvPt3XcAZ3AlAl13WQmrgia7Wg/640',
			  		'Url'=>'http://mp.weixin.qq.com/s?__biz=MjM5NDY2NTA2NA==&mid=200802468&idx=1&sn=d27480fde702e3147231be69ad9d3306'
			  	))),
			array('type'=>'list','data'=>array(array(
			  		'Title'=>'凤凰陕西微信有奖新闻召集令：我们的周末被你承包了！',
			  		'Description'=>'“凤凰陕西”广发微信有奖新闻召集令，小编诚邀您与大家共同分享，每个周末，微信新闻你来承包~阅读量前五的推荐者更有惊喜大奖相送！',
			  		'PicUrl'=>'http://mmbiz.qpic.cn/mmbiz/uy58DnvWGPlzbEE8YTr8WoE0kXoNkhuzANj50ueJW14JBBj0JrxXwHUyZysrcbxyNTVs5G0bF8YibhfSrYJzwWQ/640',
			  		'Url'=>'http://mp.weixin.qq.com/s?__biz=MjM5NDY2NTA2NA==&mid=200737265&idx=1&sn=86d7e4fde1d73526e897a2836d4b7b7d'
			  	))),
		);
		for($i=0;$i<count($wxkeys);$i++){
			if(strstr($wxkeys[$i],$key)!==false){
				return $wxkeysdata[$i];
			}
		}*/
		//查找站点关键字
		$this->CI->load->module_library('article','articlelib');
		$res=$this->CI->article_model->getArticleForWXkeyword($key);
		$row=$res?$res->num_rows():0;
		
		if($row>0){
			if($row==1){
				$r=$res->row();
				if(!$r->src){
					return array('type'=>'text','data'=>"{$r->subject}\n{$r->content}\n<a href='{$r->url}'>点击查看</a>");
				}
			}
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
	function getDrawLucker($phone){
		$this->CI->load->module_library('draw','draw_lib');
		$limit=array('limit' => 5, 'offset' => '');
		$where=array('phone'=>$phone,'pid > '=>0);
		$res=$this->CI->draw_model->getLuckerForPhone($where, $limit);
		$info=array('type'=>'text','data'=>'没有任何中奖信息！');
		if($res->num_rows()>0){
			$text[]="中奖信息如下：";
			foreach ($res->result() as $i=>$row) {
				$p_info=$row->info?$row->info:'暂未公布';
				$p_info=preg_replace("/[\n]/","\n\t",$p_info);
				$text[]=($i+1).')'.($row->name?$row->name:$row->pname)
					."\n\t奖品状态：".($row->status==0?'待领取':'已领取')
					."\n\t领奖方式：".$p_info;
			}
			$info['data']=implode("\n", $text);
		}
		return $info;
	}
}