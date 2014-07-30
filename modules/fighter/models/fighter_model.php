<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class fighter_model extends Base_model
{
	function fighter_model(){
		parent::Base_model();

		$pro_prefix = $this->config->item('backendpro_table_prefix');
		$ifengcms_prefix = $this->config->item('ifengcms_table_prefix');
		$this->_TABLES = array(
			'U'=>$pro_prefix . 'users'
			,'UF' => $pro_prefix . 'user_profiles'
			,'WU' => $ifengcms_prefix . 'wechat_user'
            
        );
	}
}