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
			'token'=>'wisheliqrcode',
			'appid'=>'wxee4d4d36823dab24',
			'appsecret'=>'daacd95bf523685d376df14424e5f6ef'
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
							$msg['subscribe']=$msg['subscribe']?$msg['subscribe']:"欢迎您关注移动政企！";
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
						$msg['keyword']=$msg['keyword']?$msg['keyword']:'您的消息已经送达，小编会在工作时间内及时回复，移动政企感谢您的关注。';
		    			$data=array('type'=>'text','data'=>$msg['keyword']);
		    			break;
		    		default:
						$msg=$this->CI->reply_model->msgData();
						$msg['keyword']=$msg['keyword']?$msg['keyword']:'您的消息已经送达，小编会在工作时间内及时回复，移动政企感谢您的关注。';
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
		$isPhone=preg_match("/^1[0-9]{10}$/", $key,$match);
		if($isPhone){
			$data=$this->bindPhoneOpenid($key,$openid);
			return $data;
		}
		//公共平台关键字
		//$data=$this->getKeyForWechat($key);
		//if($data) return $data;
		//站内关键字
		$data=$this->getKeyForSite($key,$openid);
		return $data;
	}
	//投票系统
	function remoteServerTP($key,$openid){
		$data=array('type'=>'text','data'=>'太困了，投票系统睡着了(～ o ～)~zZ，请你稍后再试');
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
		$data=array('type'=>'text','data'=>'太困了，投票系统睡着了(～ o ～)~zZ，请你稍后再试');
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
			return array('type'=>'text','data'=>"请直接提交你的问题,小编会尽快答复你\n".anchor("/",'【移动政企】'));
		}elseif(($key+0)>=1 && ($key+0)<=9999){//站点栏目
			return array('type'=>'text','data'=>"请直接提交你的问题,小编会尽快答复你\n".anchor("/",'【移动政企】'));
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
		$msg['keyword']=$msg['keyword']?$msg['keyword']:'您的消息已经送达，小编会在工作时间内及时回复，移动政企感谢您的关注。';
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
	
	function bindPhoneOpenid($phone,$openid){
		$this->CI->load->module_library('fields','fields_lib');
		$table="bb2379602f9fb6e6485e14b9ae16434a";
		$tabname=$this->CI->fields_model->fileds_table_prefix.$table;
		$data=array(
			'openid'=>$openid,
			'v8098e2b4e82c'=>$phone,
		);
		$res=$this->CI->fields_model->getFieldsDataList($tabname,array('v8098e2b4e82c'=>$phone),array('limit' => 1));
		$info=$this->weObj->getUserInfo($openid);
		if($res->num_rows()>0){
			//no openid
			$this->upOpenidForPhone($openid,$res->row(),$info['nickname']);
			return array('type'=>'text','data'=>'手机号已经绑定，不能重复绑定！！！');
		}else{
			$res=$this->CI->fields_model->getFieldsDataList($tabname,array('openid'=>$openid),array('limit' => 1));
			if($res->num_rows()>0){
				return array('type'=>'text','data'=>'此微信号已经绑定手机号，不能重复绑定！！！');
			}else{
				$data['addtime']=TIME;
				$data['ip']=$phone;
				$data['昵称']=$info['nickname'];
				$this->CI->fields_model->insert_fields_tabdata($table,$data);
			}
		}
		return array('type'=>'text','data'=>'成功绑定手机号');
	}
	function upOpenidForPhone($openid,$row,$nickname=''){
		if($row->status==2){
			$this->CI->load->module_library('fields','fields_lib');
			$table="bb2379602f9fb6e6485e14b9ae16434a";
			$tabname=$this->CI->fields_model->fileds_table_prefix.$table;
			$db=$this->CI->fields_model->db;
			$db->where(array(
				'v8098e2b4e82c'=>$row->v8098e2b4e82c,
			));
			$data=array('status'=>0,'openid'=>$openid);
			if($nickname){
				$data['昵称']=$nickname;
			}
			$db->update($tabname,$data);
		}
	}
	function upBindPhoneInfo($openid,$subing=false){
		$this->CI->load->module_library('fields','fields_lib');
		$table="bb2379602f9fb6e6485e14b9ae16434a";
		$tabname=$this->CI->fields_model->fileds_table_prefix.$table;
		
		$db=$this->CI->fields_model->db;
		$db->where(array(
			'openid'=>$openid,
		));
		if($subing){
			$db->update($tabname,array('fromaddr'=>''));
		}else{
			$db->update($tabname,array('fromaddr'=>'<font color="red">已取消关注</font>'));
		}
		
	}
	function getNick($id=0){
		$this->CI->load->module_library('fields','fields_lib');
		$table="bb2379602f9fb6e6485e14b9ae16434a";
		$tabname=$this->CI->fields_model->fileds_table_prefix.$table;
		
		$where=array('id'=>$id);
		$res=$this->CI->fields_model->getFieldsDataList($tabname,$where,array('limit' => 1));
		if($res->num_rows()>0){
			$row=$res->row();
			if(strlen($row->openid)==28){
				$info=$this->weObj->getUserInfo($row->openid);
				echo $row->openid.'::'.$info['nickname'];
			}else{
$str=<<<END
ㄗs 绝恋 oО
　　Xxづ蒾夨ㄋ
　　墙角、ˉ落泪
　　殇づ如此_惟美
　　惟美°づ
　　﹎ゞ很√想迩
　　緈幅丶谁能给
　　ˇ、遥不可及
　　泪、很累
　　﹎ゞ恬静oО
　　-”緈幅*
　　Ьū?爱泪.
　　寂静 sH〃
　　失忆d
　　擦抹不掉つ
　　騇钚得、放弃
　　.蒾夨.°
　　﹎ゞˋ纞爱の
　　-訫。[’殇]
　　ゝ
　　╰╮
　　∨蕝式、
　　请你↙吻我
　　∈ǒ～蕝‰恋↓
　　?X·
　　(_恋乄梦め
　　ゃ^莈メ蔠嚸^
　　暧昧の、签约
　　回忆,划下伤口
　　落花、笑意
　　╰つ迩给的爱
　　对沵、只有抱歉
　　╮致命恴、爱
　　掩饰的心伤
　　何去何从、
　　曾经／那麽美
　　莪、（寂寞）
　　无泪/db死鉮
　　╯仅冇旳姿态
　　涐许沵一生%
　　緈福，好远
　　那①勿、佷深╮
　　洎虐╯de誐
　　`放弃,、
　　何须感伤离别
　　伤、徝嘚欣赏
　　_背叛﹏自己、
　　心颤抖的声音
　　莣卟ㄋの情
　　悲伤式、沉默
　　对抗、心痛
　　诠释、n1的爱
　　慢嗨式！恋爱
　　青春起点
　　●ˊ不谈感情。
　　微微微、笑。
　　蕜哀鉽、邀请
　　寂寞。无从宣泄
　　ヤ鬺ベの啠莂
　　メo继续﹎℡
　　◇゛﹎ˉ补懂
　　一直、手牵手
　　街角旳、安静
　　街角、旳缠棉
　　*?▓誮メ
　　唱、单身情歌
　　：颏蓇铭芯°
　　、寂寞才说爱
　　没呼吸旳男人
　　◇陌-、 蕗ミ
　　僐嬑de巟訁♂
　　灵魂的ㄨ缠锦
　　__①苆↘紸锭
　　蕶喥↘ 芣结栤
　　请、把爱忘记
　　预约、幸福
　　?起赱丅紶
　　う封、爱
　　~♀破镓囡oо
　　ご祗为伱鈊谇
　　君灬莳鞝の
　　-〔丄档佽↑
　　焦点‘集中。
　　◇゛﹎ˉ补懂
　　ˇ.怀念緈褔
　　啭身你巳不茬
　　錵訫╬dē藕
　　注顁、鉃詓伱
　　徊忆、只褦忆
　　赤裸の情欲
　　嗳就一嗰字,
　　仅剰の骄傲。
　　鈊╅玍。疼
　　_‖劳资的伤
　　√綪艮浅?
　　戏、如此完美
　　感觉太空虚、
　　所冇旳,悲剧
　　厡唻、莪罘痛
　　后来旳、熟悉
　　烺嫚的、以为
　　╭`深步╯调.
　　诠释、内些情
　　夜、哀愁。
　　哦シ殳亽疼
　　相濡以沫、
青雉与你
写了情书
把酒沾唇
声哑
酒倌浪人
稚屿
他在笑
把酒笔落
无权拥你
第四人称
别说你在
词抒笙歌
只是爱你
酸涩皱眉
是谁眉眼
秉烛
别用流言了解我、
别以为
烫心爱人
了我情深
你我
共赴远方
时过境迁
枯等几年
余光赠你
念你知否
我多稀罕
予你最初
百夜情歌
不谈过往
讲给风听
重拾旧梦
执玖
笑你深情
怎舍，
穷胸嫉恶
手心余温
心字难书
青栀。
酒浓巷深
恰好心动
软妹裙边
怪疯患者
再遇当年
孤久则安
眉眼未改
逗比之路
回不过去
念我堇年
自若凌虐
任凭风吹
长发与眸
长街听风
烈酒灼人
北沓
无辜泪水
好久不见
等你赴约
几分醉意
尘世变迁
略斟杯酒
眼角笑意
沧海与光
苟延残喘
你隔岸观火不知对面有我
深歌浅醉
海港故人
派大星
无人久留
风吹伤口
带歌离别
Enteral丶不毁
孤独成性
难挽旧情
别将过去抱的太紧
初安故人
孤其一身
配谈
酒浓巷深
散场谎话
老街
情话烫舌
与世隔绝
♛无情便是王
梦会很长
青衫如故
街灯
葬于我心
街上流浪
别再提旧事
醉看今朝
挑拾爱人
笑当初
脾气不对劲
听折新戏
两眉聚皱
清泉煮茶
人难留
如花堪折
旧事酒浓
八巷老友
七街老酒
知你冷暖
懂你悲欢
休戚与共
比邻而居
失她醉心
失他痛心
拿着爱疯去装逼
穿着耐克闯天下
旧城爱人
故港恋人
情不知所起
一往而情深
不甘朋友
不敢恋人
沙哑情话
匿名情书
如你默认
生死枯等
一时心动
一世情伤
多年以后
很久以前
初夏浅夜
浅夜暖伤
回时愁
来时路
总有后来
没有未来
阳光与她
故事与他
爱到浓时
情到深处
推杯换盏
人走茶凉
何为佳人
所谓伊人
只是故人
执念旧人
捂风挽笑
许你初忠
久居你心
默守你情
情难自抑
满卷相思
你说富贵荣华
许我十里桃花
不复相见
与君长诀
悲剧重生
四面楚歌
归于心
一起疯一起闹
鱼骨痣
他很好
反复情话
鱼骨痣
他很好
笑看未来
假装。
曾知晓
矜持
故事很长！
故事细腻丶
只剩离歌
与共
梦到梦不醒来的梦
强颜欢笑
沉默在梦里
冰释前嫌
初梦依旧
薄情*
北辰南栀
得未曾有
十年九不遇
冷淡*
细数惭愧
游人尽离
ヾ小温柔
保留
大众女神.
心痣
似处衬你
离人怎挽
也是够了
远方
故事太巧、
清风
故人未挽
说好不再见
枳虞初梦☀
心还是热的
低等动物
你是曾经
仅我一人
阳光少年
自渡劫
惯性依赖
我与诀别
无病呻吟
阳光刺眼
别离开
胖若两人
不屑一顾
似水年华
孤者何惧
背着书包闯天下
墨染空城
〆灬拼命依赖
故意失忆گق
你的她很会装
人潮拥挤我一脚踹死你
你放手就别怪我先走
大众女神经
也是矫情
何必谈感情
病友
喜新莣旧
期盼与你
END;

		$str_arr=preg_split('/[\n\r]+/', $str);
		$index=rand(0,count($str_arr));
		$info['nickname']=preg_replace('/[ \t　]+/','',$str_arr[$index]);
		echo $row->openid;
		console($id,$info['nickname']);
			}
			if($info['nickname']){
				$db=$this->CI->fields_model->db;
				$db->where($where);
				$db->update($tabname,array('昵称'=>$info['nickname']));
			}
			
		}
		die('<meta http-equiv="refresh" content="2;url='.site_url('reply_yidong/getNick/'.($id-1)).'">');
	}
}