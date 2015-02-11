<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Userlib Helper
 *
 * Contains shortcuts to well used Userlib functions
 *
 * @package         BackendPro
 * @subpackage		Helpers
 * @author          Adam Price
 * @copyright       Copyright (c) 2008
 * @license         http://www.gnu.org/licenses/lgpl.html
 * @link            http://www.kaydoo.co.uk/projects/backendpro
 * @filesource
 */

// ---------------------------------------------------------------------------

if( ! function_exists('sendcode'))
{
	function sendcode($phone,$type)
	{
		$ci = &get_instance();
		$ci->load->model('Phonecode_model');
		$ci->load->library('sms');
		$info=$ci->Phonecode_model->sendcode($phone,$type);
		if($info){
			$ci->sms->send($phone,'【凤凰陕西】你的验证码是 '.$info['code'].'，请勿泄露。');
		}
	}
}
if( ! function_exists('checkcode'))
{
	function checkcode($phone,$code)
	{
		$ci = &get_instance();
		$ci->load->model('Phonecode_model');
		return $ci->Phonecode_model->checkcode($phone,$code);
	}
}