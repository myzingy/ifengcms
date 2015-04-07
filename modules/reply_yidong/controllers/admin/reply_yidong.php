<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class reply_yidong extends Admin_Controller
{
	/**
	 * Constructor
	 */
	function reply_yidong(){
		parent::Admin_Controller();
		// Load the Auth_form_processing class
		$this->load->library('reply_admin_lib');
	}
	function menu(){
		$this->reply_admin_lib->menu($this->_container);
	}
	function keyword($aid=0){
		$this->reply_admin_lib->keyword($this->_container,$aid);
	}
	function keylist(){
		$data['wechat_name']=$this->reply_model->wechat_name;
		$this->load->module_model('article','article_model');
		$limit=array('offset'=>$page,'limit'=>15);
		$where=array('A.type'=>5);
		$info=$this->article_model->getArticleContentList($where,$limit,true);
		$data['members'] = $info['data'];
		$data['pagination']=$info['pagination'];

		// Display Page
		$data['header'] = $this->reply_model->wechat_name_cn.' >> 关键字管理';
		$data['page'] = $this->config->item('backendpro_template_dir') . "list";
		$data['module'] = $this->reply_model->wechat_name;
		$this->load->view($this->_container,$data);
	}
	function msg($type){
		$this->reply_admin_lib->msg($this->_container,$type);
	}
	function delete(){
		if(FALSE === ($selected = $this->input->post('select')))
		{
			redirect($_SERVER['HTTP_REFERER'],'location');
		}
		$claen=$this->input->post('clean');
		foreach($selected as $aid){
			$this->article_model->delete('A',array('id'=>$aid));
			$this->article_model->delete('AC',array('aid'=>$aid));
		}
		redirect($_SERVER['HTTP_REFERER'],'location');
	}
}