<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class reply_model extends Base_model
{
	public $wechat_name='reply_zh';	
	public $wechat_name_cn='移动政企公众号';	
	function reply_model(){
		parent::Base_model();

		$pro_prefix = $this->config->item('backendpro_table_prefix');
		$cms_prefix = $this->config->item('ifengcms_table_prefix');
		$this->_TABLES = array(    
			'UNU' => $cms_prefix . 'unsubscribe_weuser',
        );
	}
	function delUnUser($openid){
		$this->delete('UNU',array('openid'=>$openid));
	}
	function addUnUser($openid){
		$this->delUnUser($openid);
		$this->insert('UNU',array('openid'=>$openid,'addtime'=>TIME));
	}
	function msgData($type='R',$newdata=array()){
		$file=BASEPATH."cache/wx_msg_".$this->wechat_name;
		if($type=='R'){
			$data=@include($file);
			$data=$data?$data:array();
			return $data;
		}
		if($type=='W'){
			$data="<?php\nreturn ".var_export($newdata,true).";\n";
			@file_put_contents($file,$data);
		}
	}
}