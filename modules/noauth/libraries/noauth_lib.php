<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * BackendPro
 *
 * A website backend system for developers for PHP 4.3.2 or newer
 *
 * @package         BackendPro
 * @author          Adam Price
 * @copyright       Copyright (c) 2008
 * @license         http://www.gnu.org/licenses/lgpl.html
 * @link            http://www.kaydoo.co.uk/projects/backendpro
 * @filesource
 */

// ---------------------------------------------------------------------------

/**
 * Userlib
 *
 * User authentication library used by BackendPro. Permits
 * protecting controllers/methods from un-authorized access.
 *
 * @package			BackendPro
 * @subpackage		Libraries
 */
class Noauth_lib
{
	function __construct()
	{
		// Get CI Instance
		$this->CI = &get_instance();

		$this->CI->load->module_model('auth','user_model');
		$this->CI->load->module_library('auth','userlib');
		$this->CI->load->library('validation');
		log_message('debug','BackendPro : Userlib class loaded');	
	}
	function login($username,$passwrod){
		$passwrod=$this->CI->userlib->encode_password($passwrod);
		$result = $this->CI->user_model->validateLogin($username,$passwrod);
		if ( $result['valid'] )
		{
			// We we have a valid user
			$user = $result['query']->row();

			// Check if the users account hasn't been activated yet
			if ( $user->active == 0 )
			{
				// NEEDS ACTIVATION
				return array('status'=>10000,'error'=>$this->CI->lang->line('userlib_account_unactivated'));
			}
			// Everything is OK
			// Save details to session
			//@TODO: This dosn't seem very safe having the login code totaly exposed
			
			$this->CI->userlib->set_userlogin($user->id);
			//$sid=md5($user->id.(TIME-TIME%(60*5)));//5分钟变更一次sid
			//$this->CI->user_model->update('Users',array('activation_key'=>$sid),array('id'=>$user->id));
			return array('status'=>0);
		}
		else
		{
			// Login details not valid
			return array('status'=>10000,'error'=>$this->CI->lang->line('userlib_login_failed'));
		}
		return array('status'=>10000,'error'=>'登录失败，请重试！');
	}
	
	function check($sid='nologin'){
		$res=$this->CI->user_model->fetch('Users','id,username,group',null,array('activation_key'=>$sid));
		if($res->num_rows()!=1){
			return null;
		}
		//$this->CI->userlib->set_userlogin($userid);
		return $res->row();
	}
	
