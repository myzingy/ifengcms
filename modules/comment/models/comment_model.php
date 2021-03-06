<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class comment_model extends Base_model
{
	function comment_model(){
		parent::Base_model();

		$pro_prefix = $this->config->item('backendpro_table_prefix');
		$cms_prefix = $this->config->item('ifengcms_table_prefix');
		$this->_TABLES = array(
			'UP' => $pro_prefix . 'user_profiles'
			,'C'=> $cms_prefix . 'comment'
			,'A'=> $cms_prefix . 'article'
        );
		$this->_TYPE=array(
			'1'=>'点评评论'
			,'2'=>'点评新闻'
			,'3'=>'点评专题'
		);
	}
	function getCommentList($where = NULL, $limit = array('limit' => NULL, 'offset' => ''),$count=false,$articleTitle=''){
		$where_fileds=array('C.type','C.subject');
		$uid=$this->session->userdata(id);
		if($count){
			if( ! is_null($where))
			{
				$this->db->where($where);
			}
			//$this->db->or_where('C.uid',$uid);
			$autowhere=$this->autowhere($where_fileds);	
			$this->db->select('count(*)',false);
			$this->db->from($this->_TABLES['C']." C");
			if($articleTitle){
				$this->db->where("C.aid in (select id from {$this->_TABLES['A']} where title like '%$articleTitle%')");
			}
			$datarows=$this->db->count_all_results();
			$pagination=$this->autopage($datarows,$limit['limit']);
		}else{
			$this->autopage(10000,$limit['limit']);
		}
		
		$this->db->select("C.*,UP.nickname");
		$this->db->from($this->_TABLES['C'] ." C");
		$this->db->join($this->_TABLES['UP'] ." UP","UP.user_id=C.uid",'left');
		if( ! is_null($where))
		{
			if(is_string($where)){
				$this->db->where($where,null,false);
			}else{
				$this->db->where($where);
			}
			
		}
		if($autowhere){eval($autowhere);}
		//$this->db->or_where('C.uid',$uid);
		$this->db->order_by('C.id','desc');
		if($articleTitle){
				$this->db->where("C.aid in (select id from {$this->_TABLES['A']} where title like '%$articleTitle%')");
			}
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
	//我的跟帖&评论我的
	function getCommentListForUser($where = NULL, $limit = array('limit' => NULL, 'offset' => ''),$count=false){
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
			$this->db->from($this->_TABLES['C']." C");
			$datarows=$this->db->count_all_results();
			$pagination=$this->autopage($datarows,$limit['limit']);
		}else{
			$this->autopage(10000,$limit['limit']);
		}
		
		$this->db->select("C.*,A.title,A.type,A.url,UP.nickname,UP.headimgurl");
		$this->db->from($this->_TABLES['C'] ." C");
		$this->db->join($this->_TABLES['A'] ." A",'A.id=C.aid','left');
		$this->db->join($this->_TABLES['UP'] ." UP",'UP.user_id=C.uid','left');
		if( ! is_null($where))
		{
			if(is_string($where)){
				$this->db->where($where,null,false);
			}else{
				$this->db->where($where);
			}
			
		}
		if($autowhere){eval($autowhere);}
		$this->db->order_by('C.id','desc');
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