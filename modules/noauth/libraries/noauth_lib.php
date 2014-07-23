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
	function Noauth_lib()
	{
		// Get CI Instance
		$this->CI = &get_instance();

		$this->CI->load->module_model('auth','user_model');
		$this->CI->load->module_library('auth','userlib');

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
				return array('status'=>-1,'error'=>$this->CI->lang->line('userlib_account_unactivated'));
			}
			// Everything is OK
			// Save details to session
			//@TODO: This dosn't seem very safe having the login code totaly exposed
			
			$this->CI->userlib->set_userlogin($user->id);
			$sid=md5($user->id.(TIME-TIME%(60*5)));//5分钟变更一次sid
			$this->CI->user_model->update('Users',array('activation_key'=>$sid),array('id'=>$user->id));
			return array('status'=>0,'sid'=>$sid);
		}
		else
		{
			// Login details not valid
			return array('status'=>-1,'error'=>$this->CI->lang->line('userlib_login_failed'));
		}
		return array('status'=>-1,'error'=>'登录失败，请重试！');
	}
	
	function check($sid='nologin'){
		$res=$this->CI->user_model->fetch('Users','id,username,group',null,array('activation_key'=>$sid));
		if($res->num_rows()!=1){
			return null;
		}
		//$this->CI->userlib->set_userlogin($userid);
		return $res->row();
	}
	
}
/* End of file Noauth_lib.php */
/* Location: ./modules/noauth/libraries/noauth_lib.php */