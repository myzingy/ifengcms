<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class vote extends Public_Controller
{
	/**
	 * Constructor
	 */
	function vote(){
		parent::Mobile_Controller();
		// Load the Auth_form_processing class
		$this->load->library('vote_lib');
	}
	function index(){
		
		$data['module'] = 'article';
		$data['header'] = '新闻管理';
		$data['page'] = $this->config->item('backendpro_template_dir') . "mobile_home";
		$this->load->view($this->_container,$data);
	}
	function display($type='list',$vid,$vmid=''){
		$data['module'] = 'vote';
		$data['TP_STR'] = $this->vote_model->TP_STR;
		$res=$this->vote_model->getVoteList(array('id'=>$vid),array('limit'=>1),false);
		if($res->num_rows()>0){
			$vote=$res->row();
			$data['title']=$data['header'] = $vote->title;
		}
		if($vmid>0){//内容页
			$data['page'] = $this->config->item('backendpro_template_dir') . "mobile_show";
			
			$res=$this->vote_model->getVoteDataList(array('id'=>$vmid),array('limit'=>1),false);
			if($res->num_rows()>0){
				$data['data']=$res->row();
				$data['data']->sn=$this->vote_model->getVoteMemberID($data['data'],true);
				$data['data']->name=$this->vote_model->getVoteMemberName($data['data'],true);
			}
			
		}else{
			$data['page'] = $this->config->item('backendpro_template_dir') . "mobile_list";
			$limit=array('offset'=>$page,'limit'=>80);
			$where=array('VM.vid'=>$vid);
			$info=$this->vote_model->getVoteDataList($where,$limit,true,'code asc,id asc');
			if($info['data']->num_rows()>0){
				foreach ($info['data']->result() as $k=>$r) {
					$r->sn=$this->vote_model->getVoteMemberID($r,true);
					$r->url=site_url('vote/display/show/'.$r->vid.'/'.$r->id);
					if($vote->displayurl){
						$r->url=str_replace('_remoteid_', $r->remoteid, $vote->displayurl);
					}
					$r->name=$this->vote_model->getVoteMemberName($r,true);
					$data['data'][$k]=$r;
				}
			}
			$data['pagination']=$info['pagination'];
		}
		$this->load->view($this->_container,$data);
	}
	##################################################
	# 微信端投票系统
	##################################################
	function ifengvote(){
		$info=$this->vote_lib->ifengvote();
		die(json_encode($info));
	}
	##################################################
	# 专题数据展示接口
	##################################################
	function ifengvotedata($type='json',$vid){
		header("Content-type: application/x-javascript");
		parse_str($_SERVER['QUERY_STRING'],$_GET);
		$res=$this->vote_model->getVoteList(array('id'=>$vid),array('limit'=>1),false);
		if($res->num_rows()>0){
			$vote=$res->row();
			$limit=$_GET['limit']?$_GET['limit']:500;
		
			$order=$_GET['order']?$_GET['order']:'code';
			$by=$_GET['by']?$_GET['by']:'asc';
			$orderby="code asc,id asc";
			if($order){
				$orderby="$order $by";
			}
			$limit=array('offset'=>$page,'limit'=>$limit);
			$where=array('VM.vid'=>$vid);
			$info=$this->vote_model->getVoteDataList($where,$limit,true,$orderby);
			$data['data']=array();
			$data['status']=1;
			if($info['data']->num_rows()>0){
				foreach ($info['data']->result() as $k=>$r) {
					$r->sn=$this->vote_model->getVoteMemberID($r,true);
					$r->name=$this->vote_model->getVoteMemberName($r,true);
					if($vote->displayurl){
						$r->url=str_replace('_remoteid_', $r->remoteid, $vote->displayurl);
					}else{
						$r->url=site_url('vote/display/show/'.$r->id);
					}
					$data['data'][$k]=$r;
				}
				$data['status']=0;
			}
			$data['datarows']=$info['datarows'];
			$data['pagination']=$info['pagination'];
		}else{
			$data=array('status'=>10000,'error'=>'vote id empty!');
		}
		if($_GET['callback']){
			$json=$_GET['callback'].'('.json_encode($data).');';
		}else{
			$json=json_encode($data);
		}
		die($json);
	}
	##################################################
	# 投票数据展示分析
	##################################################
	function ifengvotereldata(){
		$query=$_SERVER['QUERY_STRING'];
		parse_str($query,$_GET);
		$vid=$_GET['vid']?$_GET['vid']:$vid;
		$data['module'] = 'vote';
		$data['page'] = $this->config->item('backendpro_template_dir') . "mobile_reldata";
		$res=$this->vote_model->getVoteList(array('id'=>$vid),array('limit'=>1),false);
		if($res->num_rows()>0){
			$vote=$res->row();
			$data['title']=$data['header'] = $vote->title;
		}
		$limit=array('offset'=>$page,'limit'=>5000);
		$where=array('VM.vid'=>$vid);
		$info=$this->vote_model->getVoteDataList($where,$limit,true,'count desc,code asc,id asc');
		
		$data['stime']=$_GET['stime']?$_GET['stime']:date('Y-m-d',time()-86400*7);
		$data['etime']=$_GET['etime']?$_GET['etime']:date('Y-m-d');
		$stimenum=strtotime($data['stime'].' 00:00:00');
		$etimenum=strtotime($data['etime'].' 23:59:59');
		
		$data['data']=array();
		if($info['data']->num_rows()>0){
			foreach ($info['data']->result() as $k=>$r) {
				$r->sn=$this->vote_model->getVoteMemberID($r,true);
				$r->name=$this->vote_model->getVoteMemberName($r,true);
				if($_GET['stime'] || $_GET['etime']){
					$timespace1=" AND (addtime>$stimenum AND addtime<$etimenum) ";
					//特定时间内的投票
					$sql="select count(*) as count
					from {$this->vote_model->_TABLES['VH']} 
					where `vid`='{$vid}' AND `vmid`='{$r->id}'
					$timespace1
					";
					$res=$this->vote_model->db->query($sql);
					$r->count = $res->row()->count+0;
					
				}
				//统计逃票
				$sql="select count(*) as count
				from ifengcms.cms_unsubscribe_weuser
				where openid in(
					select sigid
					from {$this->vote_model->_TABLES['VH']} 
					where `vid`='{$vid}' AND `vmid`='{$r->id}' 
					$timespace1
				) $timespace1";
				$res=$this->vote_model->db->query($sql);
				$r->tpcount = $res->row()->count+0;
				
				$r->fencount=$r->count - $r->tpcount;
				$info_do['order'][$r->id]=$r->fencount;
				$info_do['data'][$r->id]=$r;
			}
			arsort ($info_do['order']);
			$data['order']=$info_do['order'];
			$data['data']=$info_do['data'];
		}
		$this->load->view($this->_container,$data);
	}
	function setRemoteVodeCode($vid,$remoteid){
		$res=$this->vote_model->fetch('VM','code',null,array(
			'vid'=>$vid,
			'remoteid'=>$remoteid
		));
		$vm=$res->row();
		if($vm->code){
			$vm->code=$this->vote_model->TP_STR.$vm->code;
			$res=$this->vote_model->fetch('V','*',null,array(
				'id'=>$vid
			));
			if($res->num_rows()>0){
				$vote=$res->row();
				//将code通知到远程url
				$vote->remoteurl=strtr($vote->remoteurl,array(
					'_remoteid_'=> $remoteid,
					'_votenum_'=> 0, 
					'_votecode_'=> $vm->code, 
				));
				$param=array(
					'time'=>TIME,
					'key'=>md5('cms.wisheli.com'.TIME),
				);
				$this->load->library('open');
				$vote->remoteurl=$vote->remoteurl.'&'.http_build_query($param);
				$resdata=$this->open->http_post($vote->remoteurl,'');
			}
			
		}
		die('document.write("'.$vm->code.'");');
	}
}