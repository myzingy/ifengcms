<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class special_model extends Base_model
{
	function special_model(){
		parent::Base_model();

		$this->_prefix = $this->config->item('ifengcms_table_prefix');
		$this->_TABLES = array(
			'S' => $this->_prefix . 'article'
        );
	}
	function getSpecialList($where = NULL, $limit = array('limit' => NULL, 'offset' => ''),$count=false,$like=null){
		$where_fileds=array('title');
		if($count){
			if( ! is_null($where))
			{
				$this->db->where($where);
			}
			$autowhere=$this->autowhere($where_fileds);	
			$this->db->select('count(*)',false);
			$this->db->from($this->_TABLES['S']." S");
			$datarows=$this->db->count_all_results();
			$pagination=$this->autopage($datarows,$limit['limit']);
		}
		
		
		$this->db->from($this->_TABLES['S'] ." S");
		
		if( ! is_null($where))
		{
			$this->db->where($where);
		}
		if($autowhere){eval($autowhere);}
		$this->db->order_by('S.id','desc');
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
}