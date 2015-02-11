<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class comment extends Admin_Controller
{
	/**
	 * Constructor
	 */
	function comment(){
		parent::Admin_Controller();
		// Load the Auth_form_processing class
		$this->load->library('comment_lib');
	}
	function index(){
		$limit=array('offset'=>$page,'limit'=>15);
		$data['params']=$this->uri->getParamsArr();
		$info=$this->comment_model->getCommentList($where,$limit,true,$data['params']['title']);
		echo $this->comment_model->db->last_query();
		$data['members'] = $info['data'];
		$data['pagination']=$info['pagination'];
		// Display Page
		$data['header'] = '评论管理';
		$data['page'] = $this->config->item('backendpro_template_dir') . "list";
		$data['module'] = 'comment';
		$data['classify']=$this->comment_model->_TYPE;
		$this->load->view($this->_container,$data);	
	}
	function switchStatus($id,$status){
		$this->comment_model->update('C',array('status'=>$status),array('id'=>$id));	
	}
	function delete(){
		if(FALSE === ($selected = $this->input->post('select')))
		{
			redirect('comment/admin/comment/index/','location');
		}
		foreach($selected as $aid)
		{
			$this->article_model->delete('C',array('id'=>$aid));
		}
		redirect('comment/admin/comment/index/','location');
	}
}