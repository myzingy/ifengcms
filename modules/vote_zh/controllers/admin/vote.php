<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class vote extends Admin_Controller
{
	/**
	 * Constructor
	 */
	function vote(){
		parent::Admin_Controller();
		// Load the Auth_form_processing class
		$this->load->library('vote_lib');
	}
	function index(){
		$limit=array('offset'=>$page,'limit'=>15);
		$info=$this->vote_model->getVoteList($where,$limit,true);
		$data['members'] = $info['data'];
		$data['pagination']=$info['pagination'];

		// Display Page
		$data['header'] = '中韩交流投票管理';
		$data['page'] = $this->config->item('backendpro_template_dir') . "list";
		$data['module'] = 'vote_zh';
		$this->load->view($this->_container,$data);
	}
	function update($id=0){
		$this->vote_lib->update($this->_container,$id);
	}
	function updatevotedata($vid,$vmid=0){
		$this->vote_lib->updatevotedata($this->_container,$vid,$vmid);
	}
	function votedata($id){
		$limit=array('offset'=>$page,'limit'=>30);
		$where=array('VM.vid'=>$id);
		$info=$this->vote_model->getVoteDataList($where,$limit,true);
		$data['vid'] = $id;
		$data['members'] = $info['data'];
		$data['pagination']=$info['pagination'];
		//
		$res=$this->vote_model->fetch('V','*',null,array('id'=>$id));
		if($res->num_rows()>0){
			$data['vote']=$res->row();
		}
		// Display Page
		$data['header'] = $data['vote']->title;
		$data['page'] = $this->config->item('backendpro_template_dir') . "list_votedata";
		$data['module'] = 'vote_zh';
		$this->load->view($this->_container,$data);
	}
	function autovotedata($vid){
		// Display Page
		$data['header'] = '导入投票数据';
		$data['page'] = $this->config->item('backendpro_template_dir') . "auto_votedata";
		$data['module'] = 'vote_zh';
		$data['vid']=$vid;
		$res=$this->vote_model->fetch('VS','*',array('rows'=>10),array('vid'=>$vid));
		if($res->num_rows()>0){
			$data['data']=$res->result();
		}
		$this->load->view($this->_container,$data);
	}
	function bindsource(){
		$info=$this->vote_lib->bindsource();
		die(json_encode($info));
	}
	function unbindsource($vid,$source_id){
		$this->vote_model->delete('VS',array('vid'=>$vid,'source_id'=>$source_id));
	}
	//执行导入动作
	function startSource($vid,$source_id){
		$info=$this->vote_model->startSource($vid,$source_id);
		die(json_encode($info));
	}
	function deleteVote($vid){
		$this->vote_model->delete('V',array('vid'=>$vid));
		die("OK");
	}
	function deleteVoteData($vmid){
		$this->vote_model->delete('VM',array('id'=>$vmid));
		die("OK");
	}
	function vote_history_clear($vid){
		$this->vote_model->delete('VH',array('vid'=>$vid));
		$this->vote_model->update('VM',array('count'=>0),array('vid'=>$vid));
		redirect($_SERVER['HTTP_REFERER'],'location');
	}
}