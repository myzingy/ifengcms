<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class vote_model extends Base_model
{
	public $codenum=2000;//数据表自增起始数字，用户编号判断基值
	public $TP_STR='PP';
	function vote_model(){
		parent::Base_model();

		$this->_prefix = $this->config->item('ifengcms_table_prefix');
		$this->_TABLES = array(
			'V' => $this->_prefix . 'vote'
            ,'VM' => $this->_prefix . 'vote_members'
            ,'VM_EXT' => $this->_prefix . 'vote_members_ext'
            ,'VH' => $this->_prefix . 'vote_history'
            ,'VS' => $this->_prefix . 'vote_source'
        );
	}
	function getVoteMemberID($obj,$fix=false){
		$fix=$fix?$this->TP_STR:'';
		return $fix.($obj->code?$obj->code:$obj->id);
	}
	function getVoteMemberName($obj,$fix=false){
		$fix=$fix?$this->TP_STR:'';
		return $obj->name?$obj->name:($fix.($obj->code?$obj->code:$obj->id));
	}
	function getVoteForNum($param){
		if(!is_array($param)){
			$param=array('snum'=>$param);
			$where=" 1 ";
		}else{
			$where=" (".TIME.">=stime && ".TIME."<=etime) ";
		}
		if($param['snum']>0 && $param['enum']>0){
			$where.=" AND (({$param['snum']}>=snum AND {$param['snum']}<=enum) OR ({$param['enum']}>=snum AND {$param['enum']}<=enum))";
		}else{
			$where.=" AND ({$param['snum']}>=snum AND {$param['snum']}<=enum) ";
		}
		if($param['noid']>0){
			$where.=" AND id<>{$param['noid']} ";
		}
		$this->db->select('*',false);
		$this->db->from($this->_TABLES['V']." V");
		$this->db->where($where,null,false);
		$this->db->order_by('etime desc,id desc');
		$res=$this->db->get();
		if($res->num_rows()>0){
			return $res->row();
		}
		return false;
	}
	
	function getVoteList($where = NULL, $limit = array('limit' => NULL, 'offset' => ''),$count=false){
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
			$this->db->from($this->_TABLES['V']." V");

			$datarows=$this->db->count_all_results();
			$pagination=$this->autopage($datarows,$limit['limit']);
		}
		
		$this->db->select('V.*');
		$this->db->from($this->_TABLES['V'] ." V");
		if( ! is_null($where))
		{
			if(is_string($where)){
				$this->db->where($where,null,false);
			}else{
				$this->db->where($where);
			}
			
		}
		if($autowhere){eval($autowhere);}
		$this->db->order_by('V.id','desc');
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
	function getVoteDataList($where = NULL, $limit = array('limit' => NULL, 'offset' => ''),$count=false,$order='code desc,id desc'){
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
			$this->db->from($this->_TABLES['VM']." VM");

			$datarows=$this->db->count_all_results();
			$pagination=$this->autopage($datarows,$limit['limit']);
		}
		
		$this->db->select('VM.*');
		$this->db->from($this->_TABLES['VM'] ." VM");
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
	public function startSource($vid,$source_id){
		//获取数据源配置信息
		$res=$this->fetch('VS','*',null,array('vid'=>$vid,'source_id'=>$source_id));
		if($res->num_rows()<1) return array('status'=>10000,'error'=>'指定数据源不存在');
		$vote_source=$res->row();
		//获取投票信息
		$res=$this->getVoteList(array('id'=>$vid),null,false);
		if($res->num_rows()<1) return array('status'=>10000,'error'=>'指定投票活动不存在');
		$vote_data=$res->row();
		//是否需要使用号段
		$iscode=$vote_data->snum && $vote_data->enum;
		if($iscode){
			$code=$vote_data->snum-1;
			$res=$this->getVoteDataList(array('vid'=>$vid),array('limit'=>1),false);
			if($res->num_rows()>0){
				$code=$res->row()->code;
			}
		}
		//获得数据源数据
		$this->autopage(true);
		$page=$this->page+0;
		$ctx = stream_context_create(array('http' => array('timeout' => 30)));
		$text=file_get_contents(site_url('fields/fieldsDataList/'.$source_id.'/page/'.$page),0,$ctx);
		if($text){
			$json=@json_decode($text);
			if($json->data->data){
				//插入数据
				foreach($json->data->data as $r){
					$check=$this->fetch('VM','*',null,array('vid'=>$vid,'remoteid'=>$r->id));
					if($check->num_rows()>0){
						$member_id=$check->row()->id;
						//修改老数据
						$member=array();
						if($source_name=$vote_source->source_name){
							$source_name_arr=explode(',',$source_name);
							foreach ($source_name_arr as $key) {
								$member['name'][]=$r->$key;
							}
							$member['name']=implode('/',$member['name']);
						}
						if($source_pic=$vote_source->source_pic){
							$member['thumb']=$r->$source_pic;
						}
						$this->update('VM',$member,array('vid'=>$vid,'remoteid'=>$r->id));
					}else{
						$member=array(
							'vid'=>$vid,
							'remoteid'=>$r->id,
						);
						//号段模式下是否会溢出
						if($iscode){
							$code+=1;
							if($code>$vote_data->enum){
								return array('status'=>10000,'error'=>'该投票活动号段已饱和，请增加号段后进行导入');
							}
							$member['code']=str_pad($code, strlen($vote_data->enum), "0", STR_PAD_LEFT);
						}
						
						if($source_name=$vote_source->source_name){
							$source_name_arr=explode(',',$source_name);
							foreach ($source_name_arr as $key) {
								$member['name'][]=$r->$key;
							}
							$member['name']=implode('/',$member['name']);
						}
						if($source_pic=$vote_source->source_pic){
							$member['thumb']=$r->$source_pic;
						}
						$this->insert('VM',$member);
						$member_id=$this->db->insert_id();
					}
					//处理扩展表数据
					$this->delete('VM_EXT',array('mid'=>$member_id));
					if($source_pic_ext=$vote_source->source_pic_ext){
						$source_pic_arr=explode(',', $source_pic_ext);
						foreach ($source_pic_arr as $field_ext) {
							if(!$r->$field_ext) continue;
							$member_ext=array(
								'mid'=>$member_id,
								'type'=>'thumb',
								'data'=>$r->$field_ext,
							);
							$this->insert('VM_EXT',$member_ext);
						}
					}
					
					
				}
				//更新绑定源
				/*
				$run_page=$json->data->limit>$json->data->rows?$json->data->rows:$json->data->limit;
				$this->db->set('run_page', 'run_page+'.$run_page, FALSE);
				$this->db->where('id',$vote_source->id);
				$this->db->update($this->_TABLES['VS']);
				 * 
				 */
				return array(
					'isnext'=>$json->data->isnext,
					'limit'=>$json->data->limit,
					'rows'=>$json->data->rows,
					'status'=>$json->data->status,
				);
			}else{
				return array('status'=>10000,'error'=>'该数据源没有新的数据');
			}
			
		}
		return array('status'=>10000,'error'=>'获取数据失败，请稍后再试');
		
	}
}