<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class draw_lib
{
	function draw_lib(){
		// Get CI Instance
		$this->CI = &get_instance();
		$this->CI->load->helper('form');
		$this->CI->load->library('validation');
		$this->CI->bep_assets->load_asset_group('FORMS');
		$this->CI->load->model('draw_model');
	}
	function form_prize($container,$id){
		$fields['id'] = "ID";
		$fields['name'] = "奖品名称";
		$fields['total'] = "奖品发行数";
		$fields['stock'] = "奖品库存";
		$fields['gailv'] = "获奖概率";
		
		$rules['name'] = 'trim|required|max_length[100]';
		$rules['total'] = 'trim|required|numeric|min_number[1]';
		$rules['stock'] = 'trim|required|numeric|min_number[0]';
		$rules['gailv'] = 'trim|required|numeric|min_number[0]';
		
		$this->CI->validation->set_fields($fields);
		$this->CI->validation->set_rules($rules);
		if ( $this->CI->validation->run() === FALSE )
		{
			// Output any errors
			$this->CI->validation->output_errors();
			// Display page
			$data['header'] = '编辑奖品';
			$data['page'] = $this->CI->config->item('backendpro_template_dir') . 'form_prize';
			$data['module'] = 'draw';
			
			//edit data
			if($id>0){
				$data['editinfo']=$this->CI->draw_model->fetch('P','*',null,array('id'=>$id))->row_array();
			}	
			$this->CI->load->view($container,$data);
		}else{
			// Submit form
			return $this->_form_prize($fields);
		}
	}
	function _form_prize($fields){
		foreach ($fields as $key => $value) {
			$data[$key]=$this->CI->input->post($key);
		}
		if($data['id']>0){
			$this->CI->draw_model->update('P',$data,array('id'=>$data['id']));
		}else{
			$this->CI->draw_model->insert('P',$data);
		}
		flashMsg('success','操作成功');
		redirect('draw/admin/draw/prize/','location');
	}
	function form_activity($container,$id){
		$fields['id'] = "ID";
		$fields['title'] = "活动名称";
		$fields['stime'] = "开始时间";
		$fields['etime'] = "结束时间";
		
		$rules['title'] = 'trim|required|max_length[100]';
		$rules['stime'] = 'trim|required';
		$rules['etime'] = 'trim|required';
		
		$this->CI->validation->set_fields($fields);
		$this->CI->validation->set_rules($rules);
		if ( $this->CI->validation->run() === FALSE )
		{
			// Output any errors
			$this->CI->validation->output_errors();
			// Display page
			$data['header'] = '编辑抽奖活动';
			$data['page'] = $this->CI->config->item('backendpro_template_dir') . 'form_activity';
			$data['module'] = 'draw';
			
			//edit data
			if($id>0){
				$data['editinfo']=$this->CI->draw_model->getDrawAllInfo($id);
			}	
			$this->CI->load->view($container,$data);
		}else{
			// Submit form
			return $this->_form_activity($fields);
		}
	}
	function _form_activity($fields){
		foreach ($fields as $key => $value) {
			$data[$key]=$this->CI->input->post($key);
		}
		$d2p=trim($this->CI->input->post('d2p'));
		$data['stime']=strtotime($data['stime']);
		$data['etime']=strtotime($data['etime']);
		if($data['id']>0){
			$this->CI->draw_model->update('D',$data,array('id'=>$data['id']));
			$this->CI->draw_model->delete('D2P',array('did'=>$data['id']));
		}else{
			$this->CI->draw_model->insert('D',$data);
			$data['id']=$this->CI->draw_model->db->insert_id();
		}
		if($d2p){
			$d2p=explode(',', $d2p);
			foreach ($d2p as $pid) {
				$this->CI->draw_model->insert('D2P',array('did'=>$data['id'],'pid'=>$pid));
			}
		}
		flashMsg('success','操作成功');
		redirect('draw/admin/draw/activity/','location');
	}
	function dodraw($id){
		$ceshiFlag=true;
		//315活动特殊处理$id
		$id=$this->create_id_315($id);
		
		$name=trim($this->CI->input->get('name'));
		$phone=trim($this->CI->input->get('phone'));
		$openid=trim($this->CI->input->get('openid'));
		$info=$this->CI->draw_model->getDrawAllInfo($id);
		if(!$info){
			return array('status'=>10000,'error'=>'抽奖活动不存在！');
		}
		if($info['draw']->stime>TIME){
			return array('status'=>10000,'error'=>$info['draw']->title.' 还没有开始！');
		}
		if($info['draw']->etime<TIME){
			return array('status'=>10000,'error'=>$info['draw']->title.' 已经结束！');
		}
		if(!$info['prize']){
			return array('status'=>10000,'error'=>'抽奖活动没有任何奖品！');
		}
		$this->CI->load->module_library('oauth','oauth_lib');
		$cookie=$this->CI->oauth_lib->getWechatCookie();
		if($cookie['openid']){
			$openid=$cookie['openid'];
			$ceshiFlag=false;
		}else{
			$ceshiFlag=true;
			$name="[测]".$name;
		}
		if(!$openid){
			if(!$name){
				return array('status'=>10000,'error'=>'请填写姓名！');
			}
			if(!$phone){
				return array('status'=>10000,'error'=>'请填写手机号！');
			}
		}
		if($phone){
			if(!preg_match("/^1[0-9]{10}$/", $phone)){
				return array('status'=>10000,'error'=>'手机号填写错误！');
			}
		}
		
		//检查用户并入库
		$history=$this->CI->draw_model->setDrawHistory($id,$openid,$name,$phone,$ceshiFlag);
		$history_id=$history['id'];
		if($history_id<1){
			return array('status'=>10000,'error'=>'你已经抽过奖了！','prize'=>$history['prize']);
		}
		//概率基数
		$gl=10000;
		$gailv=array();
		$prize_data=array();
		$prize_sli=array();
		
		foreach ($info['prize'] as $prize) {
			//商品没有库存就不参与抽奖了
			if($prize->stock<1) continue;
			$prize_data[$prize->id]=$prize;
			$prize_sli[$prize->id]=$prize->gailv;
			//$gailv=array_fill(count($gailv), $prize->gailv,$prize->id);
		}
		$gl*=count($prize_sli);
		//概率从高到低排序
		arsort ($prize_sli);
		foreach ($prize_sli as $pid=>$gailvnum) {
			$len=count($gailv);
			if($len>=$gl)break;
			$gailv_new=array_fill($len, $gailvnum,$pid);
			$gailv=array_merge($gailv,$gailv_new);
		}
		
		
		$len=count($gailv);
		if($len<$gl){
			$gailv_new=array_fill($len,$gl-$len ,0);
			$gailv=array_merge($gailv,$gailv_new);
		}
		shuffle($gailv);
		$info=array('error'=>'运气真不好，什么都没中!');
		
		//更新活动
		if(!$ceshiFlag) $this->CI->draw_model->updateActivityPrizeNum($id,'ack_num');
			
		if($gailv[0] && $prize_data[$gailv[0]]){
			//中奖了
			$info['status']=1;
			$info['msg']='中奖了,恭喜你获得'.$prize_data[$gailv[0]]->name;
			$info['prizeName']=$prize_data[$gailv[0]]->name;
			$this->CI->draw_model->update('DH'
				,array('pid'=>$gailv[0],'pname'=>$prize_data[$gailv[0]]->name)
				,array('id'=>$history_id)
			);
			//减库存
			if(!$ceshiFlag) $this->CI->draw_model->updatePrizeStockNum($gailv[0]);
			//更新获奖人数
			if(!$ceshiFlag) $this->CI->draw_model->updateActivityPrizeNum($id,'win_num');
		}else{
			$info['status']=0;
		}
		return $info;	
	}
	function updateUserInfo($id){
		$name=trim($this->CI->input->get('name'));
		$phone=trim($this->CI->input->get('phone'));
		$openid=trim($this->CI->input->get('openid'));
		$info=$this->CI->draw_model->getDrawAllInfo($id);
		if(!$info){
			return array('status'=>10000,'error'=>'抽奖活动不存在！');
		}
		if(!$info['prize']){
			return array('status'=>10000,'error'=>'抽奖活动没有任何奖品！');
		}
		$this->CI->load->module_library('oauth','oauth_lib');
		$cookie=$this->CI->oauth_lib->getWechatCookie();
		if($cookie['openid']){
			$openid=$cookie['openid'];
		}
		if(!$name){
			return array('status'=>10000,'error'=>'必填填写姓名！');
		}
		if(!$phone || !preg_match("/^1[0-9]{10}$/", $phone)){
			return array('status'=>10000,'error'=>'手机号填写错误！');
		}
		$this->CI->draw_model->setDrawHistory($id,$openid,$name,$phone);
		$info['status']=0;
		return $info;
	}
	function getLucker($id){
		//315活动特殊处理$id
		$id=$this->create_id_315($id);
		
		$info=array('status'=>10000,'error'=>'没有数据！');
		$where=array(
			'did'=>$id,
			'pid > '=>0,
		);
		$limit=array('limit' => 50, 'offset' => '');
		$res=$this->CI->draw_model->getHistoryList($where, $limit ,false);
		if($res->num_rows()>0){
			$data=array();
			foreach ($res->result() as $r) {
				array_push($data,array(
					'name'=>$r->name,
					'phone'=>$r->phone,
					'pname'=>$r->pname
				));
			}
			$info=array(
				'status'=>0,
				'data'=>$data
			);
		}
		return $info;
	}
	function check($id){
		//315活动特殊处理$id
		$id=$this->create_id_315($id);
		$openid=trim($this->CI->input->get('openid'));
		$info=array('status'=>10000,'error'=>'没有数据！');
		$where=array(
			'did'=>$id,
			'openid'=>$openid,
		);
		$limit=array('limit' => 1, 'offset' => '');
		$res=$this->CI->draw_model->getHistoryList($where, $limit ,false);
		if($res->num_rows()>0){
			$row=$res->row();
			$info=array(
				'status'=>0,
				'error'=>'没有中奖'
			);
			if($row->pid){
				$info=array(
					'status'=>1,
					'prizeName'=>$row->pname,
					'prizeTime'=>date('Y-m-d H:i:s',$row->addtime)
				);
			}
		}
		return $info;
	}
	function create_id_315($id){
		if($id=='3'){
			$dd=date('d',TIME);
			return $dd-7;
		}
		return $id;
	}
}