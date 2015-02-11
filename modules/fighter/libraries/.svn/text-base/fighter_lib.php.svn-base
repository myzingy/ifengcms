<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class fighter_lib
{
	function fighter_lib(){
		// Get CI Instance
		$this->CI = &get_instance();
		$this->CI->load->helper('form');
		$this->CI->load->library('validation');
		$this->CI->load->helper('cookie');
		$this->CI->load->model('fighter_model');
	}
	function getFighterID($appid,$siteid,$openid){
		$key=$this->CI->fighter_model->insertFighterID($appid,$siteid,$openid);
		return $key;
	}
	function setFighterCookie($data=array()){
		$old=$this->getFighterCookie();
		foreach($data as $key=>$value){
			$old[$key]=$value;
		}
		//创建记录微信用户
		if($old['openid']){
			$old['fighterID']=$this->getFighterID($old['appid'],$old['siteid'],$old['openid']);
			$res=$this->CI->fighter_model->fetch('WU','*',null,array('openid'=>$old['openid']));
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
				$this->CI->fighter_model->update('WU',$weuser,array('openid'=>$old['openid']));
			}else{
				$weuser['addtime']=TIME;
				$this->CI->fighter_model->insert('WU',$weuser);
			}
		}
		set_cookie('FIG_DATA',json_encode($old),86400*360);//存储一年
		//创建记录站点用户
		if($old['openid']){
			$res=$this->CI->fighter_model->fetch('UF','*',null,array('openid'=>$old['openid']));
			if($res->num_rows()>0){
				$row=$res->row();
				$uid=$row->user_id;
			}else{
				//创建用户
				$username=md5($weuser['openid']);
				$user=array(
					'username'=>$username,
					'password'=>$username,
					'email'=>$username.'@cms.com',
					'active'=>1,
					'group'=>1,
					'created'=>date('Y-m-d H:i:s',TIME)
				);
				$this->CI->open_model->insert('U',$user);
				$uid=$this->CI->open_model->db->insert_id();
				$this->CI->open_model->insert('UP',array('user_id'=>$uid,'openid'=>$weuser['openid']));
			}
			//用户登陆
			$this->CI->load->module_library('auth','userlib');
			$this->CI->userlib->set_userlogin($uid);
		}
	}
	function getFighterCookie(){
		$info=array();
		$data=get_cookie('FIG_DATA');
		if($data){
			$info=json_decode($data,true);
		}
		return $info;
	}
	function getFighterStatus(){
		$cookie=$this->getFighterCookie();
		if($cookie['fighterID']){
			$res=$this->CI->fighter_model->fetch('FIG','*',null,array('id'=>$cookie['fighterID']));
			if($res->num_rows()<1){
				return -1;
			}else{
				$row=$res->row();
				return $row->status;
			}
		}
		return -1;
	}
	//
	function form($container){
		$fields['fields'] = "fields";
		
		$rules['fieldsfields'] = 'trim|required|min_length[8]|max_length[32]';
		
		$this->CI->validation->set_fields($fields);
		$this->CI->validation->set_rules($rules);
		if ( $this->CI->validation->run() === FALSE )
		{
			return array('status'=>1,'error'=>$this->CI->validation->_error_array[0]);
		}else{
			// Submit form
			return $this->_from();
		}
	}
	function _from(){
		$data['fields']=$this->CI->input->post('fields');
		
		return array('status'=>0);
	}
}