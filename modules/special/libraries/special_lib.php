<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class special_lib
{
	function special_lib(){
		// Get CI Instance
		$this->CI = &get_instance();
		$this->CI->load->helper('form');
		$this->CI->load->library('validation');
		$this->CI->bep_assets->load_asset_group('FORMS');
		$this->CI->load->model('special_model');
	}
	function update($container){
		$fields['fields'] = "fields";
		
		$rules['fieldsfields'] = 'trim|required|min_length[8]|max_length[32]';
		
		$this->CI->validation->set_fields($fields);
		$this->CI->validation->set_rules($rules);
		if ( $this->CI->validation->run() === FALSE )
		{
			// Output any errors
			$this->CI->validation->output_errors();

			// Display page
			$data['header'] = $pageinfo['header'];
			$data['page'] = $this->CI->config->item('backendpro_template_dir') . 'form';
			$data['module'] = 'special';
			$this->CI->load->view($container,$data);
		}else{
			// Submit form
			return $this->_from();
		}
	}
	function _update(){
		$data['fields']=$this->CI->input->post('fields');
		
		flashMsg('success','操作成功');
		redirect('special/admin/special/index/','location');
	}
}