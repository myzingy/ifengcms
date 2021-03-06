<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class comment_lib
{
	function comment_lib(){
		// Get CI Instance
		$this->CI = &get_instance();
		$this->CI->load->helper('form');
		$this->CI->load->library('validation');
		$this->CI->bep_assets->load_asset_group('FORMS');
		$this->CI->load->model('comment_model');
	}
	function add_comment($uid){
		$data['uid']=$uid;
		$res=$this->CI->comment_model->fetch('C','max(addtime) as addtime',null,$data);
		if($res->num_rows()>0){
			$time=$res->row()->addtime;
			if(TIME-$time<60){
				return false;
			}
		}
		$data['aid']=$this->CI->input->post('aid');
		$data['subject']=$this->CI->input->post('subject');
		$data['ip']=$this->CI->input->ip_address();
		$data['addtime']=TIME;
		$this->CI->comment_model->insert('C',$data);
		return true;
	}
}