<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class api_lib
{
	function api_lib(){
		// Get CI Instance
		$this->CI = &get_instance();
		$this->CI->load->model('yidong_model');
	}
	public function status(&$arr,$code,$str=''){
		$error=$this->statusCode[$code];
		if($error){
			$arr['status']=$code;
			$arr['error']=$str?$str:$error;
		}else{
			$arr['status']=10000;
			$arr['error']=$code;	
		}
		return $arr;
	}
	function getDeviceForPhone($phone){
		$res=$this->CI->yidong_model->fetch('UP','*',null,array('phone'=>$phone));
		$info=array('status'=>10000);
		if($res->num_rows()<1){
			$info['error']='打400电话吧';
			return $info;
		}
		$classify=$res->row()->reg;
		$limit=array('offset'=>$page,'limit'=>10);
		$where=array('status'=>0);
		$res=$this->CI->yidong_model->getDevicesList($where,$limit,false,$classify);
		$info=array('status'=>0,'data'=>array());
		$package_fields=array('yonghujiaokuan','yuefanhuafei','zuidixiaofei','heyueqi','chanpin','chanpinneirong');
		foreach ($res->result() as $r) {
			//$package	
			$package=array();
			foreach ($package_fields as $key) {
				$package[]=array(
					'label'=>eval("return yidong_model::{$key};"),
					'value'=>$r->$key,
				);
			}
			//$color
			$devinfo=$this->CI->yidong_model->getDevicesInfo($r->id);
			$color=$devinfo['color'];
			//$device
			$device=array(
				'id'=>$r->id,
				'name'=>$r->name,
				'img'=>base_url().$r->img,
				'color'=>$color,
				'package'=>$package,
			);
			array_push($info['data'],$device);
		}
		return $info;
	}
}