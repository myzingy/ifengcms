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
 * MY_Validation
 *
 * Implements custom validation functions for use
 * when validation forms in BackendPro
 *
 * @package			BackendPro
 * @subpackage		Libraries
 */
class MY_Validation extends CI_Validation
{
	function __construct()
	{
		parent::__construct();

		// Get CI Instance
		$this->CI = &get_instance();

		log_message('debug','BackendPro : MY_Validation class loaded');
	}

	/**
	 * Set Default Value
	 *
	 * Assigns a default value to a form field
	 *
	 * @access public
	 * @param mixed $data Field name OR Array
	 * @param mixed $value Field value
	 */
	function set_default_value($data=NULL, $value=NULL)
	{
		if (is_array($data))
		{
			foreach($data as $field => $value)
			{
				$this->set_default_value($field,$value);
			}
			return;
		}

		$this->$data    = $value;
		$_POST[$data]   = $value;
	}

	/**
	 * Output Validation Errors
	 *
	 * Using the Status class move all errors into an error
	 * message
	 *
	 * @access public
	 */
	function output_errors()
	{
		// Make sure the status module is
		$this->CI->load->module_library('status','status');

		foreach ( $this->_error_array as $error )
		{
			flashMsg('warning',$error);
		}
	}

	/**
	 * Check for valid captcha
	 *
	 * Contact the ReCaptcha server and check the input is valid
	 *
	 * @access public
	 * @return boolean
	 */
	function valid_captcha()
	{
		// Make sure the captcha library is loaded
		$this->CI->load->module_library('recaptcha','Recaptcha');

		// Set the error message
		$this->CI->validation->set_message('valid_captcha', $this->CI->lang->line('userlib_validation_captcha'));

		// Perform check
		$this->CI->recaptcha->recaptcha_check_answer($this->CI->input->server('REMOTE_ADDR'), $this->CI->input->post('recaptcha_challenge_field'), $this->CI->input->post('recaptcha_response_field'));

		return $this->CI->recaptcha->is_valid;
	}

	/**
	 * Check that the username is spare
	 *
	 * Check that the username given is not in use
	 *
	 * @access public
	 * @param string $username Username
	 * @return boolean
	 */
	function spare_username($username)
	{
		$query = $this->CI->user_model->fetch('Users',NULL,NULL,array('username'=>$username));

		// Set the error message
		$this->CI->validation->set_message('spare_username', $this->CI->lang->line('userlib_validation_username'));

		return ($query->num_rows() == 0) ? TRUE : FALSE;
	}

	/**
	 * Check that the email is spare
	 *
	 * Check that the username given is not in use by another user
	 *
	 * @access public
	 * @param string $email Email
	 * @retrun boolean
	 */
	function spare_email($email)
	{
		$query = $this->CI->user_model->fetch('Users',NULL,NULL,array('email'=>$email));

		// Set the error message
		$this->CI->validation->set_message('spare_email', $this->CI->lang->line('userlib_validation_email'));

		return ($query->num_rows() == 0) ? TRUE : FALSE;
	}

	/**
	 * Check Spare Username
	 *
	 * When modifying a user check the username is spare
	 *
	 * @access public
	 * @param string $username Username
	 * @return boolean
	 */
	function spare_edit_username($username)
	{
		$query = $this->CI->user_model->fetch('Users',NULL,NULL,array('username'=>$username,'id !='=>$this->CI->input->post('id')));

		// Set the error message
		$this->CI->validation->set_message('spare_edit_username', $this->CI->lang->line('userlib_validation_username'));

		return ($query->num_rows() == 0) ? TRUE : FALSE;
	}

	/**
	 * Check Spare Email
	 *
	 * When modifying a user check the email is spare
	 *
	 * @access public
	 * @param string $email Email
	 * @retrun boolean
	 */
	function spare_edit_email($email)
	{
		$query = $this->CI->user_model->fetch('Users',NULL,NULL,array('email'=>$email,'id !='=>$this->CI->input->post('id')));

		// Set the error message
		$this->CI->validation->set_message('spare_edit_email', $this->CI->lang->line('userlib_validation_email'));

		return ($query->num_rows() == 0) ? TRUE : FALSE;
	}
	
