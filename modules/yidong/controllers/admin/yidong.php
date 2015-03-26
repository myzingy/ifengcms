<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class yidong extends Admin_Controller
{
	/**
	 * Constructor
	 */
	function yidong(){
		parent::Admin_Controller();
		// Load the Auth_form_processing class
		$this->load->library('yidong_lib');
	}
	function devices(){
		$limit=array('offset'=>$page,'limit'=>30);
		$data['params']=$this->uri->getParamsArr();
		//$where=array('A.type'=>1);
		$info=$this->yidong_model->getDevicesList($where,$limit,true);
		//echo $this->yidong_model->db->last_query();
		$data['members'] = $info['data'];
		$data['pagination']=$info['pagination'];
		// Display Page
		$data['header'] = '机型管理';
		$data['page'] = $this->config->item('backendpro_template_dir') . "list_devices";
		$data['module'] = 'yidong';
		//$data['TYPE']=$this->yidong_model->_TYPE;
		//$data['classify'] =$this->yidong_model->classifyData();
		$this->load->view($this->_container,$data);
	}
	function devices_form($aid=0){
		$this->yidong_lib->devices_form($this->_container,$aid);
	}
	function reservation(){
		$limit=array('offset'=>$page,'limit'=>30);
		$data['params']=$this->uri->getParamsArr();
		//$where=array('A.type'=>1);
		$info=$this->yidong_model->getReserData($where,$limit,true);
		//echo $this->yidong_model->db->last_query();
		$data['members'] = $info['data'];
		$data['pagination']=$info['pagination'];
		// Display Page
		$data['header'] = '预约管理';
		$data['page'] = $this->config->item('backendpro_template_dir') . "list_reservation";
		$data['module'] = 'yidong';
		$data['tab_fields']=$this->yidong_model->reser_table_key;
		$data['timeout']=yidong_model::timeout;
		//$data['TYPE']=$this->yidong_model->_TYPE;
		//$data['classify'] =$this->yidong_model->classifyData();
		$this->load->view($this->_container,$data);
	}
	function switchReserStatus($id,$status){
		$this->yidong_model->update('DR',array('status'=>$status),array('id'=>$id));	
	}
	function switchDeviceStatus($id,$status){
		$this->yidong_model->update('D',array('status'=>$status),array('id'=>$id));	
	}
	function order($id,$order){
		$this->yidong_model->update('D',array('order'=>$order+0),array('id'=>$id));
	}
}