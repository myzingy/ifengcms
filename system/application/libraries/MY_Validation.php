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
	function MY_Validation()
	{
		parent::CI_Validation();

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
		return ( ! preg_match("/^(1(([35][0-9])|(47)|[8][01236789]))\d{8}$/", $phone)) ? FALSE : TRUE;
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
}
/* End of file MY_Validation.php */
/* Location: ./system/application/libraries/MY_Validation.php */