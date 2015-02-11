<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class praise_model extends Base_model
{
	function praise_model(){
		parent::Base_model();

		$this->_prefix = $this->config->item('ifengcms_table_prefix');
		$this->_TABLES = array(
			'P' => $this->_prefix . 'praise'
        );
	}
	function count($pkid){
		$this->db->select('count(*) as count');
		$this->db->from($this->_TABLES['P']);
		$this->db->group_by('type');
		$this->db->where('pkid',$pkid);
		$this->db->order_by('type','desc');
		return $this->db->get();
	}
}