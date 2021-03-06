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
	public $_TYPE=array(
		1=>array('name'=>'新闻','tpl'=>'details','block'=>''),
		2=>array('name'=>'专题','tpl'=>'details','block'=>'<b class="label">专题</b>'),
		3=>array('name'=>'互动','tpl'=>'details_interact','block'=>''),
		4=>array('name'=>'推广','tpl'=>'details_ads','block'=>'<b class="label">推广</b>'),
		5=>array('name'=>'微信关键字','tpl'=>'details_ads','block'=>'<b class="label">微信关键字</b>'),
	);	
	function __construct()
	{
		parent::__construct();

		$pro_prefix = $this->config->item('backendpro_table_prefix');
		$cms_prefix = $this->config->item('ifengcms_table_prefix');
		$this->_TABLES = array(    'A' => $cms_prefix . 'article',
                                    'AC' => $cms_prefix . 'article_content',
                                    'C'=>$cms_prefix . 'classify',
                                    'ACL'=>$cms_prefix . 'article_classify',
                                    'AA'=>$cms_prefix . 'article_applaud',//赞
                                    'CT'=>$cms_prefix . 'comment',//评论
                                    'AF'=>$cms_prefix . 'article_favorites',//收藏
                                    'UP' => $pro_prefix . 'user_profiles'
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
	function getArticleList($where = NULL, $limit = array('limit' => NULL, 'offset' => ''),$count=false,$classify=0){
		$where_fileds=array('A.title');
		if($count){
			$this->db->where('A.type<>',5,false);
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
			$this->db->from($this->_TABLES['A']." A");
			if($classify>0){
				$this->db->where("A.id in (select aid from {$this->_TABLES['ACL']} where cid=$classify)");
			}
			$datarows=$this->db->count_all_results();
			$pagination=$this->autopage($datarows,$limit['limit']);
		}
		
		$this->db->select('A.*,count(CT.id) as comment');
		$this->db->from($this->_TABLES['A'] ." A");
		$this->db->where('A.type<>',5,false);
		if( ! is_null($where))
		{
			if(is_string($where)){
				$this->db->where($where,null,false);
			}else{
				$this->db->where($where);
			}
			
		}
		$this->db->join($this->_TABLES['CT'] ." CT",'CT.aid=A.id','left');
		if($autowhere){eval($autowhere);}
		$this->db->order_by('A.order','desc');
		$this->db->order_by('A.id','desc');
		$this->db->group_by('A.id');
		if($classify>0){
			$this->db->where("A.id in (select aid from {$this->_TABLES['ACL']} where cid=$classify)");
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
	function getArticleContentList($where = NULL, $limit = array('limit' => NULL, 'offset' => ''),$count=false,$classify=0){
		$where_fileds=array('A.title');
		if($count){
			if( ! is_null($where))
			{
				$this->db->where($where);
			}
			$autowhere=$this->autowhere($where_fileds);	
			$this->db->select('count(*)',false);
			$this->db->from($this->_TABLES['A']." A");
			if($classify>0){
				$this->db->where("A.id in (select aid from {$this->_TABLES['ACL']} where cid=$classify)");
			}
			$datarows=$this->db->count_all_results();
			$pagination=$this->autopage($datarows,$limit['limit']);
		}
		
		$this->db->select('A.*,AC.content,count(CT.id) as comment,count(AA.id) as applaud,count(AF.id) as favorites,UP.nickname,UP.headimgurl');
		$this->db->from($this->_TABLES['A'] ." A");
		
		if( ! is_null($where))
		{
			$this->db->where($where);
		}
		$this->db->join($this->_TABLES['CT'] ." CT",'CT.aid=A.id','left');
		$this->db->join($this->_TABLES['AC'] ." AC",'AC.aid=A.id','left');
		$this->db->join($this->_TABLES['AA'] ." AA",'AA.aid=A.id','left');
		$this->db->join($this->_TABLES['AF'] ." AF",'AF.aid=A.id','left');
		$this->db->join($this->_TABLES['UP'] ." UP",'UP.user_id=A.uid','left');
		if($autowhere){eval($autowhere);}
		$this->db->order_by('A.order','desc');
		$this->db->order_by('A.id','desc');
		$this->db->group_by('A.id');
		if($classify>0){
			$this->db->where("A.id in (select aid from {$this->_TABLES['ACL']} where cid=$classify)");
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
	function classifyData($type='R'){
		$file=BASEPATH."cache/classify";
		if($type=='R' || is_int($type)){
			$data=@include($file);
			$data=$data?$data:array();
			if($type!='R'){
				return $data[$type];
			}
			return $data;
		}
		if($type=='W'){
			$info=$this->fetch('C','*',null,null);
			$data=array();
			if($info->num_rows()>0){
				foreach($info->result() as $r){
					$data[$r->type][$r->id]=$r->name;
				}
			}
			$data="<?php\nreturn ".var_export($data,true).";\n";
			@file_put_contents($file,$data);
		}
	}
	function getUrl($id,$type="",$url=""){
		if($url) return $url;
		$page=$this->_TYPE[$type]['tpl']?$this->_TYPE[$type]['tpl']:'details';
		return base_url().'mnew/'.$page.'.html?id='.$id;
	}
	//通过关键字获取文章列表
	function getArticleForKeyword($key=''){
		$key=trim($key);	
		if(!$key) return false;
		$this->db->select('*');
		$this->db->from($this->_TABLES['A']);
		$this->db->where('status',0);
		$this->db->where('type<>',5,false);
		$this->db->like('title',$key);
		$this->db->order_by('id','desc');
		$this->db->limit(10);
		$res=$this->db->get();
		if($res->num_rows()==0) return false;
		return $res;
	}
	//通过关键字获取文章内容
	function getArticleForWXkeyword($key=''){
		$key=trim($key);	
		if(!$key) return false;
		
		$this->db->select('A.*,AC.content');
		$this->db->from($this->_TABLES['A'] ." A");
		$this->db->join($this->_TABLES['AC'] ." AC",'AC.aid=A.id','left');
		$this->db->where('type',5);
		$this->db->like('title',$key);
		$this->db->order_by('id','desc');
		$this->db->limit(10);
		$res=$this->db->get();
		if($res->num_rows()==0) return false;
		return $res;
	}
}

/* End of file: user_model.php */
/* Location: ./modules/auth/controllers/admin/user_model.php */