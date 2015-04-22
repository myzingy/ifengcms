<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class api_lib
{
	var $user=null;	
	var $statusCode=array(
		-1=>'登录超时，请重新登录。'
		,0=>'正常'
		,10000=>'一般性错误'
		,10001=>'接口未定义'
		,10002=>'非法Token'
	);	
	function __construct(){
		// Get CI Instance
		$this->CI = &get_instance();
		//$this->CI->load->library('validation');
		//$this->CI->bep_assets->load_asset_group('FORMS');
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
	public function check(){
		$uid=$this->CI->session->userdata('id');
		$username=$this->CI->session->userdata('username');
		if(!$uid) return false;
		$this->user=array(
			'id'=>$uid,
			'username'=>$username
		);
		return true;
	}
	//获取用户信息
	function getUserInfo(){
		$this->CI->load->module_library('auth','userlib');
		$res=$this->CI->user_model->getUsers(array('users.id'=>$this->user['id']));
		$user= $res->row();
		$info=array(
			'nickname'=>$user->nickname,
			'last_visit'=>$user->last_visit,
			'headimgurl'=>$user->headimgurl,
		);
		$this->status($info, 0);
		return $info;
	}
	//文件上传
	function uploadImage(){
		$config['upload_path'] = 'uploads/'.date('Ymd',TIME);
		@mkdir($config['upload_path']);
		$config['allowed_types'] = 'jpg|jpeg|png';
		$config['max_size'] = '10240';
		$config['max_width']  = '8000';
		$config['max_height']  = '8000';
		//$config['file_name'] = $filename;
		$config['encrypt_name'] = true;
		$this->CI->load->library('upload', $config);
		//wechat 类型为 application/octet-stream
		if($_FILES['file']['type']=='application/octet-stream'){
			$filext=explode(".", $_FILES['file']['name']);
			$filext=$filext[count($filext)-1];
			$ext=array(
				'jpeg'	=>	'image/jpeg',
				'jpg'	=>	'image/jpeg',
				'jpe'	=>	'image/jpeg',
				'png'	=>	'image/png',
			);
			$_FILES['file']['type']=$ext[$filext]?$ext[$filext]:'image/jpeg';
		}
		/*
		return array(
			'status'=>10000,
			'error'=>json_encode($_FILES)
		);
		*/ 
		if (!$this->CI->upload->do_upload('file'))
		{
			$upload = $this->CI->upload->display_errors();
		} 
		else
		{
			$upload = $this->CI->upload->data();
		}
		if(is_array($upload)){
			$imgflag=true;
			$res=array(
				'status'=>0,
				'relurl'=>$config['upload_path'].'/'.$upload['file_name'],
				'src'=>base_url().$config['upload_path'].'/'.$upload['file_name']
			);
		}else{
			$res=array(
				'status'=>10000,
				'error'=>$upload
			);
		}
		return $res;
	}
	#################################################
	# 用户注册登录
	#################################################
	function register(){
		$this->CI->load->module_helper('phonecode','phonecode');	
		$post['data']['username']=$this->CI->input->post('username');
		$post['data']['code']=$this->CI->input->post('code');
		$post['data']['password']=$this->CI->input->post('password');
		if(checkcode($post['data']['username'],$post['data']['code'])){
			$this->CI->load->module_library('noauth','noauth_lib');
			$res=$this->CI->noauth_lib->register();
			if($res['status']!=0){
				$this->status($post,10000,$res['error']);
			}else{
				$post=array(
					'sid'=>$res['sid'],
					'nickname'=>$res['nickname']
				);
				$this->status($post,0);
			}
		}else{
			$this->status($post,10000,"手机验证码验证失败");
		}
		return $post;
	}
	function login(){
		$this->CI->load->module_library('noauth','noauth_lib');
		$post['username']=$this->CI->input->post('username');
		$post['password']=$this->CI->input->post('password');
		$info=$this->CI->noauth_lib->login($post['username'],$post['password']);
		if($info['status']==0){
			$this->CI->load->module_library('oauth','oauth_lib');
			$cookie=$this->CI->oauth_lib->getWechatCookie();
			$info['referer']=$cookie['referer']?$cookie['referer']:site_url();
		}
		return $info;
	}
	function forget($data){
		$this->CI->load->module_helper('phonecode','phonecode');	
		$data['password']=trim($data['password']);
		if($data['password']!=$data['re_password']){
			$this->status($data,10000,"两次密码输入不一致");
			return $data;
		}
		$len=$this->CI->preference->item('min_password_length');
		if(strlen($data['password'])<$len){
			$this->status($data,10000,"密码长度不能少于{$len}位");
			return $data;
		}
		if(checkcode($data['phone'],$data['code'])){
			$flag=$this->CI->noauth_lib->changePasswordPhone($data);
			if($flag){
				$this->status($data,0);
			}else{
				$this->status($data,10000,"此手机未被注册，请重新注册");
			}
		}else{
			$this->status($data,10000,"手机验证码验证失败");
		}
		return $data;
	}
	function modifyUser(){
		$this->CI->load->module_library('noauth','noauth_lib');
		$data=$this->CI->noauth_lib->modifyUser($this->user['id']);
		if($data['status']!=0){
			$this->status($data,10000,$data['error']);
		}
		return $data;
	}
	function sendsms(){
		$this->CI->load->module_helper('phonecode','phonecode');
		$post['type']=$this->CI->input->post('type');
		$post['phone']=$this->CI->input->post('username');
		switch($post['type']){
			case "register":
				$this->CI->load->library('validation');
				$fields['username'] = '手机号';
				$rules['username'] = 'trim|required|valid_phone';
				$this->CI->validation->set_fields($fields);
				$this->CI->validation->set_rules($rules);
				if ( $this->CI->validation->run() === FALSE ){
					$this->status($post,10000,$this->CI->validation->_error_array[0]);
				}
				else
				{
					sendcode($this->CI->input->post('username'),1);
					$this->status($post,0);
				}
			break;
			case "forget":
				$this->CI->load->module_model('auth','user_model');
				$res=$this->CI->user_model->fetch('Users','id',null,array('username'=>$data['phone']));
				if($res->num_rows()!=1){
					$this->status($post,10000,"此手机未被注册，请重新注册");
				}else{
					sendcode($post['phone'],2);
					$this->status($post,0);
				}
				
			break;
		}
		
		return $post;
	}
	//上传头像
	function uploadHeadImage(){
		$info=$this->uploadImage();
		if($info['status']==0){
			$this->CI->load->module_model('auth','user_model');
			$this->CI->user_model->update('UserProfiles',array('headimgurl'=>$info['src']),array('user_id'=>$this->user['id']));
			//缩略图
			$config['image_library'] = 'gd2';
			$config['source_image'] = $info['relurl'];
			$config['dynamic_output'] = FALSE;
			$config['quality'] = '80%';
			$config['new_image'] = $info['relurl'].'_avatar.jpg';
			$config['width'] = 120;
			$config['height'] = 120;
			$config['create_thumb'] = false;
			$config['thumb_marker'] = '_thumb';
			$config['maintain_ratio'] = TRUE;
			$config['master_dim'] = 'auto';//auto, width, height 指定主轴线 
			$this->CI->load->library('image_lib', $config);
			$this->CI->image_lib->resize();
			$info['src'].='_avatar.jpg';
		}
		return $info;
	}
	//显示图像
	function avatar($uid=0){
		$uid=$uid?$uid:$this->user['id'];
		if($uid){
			$this->CI->load->module_model('auth','user_model');
			$res=$this->CI->user_model->fetch('UserProfiles','headimgurl',null,array('user_id'=>$uid));
			if($res->num_rows()==1){
				$avatar=$res->row()->headimgurl;
				header('Location:'.$avatar.'_avatar.jpg');
				exit;
			}
		}
		header('Location:'.base_url().'mnew/data/userdefault.jpg');
		exit;
	}
	#################################################
	# 交互行为
	#################################################
	//点赞
	function applaud(){
		$this->CI->load->module_library('article','articlelib');
		$flag=$this->CI->articlelib->article_push('AA',$this->user['id']);
		if($flag){
			$this->status($info, 0);
		}else{
			$this->status($info, 10000,'已经点过赞了！');
		}
		return $info;
	}
	//收藏
	function favorites(){
		$this->CI->load->module_library('article','articlelib');
		$flag=$this->CI->articlelib->article_push('AF',$this->user['id']);
		if($flag){
			$this->status($info, 0);
		}else{
			$this->status($info, 10000,'已经收藏了！');
		}
		return $info;
	}
	//取消收藏
	function cancelFavorites($aid){
		$this->CI->load->module_library('article','articlelib');
		$this->CI->article_model->delete('AF',array('uid'=>$this->user['id'],'aid'=>$aid));
		$this->status($info, 0);
		return $info;
	}
	//评论
	function comment(){
		$this->CI->load->module_library('comment','comment_lib');
		$flag=$this->CI->comment_lib->add_comment($this->user['id']);
		if($flag){
			$this->status($info, 0);
		}else{
			$this->status($info, 10000,'你慢点啊，奴家受不了！');
		}
		return $info;
	}
	#################################################
	# 用户文章系统
	#################################################
	//发布
	function release(){
		$this->CI->load->module_library('article','articlelib');
		$info=$this->CI->articlelib->release($this->user['id']);
		return $info;
	}
	//删除发布
	function deleteRelease($aid){
		$this->CI->load->module_library('article','articlelib');
		$where=array('uid'=>$this->user['id'],'id'=>$aid);
		$res=$this->CI->article_model->fetch('A','status',null,$where);
		if($res->num_rows()==1){
			$status=$res->row()->status;
			if($status==0){
				$this->status($info, 10000,'已通过审核，无法删除！');
			}else{
				$this->CI->article_model->delete('A',$where);
				$this->status($info, 0);
			}
		}else{
			$this->status($info, 10000,'没有找到您的大作！');
		}
		return $info;
	}
	//我发布的新闻
	function getMyNews($limit=5,$page=0){
		$limit=array('offset'=>$page,'limit'=>$limit);
		$this->CI->load->module_library('article','articlelib');
		$this->CI->load->module_helper('article','article');
		$where=array('A.uid'=>$this->user['id']);
		$res=$this->CI->article_model->getArticleList($where,$limit,false);
		if($res->num_rows()>0){
			$info['status']=0;
			$info['data']=$res->result();
			foreach ($info['data'] as $i => $r) {
				$info['data'][$i]->src=base_url().$r->src;
				$info['data'][$i]->url=getArticleUrl($r->id,$r->type,$r->url);
				
			}
		}else{
			$info['status']=10000;
		}
		return $info;
	}
	#################################################
	# 用户点评
	#################################################
	//我点评的&点评我的
	function replyData($classify=1,$limit=5,$page=0){
		$this->CI->load->module_library('comment','comment_lib');
		$this->CI->load->module_helper('article','article');
		$limit=array('offset'=>$page,'limit'=>$limit);
		if($classify==2){
			$where=" C.aid in (select T_A.id from {$this->CI->comment_model->_TABLES['A']} as T_A where T_A.uid={$this->user['id']})";
		}else{
			$where=array('C.uid'=>$this->user['id']);	
		}
		$res=$this->CI->comment_model->getCommentListForUser($where,$limit,false);
		if($res->num_rows()>0){
			$info['status']=0;
			$info['data']=$res->result();
			foreach ($info['data'] as $i => $r) {
				$info['data'][$i]->addtime=date('Y-m-d H:i',$r->addtime);
				$info['data'][$i]->url=getArticleUrl($r->id,$r->type,$r->url);
			}
		}else{
			$info['status']=10000;
		}
		return $info;
	}
	#################################################
	# 用户收藏
	#################################################
	function getFavorites($limit=5,$page=0){
		$limit=array('offset'=>$page,'limit'=>$limit);
		$this->CI->load->module_library('article','articlelib');
		$where=" A.id in (select aid from {$this->CI->article_model->_TABLES['AF']} where uid={$this->user['id']}) ";
		$res=$this->CI->article_model->getArticleList($where,$limit,false);
		$this->CI->load->module_helper('article','article');
		if($res->num_rows()>0){
			$info['status']=0;
			$info['data']=$res->result();
			foreach ($info['data'] as $i => $r) {
				$info['data'][$i]->src=base_url().$r->src;
				$info['data'][$i]->url=getArticleUrl($r->id,$r->type,$r->url);
			}
		}else{
			$info['status']=10000;
		}
		return $info;
	}
	#################################################
	# 统计代码
	#################################################
	function totalcode($pkid,$siteid=1){
		$this->CI->load->module_library('open','open_lib');
		die($this->CI->open_lib->create_total_code(array(
			'pkid'=>$pkid,
			'siteid'=>$siteid
		)));
	}
	function totalcodeActivity($pkid,$siteid=1){
		$this->CI->load->module_library('open','open_lib');
		die($this->CI->open_lib->create_total_code(array(
			//'pkid'=>$pkid,
			'siteid'=>$siteid,
			'type'=>'activity'
		)));
	}
	#################################################
	# 抽奖
	#################################################
	function dodraw($id){
		$this->CI->load->module_library('draw','draw_lib');
		return $this->CI->draw_lib->dodraw($id);
	}
	function updateDrawUser($id){
		$this->CI->load->module_library('draw','draw_lib');
		return $this->CI->draw_lib->updateUserInfo($id);
	}
	function getLucker($id){
		$this->CI->load->module_library('draw','draw_lib');
		return $this->CI->draw_lib->getLucker($id);
	}
	function checkDrawUser($id){
		$this->CI->load->module_library('draw','draw_lib');
		return $this->CI->draw_lib->check($id);
	}
	#################################################
	# 答题
	#################################################
	#api/act/getDayQuestions/20/oWQRqs1w-Rabo73VPQ3jCFf3R_gk?callback=jquery12312312&1
	#api/act/quesUser/20/oWQRqs1w-Rabo73VPQ3jCFf3R_gk?callback=jquery12312312&v60d0458ac6eb=vking&v9f9d36327d96=12312312312
	#api/act/doques/20/oWQRqs1w-Rabo73VPQ3jCFf3R_gk?callback=jquery12312312&v4439727b7394=D%204444&v96c3c11f1460=A%2011111111&v54d1449a308e=123
	function getDayQuestions($id='',$openid=''){
		$this->CI->load->module_library('fields','fields_lib');
		return $this->CI->fields_lib->getDayQuestions($id,$openid);
	}
	function quesUser($id,$openid=''){
		$this->CI->load->module_library('fields','fields_lib');
		return $this->CI->fields_lib->quesUser($id,$openid);
	}
	function doques($id,$openid=''){
		$this->CI->load->module_library('fields','fields_lib');
		return $this->CI->fields_lib->doques($id,$openid);
	}
	function getSourceList($id,$limit_row=10){
		$this->CI->load->module_library('fields','fields_lib');
		$row=$this->CI->fields_lib->getFieldsData($id);
		$info=array('status'=>10000);
		if($row['status']==0){
			$field=$row['data'];
			$table=$this->CI->fields_model->fileds_table_prefix.$field->tab_name;
			$res=$this->CI->fields_model->getFieldsDataList($table,NULL, array('limit' => $limit_row, 'offset' => 0),false,$order='source desc');
			if($res->num_rows()>0){
				$data=array();
				foreach ($res->result() as $r) {
					array_push($data,array(
						'name'=>$r->v60d0458ac6eb,
						'phone'=>$r->v9f9d36327d96,
						'source'=>$r->source
					));
				}
				$info=array(
					'status'=>0,
					'data'=>$data
				);
			}
		}
		return $info;
	}
	#################################################
	# 获取用户授权及授权跳转
	#################################################
	function getWechatAuth(){
		$this->CI->load->module_library('oauth','oauth_lib');
		$cookie=$this->CI->oauth_lib->getWechatCookie();
		$url=$this->CI->input->get('redirect_uri');
		if(!$cookie['openid']){
			$state=base64_encode($url);
			$url='http://121.199.26.107:9999/auth.php?redirect_uri='.base64_encode(site_url('api/act/setWechatAuth')).'&scope=snsapi_base&state='.$state;
		}else{
			if(strpos($url, '?')===false){
				$url.='?';
			}
			$url.='&openid='.$cookie['openid'];
		}
		redirect($url);
		exit;
	}
	function setWechatAuth(){
		$this->CI->load->module_library('oauth','oauth_lib');
		$this->CI->oauth_lib->setWechatCookie($_GET);
		$url=urldecode(base64_decode($_GET['state']));
		if(strpos($url, '?')===false){
			$url.='?';
		}
		$url.='&openid='.$_GET['openid'];
		redirect($url);
		exit;
	}
	
	function getWechatJS(){
		$this->CI->load->module_library('oauth','oauth_lib');
		$cookie=$this->CI->oauth_lib->getWechatCookie();
		$info['openid']=$cookie['openid'];
		//jsapi_ticket
		$this->CI->load->library('wechat');
		$info['jsapi_ticket']=$this->CI->wechat->getJsTicket();
		//getJsSign
		$info['jsapi_sign']=$this->CI->wechat->getJsSign($_SERVER['HTTP_REFERER']);
		return $info;
	}
	##################################################
	# 网页端投票系统
	##################################################
	function ifengvote2web(){
		$this->CI->load->module_library('vote','vote_lib');
		return $this->CI->vote_lib->ifengvote2web();
	}
}