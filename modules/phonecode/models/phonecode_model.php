<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * BackendPro
 *
 * An open source development control panel written in PHP
 *
 * @package		BackendPro
 * @author		Adam Price
 * @copyright	Copyright (c) 2008, Adam Price
 * @license		http://www.gnu.org/licenses/lgpl.html
 * @link		http://www.kaydoo.co.uk/projects/backendpro
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * User_model
 *
 * Provides functionaly to query all tables related to the
 * user.
 *
 * @package   	BackendPro
 * @subpackage  Models
 */
class Phonecode_model extends Base_model
{
	var $steptime=60;	//发送间隔
	var $outtime=600;	//失效时间	
	function Phonecode_model()
	{
		parent::Base_model();

		$this->_prefix = $this->config->item('ifengcms_table_prefix');
		$this->_TABLES = array('PC' => $this->_prefix . 'phonecode');

		log_message('debug','BackendPro : Phonecode_model class loaded');
	}
	function sendcode($phone='',$type){
		
		$res=$this->checkdata($phone,$this->steptime);
		if($res->num_rows()==1){
			return false;
		}
		$code=rand(100001,999999);
		$this->insert('PC',array('phone'=>$phone,'time'=>TIME,'code'=>$code,'type'=>$type));
		$id=$this->db->insert_id();
		return array('id'=>$id,'code'=>$code);
	}
	function checkcode($phone='',$code=''){
		$res=$this->checkdata($phone,$this->outtime);
		if($res->num_rows()==1){
			$decode=$res->row()->code;	
			return ($decode==$code);
		}	
		return false;
	}
	function checkdata($phone='',$time){
		$this->db->select('code');
		$this->db->from($this->_TABLES['PC']);
		$this->db->where(array('time >'=>TIME-$time,'phone'=>$phone));
		$this->db->order_by('time','desc');
		$this->db->limit(1);
		$res=$this->db->get();
		return $res;
	}
}