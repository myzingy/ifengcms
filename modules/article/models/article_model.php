<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * BackendPro
 *
 * An open source development control panel written in PHP
 *
 * @package		BackendPro
 * @author		Adam Price
 * @copyright	Copyright (c) 2008, Adam Price
 * @license		http://www.gnu.org/licenses/lgpl.html
 * @link		http://www.kaydoo.co.uk/projects/backendpro
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * User_model
 *
 * Provides functionaly to query all tables related to the
 * user.
 *
 * @package   	BackendPro
 * @subpackage  Models
 */
class Article_model extends Base_model
{
	function Article_model()
	{
		parent::Base_model();

		$this->_prefix = $this->config->item('ifengcms_table_prefix');
		$this->_TABLES = array(    'A' => $this->_prefix . 'article',
                                    'AC' => $this->_prefix . 'article_content',
                                    'AP' => $this->_prefix . 'article_pop',
                                    'C'=>$this->_prefix . 'classify',
                                    'ACL'=>$this->_prefix . 'article_classify',
                                    );
	}
	function getArticleAllInfo($aid){
		$this->db->select('*',false);
		$this->db->from($this->_TABLES['A']." A");
		$this->db->join($this->_TABLES['AC'] ." AC",'AC.aid=A.id','left');
		$this->db->where(array('A.id'=>$aid));
		$res=$this->db->get();
		$info=array();
		$info['classify']=array();
		if($res->num_rows()>0){
			$info=$res->row_array();
			$this->db->select('*',false);
			$this->db->from($this->_TABLES['ACL']." ACL");
			$this->db->where(array('ACL.aid'=>$aid));
			$res=$this->db->get();
			if($res->num_rows()>0){
				foreach ($res->result() as $r) {
					$info['classify'][]=$r->cid;
				}
			}
		}
		return $info;
	}
	function getArticleList($where = NULL, $limit = array('limit' => NULL, 'offset' => ''),$count=false,$like=null){
		$where_fileds=array('title');
		if($count){
			if( ! is_null($where))
			{
				$this->db->where($where);
			}
			$autowhere=$this->autowhere($where_fileds);	
			$this->db->select('count(*)',false);
			$this->db->from($this->_TABLES['A']." A");
			$datarows=$this->db->count_all_results();
			$pagination=$this->autopage($datarows,$limit['limit']);
		}
		
		
		$this->db->from($this->_TABLES['A'] ." A");
		
		if( ! is_null($where))
		{
			$this->db->where($where);
		}
		if($like['A.position'])
		{
			$this->db->like($like);
		}
		if($autowhere){eval($autowhere);}
		$this->db->order_by('A.order','desc');
		$this->db->order_by('A.id','desc');
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
	function classifyData($type='R'){
		$file=BASEPATH."cache/classify";
		if($type=='R'){
			$data=@include($file);
			return $data?$data:array();
		}
		if($type=='W'){
			$info=$this->fetch('C','*',null,null);
			$data=array();
			if($info->num_rows()>0){
				foreach($info->result() as $r){
					$data[$r->id]=$r->name;
				}
			}
			$data="<?php\nreturn ".var_export($data,true).";\n";
			@file_put_contents($file,$data);
		}
	}	
}

/* End of file: user_model.php */
/* Location: ./modules/auth/controllers/admin/user_model.php */