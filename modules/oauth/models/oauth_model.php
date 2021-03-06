<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class oauth_model extends Base_model
{
	function __construct(){
		parent::__construct();

		$pro_prefix = $this->config->item('backendpro_table_prefix');
		$cms_prefix = $this->config->item('ifengcms_table_prefix');
		$this->_TABLES = array(
			'U' => $pro_prefix . 'users'
            ,'UP' => $pro_prefix . 'user_profiles'
            ,'WU' => $cms_prefix . 'wechat_user'
        );
	}
	function getUserForOpenID($openid=''){
		$this->db->select('*');
		$this->db->from($this->_TABLES['UP']." UP");
		$this->db->join($this->_TABLES['U'] ." U",'U.id=UP.userid','left');
		$this->db->where(array('UP.openid'=>$openid));
		$res=$this->db->get();
		if($res->num_rows()>0){
			return $res->row();
		}
		return false;
	}
}