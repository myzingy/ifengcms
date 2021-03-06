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
		//是否已经预约
		$reser_data=$this->isReservation($phone);
		if($reser_data){
			return $reser_data;
		}
		//是否可以参加
		$res=$this->CI->yidong_model->fetch('UP','*',null,array('phone'=>$phone));
		$info=array('status'=>10000);
		if($res->num_rows()<1){
			//使用裸机政策 3
			$classify=3;
		}else{
			$classify=$res->row()->reg;
		}
		$limit=array('offset'=>$page,'limit'=>10);
		$where=array('status'=>0);
		$res=$this->CI->yidong_model->getDevicesList($where,$limit,false,$classify);
		$info=array('status'=>0,'data'=>array());
		$package_fields=array('yonghujiaokuan','yuefanhuafei','zuidixiaofei','heyueqi','chanpin','chanpinneirong','yuebujiaofei');
		foreach ($res->result() as $r) {
			
			//$color
			$devinfo=$this->CI->yidong_model->getDevicesInfo($r->id);
			$color=$devinfo['color'];
			//$package	
			$package=array();
			if($classify!=3){
				$_pack=$devinfo['package'][$classify];
				foreach ($package_fields as $key) {
					$package[]=array(
						'label'=>eval("return yidong_model::{$key};"),
						'value'=>$_pack->$key,
					);
				}
			}else{
				$package[]=array(
					'label'=>yidong_model::yonghujiaokuan,
					'value'=>$devinfo['yonghujiaokuan'],
				);
				foreach ($this->CI->yidong_model->luoji as $key=>$val) {
					$package[]=array(
						'label'=>$val,
						'value'=>$devinfo[$key],
					);
				}
			}
			
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
	function reservation(){
		$phone=$this->CI->input->get('phone');
		$did=$this->CI->input->get('did');
		$cid=$this->CI->input->get('cid');
		
		//是否已经预约
		$reser_data=$this->isReservation($phone);
		if($reser_data){
			return $reser_data;
		}
		//是否可以参加
		$res=$this->CI->yidong_model->fetch('UP','*',null,array('phone'=>$phone));
		$info=array('status'=>10000);
		if($res->num_rows()<1){
			$info['error']='打400电话吧';
			return $info;
		}
		$classify=$res->row()->reg;
		
		//判断设备
		$devinfo=$this->CI->yidong_model->getDevicesInfo($did);
		$_pack=$devinfo['package'][$classify];
		if(!$_pack){
			$info['error']='预定机型错误，请重试';
			return $info;
		}
		/*
		if(!in_array($classify, $devinfo['classify'])){
			$info['error']='预定机型错误，请重试';
			return $info;
		}*/
		$isInColor=false;
		foreach ($devinfo['color'] as $color) {
			if($color['id']==$cid){
				$isInColor=$color;
				if($color['last_stock']<1){
					$info['error']='机型已经被预定完，但还没有领取完，请稍后再预定。';
					return $info;
				}
			}
		}
		if(!$isInColor){
			$info['error']='预定机型颜色错误，请重试';
			return $info;
		}
		//存储预约数据
		$reser_table_key=$this->CI->yidong_model->reser_table_key;
		$data=array(
			'addtime'=>TIME,
			'ip'=>$this->CI->input->ip_address(),
			$reser_table_key['phone']=>$phone,
			$reser_table_key['did']=>$did,
			$reser_table_key['cid']=>$cid,
			$reser_table_key['info']=>$this->dev2text($devinfo,$_pack,$isInColor),
		);
		$this->CI->yidong_model->insert('DR',$data);
		$info['status']=0;
		return $info;
	}
	function isReservation($phone){
		$where=array($this->CI->yidong_model->reser_table_key['phone']=>$phone);
		$limit=array('limit'=>1);
		$res=$this->CI->yidong_model->getReserData($where,$limit,false);
		if($res->num_rows()>0){
			$reser_data=$res->row();
			if($reser_data->addtime<(TIME+yidong_model::timeout)){
				$info['status']='10001';
				$info['error']='已经预约';
				$__info=$this->CI->yidong_model->reser_table_key['info'];
				$info['info']=$reser_data->$__info;
				return $info;
			}
		}
		return false;
	}
	function dev2text($devinfo,$_pack,$isInColor){
		$text="[机型：{$devinfo['name']}]\t\t";
		$text.="[机型颜色：{$isInColor['color']}]\n";
		$text.="套餐数据：\n";
		$package_fields=array('yonghujiaokuan','yuefanhuafei','zuidixiaofei','heyueqi','chanpin','chanpinneirong','yuebujiaofei');
		foreach ($package_fields as $i=>$key) {
			$text.="[".eval("return yidong_model::{$key};")."：{$_pack->$key}]".(($i%2==0 && $i>0)?"\n":"\t\t");	
		}
		return $text;
	}
}