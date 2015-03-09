<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class praise_model extends Base_model
{
	function praise_model(){
		parent::Base_model();

		$this->_prefix = $this->config->item('ifengcms_table_prefix');
		$this->_TABLES = array(
			'P' => $this->_prefix . 'praise'
        );
	}
	function count($pkid){
		$this->db->select('count(*) as count,type');
		$this->db->from($this->_TABLES['P']);
		$this->db->group_by('type');
		$this->db->where('pkid',$pkid);
		$this->db->order_by('type','asc');
		return $this->db->get();
	}
	function getList($where = NULL, $limit = array('limit' => NULL, 'offset' => ''),$count=false){
		$where_fileds=array();
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
			$this->db->group_by('P.pkid');
			$datarows=$this->db->count_all_results();
			$pagination=$this->autopage($datarows,$limit['limit']);
		}
		
		$this->db->select('P.*,count(P.id) as count');
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
		$this->db->group_by('P.pkid');
		if($count){
			return array(
				'datarows'=>$datarows,
				'pagination'=>$pagination,
				'data'=>$this->db->get()
			);
		}
		return $this->db->get();
	}
}