<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class special_model extends Base_model
{
	function special_model(){
		parent::Base_model();

		$this->_prefix = $this->config->item('weixin_table_prefix');
		$this->_TABLES = array(
			'U' => $this->_prefix . 'users'
            ,'UP' => $this->_prefix . 'user_profiles'
        );
	}
}