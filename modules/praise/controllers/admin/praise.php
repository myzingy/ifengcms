<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class praise extends Admin_Controller
{
	/**
	 * Constructor
	 */
	function praise(){
		parent::Admin_Controller();
		// Load the Auth_form_processing class
		$this->load->library('praise_lib');
	}
	function index(){
		$limit=array('offset'=>$page,'limit'=>30);
		$info=$this->praise_model->getList($where,$limit,true);
		$data['members'] = $info['data'];
		$data['pagination']=$info['pagination'];
		// Display Page
		$data['header'] = '点赞记录';
		$data['page'] = $this->config->item('backendpro_template_dir') . "list";
		$data['module'] = 'praise';
		$this->load->view($this->_container,$data);
	}
	function form(){
		$this->praise_lib->form($this->_container);
	}
}