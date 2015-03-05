<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class fields_model extends Base_model
{
	function fields_model(){
		parent::Base_model();

		$this->_prefix = $this->config->item('ifengcms_table_prefix');
		$this->_TABLES = array(
			'F' => $this->_prefix . 'fields'
            ,'FE' => $this->_prefix . 'fields_ext'
        );
		$this->fileds_table_prefix=$this->_prefix.'fields_form_';
		$this->load->dbforge();
	}
	function create_table($name,$fields_arr){
		$tabname=$this->fileds_table_prefix.$name;
		$cflag=false;
		$tflag=$this->db->table_exists($tabname);
		if($tflag){
			$cflag=true;
			$this->db->select('count(*)',false);
			$this->db->from($tabname);
			$datarows=$this->db->count_all_results();
			$cflag=$datarows>0?true:false;
			if(!$cflag){
				$this->dbforge->drop_table($tabname);
			}
		}
		//不存在表则创建表
		if(!$cflag){
			$fields = array(
                'id' => array(
                     'type' => 'INT',
                     'constraint' => 10, 
                     'unsigned' => TRUE,
                     'auto_increment' => TRUE,
                     'comment' =>'ID'
                 ),
                 'addtime'=>array(
				 	'type' => 'INT',
                     'constraint' => 10,
                     'comment' =>'添加时间'
				 ),
                 'ip'=>array(
				 	'type' => 'VARCHAR',
                     'constraint' => 15,
                     'comment' =>'IP'
				 ),
				 'status'=>array(
				 	'type' => 'TINYINT',
				 	'constraint' => 1,
				 	'default'=>1,
                    'comment' =>'状态' 
				 ),
				 'fromaddr'=>array(
				 	'type' => 'VARCHAR',
				 	'constraint' => 150,
                    'comment' =>'来源'
				 ),
				 'openid'=>array(
				 	'type' => 'VARCHAR',
				 	'constraint' => 150,
                    'comment' =>'OPENID'
				 ),
				 'source'=>array(
				 	'type' => 'INT',
				 	'constraint' => 10,
				 	'default'=>0,
                    'comment' =>'答题得分'
				 ),
				 
            );
			foreach ($fields_arr as $dbkey => $value) {
				if(!trim($dbkey)) continue;
				$fields[$dbkey]=array(
                     'type' => 'VARCHAR',
                     'constraint' => 500,
                     'comment' =>$value['label']
                 );
			}
			$this->dbforge->add_field($fields);
			$this->dbforge->add_key('id', TRUE); 
			$this->dbforge->create_table($tabname,true);
			return true;
		}
		return false;
	}
	function insert_fields_tabdata($name,$data){
		$tabname=$this->fileds_table_prefix.$name;
		$tflag=$this->db->table_exists($tabname);
		if(!$tflag){
			return array('status'=>10000,'error'=>'表单错误，需要管理员重新设置');
		}
		$this->db->select('count(*)',false);
		$this->db->from($tabname);
		$this->db->where('ip',$data['ip']);
		$datarows=$this->db->count_all_results();
		if($datarows>50){
			return array('status'=>10000,'error'=>'提交失败，你的IP提交人数太多了');
		}
		$this->db->insert($tabname,$data);
		return array('status'=>0,'data'=>array('id'=>$this->db->insert_id()));
	}
	function update_fields_tabdata($name,$data,$where){
		$tabname=$this->fileds_table_prefix.$name;
		$tflag=$this->db->table_exists($tabname);
		if(!$tflag){
			return array('status'=>10000,'error'=>'表单错误，需要管理员重新设置');
		}
		$this->db->where($where);
		foreach ($data as $key => $value) {
			$this->db->set($key,$value,!($key=='source'));
		}
		$this->db->update($tabname);
		return array('status'=>0,'rows'=>$this->db->affected_rows(),'data'=>array('id'=>$where['id']));
	}
	function getFieldsList($where = NULL, $limit = array('limit' => NULL, 'offset' => ''),$count=false){
		$where_fileds=array('F.name','F.id');
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
			$this->db->from($this->_TABLES['F']." F");

			$datarows=$this->db->count_all_results();
			$pagination=$this->autopage($datarows,$limit['limit']);
		}
		
		$this->db->select('F.*');
		$this->db->from($this->_TABLES['F'] ." F");
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
		$this->db->order_by('F.id','desc');
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
	function getFieldsDataList($table,$where = NULL, $limit = array('limit' => NULL, 'offset' => ''),$count=false,$order='id desc'){
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
			$this->db->from($table);

			$datarows=$this->db->count_all_results();
			$pagination=$this->autopage($datarows,$limit['limit']);
		}
		
		$this->db->select('*');
		$this->db->from($table);
		if( ! is_null($where))
		{
			if(is_string($where)){
				$this->db->where($where,null,false);
			}else{
				$this->db->where($where);
			}
			
		}
		if($autowhere){eval($autowhere);}
		$this->db->order_by($order);
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
	function deleteField($fid){
		$res=$this->fields_model->fetch('F','*',null,array('id'=>$fid));
		if($res->num_rows()>0){
			$field=$res->row();
			$tabname=$this->fields_model->fileds_table_prefix.$field->tab_name;
			$tflag=$this->db->table_exists($tabname);
			if($tflag){
				$this->db->select('count(*)',false);
				$this->db->from($tabname);
				$datarows=$this->db->count_all_results();
				if($datarows>0){
					return array('status'=>10000,'error'=>'已经存在记录，此表单不能删除');
				}else{
					$this->dbforge->drop_table($tabname);
				}
			}
			$this->fields_model->delete('F',array('id'=>$fid));
		}
		return array('status'=>0);
	}
}