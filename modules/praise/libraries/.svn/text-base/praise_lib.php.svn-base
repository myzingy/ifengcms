<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class praise_lib
{
	function praise_lib(){
		// Get CI Instance
		$this->CI = &get_instance();
		$this->CI->load->helper('form');
		$this->CI->load->library('validation');
		$this->CI->bep_assets->load_asset_group('FORMS');
		$this->CI->load->model('praise_model');
	}
	function form($container){
		$fields['fields'] = "fields";
		
		$rules['fieldsfields'] = 'trim|required|min_length[8]|max_length[32]';
		
		$this->CI->validation->set_fields($fields);
		$this->CI->validation->set_rules($rules);
		if ( $this->CI->validation->run() === FALSE )
		{
			return array('status'=>1,'error'=>$this->CI->validation->_error_array[0]);
		}else{
			// Submit form
			return $this->_from();
		}
	}
	function _from(){
		$data['fields']=$this->CI->input->post('fields');
		
		return array('status'=>0);
	}
}