	function valid_phone($phone){
		//^(1(([35][0-9])|(47)|[8][01236789]))\d{8}$
		return ( ! preg_match("/^1\d{10}$/", $phone)) ? FALSE : TRUE;
	}
	function valid_url($url){
		$this->CI->validation->set_message('valid_url', '链接地址必须以http://开头');
		return (! preg_match("/^http:\/\/.+/", $url))?false:true;
	}
	function min_number($num,$val){
		if (preg_match("/[^0-9\.]/", $val))
		{
			return FALSE;
		}
		return ($num < $val) ? FALSE : TRUE;
	}
	function max_number($num,$val){
		if (preg_match("/[^0-9\.]/", $val))
		{
			return FALSE;
		}
		return ($num > $val) ? FALSE : TRUE;
	}
	function debitcard($val){
		if (!preg_match("/[0-9]{16,19}/", $val))
		{
			return FALSE;
		}
		$arr=str_split($val);
		$last=array_pop($arr);
		$len=count($arr)-1;
		$sum=0;
		for($i=0;$i<=$len;$i++){
			$ji=$arr[$len-$i];	
			if($i%2==0){
				$ji*=2;
				if($ji>9){
					$ji_gewei=$ji%10;
					$ji=1+$ji_gewei;
				}
			}
			$sum+=$ji;
		}
		if(($sum+$last)%10==0){
			return true;
		}
		return false;
	}
	function set_rules($data, $rules = '')
	{
		if ( ! is_array($data))
		{
			if ($rules == '')
				return;
				
			$data = array($data => $rules);
		}
	
		foreach ($data as $key => $val)
		{
			$this->_rules[str_replace("[]","",$key)] = $val;
		}
	}
	function field_truename($name){
		$this->CI->validation->set_message('field_truename',"请输入真实%s");
		if (preg_match("/^[\x{4e00}-\x{9fa5}]{2,4}$/u",$name)) {
			return true;
		}
		return false;
	}
	function field_phone($phone,$table_field){
		if($phone){
			$flag=$this->valid_phone($phone);
			if(!$flag){
				$this->CI->validation->set_message('field_phone',"%s不正确，请重新填写");
				return $flag;
			}
			$this->CI->validation->set_message('field_phone',"%s已经存在，请更换");
			list($table,$field)=explode(',', $table_field);
			$res=$this->CI->user_model->db->query("select * from {$table} where `$field`='{$phone}'");
			if($res->num_rows()>0) return false;
		}
		return true;
	}
	function valid_mancode($code){
		$mycode=array();//存放code信息
		if(strlen($code)!=18 && strlen($code)!=16){
			$this->CI->validation->set_message('valid_mancode',"%s不正确，请重新填写");
			return false;	
		}
		if(strlen($code)==16){
			$code=substr($code,0,6)."19".substr($code,6);
		}
		$mycode['p_code']=substr($code,0,6);//区位码
		$mycode['b_code']=substr($code,6,8);//生日码
		$mycode['o_code']=substr($code,14,3);//顺序码
		$mycode['t_code']=substr($code,17);//校验码
		$f=$this->getf($code);
		if($mycode['t_code']!=$f){
			$this->CI->validation->set_message('valid_mancode',"%s不正确，请重新填写");
			return false;	
		}
		return true;
	}
	function getf($code){//获取权位
		$s=explode(" ","7 9 10 5 8 4 2 1 6 3 7 9 10 5 8 4 2");//加权因子
		$f=explode(" ","1 0 X 9 8 7 6 5 4 3 2");//对应效检码
		for($i=0;$i<17;$i++){
			$ts+=$s[$i]*substr($code,$i,1);	
		}
		$ts=$ts%11;
		return $f[$ts];
	}
	function field_mancode($mancode,$table_field){
		if($mancode){
			$flag=$this->valid_mancode($mancode);
			if(!$flag){
				$this->CI->validation->set_message('field_mancode',"%s不正确，请重新填写");
				return $flag;
			}
			$this->CI->validation->set_message('field_mancode',"%s已经存在，请更换");
			list($table,$field)=explode(',', $table_field);
			$res=$this->CI->user_model->db->query("select * from {$table} where `$field`='{$mancode}'");
			if($res->num_rows()>0) return false;
		}
		return true;
		
	}
}
/* End of file MY_Validation.php */
/* Location: ./system/application/libraries/MY_Validation.php */