	function register(){
		// Setup fields
		$fields['username'] = $this->CI->lang->line('userlib_phone');
		$fields['password'] = $this->CI->lang->line('userlib_password');
		//$fields['re_password'] = $this->CI->lang->line('userlib_confirm_password');
		//$fields['truename'] = $this->CI->lang->line('userlib_truename');
		//$fields['nickname'] = '昵称';
		$this->CI->validation->set_fields($fields);

		// Set Rules
		$rules['username'] = 'trim|required|valid_phone';
		//$rules['truename'] = 'trim|max_length[4]';
		//$rules['nickname'] = 'trim|required|max_length[11]';
		$rules['password'] = 'trim|required|min_length['.$this->CI->preference->item('min_password_length').']';
		$this->CI->validation->set_rules($rules);

		if ( $this->CI->validation->run() === FALSE )
		{
			// Output any errors
			//$this->CI->validation->output_errors();
			//return $this->CI->validation->_error_array[0];
			return array('status'=>10000,'error'=>$this->CI->validation->_error_array[0]);
			// Display page
		}
		else
		{
			// Submit form
			return $this->_register();
		}
	}
	function _register(){
		// Build
		$data['users']['username'] = $this->CI->input->post('username');
		$res=$this->CI->user_model->fetch('Users','id',null,array('username'=>$data['users']['username']));
		$newFlag=$res->num_rows()<1;
		if(!$newFlag){
			$data['user_profiles']['user_id']=$res->row()->id;
			$data['user_profiles']['nickname']=preg_replace("/^[0-9]{8}/", 'ifeng@', $data['users']['username']);
			if($this->CI->input->post('password')){
				$data['users']['password'] = $this->CI->userlib->encode_password($this->CI->input->post('password'));
				$this->CI->user_model->update('Users',$data['users'],array('id'=>$data['user_profiles']['user_id']));
			}
		}else{
			$data['users']['email'] = $data['users']['username'].'@wisheli.com';
			$data['users']['password'] = $this->CI->userlib->encode_password($this->CI->input->post('password'));
			$data['users']['group'] = $this->CI->preference->item('default_user_group');
			$data['users']['created'] = date("Y-m-d H:i:s",TIME);
			$data['users']['active'] = 1;
			$data['users']['activation_key'] = md5(rand());//存活期分钟变更一次sid;
			$data['user_profiles']['user_id']=0;
			//$data['user_profiles']['truename']=$this->CI->input->post('truename');
			//$data['user_profiles']['truename']=$data['user_profiles']['truename']?$data['user_profiles']['truename']:$data['users']['username'];
			//$data['user_profiles']['nickname']=$this->CI->input->post('nickname');
			$data['user_profiles']['nickname']=$data['users']['username'];
			$data['user_profiles']['phone']=$data['users']['username'];
			
			
			//$data['user_profiles']['peoplecard']=$this->CI->input->post('peoplecard');
			
			
			// Add user details to DB
			$this->CI->user_model->insert('Users',$data['users']);
	
			// Get the auto insert ID
			$data['user_profiles']['user_id'] = $this->CI->db->insert_id();
		}
		$this->CI->load->module_library('oauth','oauth_lib');
		$cookie=$this->CI->oauth_lib->getWechatCookie();
		if($cookie['openid']){
			$data['user_profiles']['openid']=$cookie['openid'];
		}
		// Add user_profile details to DB
		if(!$newFlag){
			$this->CI->user_model->update('UserProfiles',$data['user_profiles'],array('user_id'=>$data['user_profiles']['user_id']));	
		}else{
			$this->CI->user_model->insert('UserProfiles',$data['user_profiles']);
		}
		return array('status'=>0,'nickname'=>$data['user_profiles']['nickname']);
		
	}
	function changePassword($uid,$password){
		$this->CI->user_model->update('Users',array('password'=>$password),array('id'=>$uid));
	}
	function checkOldPassword($uid,$password=''){
		$password=$this->CI->userlib->encode_password($password);
		$useres=$this->CI->user_model->getUsers(
			array(
				'users.id'=>$uid,
				'users.password'=>$password
			));
		if($useres->num_rows()!=1){
			return false;
		}
		return true;
	}
	function _modifyUser($uid){	
		$password=$this->CI->input->post('password');
		if($password){
			$old_password=$this->CI->input->post('old_password');
			if(!$this->checkOldPassword($uid,$old_password)){
				return array('status'=>10000,'error'=>'原始密码错误');
			}
			$password= $this->CI->userlib->encode_password($password);
			$this->changePassword($uid,$password);	
		}
		$UserProfiles=array();
		$nickname=$this->CI->input->post('nickname');
		if($nickname){
			$UserProfiles['nickname']=$nickname;
		}
		if($UserProfiles){
			$this->CI->user_model->update('UserProfiles',$UserProfiles,array('user_id'=>$uid));
		}
		
		return array('status'=>0);
	}
	function modifyUser($uid){
		// Setup fields
		$fields['old_password']='原始密码';
		$fields['password'] = $this->CI->lang->line('userlib_password');
		$fields['nickname'] = '昵称';
		$this->CI->validation->set_fields($fields);

		// Set Rules
		$rules['password'] = 'trim|min_length['.$this->CI->preference->item('min_password_length').']';
		$rules['nickname'] = 'trim|true|max_length[15]';
		$this->CI->validation->set_rules($rules);

		if ( $this->CI->validation->run() === FALSE )
		{
			// Output any errors
			//$this->CI->validation->output_errors();
			return array('status'=>10000,'error'=>$this->CI->validation->_error_array[0]);
			// Display page
		}
		else
		{
			// Submit form
			return $this->_modifyUser($uid);
		}
	}
}
/* End of file Noauth_lib.php */
/* Location: ./modules/noauth/libraries/noauth_lib.php */