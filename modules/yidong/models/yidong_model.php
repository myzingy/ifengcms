<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class yidong_model extends Base_model
{
	const yonghujiaokuan='用户缴款';
	const yuefanhuafei='月返还话费';
	const zuidixiaofei='最低消费';
	const heyueqi='合约期';
	const chanpin='产品';
	const chanpinneirong='产品内容';
	public $ZHENGCE=array(
		'1'=>'一年免费打',
		'2'=>'7折租机',
	);
	const timeout=86400;
	function yidong_model(){
		parent::Base_model();
		$cms_prefix = $this->config->item('ifengcms_table_prefix');
		$yidong_prefix='yidong_';
		$this->_TABLES = array(
			'D' => $yidong_prefix . 'devices',
			'DC'=> $yidong_prefix . 'devices_color',
			'D2C'=> $yidong_prefix . 'devices_classify',
			'UP'=>$yidong_prefix . 'userphone',
			'DR'=> $cms_prefix.'fields_form_85c109b6ae9ba50c38a67611158f0040',//预约表
        );
		$this->reser_table_key=array(
			'did'=>'vde798837c018',
			'cid'=>'v301e4029e7d9',
			'info'=>'v32610fd7b2d9',
		);
	}
	function getDevicesInfo($did){
		$data=array();
		$res=$this->fetch('D','*',null,array('id'=>$did));
		if($res->num_rows>0){
			$data=$res->row_array();
			$data['color']=array();
			
			$res=$this->getDeviceColor($did);
			if($res->num_rows>0){
				foreach ($res->result_array() as $i=>$row) {
					$row['last_stock']=$row['stock']-$row['reserNum'];
					$row['img']=$row['img']?(base_url().$row['img']):'';
					$data['color'][]=$row;
				}
			}
			
			$res=$this->fetch('D2C','*',null,array('did'=>$did));
			$data['classify']=array();
			if($res->num_rows>0){
				foreach ($res->result() as $row) {
					$data['classify'][]=$row->cid;
				}
			}
		}
		return $data;
	}
	function getDeviceColor($did){
		$this->db->select('DC.*,count(DR.id) as reserNum');
		$this->db->from($this->_TABLES['DC'] ." DC");
		$this->db->join($this->_TABLES['DR'] ." DR",'DR.'.($this->reser_table_key['cid']).'=DC.id and DR.addtime>'.(TIME-self::timeout),'left');
		$this->db->group_by('DC.id');
		$this->db->where('did',$did);
		return $this->db->get();
	}
	function getDevicesList($where = NULL, $limit = array('limit' => NULL, 'offset' => ''),$count=false,$classify=0){
		$where_fileds=array('D.name');
		if($count){
			if( ! is_null($where))
			{
				if(is_string($where)){
					$this->db->where($where,null,false);
				}else{
					$this->db->where($where);
				}
				
			}
			$autowhere=$this->autowhere($where_fileds);	
			$this->db->select('count(*)',false);
			$this->db->from($this->_TABLES['D']." D");
			if($classify>0){
				$this->db->where("D.id in (select did from {$this->_TABLES['D2C']} where cid=$classify)");
			}
			$datarows=$this->db->count_all_results();
			$pagination=$this->autopage($datarows,$limit['limit']);
		}
		
		//$this->db->select('D.*,count(DR.id) as reser_num');
		$this->db->from($this->_TABLES['D'] ." D");
		if( ! is_null($where))
		{
			if(is_string($where)){
				$this->db->where($where,null,false);
			}else{
				$this->db->where($where);
			}
			
		}
		//$this->db->join($this->_TABLES['DR'] ." DR",'DR.did=D.id','left');
		if($autowhere){eval($autowhere);}
		if($classify>0){
			$this->db->where("D.id in (select did from {$this->_TABLES['D2C']} where cid=$classify)");
		}
		$this->db->order_by('D.order','desc');
		$this->db->order_by('D.id','desc');
		
		if( ! is_null($limit['limit']))
		{
			$this->db->limit($limit['limit'],( ($this->page!=0)?$this->page:$limit['offset']));
		}
		if($count){
			return array(
				'datarows'=>$datarows,
				'pagination'=>$pagination,
				'data'=>$this->db->get()
			);
		}
		return $this->db->get();
	}
	function _insert_D2C($data){
		foreach ($data as $value) {
			$this->db->insert($this->_TABLES['D2C'],$value);
		}
	}
	function unlink_device2color($did,$cids){
		$this->db->where('did',$did);
		$this->db->where_not_in('id',$cids);
		$this->db->delete($this->_TABLES['DC']);
	}
}