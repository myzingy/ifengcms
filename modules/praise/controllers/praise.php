<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class praise extends Public_Controller
{
	/**
	 * Constructor
	 */
	function praise(){
		parent::Public_Controller();
		// Load the Auth_form_processing class
		$this->load->library('praise_lib');
	}
	function index($pkid='',$action='look'){
		if(!$pkid) return false;
		parse_str($_SERVER['QUERY_STRING'],$_GET);
		if($action=='up' || $action=='down' || (is_numeric($action))){
			$data['pkid']=$pkid;
			$data['ip']=$this->input->ip_address();
			$res=$this->praise_model->fetch('P','*',null,$data);
			if($res->num_rows()>0){
				$info=array('status'=>10000,'error'=>'你已经投过票了');
			}else{
				$data['addtime']=TIME;
				if(is_numeric($action)){
					$data['type']=$action;
				}else{
					$data['type']=($action=='down')?1:0;
				}
				$this->praise_model->insert('P',$data);
			}
		}
		if(!$info){
			$info=array('status'=>0,'up'=>0,'down'=>0);
			$res=$this->praise_model->count($pkid);
			if($res->num_rows()>0){
				$row=$res->result();
				foreach ($row as $r) {
					$base[$r->type]=$r->count;
				}
				$info=array(
					'status'=>0,
					'up'=>$row[0]->count+0,
					'down'=>$row[1]->count+0,
					'base'=>$base
					
				);
			}
		}
		$json=json_encode($info);
		if($_GET['jscallback']){
			die("{$_GET['jscallback']}({$json});");
		}
		die ($json);
	}
}