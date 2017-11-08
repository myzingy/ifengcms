<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class vote_lib
{
	function vote_lib(){
		// Get CI Instance
		$this->CI = &get_instance();
		$this->CI->load->helper('form');
		$this->CI->load->library('validation');
		$this->CI->bep_assets->load_asset_group('FORMS');
		$this->CI->load->model('vote_model');
	}
	function update($container,$id=0){
		$fields['title'] = "活动名称";
		$fields['snum'] = "开始号码";
		$fields['enum'] = "结束号码";
		$fields['stime'] = "开始时间";
		$fields['etime'] = "结束时间";
		$fields['rule'] = "投票规则";
		$fields['testip'] = "测试IP";
		$fields['subject'] = "活动简介";
		$fields['remoteurl'] = "外部服务地址";
		$fields['displayurl'] = "外部展示地址";
		$fields['background'] = "背景色";
		$fields['vote_max'] = "投票次数";
		
		$rules['title'] = 'trim|required|max_length[64]';
		$rules['snum'] = 'trim|integer|min_number[0]|max_number[9999]';
		$rules['enum'] = 'trim|integer|min_number[0]|max_number[9999]';
		$rules['stime'] = 'trim|required';
		$rules['etime'] = 'trim|required';
		$rules['remoteurl'] = 'trim';
		$rules['displayurl'] = 'trim';
		$rules['background'] = 'trim';
        $rules['vote_max'] = 'trim|required';
		
		$this->CI->validation->set_fields($fields);
		$this->CI->validation->set_rules($rules);
		$data['jsform']=rules_ci2js($rules,$fields);
		if ( $this->CI->validation->run() === FALSE )
		{
			if($_SERVER['HTTP_X_REQUESTED_WITH']){
				$info= array('status'=>10000,'error'=>$this->CI->validation->_error_array[0]);
				die(json_encode($info));
			}
			// Output any errors
			$this->CI->validation->output_errors();

			// Display page
			$data['header'] = '编辑投票信息';
			$data['page'] = $this->CI->config->item('backendpro_template_dir') . 'form';
			$data['module'] = 'vote';
			if($id>0){
				$res=$this->CI->vote_model->fetch('V','*',null,array('id'=>$id));
				if($res->num_rows()>0){
					$data['editinfo']=$res->row_array();
				}
			}
			$data['testip']=$this->CI->input->ip_address();
			$this->CI->load->view($container,$data);
		}else{
			// Submit form
			return $this->_update($fields);
		}
	}
	function _update($fields){
		$id=$this->CI->input->post('id');
		foreach ($fields as $key => $value) {
			$data[$key]=$this->CI->input->post($key,false);
		}
		$data['etime']=strtotime($data['etime']);
		$data['stime']=strtotime($data['stime']);

        if ($_FILES['thumb']['name']){ //需要图片
            //game thumb
            $day=date('Ymd',TIME);
            $path='uploads/'.$day;
            $dir=FCPATH.$path;
            $filename=$day.rand(10000,99999);
            @mkdir($dir,0777);
            $config['upload_path'] = $dir;
            $config['allowed_types'] = 'jpg|jpeg|png|bmp';
            $config['max_size'] = 1024*8;
            $config['max_width']  = 8000;
            $config['max_height']  = 8000;
            //$config['file_name'] = $filename;
            $config['encrypt_name'] = true;
            $this->CI->load->library('upload', $config);
            
            $imgflag=false;
            
            if (!$this->CI->upload->do_upload('thumb'))
            {
                $upload = $this->CI->upload->display_errors();
            } 
            else
            {
                $upload = $this->CI->upload->data();
            }
            if(is_array($upload)){
                $imgflag=true;
                $data['thumb']=base_url().$path.'/'.$upload['file_name'];
            }
            //if(!$imgflag) flashMsg('info','你本次操作没有上传任何图片。');
        }

        if ($_FILES['thumb2']['name']){ //需要图片
            //game thumb
            $day=date('Ymd',TIME);
            $path='uploads/'.$day;
            $dir=FCPATH.$path;
            $filename=$day.rand(10000,99999);
            @mkdir($dir,0777);
            $config['upload_path'] = $dir;
            $config['allowed_types'] = 'jpg|jpeg|png|bmp';
            $config['max_size'] = 1024*8;
            $config['max_width']  = 8000;
            $config['max_height']  = 8000;
            //$config['file_name'] = $filename;
            $config['encrypt_name'] = true;
            $this->CI->load->library('upload', $config);
            
            $imgflag=false;
            
            if (!$this->CI->upload->do_upload('thumb2'))
            {
                $upload = $this->CI->upload->display_errors();
            } 
            else
            {
                $upload = $this->CI->upload->data();
            }
            if(is_array($upload)){
                $imgflag=true;
                $data['thumb2']=base_url().$path.'/'.$upload['file_name'];
            }
            //if(!$imgflag) flashMsg('info','你本次操作没有上传任何图片。');
        }

		if($data['enum'] || $data['snum']){
			if($data['enum']>$data['snum']){
				if($data['etime']>$data['stime']){
					//检查号段占用
					$pkarr=array(
						'snum'=>$data['snum'],
						'enum'=>$data['enum'],
					);
					if($id>0){
						$pkarr['noid']=$id;
					}
					$vote=$this->CI->vote_model->getVoteForNum($pkarr);
					if($vote){
						$info= array('status'=>10000,'error'=>"号码段被[{$vote->title}]({$vote->snum}-{$vote->enum})占用");
					}else{
						if($id>0){
							$this->CI->vote_model->update('V',$data,array('id'=>$id));
						}else{
							$this->CI->vote_model->insert('V',$data);
						}
						$info=array('status'=>0);
					}
				}else{
					$info= array('status'=>10000,'error'=>'活动时间设置错误，请设置格式为'.date('Y-m-d H:i:s'));
				}
			}else{
				$info= array('status'=>10000,'error'=>'结束号码必须大于开始号码');
			}
		}else{

			if($id>0){
				$this->CI->vote_model->update('V',$data,array('id'=>$id));
			}else{
				$this->CI->vote_model->insert('V',$data);
			}
			$info=array('status'=>0);
		}
		
		die(json_encode($info));
	}
	
	function updatevotedata($container,$vid,$vmid=0){
		$fields['vid'] = "活动ID";
		$fields['name'] = "名称";
		$fields['code'] = "号码";
		$fields['count'] = "票数";
		$fields['info'] = "简介";
		$fields['custom'] = "自定义字段";
		
		$rules['name'] = 'trim|required';
		$rules['code'] = 'trim|integer|min_number[1]|max_number[2000]';
		$rules['count'] = 'trim|integer|min_number[0]';
		$rules['info'] = 'trim';
		
		$this->CI->validation->set_fields($fields);
		$this->CI->validation->set_rules($rules);
		$data['jsform']=rules_ci2js($rules,$fields);
		if ( $this->CI->validation->run() === FALSE )
		{
			if($_SERVER['HTTP_X_REQUESTED_WITH']){
				$info= array('status'=>10000,'error'=>$this->CI->validation->_error_array[0]);
				die(json_encode($info));
			}
			// Output any errors
			$this->CI->validation->output_errors();

			// Display page
			$data['header'] = '编辑投票成员信息';
			$data['page'] = $this->CI->config->item('backendpro_template_dir') . 'form_votedata';
			$data['module'] = 'vote';
			$data['vid'] = $vid;
			if($vmid>0){
				$res=$this->CI->vote_model->fetch('VM','*',null,array('id'=>$vmid));
				if($res->num_rows()>0){
					$data['editinfo']=$res->row_array();
				}
			}
			$this->CI->load->view($container,$data);
		}else{
			// Submit form
			$info=$this->_updatevotedata($fields);
			die(json_encode($info));
		}
	}
	function _updatevotedata($fields){
		$id=$this->CI->input->post('id');
		foreach ($fields as $key => $value) {
			$data[$key]=$this->CI->input->post($key);
		}
		//检查code冲突越界
		$res=$this->CI->vote_model->fetch('V','*',null,array('id'=>$data['vid']));
		if($res->num_rows()>0){
			$vote=$res->row();
			if($vote->snum && $vote->enum && $data['code']){
				if(!($data['code']+0>=$vote->snum && $data['code']+0<=$vote->enum)){
					return array('status'=>10000,'error'=>'本活动号码段为'.$vote->snum.'-'.$vote->enum.'，请修改号码'.$data['code']);
				}
			}
		}else{
			return array('status'=>10000,'error'=>'不存在投票活动，无法操作');
		}
		//检查code冲突
		if($data['code']){
			$res=$this->CI->vote_model->fetch('VM','*',null,array('vid'=>$data['vid'],'code'=>$data['code']));
			if($res->num_rows()>0){
				$vote=$res->row();
				if($vote->id!=$id){
					return array('status'=>10000,'error'=>'成员号码已经存在，请修改');
				}
			}
		}
		
		if ($_FILES['thumb']['name']){ //需要图片
			//game thumb
			$day=date('Ymd',TIME);
			$path='uploads/'.$day;
			$dir=FCPATH.$path;
			$filename=$day.rand(10000,99999);
			@mkdir($dir,0777);
			$config['upload_path'] = $dir;
			$config['allowed_types'] = 'jpg|jpeg|png|bmp';
			$config['max_size'] = 1024*8;
			$config['max_width']  = 8000;
			$config['max_height']  = 8000;
			//$config['file_name'] = $filename;
			$config['encrypt_name'] = true;
			$this->CI->load->library('upload', $config);
			
			$imgflag=false;
			
			if (!$this->CI->upload->do_upload('thumb'))
			{
				$upload = $this->CI->upload->display_errors();
			} 
			else
			{
				$upload = $this->CI->upload->data();
			}
			if(is_array($upload)){
				$imgflag=true;
				$data['thumb']=base_url().$path.'/'.$upload['file_name'];
			}
			//if(!$imgflag) flashMsg('info','你本次操作没有上传任何图片。');
		}
		if($id>0){
			$this->CI->vote_model->update('VM',$data,array('id'=>$id));
		}else{
			$this->CI->vote_model->insert('VM',$data);
		}
		return array('status'=>0);
	}
	function bindsource(){
		$data['vid'] = "活动ID";
		$data['source_fieldname'] = "名称";
		$data['source_id'] = "名称";
		$data['source_name'] = "号码";
		$data['source_pic'] = "简介";
		$data['source_code'] = "简介";
		foreach ($data as $key => $value) {
			$data[$key]=$this->CI->input->post($key);
		}
		if(count($data['source_name'])>0){
			$data['source_name']=implode(',', $data['source_name']);
		}
		if(count($data['source_pic'])>0){
			$data['source_pic_ext']=implode(',', $data['source_pic']);
		}
		$data['source_pic']=$data['source_pic'][0];
		if($data['vid'] && $data['source_id'] && ($data['source_name'] || $data['source_pic'])){
			$res=$this->CI->vote_model->fetch('VS','*',null,array(
				'vid'=>$data['vid'],
				'source_id'=>$data['source_id']
			));
			if($res->num_rows>0){
				return array('status'=>10000,'error'=>'已经绑定过了');
			}
			$this->CI->vote_model->insert('VS',$data);
			return array('status'=>0);
		}else{
			return array('status'=>10000,'error'=>'错误，请重新选择数据源');
		}
	}
	##################################################
	# 微信端投票系统
	##################################################
	function ifengvote(){
		$vote_num=1;
		$data['sigid']=$this->CI->input->post('openid');
		$data['vmid']=$this->CI->input->post('postid');
		$data['ip']=$this->CI->input->post('ip');
		if(!($data['sigid'] && $data['vmid'] && $data['ip'])){
			return array('status'=>12345,'error'=>'上山打老虎!');
		};
		if($data['sigid']=='admin'){
			$_a=explode('|',$data['vmid']);
			$data['vmid']=$_a[0];
			$vote_num+=$_a[1];
		}
		if($data['vmid']>0){
			$table=$this->CI->vote_model->_TABLES;
			$db=$this->CI->vote_model->db;
			
			//获得活动信息
			if($data['vmid']<$this->CI->vote_model->codenum){
				$vote=$this->CI->vote_model->getVoteForNum($data['vmid']);
				
			}else{
				$vote=false;
				$db->select('V.*');
				$db->from($table['VM']. " VM");
				$db->join($table['V']. " V","VM.vid=V.id",'left');
				$db->where("VM.id",$data['vmid']);
				$res=$db->get();
				if($res->num_rows()>0){
					$vote=$res->row();
				}
			}
			if(!$vote){
				return array('status'=>10000,'error'=>'投票活动不存在，请核对投票信息');
			}
			$istester=($vote->testip==$data['ip']);
			//补齐投票前面的0
			$data['vmid']=str_pad($data['vmid'], strlen($vote->enum), "0", STR_PAD_LEFT);
			//获得被投票者信息
			if($data['vmid']<$this->CI->vote_model->codenum){
				$vote_member_res=$this->CI->vote_model->getVoteDataList(array('vid'=>$vote->id,'code'=>$data['vmid']),array('limit'=>1),false);
			}else{
				$vote_member_res=$this->CI->vote_model->getVoteDataList(array('id'=>$data['vmid']),array('limit'=>1),false);
			}
			if($vote_member_res->num_rows()<1){
				return array('status'=>10000,'error'=>'投票号码不正确，请核对投票信息');
			}
			$vote_member=$vote_member_res->row();
			if($vote->stime>TIME && !$istester){
				return array('status'=>10000,'error'=>$vote->title.'还没开始，开始时间为：'.date('Y-m-d H:i:s',$vote->stime));
			}
			if($vote->etime<TIME && !$istester){
				return array('status'=>10000,'error'=>$vote->title.'已经结束了!');
			}
			if(!$istester){
				$sql = " SELECT id FROM {$table['VH']} "
				." WHERE `vid`='{$vote->id}' "
				." AND `sigid`='{$data['sigid']}'";
				if($vote->rule==1){
					$sql.=" AND `addtime` >=".strtotime(date('Y-m-d 00:00:00',TIME));
				}else{
					$sql.=" AND `addtime` >= {$vote->stime}";
				}
				$res= $db->query( $sql );
				if($res->num_rows()>=$vote->vote_max){
					
					if($vote->displayurl){
						$vote->displayurl=str_replace('_remoteid_', $vote_member->remoteid, $vote->displayurl);
						$display_url="\n<a href=\"".$vote->displayurl."\">{$vote_member->name}想你了，赶快邀请更多朋友关注我们投票吧！</a>";
					}else{
						$display_url="\n<a href=\"".site_url('vote/display/show/'.$vote_member->vid.'/'.$vote_member->id)."\">{$vote_member->name}想你了，赶快邀请更多朋友关注我们投票吧！</a>";	
					}
					return array('status'=>10000,'error'=>'你已经投过票了，当前'.($vote_member->count)."票!\n".$display_url);
				}
			}
			
			$vote_count = $vote_member->count+$vote_num;
			$this->CI->vote_model->update('VM',array('count'=>$vote_count),array('id'=>$vote_member->id));
			//记录投票数据
			$data['vmid']=$vote_member->id;
			$data['addtime']=TIME;
			$data['vid']=$vote_member->vid;
			$this->CI->vote_model->insert('VH', $data );
			//通知remoteurl
			if($vote->remoteurl){
				$vote->remoteurl=strtr($vote->remoteurl,array(
					'_remoteid_'=> $vote_member->remoteid,
					'_votenum_'=> $vote_count, 
					'_votecode_'=> $this->CI->vote_model->TP_STR.$vote_member->code, 
				));
				$param=array(
					'time'=>TIME,
					'key'=>md5('cms.wisheli.com'.TIME),
				);
				$this->CI->load->library('open');
				$vote->remoteurl=$vote->remoteurl.'&'.http_build_query($param);
				$resdata=$this->CI->open->http_post($vote->remoteurl.'&'.http_build_query($param),'');
				//var_dump($vote->remoteurl,$resdata);
				//exit;
			}
			$vote_member_name=$this->CI->vote_model->getVoteMemberName($vote_member,true);
			$msg="你已成功给编号（".$this->CI->vote_model->getVoteMemberID($vote_member,true)."）的 ".$vote_member_name." 投票，当前票数为{$vote_count}。";
			$msg.="\n{$vote_member_name}想你了，ta的位置岌岌可危，赶快邀请更多朋友关注我们投票吧！";
			if($vote->displayurl){
				$vote->displayurl=str_replace('_remoteid_', $vote_member->remoteid, $vote->displayurl);
				$msg.="\n<a href=\"".$vote->displayurl."\">查看{$vote_member_name}</a>";
			}else{
				$msg.="\n<a href=\"".site_url('vote/display/show/'.$vote_member->vid.'/'.$vote_member->id)."\">查看{$vote_member_name}</a>";	
			}
			return array('status'=>0,'msg'=>$msg,'count'=>$vote_count);
			
		}else{
			return array('status'=>10000,'error'=>'vmid is null');
		}
	}
	##################################################
	# 网页端投票系统
	##################################################
	function ifengvote2web($vmid=0){
		$ip=$this->CI->input->ip_address();
		$_POST['openid']=$ip;
		
		$this->CI->load->module_library('oauth','oauth_lib');
		$cookie=$this->CI->oauth_lib->getWechatCookie();
		if($cookie['openid']){
			$_POST['openid']=$cookie['openid'];
		}
		
		$_POST['ip']=$ip;
		$_POST['postid']=$vmid?$vmid:($_GET['vmid']+0);
		if($_POST['postid']=='3615'){
			return array('status'=>10000,'error'=>json_encode($_POST));
		}
		return $this->ifengvote();
	}
}
