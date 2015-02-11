<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class fields extends Admin_Controller
{
	/**
	 * Constructor
	 */
	function fields(){
		parent::Admin_Controller();
		// Load the Auth_form_processing class
		$this->load->library('fields_lib');
	}
	function act($action,$id=0){
		$info=array();
		$this->fields_lib->status($info,10000);
		$check=$this->fields_lib->check();
		if($check){
			$prarm=func_get_args();	
			$info=array(
				'get'=>$prarm
				,'post'=>$_POST
			);
			$this->fields_lib->status($info,10000);
			
			if(method_exists($this->fields_lib,$action)){
				$info=call_user_func(array(&$this->fields_lib, $action),$id);
				$info['error']=$info['error']?$info['error']:'';
				$info['act']=$action;
			}else{
				$this->fields_lib->status($info,10001,__CLASS__.'->'.$action.'() 没有定义');
			}
		}else{
			$this->fields_lib->status($info,$check->status,$check->error);
		}
		die(json_encode($info));
	}
	function index(){
		$limit=array('offset'=>$page,'limit'=>15);
		$where=array();
		$info=$this->fields_model->getFieldsList($where,$limit,true);
		
		$data['members'] = $info['data'];
		$data['pagination']=$info['pagination'];
		// Display Page
		$data['header'] = '表单管理';
		$data['page'] = $this->config->item('backendpro_template_dir') . "list_fields";
		$data['module'] = 'fields';
		$this->load->view($this->_container,$data);
	}
	function formdata($fid=0){
		$res=$this->fields_model->fetch('F','*',null,array('id'=>$fid));
		if($res->num_rows()>0){
			$field=$res->row();
			$table=$this->fields_model->fileds_table_prefix.$field->tab_name;
			$data['fid']=$fid;
			$data['dbkey']=json_decode($field->fields_json,true);
			$data['fields']=$this->fields_model->db->list_fields($table);
			$data['tab_index']=$fid;
		}
		$limit=array('offset'=>$page,'limit'=>15);
		$where=array();
		$info=$this->fields_model->getFieldsDataList($table,$where,$limit,true);
		
		$data['members'] = $info['data'];
		$data['pagination']=$info['pagination'];
		// Display Page
		$data['header'] = $field->name.'【数据列表】';
		$data['page'] = $this->config->item('backendpro_template_dir') . "list_fields_data";
		$data['module'] = 'fields';
		$this->load->view($this->_container,$data);
	}
	function total($fid=0){
		$res=$this->fields_model->fetch('F','*',null,array('id'=>$fid));
		if($res->num_rows()>0){
			$field=$res->row();
			$table=$this->fields_model->fileds_table_prefix.$field->tab_name;
			$data['fid']=$fid;
			$data['dbkey']=json_decode($field->fields_json,true);
			$data['fields']=$this->fields_model->db->list_fields($table);
			$data['tab_index']=$fid;
		}
		// Display Page
		$data['header'] = $field->name.'【统计分析】';
		$data['page'] = $this->config->item('backendpro_template_dir') . "list_fields_total";
		$data['module'] = 'fields';
		$this->load->view($this->_container,$data);
	}
	function totaldata(){
		$fid=$this->input->post('fid');
		$stime=$this->input->post('stime');
		$etime=$this->input->post('etime');
		$dbkey=$this->input->post('dbkey');
		$res=$this->fields_model->fetch('F','*',null,array('id'=>$fid));
		$total['status']=10000;
		if($res->num_rows()>0){
			$field=$res->row();
			$tabname=$this->fields_model->fileds_table_prefix.$field->tab_name;
			$tflag=$this->db->table_exists($tabname);
			$fields_json=json_decode($field->fields_json,true);
			$fields_json['fromaddr']['label']='来源';
			$fields_json['addtime']['label']='添加日期';
			if($tflag){
				$limit=array('offset'=>$page,'limit'=>5000);
				$where=' 1=1 ';
				if($stime){
					$where.=' and addtime > '.strtotime($stime);
				}
				if($etime){
					$where.=' and addtime < '.strtotime($etime." 23:59:59");
				}
				$info=$this->fields_model->getFieldsDataList($tabname,$where,$limit,true);
				
				//var_dump($this->fields_model->db->last_query());
				if($info['datarows']>0){
					$total['status']=0;
					$total['rows']=$info['datarows'];
					$total['data']=array();
					$total['limit']=$this->fields_model->page+$limit['limit'];
					$total['isnext']=$info['data']->num_rows<$limit['limit']?false:true;
					
					foreach ($info['data']->result() as $r) {
						foreach ($dbkey as $key) {
							
							if($str=$r->$key){
								if($key=='addtime' || ($r->$key>1234567890 && $r->$key<9999999999)){
									$str=date('Y-m-d',$r->$key);
								}
								if(empty($total['data'][$key])){
									$total['data'][$key]=array(
										'label'=>$fields_json[$key]['label'],
										'options'=>array()
									);
								}
								$arr=explode(',',$str);
								foreach ($arr as $value) {
									$total['data'][$key]['options'][$value]+=1;
								}
							}
						}
					}
				}
			}
		}
		die(json_encode($total));
	}
	function switchStatus($fid,$status){
		$res=$this->fields_model->fetch('F','*',null,array('id'=>$fid));
		$select=$this->input->post('select');
		if($res->num_rows()>0 && $select){
			$field=$res->row();
			$table=$this->fields_model->fileds_table_prefix.$field->tab_name;
			foreach ($select as $id) {
				$this->fields_model->db->update($table,array('status'=>$status),array(
					'id'=>$id
				));
			}
		}
		redirect($_SERVER['HTTP_REFERER'],'location');
	}
	function deleteField($fid){
		if(!check('diyfields','delete',false)){
			$info=array('status'=>10000,'error'=>'你没有权限操作');
		}else{
			$info=$this->fields_model->deleteField($fid);
		}
		die(json_encode($info));
	}
	///////////
	function getTermData(){
		/*
		$db=$this->fields_model->db;
		$res=$db->query("SELECT GROUP_CONCAT(id) as checked from `ifengcms`.`cms_fields_form_8ef4feba8121808e205865b62ff3323a` where status=0");
		$checked=$res->row()->checked;
		$sql="truncate table `ifengcms`.`cms_fields_form_8ef4feba8121808e205865b62ff3323a`";
		$db->query($sql);
		$sql="insert into `ifengcms`.`cms_fields_form_8ef4feba8121808e205865b62ff3323a` 
		(`id`,`v6ce21a3cc61f`,`v960ef26b1eac`,`veb17a0e15b39`,`v616dad39708b`,`addtime`) 
		select `team_id`,`name`,concat('http://www.duduxy.com/',`avatar`),`description`,`members_total`,`created_time` from `wisheli_mall`.`ecm_team`";
		$db->query($sql);
		if($checked){
			//还原之前OK状态
			$db->query("UPDATE `ifengcms`.`cms_fields_form_8ef4feba8121808e205865b62ff3323a` SET `status`=0 WHERE `id` in ($checked)");
		}
		*/
		$timeout = array(
			'http' => array(
				'timeout' => 30
			)
		);
		
		$ctx = stream_context_create($timeout);
		$json = file_get_contents("http://www.duduxy.com/index.php?app=teamlist&act=json&total=0", 0, $ctx);
		if($json){
			$data=json_decode($json);
			$db=$this->fields_model->db;
			$res=$db->query("SELECT GROUP_CONCAT(id) as checked from `ifengcms`.`cms_fields_form_8ef4feba8121808e205865b62ff3323a` where status=0;");
			$checked=$res->row()->checked;
			$sql="truncate table `ifengcms`.`cms_fields_form_8ef4feba8121808e205865b62ff3323a`;";
			$db->query($sql);
			foreach($data as $r){
				$row=array(
					'id'=>$r->team_id,
					'v6ce21a3cc61f'=>$r->name,
					'v960ef26b1eac'=>'http://www.duduxy.com/'.$r->avatar,
					'veb17a0e15b39'=>preg_replace("/[\n\r\t ]+/", '',$r->description),
					'v616dad39708b'=>$r->members_total,
					'addtime'=>$r->created_time,
					'v565c535fbcc2'=>$r->money,//公益金
					'v9ae44464b750'=>$r->team_sales,//销售额
					'v1f64645fe613'=>'http://www.duduxy.com/index.php?app=team&teamid='.$r->team_id,
					
				);
				$db->insert('cms_fields_form_8ef4feba8121808e205865b62ff3323a',$row);
			}
			if($checked){
				//还原之前OK状态
				$db->query("UPDATE `ifengcms`.`cms_fields_form_8ef4feba8121808e205865b62ff3323a` SET `status`=0 WHERE `id` in ($checked)");
			}
		}
		echo "ok";
	}
	function getDuduxyData(){
		$timeout = array(
			'http' => array(
				'timeout' => 30
			)
		);
		
		$ctx = stream_context_create($timeout);
		$json = file_get_contents("http://www.duduxy.com/index.php?app=teamlist&act=memberjson", 0, $ctx);
		if($json){
			$data=json_decode($json);
			$db=$this->fields_model->db;
			//$res=$db->query("SELECT GROUP_CONCAT(id) as checked from `ifengcms`.`cms_fields_form_8ef4feba8121808e205865b62ff3323a` where status=0;");
			//$checked=$res->row()->checked;
			$sql="truncate table `ifengcms`.`cms_fields_form_ffa40c23660bd339e86c885a72750574`;";
			$db->query($sql);
			foreach($data as $r){
				$row=array(
					'id'=>$r->user_id,
					'addtime'=>$r->reg_time,
					'v60d0458ac6eb'=>$r->real_name,
					'v5a93d3f72d13'=>$r->user_name,
					'v787b5677e325'=>$r->gender,
					'v19fb21791f0a'=>$r->last_login					
				);
				$db->insert('cms_fields_form_ffa40c23660bd339e86c885a72750574',$row);
			}
		}
		echo "ok";
	}
}