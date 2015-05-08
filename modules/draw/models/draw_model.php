<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class draw_model extends Base_model
{
	function draw_model(){
		parent::Base_model();

		$this->_prefix = $this->config->item('ifengcms_table_prefix');
		$this->_TABLES = array(
			'D' => $this->_prefix . 'draw'
            ,'P' => $this->_prefix . 'draw_prize'
            ,'D2P' => $this->_prefix . 'draw_prize_rel'
            ,'DH'=>$this->_prefix . 'draw_history'
        );
	}
	function getDrawAllInfo($id){
		$res=$this->fetch('D','*',null,array('id'=>$id));
		if($res->num_rows()<1) return false;
		$info['draw']=$res->row();
		$this->db->select('*');
		$this->db->from($this->_TABLES['P']);
		$this->db->where("id in (select pid from {$this->_TABLES['D2P']} where did='{$id}')");
		$res=$this->db->get();
		$info['prize']=false;
		if($res->num_rows()>0){
			$info['prize']=$res->result();
		}
		$this->db->where('id',$id);
		$this->db->set('view_num','view_num+1',false);
		//$this->db->set('ack_num','ack_num+1',false);
		$this->db->update($this->_TABLES['D']);
		return $info;
	}
	function setDrawHistory($id,$openid,$name,$phone,$ceshiFlag){
		//$res=$this->fetch('DH','*',null,array('did'=>$id,'phone'=>$phone));
		$this->db->select('*');
		$this->db->from($this->_TABLES['DH']." DH");
		if($phone){
			$this->db->where(" did='$id' and (phone='{$phone}' or openid='{$openid}') ",null,false);
		}else{
			$this->db->where(" did='$id' and openid='{$openid}' ",null,false);
		}
		$res=$this->db->get();
		$data=array('id'=>0);
		$type=$ceshiFlag?1:0;
		if($res->num_rows()>0){
			$this->update('DH',array('phone'=>$phone,'name'=>$name,'type'=>$type),array('did'=>$id,'openid'=>$openid));
			$row=$res->row();
			$data['prize']=array(
				'status'=>$row->pid?1:0,
				'name'=>$row->pname,
				'time'=>date('Y-m-d H:i:s',$row->addtime)
			);
		}else{
			$this->insert('DH',array('did'=>$id,'phone'=>$phone,'name'=>$name,'addtime'=>TIME,'openid'=>$openid,'type'=>$type));
			$data['id']=$this->db->insert_id();
		}
		return $data;
	}
	function updatePrizeStockNum($id){
		$this->db->where('id',$id);
		$this->db->set('stock','stock-1',false);
		$this->db->update($this->_TABLES['P']);
	}
	function updateActivityPrizeNum($id,$field='ack_num'){
		$this->db->where('id',$id);
		$this->db->set($field,$field.'+1',false);
		$this->db->update($this->_TABLES['D']);
	}
	function getHistoryList($where = NULL, $limit = array('limit' => NULL, 'offset' => ''),$count=false){
		$where_fileds=array('DH.pid','DH.phone');
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
			$this->db->from($this->_TABLES['DH']." DH");

			$datarows=$this->db->count_all_results();
			$pagination=$this->autopage($datarows,$limit['limit']);
		}
		
		$this->db->select('DH.*');
		$this->db->from($this->_TABLES['DH'] ." DH");
		if( ! is_null($where))
		{
			if(is_string($where)){
				$this->db->where($where,null,false);
			}else{
				$this->db->where($where);
			}
			
		}
		if($count){
			if($autowhere){eval($autowhere);}
		}else{
			$this->autowhere($where_fileds);	
		}
		$this->db->order_by('DH.id','desc');
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
	function getActivityList($where = NULL, $limit = array('limit' => NULL, 'offset' => ''),$count=false){
		$where_fileds=array('title');
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

			$datarows=$this->db->count_all_results();
			$pagination=$this->autopage($datarows,$limit['limit']);
		}
		
		$this->db->select('D.*,GROUP_CONCAT(P.name) as prizesname');
		$this->db->from($this->_TABLES['D'] ." D");
		$this->db->join($this->_TABLES['D2P'] ." D2P",'D2P.did=D.id','left');
		$this->db->join($this->_TABLES['P'] ." P",'P.id=D2P.pid','left');
		if( ! is_null($where))
		{
			if(is_string($where)){
				$this->db->where($where,null,false);
			}else{
				$this->db->where($where);
			}
			
		}
		if($count){
			if($autowhere){eval($autowhere);}
		}else{
			$this->autowhere($where_fileds);	
		}
		$this->db->order_by('D.id','desc');
		$this->db->group_by('D.id');
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
	function getPrizeList($where = NULL, $limit = array('limit' => NULL, 'offset' => ''),$count=false){
		$where_fileds=array('title');
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
			$this->db->from($this->_TABLES['P']." P");

			$datarows=$this->db->count_all_results();
			$pagination=$this->autopage($datarows,$limit['limit']);
		}
		
		$this->db->select('P.*');
		$this->db->from($this->_TABLES['P'] ." P");
		if( ! is_null($where))
		{
			if(is_string($where)){
				$this->db->where($where,null,false);
			}else{
				$this->db->where($where);
			}
			
		}
		if($count){
			if($autowhere){eval($autowhere);}
		}else{
			$this->autowhere($where_fileds);	
		}
		$this->db->order_by('P.id','desc');
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
	function getLuckerForPhone($where, $limit ){
		$this->db->select('DH.*,P.*');
		$this->db->from($this->_TABLES['DH'] ." DH");
		$this->db->join($this->_TABLES['P'] ." P",'P.id=DH.pid','left');
		if( ! is_null($where))
		{
			if(is_string($where)){
				$this->db->where($where,null,false);
			}else{
				$this->db->where($where);
			}
			
		}
		$this->db->order_by('DH.id','desc');
		if( ! is_null($limit['limit']))
		{
			$this->db->limit($limit['limit'],( ($this->page!=0)?$this->page:$limit['offset']));
		}
		return $this->db->get();
	}
}