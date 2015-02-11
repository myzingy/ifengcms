<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class ads extends Admin_Controller
{
	/**
	 * Constructor
	 */
	function ads(){
		parent::Admin_Controller();
		// Load the Auth_form_processing class
		$this->load->library('ads_lib');
		$this->load->module_helper('article','article');
	}
	function index(){
		$limit=array('offset'=>$page,'limit'=>15);
		$where=array('A.type'=>4);
		$info=$this->article_model->getArticleList($where,$limit,true);
		$data['members'] = $info['data'];
		$data['pagination']=$info['pagination'];

		// Display Page
		$data['header'] = '广告管理';
		$data['page'] = $this->config->item('backendpro_template_dir') . "list";
		$data['module'] = 'ads';
		$this->load->view($this->_container,$data);
	}
	function update($id=0){
		$this->ads_lib->update($this->_container,$id);
	}
	function delete()
	{
		if(FALSE === ($selected = $this->input->post('select')))
		{
			redirect('ads/admin/ads/index/','location');
		}
		foreach($selected as $aid)
		{
			$res=$this->article_model->fetch('A','*',null,array('id'=>$aid));
			if($res->num_rows()>0){
				$row=$res->row();
				@unlink(FCPATH.$row->src);
				$this->article_model->delete('A',array('id'=>$aid));
			}
		}
		redirect('ads/admin/ads/index/','location');
	}
}