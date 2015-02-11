<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class xwzwb extends Mobile_Controller
{
	/**
	 * Constructor
	 */
	function xwzwb(){
		parent::Mobile_Controller();
		// Load the Auth_form_processing class
		$this->load->library('xwzwb_lib');
	}
	function index(){
		$limit=array('offset'=>$page,'limit'=>15);
		$data['params']=$this->uri->getParamsArr();
		$where=array('A.type'=>1);
		//$info=$this->article_model->getArticleList($where,$limit,true,$data['params']['classify']);
		//echo $this->article_model->db->last_query();
		$data['members'] = $info['data'];
		$data['pagination']=$info['pagination'];
		// Display Page
		$data['header'] = '新闻管理';
		$data['page'] = $this->config->item('backendpro_template_dir') . "index";
		$data['module'] = 'xwzwb';
		//$data['classify'] =$this->article_model->classifyData();
		$this->load->view($this->_container,$data);
	}
}