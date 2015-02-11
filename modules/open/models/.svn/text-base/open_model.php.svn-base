<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class open_model extends Base_model
{
	function open_model(){
		parent::Base_model();

		$this->_prefix = $this->config->item('weixin_table_prefix');
		$this->_TABLES = array(
			'U' => 'be_users'
			,'UP' => 'be_user_profiles'
            ,'S' => $this->_prefix . 'site'
        );
	}
}