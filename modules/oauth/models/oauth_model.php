<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class oauth_model extends Base_model
{
	function oauth_model(){
		parent::Base_model();

		$this->_prefix = $this->config->item('weixin_table_prefix');
		$this->_TABLES = array(
			'U' => $this->_prefix . 'users'
            ,'UP' => $this->_prefix . 'user_profiles'
        );
	}
}