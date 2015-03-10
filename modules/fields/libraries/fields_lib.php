<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class fields_lib
{
	function fields_lib(){
		// Get CI Instance
		$this->CI = &get_instance();
		$this->CI->load->helper('form');
		$this->CI->load->library('validation');
		$this->CI->bep_assets->load_asset_group('FORMS');
		$this->CI->load->model('fields_model');
	}
	var $statusCode=array(
		-1=>'登录超时，请重新登录。'
		,0=>'正常'
		,10000=>'一般性错误'
		,10001=>'接口未定义'
		,10002=>'非法Token'
	);
	public function status(&$arr,$code,$str=''){
		$error=$this->statusCode[$code];
		if($error){
			$arr['status']=$code;
			$arr['error']=$str?$str:$error;
		}else{
			$arr['status']=$code;
			$arr['error']=$str?$str:$this->statusCode[10000];
		}
		return $arr;
	}
	//检查是否合法请求
	public function check(){
		return true;
	}
	//
	function getFieldsData($id=0){
		if($id>0){
			$res=$this->CI->fields_model->fetch('F','*',null,array('id'=>$id));
			if($res->num_rows()>0){
				$data=$res->row();
				$data->stimeint=$data->stime;
				$data->etimeint=$data->etime;
				$data->stime=date('Y-m-d H:i:00',$data->stime);
				$data->etime=date('Y-m-d H:i:00',$data->etime);
				return array('status'=>0,'data'=>$data);
			}
			return array('status'=>10000,'error'=>'empty data');
		}
	}
	//
	function saveStep1(){
		if(!check('diyfields','update',false)){
			return array('status'=>10000,'error'=>'你没有权限操作');
		}
		$fields['name'] = "表单名称";
		$fields['stime'] = "开始时间";
		$fields['etime'] = "结束时间";
		
		$rules['name'] = 'trim|required|min_length[2]|max_length[32]';
		$rules['stime'] = 'trim|required';
		$rules['etime'] = 'trim|required';
		
		$this->CI->validation->set_fields($fields);
		$this->CI->validation->set_rules($rules);
		if ( $this->CI->validation->run() === FALSE )
		{
			return array('status'=>10000,'error'=>$this->CI->validation->_error_array[0]);
		}else{
			// Submit form
			$id=$this->CI->input->post('id');
			$data['name']=$this->CI->input->post('name');
			$data['stime']=$this->CI->input->post('stime');
			$data['etime']=$this->CI->input->post('etime');
			$data['stime']=strtotime($data['stime']);
			$data['etime']=strtotime($data['etime']);
			$data['type']=$this->CI->input->post('type');
			if($data['stime']<TIEM-86400*30){
				return array('status'=>10000,'error'=>'开始时间设置错误');
			}
			if($data['etime']<TIEM){
				return array('status'=>10000,'error'=>'结束时间设置错误');
			}
			if($id>0){
				$this->CI->fields_model->update('F',$data,array('id'=>$id));
			}else{
				$data['tab_name']=md5($data['name'].rand());
				$this->CI->fields_model->insert('F',$data);
				$id=$this->CI->fields_model->db->insert_id();
			}
		}
		return array('status'=>0,'id'=>$id,'name'=>$data['name']);
	}
	function dostyle($fields_style){
		$tmp_style=strtr($fields_style,array(
			'&lt;'=>'<',
			'&gt;'=>'>',
		));
		$tmp_style=explode('[removed]',$tmp_style);
		$style='';
		foreach ($tmp_style as $i => $value) {
			$style.=$value;
			if(count($tmp_style)-1==$i) break;
			$style.=(($i%2==0)?'<script>':'</script>');
		}
		return $style;
	}
	/*
	 * 比较2个对象键值是否相同
	 */
	function checkObjKey($obj1,$obj2){
		$obj1=is_string($obj1)?json_decode($obj1):$obj1;
		$obj2=is_string($obj2)?json_decode($obj2):$obj2;
		foreach ($obj1 as $key => $value) {
			$arr1[]=$key;
		}
		foreach ($obj2 as $key => $value) {
			$arr2[]=$key;
		}
		sort($arr1);
		sort($arr2);
		if($arr1==$arr2){
			return true;
		}
		return false;
	}
	function saveStep2(){
		if(!check('diyfields','update',false)){
			return array('status'=>10000,'error'=>'你没有权限操作');
		}
		$id=$this->CI->input->post('id');
		$fields_json_str=$this->CI->input->post('fields_json');
		$fields_html=$this->CI->input->post('fields_html');
		$fields_htmls=$this->CI->input->post('fields_htmls');
		
		$fields_style=$this->CI->input->post('fields_style');
		$fields_style_mobile=$this->CI->input->post('fields_style_mobile');
		$data['fields_style']=$this->dostyle($fields_style);
		$data['fields_style_mobile']=$this->dostyle($fields_style_mobile);
		
		$fields_json=json_decode($fields_json_str,true);
		if(!$fields_json){
			return array('status'=>10000,'error'=>'请设置字段');
		}
		$row=$this->getFieldsData($id);
		if($row['status']==0){
			$flag=$this->CI->fields_model->create_table($row['data']->tab_name,$fields_json);
			if($flag){
				$data['fields_json']=$fields_json_str;
				$data['fields_html']=$fields_html;
				$data['fields_htmls']=$fields_htmls;
				$arr=array('status'=>0,'error'=>'设置成功');
			}else{
				$fields_json_status=$this->checkObjKey($fields_json_str,$row['data']->fields_json);
				
				if($fields_json_status){//只是改变了表单名称之外的其它属性
					$data['fields_json']=$fields_json_str;
					$data['fields_html']=$fields_html;
					$data['fields_htmls']=$fields_htmls;
					$arr=array('status'=>0,'error'=>'设置成功，更新了表单内容');
				}else{
					$arr=array('status'=>10001,'error'=>'已经存在表单数据，只修改了样式属性');
				}
			}
			$this->CI->fields_model->update('F',$data,array('id'=>$id));
			return $arr;
		}
		return array('status'=>10000,'error'=>'设置失败，请关闭此页面，重新设置');
	}
	function form($fid){
		if(!$fid){
			return array('status'=>10000,'error'=>'提交失败，请重新打开报名页面');
		}
		$row=$this->getFieldsData($fid);
		if($row['status']!=0){
			return array('status'=>10000,'error'=>'表单数据不存在，请重新打开报名页面');
		}
		$fdata=$row['data'];
		if($fdata->stimeint>TIME){
			return array('status'=>10000,'error'=>$fdata->name.'还没有开始，开始时间为：'.$fdata->stime);
		}
		if($fdata->etimeint<TIME){
			return array('status'=>10000,'error'=>'来晚一步，'.$fdata->name.'已经结束了');
		}
		$fields_json=json_decode($fdata->fields_json,true);
		if(!$fields_json){
			return array('status'=>10000,'error'=>'表单错误，需要管理员重新设置');
		}
		foreach ($fields_json as $key => $value) {
			if(!trim($key)) continue;
			$fields[$key] = $value['label'];
			if($value['multiple']){
				$rules[$key] = 'required';
			}elseif($value['rules']){
				$table=$this->CI->fields_model->fileds_table_prefix.$fdata->tab_name;
				$rules[$key] = str_replace('{table}', $table.','.$key, $value['rules']).'|required';
			}elseif($value['filetype']){
				//文件
				$rules[$key]='';
			}else{
				$rules[$key] = 'trim|required';
			}
			if($value['isnull']){
				$rules[$key] = str_replace('|required','',$rules[$key]);
			}
		}
		$this->CI->validation->set_fields($fields);
		$this->CI->validation->set_rules($rules);
		
		if ( $this->CI->validation->run() === FALSE )
		{
			//$this->CI->validation->output_errors();	
			return array('status'=>10002,'error'=>$this->CI->validation->_error_array[0]);
		}else{
			// Submit form
			$data['addtime']=TIME;
			$data['ip']=$this->CI->input->ip_address();
			foreach ($fields_json as $key => $value) {
				if(!trim($key)) continue;
				if($value['filetype']){
					//上传文件
					$day=date('Ymd',TIME);
					$path='uploads/'.$day;
					$dir=FCPATH.$path;
					$filename=$day.rand(10000,99999);
					@mkdir($dir,0777);
					$config['upload_path'] = $dir;
					$config['allowed_types'] = $value['filetype'];
					$config['max_size'] = 1024*($value['filesize']+0);
					$config['encrypt_name'] = true;
					$this->CI->load->library('upload', $config);
					if (!$this->CI->upload->do_upload($key)){
						if(!$value['isnull']){
							return array('status'=>10001,'error'=>$value['label'].$this->CI->upload->display_errors());
						}
					}else{
						$upload = $this->CI->upload->data();
					}
					if(is_array($upload)){
						$data[$key]=base_url().$path.'/'.$upload['file_name'];
					}else{
						if(!$value['isnull']){
							return array('status'=>10000,'error'=>$value['label'].$this->CI->upload->display_errors());
						}
					}
				}else{
					$data[$key]=$this->CI->input->post($key);
				}
				if(is_array($data[$key])){
					$data[$key]=implode(',', $data[$key]);
				}
			}
			//fromadd
			$data['fromaddr']=$this->CI->input->post('fromaddr');
			$data['openid']=$this->CI->input->post('openid');
			$res=$this->CI->fields_model->insert_fields_tabdata($fdata->tab_name,$data);
			return $res;
		}
	}
	function checkManCode(){
		//$fieldid,$mancodefield,$mancode
		$fieldid=$this->CI->input->post('fieldid')+0;
		if($fieldid<1){
			return array('status'=>10003,'error'=>'表单错误，联系管理员解决此问题');
		}
		$mancodefield=$this->CI->input->post('codename');
		$mancode=$this->CI->input->post($mancodefield);
		$fields=array($mancodefield=>'身份证');
		$rules=array($mancodefield=>'trim|required|valid_mancode');
		$this->CI->validation->set_fields($fields);
		$this->CI->validation->set_rules($rules);
		
		if ( $this->CI->validation->run() === FALSE ){	
			return array('status'=>10002,'error'=>$this->CI->validation->_error_array[0]);
		}else{
			$res=$this->CI->fields_model->fetch('F','*',null,array('id'=>$fieldid));
			if($res->num_rows()>0){
				$field=$res->row();
				$table=$this->CI->fields_model->fileds_table_prefix.$field->tab_name;
				$dbkey=json_decode($field->fields_json,true);
				if(!$dbkey[$mancodefield]){
					return array('status'=>10000,'error'=>'表单错误，联系管理员解决此问题');
				}
				$limit=array('offset'=>0,'limit'=>1);
				$where=array($mancodefield=>$mancode);
				$res=$this->CI->fields_model->getFieldsDataList($table,$where,$limit,false);
				if($res->num_rows()>0){
					return array('status'=>0,'isexist'=>true,'data'=>$res->row());
				}else{
					return array('status'=>0,'isexist'=>false);
				}
				
			}
			return array('status'=>10001,'error'=>'表单错误，联系管理员解决此问题');
		}
		
	}
	function formChuangYe($fid){
		$updateID=$this->CI->input->post('id');
		$updateStep=$this->CI->input->post('step');
		$updateCodeName=$this->CI->input->post('codename');
		if(!$fid){
			return array('status'=>10000,'error'=>'提交失败，请重新打开报名页面');
		}
		$row=$this->getFieldsData($fid);
		
		if($row['status']!=0){
			return array('status'=>10000,'error'=>'表单数据不存在，请重新打开报名页面');
		}
		$fdata=$row['data'];
		if($fdata->stimeint>TIME){
			return array('status'=>10000,'error'=>$fdata->name.'还没有开始，开始时间为：'.$fdata->stime);
		}
		if($fdata->etimeint<TIME){
			return array('status'=>10000,'error'=>'来晚一步，'.$fdata->name.'已经结束了');
		}
		$fields_json=json_decode($fdata->fields_json,true);
		if(!$fields_json){
			return array('status'=>10000,'error'=>'表单错误，需要管理员重新设置');
		}
		foreach ($fields_json as $key => $value) {
			if(!trim($key)) continue;
			$fields[$key] = $value['label'];
			if($value['multiple']){
				$rules[$key] = 'required';
			}elseif($value['rules']){
				$table=$this->CI->fields_model->fileds_table_prefix.$fdata->tab_name;
				$rules[$key] = str_replace('{table}', $table.','.$key, $value['rules']).'|required';
			}elseif($value['filetype']){
				//文件
				$rules[$key]='';
			}else{
				$rules[$key] = 'trim|required';
			}
			if($value['isnull']){
				$rules[$key] = str_replace('|required','',$rules[$key]);
			}
			if($updateID>0){
				$rules[$key] = strtr($rules[$key],array(
					"field_phone[{$table},{$key}]"=>valid_phone,
					"field_mancode[{$table},{$key}]"=>valid_mancode
				));
			}
		}
		$this->CI->validation->set_fields($fields);
		$this->CI->validation->set_rules($rules);
		
		if ( $this->CI->validation->run() === FALSE)
		{
			//$this->CI->validation->output_errors();	
			return array('status'=>10002,'error'=>$this->CI->validation->_error_array[0]);
		}else{
			// Submit form
			$data['addtime']=TIME;
			$data['ip']=$this->CI->input->ip_address();
			foreach ($fields_json as $key => $value) {
				if(!trim($key)) continue;
				if($value['filetype']){
					//上传文件
					$day=date('Ymd',TIME);
					$path='uploads/'.$day;
					$dir=FCPATH.$path;
					$filename=$day.rand(10000,99999);
					@mkdir($dir,0777);
					$config['upload_path'] = $dir;
					$config['allowed_types'] = $value['filetype'];
					$config['max_size'] = 1024*($value['filesize']+0);
					$config['encrypt_name'] = true;
					$this->CI->load->library('upload', $config);
					if (!$this->CI->upload->do_upload($key)){
						if(!$value['isnull']){
							return array('status'=>10001,'error'=>$this->CI->upload->display_errors());
						}
					}else{
						$upload = $this->CI->upload->data();
					}
					if(is_array($upload)){
						$data[$key]=base_url().$path.'/'.$upload['file_name'];
					}else{
						if(!$value['isnull']){
							return array('status'=>10000,'error'=>$this->CI->upload->display_errors());
						}
					}
				}else{
					$data[$key]=$this->CI->input->post($key);
				}
				if(is_array($data[$key])){
					$data[$key]=implode(',', $data[$key]);
				}
			}
			if($updateID>0){
				$res=$this->CI->fields_model->update_fields_tabdata($fdata->tab_name,$data,array(
					'id'=>$updateID,
					$updateCodeName=>$data[$updateCodeName]
				));
			}else{
				//fromadd
				$data['fromaddr']=$this->CI->input->post('fromaddr');
				$res=$this->CI->fields_model->insert_fields_tabdata($fdata->tab_name,$data);
			}
			return $res;
		}
	}
	function formChuangYePart2($fid){
		$updateCodeName=$this->CI->input->post('codename');
		$updateFileName=$this->CI->input->post('filename');
		if(!$fid){
			return array('status'=>10000,'error'=>'提交失败，请重新打开报名页面');
		}
		$row=$this->getFieldsData($fid);
		
		if($row['status']!=0){
			return array('status'=>10000,'error'=>'表单数据不存在，请重新打开报名页面');
		}
		$fdata=$row['data'];
		if($fdata->stimeint>TIME){
			return array('status'=>10000,'error'=>$fdata->name.'还没有开始，开始时间为：'.$fdata->stime);
		}
		if($fdata->etimeint<TIME){
			return array('status'=>10000,'error'=>'来晚一步，'.$fdata->name.'已经结束了');
		}
		$fields_json=json_decode($fdata->fields_json,true);
		if(!$fields_json){
			return array('status'=>10000,'error'=>'表单错误，需要管理员重新设置');
		}
		
		$fields=array($updateCodeName=>'身份证');
		$rules=array($updateCodeName=>'trim|required|valid_mancode');
		$this->CI->validation->set_fields($fields);
		$this->CI->validation->set_rules($rules);
		
		
		if ( $this->CI->validation->run() === FALSE)
		{
			//$this->CI->validation->output_errors();	
			return array('status'=>10002,'error'=>$this->CI->validation->_error_array[0]);
		}else{
			$data[$updateCodeName]=$this->CI->input->post($updateCodeName);
			//上传文件
			$value=$fields_json[$updateFileName];
			$day=date('Ymd',TIME);
			$path='uploads/'.$day;
			$dir=FCPATH.$path;
			$filename=$day.rand(10000,99999);
			@mkdir($dir,0777);
			$config['upload_path'] = $dir;
			$config['allowed_types'] = $value['filetype'];
			$config['max_size'] = 1024*($value['filesize']+0);
			$config['encrypt_name'] = true;
			$this->CI->load->library('upload', $config);
			if (!$this->CI->upload->do_upload($updateFileName)){
				return array('status'=>10001,'error'=>$this->CI->upload->display_errors());
			}else{
				$upload = $this->CI->upload->data();
			}
			if(is_array($upload)){
				$data[$updateFileName]=base_url().$path.'/'.$upload['file_name'];
			}else{
				return array('status'=>10000,'error'=>$this->CI->upload->display_errors());
			}
			
			$res=$this->CI->fields_model->update_fields_tabdata($fdata->tab_name,$data,array(
				$updateCodeName=>$data[$updateCodeName]
			));
			return $res;
		}
	}
	function checkSMS(){
		$this->CI->load->library('sms');
		$res=$this->CI->sms->check();
		$res=preg_split("/[\n\r,]/", $res);
		return array('status'=>$res[0],'data'=>array('total'=>$res[1],'last'=>$res[2]),'error'=>'查询失败!');
	}
	function sendMsg(){
		$__LIMIT=99;//一次群发99条
		$info['fid']=$fid=$this->CI->input->post('fid');
		$info['dbkey']=$dbkey=$this->CI->input->post('dbkey');
		$phones=trim($this->CI->input->post('phones'));
		$info['content']=$content=trim($this->CI->input->post('content'));
		$offset=$this->CI->input->post('offset')+0;
		$total=$this->CI->input->post('total')+0;
		if(!$content){
			return array('status'=>10000,'error'=>'发送内容不能为空');
		}
		if(!$dbkey){
			if(!$phones){
				return array('status'=>10000,'error'=>'发送号码不能为空');
			}
			$phones=preg_replace("/[ \t]+/", "", $phones);
			$phones=preg_split("/[\n\r,]+/", $phones);
			$info['total']=$total>0?$total:count($phones);//总条数
			$info['isnext']=false;
			$phones_arr=array_chunk($phones, $__LIMIT);
			$phones=$phones_arr[0];
			$info['phones']='';
			for($i=1;$i<count($phones_arr);$i++){
				$info['isnext']=true;
				$info['phones'].=implode(',',$phones_arr[$i]).',';
			}
		}else{
			$row=$this->getFieldsData($fid);
			if($row['status']!=0){
				return array('status'=>10000,'error'=>'表单数据不存在，请重新打开报名页面');
			}
			$fdata=$row['data'];
			$table=$this->CI->fields_model->fileds_table_prefix.$fdata->tab_name;
			//计算总条数
			$sql="SELECT count(*) as total from `ifengcms`.`{$table}`";
			$res=$this->CI->fields_model->db->query($sql);
			$info['total']=$res->row()->total;//总条数
			
			
			$sql="SELECT {$dbkey} as phone from `ifengcms`.`{$table}` limit {$offset},{$__LIMIT}";
			$res=$this->CI->fields_model->db->query($sql);
			$phones=array();
			foreach ($res->result() as $r) {
				array_push($phones,$r->phone);
			}
			$info['isnext']=false;
			if(($offset+$__LIMIT)<$info['total']){
				$info['isnext']=true;
			}
		}
		if(!$phones){
			return array('status'=>10000,'error'=>'发送号码地址不能为空');
		}
		
		$info['offset']=$offset+$__LIMIT;
		$info['offset']=$info['offset']>$info['total']?$info['total']:$info['offset'];
		
		$this->CI->load->library('sms');
		//$phones_arr=array_chunk($phones, 500);
		//var_dump(count($phones),count($phones_arr)) ;
		
		$ct=count($phones);
		$phone_str='';
		foreach ($phones as $i=>$p) {
			if(!preg_match("/^1[0-9]{10}$/", $p)) continue;
			$phone_str.=$p.($ct==$i+1?'':',');
		}
		//var_dump($phone_str);
		$this->CI->sms->send($phone_str,$content);
		
		return array('status'=>0,'data'=>$info,'error'=>'发送完成!','phone_str'=>$phone_str);
	}
	/*
	 * 题库相关操作
	 */
	const ques_username='姓名';
	const ques_phone='手机';
	const ques_fen=1;//一题10分
	//获得当天的问题
	function getDayQuestions($id=0,$openid=''){
		$this->CI->load->module_library('oauth','oauth_lib');
		$cookie=$this->CI->oauth_lib->getWechatCookie();
		$openid=$cookie['openid']?$cookie['openid']:$openid;
		
		$row=$this->getFieldsData($id);
		if($row['status']==0){
			$field=$row['data'];
			if($field->stimeint>TIME){
				return array('status'=>10000,'error'=>$field->name.'还没有开始，开始时间为：'.$field->stime);
			}
			
			$table=$this->CI->fields_model->fileds_table_prefix.$field->tab_name;
			$dbkey=json_decode($field->fields_json,true);
			//$table_fields=$this->CI->fields_model->db->list_fields($table);
			
			$limit=array('offset'=>$page,'limit'=>1);
			$where=array('openid'=>$openid);
			$res=$this->CI->fields_model->getFieldsDataList($table,$where,$limit,false);
			if($res->num_rows()>0){
				//用户答过题
				$data['isActive']=true;
				$userdata = $res->row();
				$data['source']=$userdata->source;
			}else{
				$data['isActive']=false;
				$data['source']=0;
			}
			$queAll=array();
			foreach ($dbkey as $key => $value) {
				if($value['label']==self::ques_username || $value['label']==self::ques_phone){
					$user[$value['label']]=$key;
				}else{
					$option=preg_split('/\n/',$value['inline-radios']);
					$queAll[$key]=array(
						'label'=>$value['label'],
						'name'=>$key,
						'option'=>$option
					);
				}
			}
			$data['today']=$this->_dayQuestions($openid);
			if(!$data['today']){
				$newq_key=array_rand ($queAll,3);
				$newq=array();
				foreach ($newq_key as $key) {
					$newq[$key]=$queAll[$key];
				}
				$data['today']=$this->_dayQuestions($openid,array(
					'questions'=>$newq,
					'user'=>$user,
					'isActive'=>false,
					'answer'=>array()
				));
			}
			//设置问题是否已经回答正确
			/*
			foreach ($data['today']['questions'] as $key => $value) {
				$data['today']['questions'][$key]['answer']=false;
				if($userdata->$key==$dbkey[$key]['answer']){
					$data['today']['questions'][$key]['answer']=true;
				}
			}*/
			$data['userinfo']=array(
				'name'=>$userdata->$user[self::ques_username],
				'phone'=>$userdata->$user[self::ques_phone],
			);
			$data['status']=0;
			if($field->etimeint<TIME){
				$data['status']=10000;
				$data['error']=$field->name.'已经结束了';
			}
			return $data;
		}
		
	}
	//创建今天的试卷
	function _dayQuestions($openid='',$data=array()){
		$dir=BASEPATH.'cache/2015315/';
		$day=date('Ymd',TIME);
		@mkdir($dir);
		$questions=array();
		if(file_exists($dir.$openid)){
			$questions=include($dir.$openid);
		}
		if($data){
			//写入题库
			$questions[$day]=$data;
			file_put_contents($dir.$openid, '<?php return '.var_export($questions,true).';');
		}
		return $questions[$day];
	}
	//设置答题用户
	function quesUser($id=0,$openid=''){
		$this->CI->load->module_library('oauth','oauth_lib');
		$cookie=$this->CI->oauth_lib->getWechatCookie();
		$openid=$cookie['openid']?$cookie['openid']:$openid;
		$row=$this->getFieldsData($id);
		if($row['status']==0){
			$field=$row['data'];
			if($field->stimeint>TIME){
				return array('status'=>10000,'error'=>$field->name.'还没有开始，开始时间为：'.$field->stime);
			}
			if($field->etimeint<TIME){
				return array('status'=>10000,'error'=>'来晚一步，'.$field->name.'已经结束了');
			}
			$table=$this->CI->fields_model->fileds_table_prefix.$field->tab_name;
			$today=$this->_dayQuestions($openid);
			if($today['user']){
				foreach ($today['user'] as $value => $key) {
					$$value=trim($this->CI->input->get($key));
					if(!$$value){
						return array('status'=>10000,'error'=>$value.'必须填写');
					}
					if($value==self::ques_phone){
						if(!preg_match("/^1[0-9]{10}$/", $$value)){
							return array('status'=>10000,'error'=>$value.'填写错误');
						}
					}
					$user[$key]=$$value;
				}
				$res=$this->CI->fields_model->getFieldsDataList($table,array('openid'=>$openid),null,false);
				if($res->num_rows()<1){
					$user['openid']=$openid;
					$this->CI->fields_model->insert_fields_tabdata($field->tab_name,$user);
				}
				return array('status'=>0);
			}
			return array('status'=>10000,'error'=>'出错了，请重新打开试卷');
		}
		return array('status'=>10000,'error'=>'出错了，题库不存在');
		
	}
	//答题
	function doques($id=0,$openid=''){
		$this->CI->load->module_library('oauth','oauth_lib');
		$cookie=$this->CI->oauth_lib->getWechatCookie();
		if(!$cookie['openid']){
			return array('status'=>10000,'error'=>'请从微信参与活动');
		}
		$openid=$cookie['openid']?$cookie['openid']:$openid;
		$today=$this->_dayQuestions($openid);
		if($today['isActive']){
			return array('status'=>10000,'error'=>'你今天已经参与，快快分享给好友吧');
		}
		$row=$this->getFieldsData($id);
		if($row['status']==0){
			$field=$row['data'];
			if($field->stimeint>TIME){
				return array('status'=>10000,'error'=>$field->name.'还没有开始，开始时间为：'.$field->stime);
			}
			if($field->etimeint<TIME){
				return array('status'=>10000,'error'=>'来晚一步，'.$field->name.'已经结束了');
			}
			$dbkey=json_decode($field->fields_json,true);
			$table=$this->CI->fields_model->fileds_table_prefix.$field->tab_name;
			$source=0;
			if($today['questions']){
				foreach ($today['questions'] as $key => $value) {
					$answer[$key]=$this->CI->input->get($key);
					if(!$answer[$key]){
						return array('status'=>10000,'error'=>'请完成 '.$value['label']);
					}
					if(trim($answer[$key])==trim($dbkey[$key]['answer'])){
						$source++;
					}
					$today['answer'][$key]=$answer[$key];
				}
				$answer['source']='source + '.($source*self::ques_fen);
				//更新今日答题记录
				$today['isActive']=true;
				$this->_dayQuestions($openid,$today);
				//更新自己的得分
				$this->CI->fields_model->update_fields_tabdata($field->tab_name,$answer,array('openid'=>$openid));
				//更新分享者得分
				$fkid=$this->CI->input->get('fkid');
				if($fkid && $fkid!=$openid){
					$this->CI->fields_model->update_fields_tabdata($field->tab_name,$answer,array('openid'=>$fkid));
				}
				return array('status'=>0,'source'=>$source*self::ques_fen);
			}
			return array('status'=>10000,'error'=>'出错了，请重新打开试卷');
		}
		return array('status'=>10000,'error'=>'出错了，题库不存在');
	}
}

