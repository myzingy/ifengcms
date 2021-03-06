<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class comment extends Public_Controller
{
	/**
	 * Constructor
	 */
	function comment(){
		parent::Public_Controller();
		// Load the Auth_form_processing class
		$this->load->library('comment_lib');
	}
	function index(){
		parent::Ajax_Controller();
		$limit=array('offset'=>$page,'limit'=>15);
		//$data['params']=$this->uri->getParamsArr();
		$where=array(
			'C.status'=>0
		);
		$info=$this->comment_model->getCommentList($where,$limit,true,$data['params']['title']);
		//echo $this->comment_model->db->last_query();
		$data['members'] = $info['data'];
		$data['pagination']=$info['pagination'];
		//Display Page
		$data['page'] = $this->config->item('backendpro_template_dir') . "list_ajax";
		$data['module'] = 'comment';
		$this->load->view($this->_container,$data);
	}
	function form(){
		$this->comment_lib->form($this->_container);
	}
	function getCommnet($aid,$limit=10){
		$limit=array('offset'=>$page,'limit'=>10);
		$where=array('C.status'=>0);
		$uid=$this->session->userdata('id');
		if($uid>0){
			$where=" C.aid ='$aid' AND ( C.status=0 OR C.uid=$uid ) ";
		}else{
			if($aid>0){
				$where['C.aid']=$aid;
			}
		}
		$res=$this->comment_model->getCommentList($where,$limit,false);
		if($res->num_rows()>0){
			$info['status']=0;
			$info['data']=$res->result();
		}else{
			$info['status']=10000;
		}
		die(json_encode($info));
	}
}