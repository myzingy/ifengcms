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
	//第三方登陆流程
	public function login(){	
		$info=array();
		if(!$this->CI->session->userdata('siteid')){
			parse_str($_SERVER['QUERY_STRING'], $_GET);
			$siteid=$_GET['siteid'];
			$appid=$_GET['appid'];
			$res=$this->CI->open_model->fetch('S','*',null,array('appid'=>$appid,'siteid'=>$siteid));
			if($res->num_rows()<1){
				$username=md5($appid.$siteid);
				$user=array(
					'username'=>$username,
					'password'=>$username,
					'email'=>$username.'@wisheli.com',
					'active'=>1,
					'group'=>1,
					'created'=>date('Y-m-d H:i:s',TIME)
				);
				$this->CI->open_model->insert('U',$user);
				$uid=$this->CI->open_model->db->insert_id();
				$this->CI->open_model->insert('UP',array('user_id'=>$uid));
				$this->CI->open_model->insert('S',array('uid'=>$uid,'siteid'=>$siteid,'appid'=>$appid));
			}
			$this->CI->session->set_userdata(array('zdj_siteid'=>$siteid,'zdj_appid'=>$appid));
		}
		echo '<script type="text/javascript">';
		echo 'top.location.href="'.base_url().'platform/business";';
		echo '</script>';
		exit;
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
	//购买统计接口
	public function noticeByBuy(){
		parse_str($_SERVER['QUERY_STRING'], $_GET);
		$this->CI->load->module_library('buyers','buyers_lib');	
		$buyers['appid']=$_GET['appid']?$_GET['appid']:$this->CI->input->post('appid');
		$buyers['siteid']=$_GET['siteid']?$_GET['siteid']:$this->CI->input->post('siteid');
		$buyers['openid']=$this->CI->input->post('openid');
		//$buyers['price']=$this->CI->input->post('price');
		$buyers['name']=$this->CI->input->post('name');
		$buyers['phone']=$this->CI->input->post('phone');
		//首次添加消费者信息或得到id
		$buyersList['buyid']=$this->CI->buyers_model->updateBuyers($buyers);
		
		$buyers['price']=0;
		$buyers['buynum']=0;
		
		$buyersList['addtime']=TIME;
		$buyersList['ip']=$this->CI->input->post('ip');
		$buyersList['popcode']=$this->CI->input->post('popcode');
		
		$buyersList_arr['price']=$this->CI->input->post('price');
		$buyersList_arr['title']=$this->CI->input->post('title');
		$buyersList_arr['pkid']=$this->CI->input->post('pkid');
		$buyersList_arr['number']=$this->CI->input->post('number');
		if(!is_array($buyersList_arr['pkid'])){
			$buyersList_arr['price']=array($buyersList_arr['price']);
			$buyersList_arr['title']=array($buyersList_arr['title']);
			$buyersList_arr['pkid']=array($buyersList_arr['pkid']);
			$buyersList_arr['number']=array($buyersList_arr['number']);
		}
		foreach($buyersList_arr['pkid'] as $i=>$pkid){
			$buyersList['price']=$buyersList_arr['price'][$i];
			$buyersList['title']=$buyersList_arr['title'][$i];
			$buyersList['pkid']=$pkid;
			$buyersList['number']=$buyersList_arr['number'][$i];
			$this->CI->buyers_model->insert('UL',$buyersList);
			$buyers['price']+=($buyersList['price']*$buyersList['number']);
			$buyers['buynum']+=$buyersList['number'];
		}
		//将购买数据更新到总表
		$this->CI->buyers_model->updateBuyers($buyers);
		
	}
	//获取战斗机唯一标识
	function getFighterID(){
		parse_str($_SERVER['QUERY_STRING'], $_GET);
		$buyers['appid']=$_GET['appid']?$_GET['appid']:$this->CI->input->post('appid');
		$buyers['siteid']=$_GET['siteid']?$_GET['siteid']:$this->CI->input->post('siteid');
		$buyers['openid']=$this->CI->input->post('openid');
		$this->CI->load->module_library('fighter','fighter_lib');
		$info['fighterID']=$this->CI->fighter_lib->getFighterID($buyers['appid'],$buyers['siteid'],$buyers['openid']);
		$this->status($info,0);
		return $info;
	}
	#################################################
	# 金币操作
	#################################################
	function doGold($act='sub'){
		$gold=$this->CI->input->post('gold');
		$figid=$this->CI->input->post('figid');
		$this->CI->load->module_library('fighter','fighter_lib');
		return $this->CI->fighter_model->setFighterGold($act,$figid,$gold);
	}
	//增删金币
	function addGold(){
		return $this->doGold('add');
	}
	function subGold(){
		return $this->doGold('sub');
	}
	//查询金币
	function queryGold(){
		return $this->doGold('query');
	}
	
}
