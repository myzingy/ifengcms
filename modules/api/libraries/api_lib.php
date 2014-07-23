<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class api_lib
{
	var $user=null;	
	var $statusCode=array(
		-1=>'登录超时，请重新登录。'
		,0=>'正常'
		,10000=>'一般性错误'
		,10001=>'接口未定义'
		,10002=>'非法Token'
	);
	var $limit=15;
	function __construct(){
		// Get CI Instance
		$this->CI = &get_instance();
	}
	public function status(&$arr,$code,$str=''){
		$error=$this->statusCode[$code];
		if($error){
			$arr['status']=$code;
			$arr['error']=$str?$str:$error;
		}else{
			$arr['status']=10000;
			$arr['error']=$code;	
		}
		return $arr;
	}
	public function check(){
		$siteid=$this->CI->session->userdata('zdj_siteid');
		$appid=$this->CI->session->userdata('zdj_appid');
		if(!$siteid) return false;
		$this->user=array(
			'siteid'=>$siteid,
			'appid'=>$appid
		);
		return true;
	}
	//获取用户信息
	function getUserInfo(){
		$info= array('user'=>$this->user);
		$this->status($info, 0);
		return $info;
	}
	//文件上传
	function uploadImage(){
		$formName	= 'files';
		if($_FILES[$formName] && $_FILES[$formName]['error'][0] == 0){
			if (!in_array($_FILES[$formName]['type'][0], array('image/jpeg','image/pjpeg','image/x-png','image/png','image/gif'))){
				$this->status($info, 10000,'只允许上传jpg,gif,png格式图片!');
				return $info;
			}		
			$name = $_FILES[$formName]['name'][0];
			$filename = md5($name).substr($name, strrpos($name, '.'));
			
			$this->CI->load->module_helper('baebcs','baebcs');
			$baidu_bcs=baidu_bcs();
			$url=bsc_upload($baidu_bcs,$_FILES[$formName]['tmp_name'][0],$this->user['siteid'].'/'.$filename);
				
			if(!$url){
				$this->status($info, 10000,'上传文件失败，请重新上传');
			}else{
				$info= array('files'=>array('name'=>$url));	
				$this->status($info, 0);
			}
			return $info;
		}
		$this->status($info, 10000,'请选择上传文件!');
		return $info;
	}
	#################################################
	# Dashboard
	#################################################
	function getDashboard(){
		$Activity=$this->getActivityChat();
		$Fighter=$this->getFighterChat();
		$Buyers=$this->getBuyersChat();
		$Bedge=$this->getBedgeChat();
		$info['chat']=array(
			array('id'=>'#Activity','data'=>$Activity['data']),
			array('id'=>'#Fighter','data'=>$Fighter['data']),
			array('id'=>'#Buyers','data'=>$Buyers['data']),
			array('id'=>'#Bedge','data'=>$Bedge['data']),
		);
		$this->status($info, 0);
		return $info;
	}
	#################################################
	# 活动数据
	#################################################
	//获取活动统计图表数据
	function getActivityChat(){
		$this->CI->load->module_library('stat','stat_lib');
		$info['data']=$this->CI->stat_model->getActivityChat($this->user['appid'],$this->user['siteid']);
		$this->status($info, 0);
		return $info;
	}
	//获取活动列表
	function getActivityList(){
		$this->CI->load->module_library('stat','stat_lib');
		$search=array(
			'A.appid'=>$this->user['appid'],
			'A.siteid'=>$this->user['siteid']
		);
		$limit = array('limit' => $this->limit, 'offset' => '');
		$data=$this->CI->stat_model->getActivityList($search,$limit,true);
		$info['totalItems']=$data['datarows'];
		$info['items']=array();
		foreach ($data['data']->result() as $r) {
			array_push($info['items'],array(
				"id"=>$r->id,
				"title"=>$r->title,
	            "startTime"=> "YYYY-mm-dd",
	            "endTime"=>"YYYY-mm-dd",
	            "popnum"=> $r->popnum,
	            "disnum"=>$r->disnum,
	            "actnum"=>$r->actnum,
	            "buynum"=>$r->buynum,
	            "chatUrl"=> "#!/activity/chat",
	            "poperUrl"=> "#!/activity/pop/".$r->id,
	            "activerUrl"=> "#!/activity/active/".$r->id
			));
		}	
		$this->status($info, 0);
		return $info;
	}
	//获取活动详细数据
	function getActivityInfo($act,$id){
		switch ($act) {
			case 'preview':
				$this->CI->load->module_library('stat','stat_lib');
				$info['data']=$this->CI->stat_model->getActivityChat($this->user['appid'],$this->user['siteid'],$id+0);
				break;
			case 'audiences':
				$this->CI->load->module_library('stat','stat_lib');
				$info['data']=$this->CI->stat_model->getActivityChat($this->user['appid'],$this->user['siteid'],$id+0);
				break;
			case 'promotion':
				$this->CI->load->module_library('stat','stat_lib');
				$info['data']=$this->CI->stat_model->getActivityChat($this->user['appid'],$this->user['siteid'],$id+0);
				break;
			case 'pop':
				$this->CI->load->module_library('fighter','fighter_lib');
				$search=array(
					'FIG.appid'=>$this->user['appid'],
					'FIG.siteid'=>$this->user['siteid']
				);
				$limit = array('limit' => $this->limit, 'offset' => '');
				$data=$this->CI->fighter_model->getFighterList($search,$limit,true);
				$info['totalItems']=$data['datarows'];
				$info['items']=array();
				foreach ($data['data']->result() as $r) {
					array_push($info['items'],array(
						"id"=>$r->id,
						"name"=>$r->name,
			            "phone"=> $r->phone,
			            "popnum"=> $r->popnum,
			            "disnum"=> $r->disnum,
			            "actnum"=> $r->actnum,
			            "buynum"=> $r->buynum,
			            "status"=> $r->status,
			            "infoUrl"=> "#!/fighter/info",
			            "updateUrl"=> "#!/fighter/update",
					));
				}
				break;
			case 'buy':
				
				break;
			default:
				
				break;
		}
		$this->status($info, 0);
		return $info;
	}
	#################################################
	# 战斗机数据
	#################################################
	//获取战斗机统计图表数据
	function getFighterChat(){
		$this->CI->load->module_library('fighter','fighter_lib');
		$info['data']=$this->CI->fighter_model->getFighterChat($this->user['appid'],$this->user['siteid']);
		$this->status($info, 0);
		return $info;
	}
	//获取战斗机列表
	function getFighterList(){
		$this->CI->load->module_library('fighter','fighter_lib');
		$search=array(
			'FIG.appid'=>$this->user['appid'],
			'FIG.siteid'=>$this->user['siteid']
		);
		$limit = array('limit' => $this->limit, 'offset' => '');
		$data=$this->CI->fighter_model->getFighterList($search,$limit,true);
		$info['totalItems']=$data['datarows'];
		$info['items']=array();
		foreach ($data['data']->result() as $r) {
			array_push($info['items'],array(
				"id"=>$r->id,
				"name"=>$r->name,
	            "phone"=> $r->phone,
	            "popnum"=> $r->popnum,
	            "disnum"=> $r->disnum,
	            "actnum"=> $r->actnum,
	            "buynum"=> $r->buynum,
	            "status"=> $r->status,
	            "infoUrl"=> "#!/fighter/info",
	            "updateUrl"=> "#!/fighter/update",
			));
		}
		$this->status($info, 0);
		return $info;
	}
	#################################################
	# 消费者数据
	#################################################
	//获取消费者统计图表数据
	function getBuyersChat(){
		$this->CI->load->module_library('buyers','buyers_lib');
		$info['data']=$this->CI->buyers_model->getBuyersChat($this->user['appid'],$this->user['siteid']);
		$this->status($info, 0);
		return $info;
	}
	//获取消费者列表
	function getBuyersList(){
		$this->CI->load->module_library('buyers','buyers_lib');
		$search=array(
			'U.appid'=>$this->user['appid'],
			'U.siteid'=>$this->user['siteid']
		);
		$limit = array('limit' => $this->limit, 'offset' => '');
		$data=$this->CI->buyers_model->getBuyersList($search,$limit,true);
		$info['totalItems']=$data['datarows'];
		$info['items']=array();
		foreach ($data['data']->result() as $r) {
			array_push($info['items'],array(
				"name"=>$r->name,
				"phone"=>$r->phone,
				"id"=>$r->id,
	            "buynum"=> $r->buynum,
	            "price"=>$r->price,
	            "infoUrl"=> "#!/buyers/info/".$r->id,
			));
		}
		$this->status($info, 0);
		return $info;
	}
	#################################################
	# 徽章数据
	#################################################
	//获取徽章列表
	function getBedgeList(){
		$this->CI->load->module_library('bedge','bedge_lib');
		$search=array(
			'B.appid'=>$this->user['appid'],
			'B.siteid'=>$this->user['siteid']
		);
		$limit = array('limit' => $this->limit, 'offset' => '');
		$data=$this->CI->bedge_model->getBedgeList($search,$limit,true);
		$info['totalItems']=$data['datarows'];
		$info['items']=array();
		foreach ($data['data']->result() as $r) {
			array_push($info['items'],array(
				"name"=>$r->name,
				"ico"=>$r->ico,
				"type"=>$r->type,
				"relnum"=>$r->relnum,
	            "getnum"=> $r->getnum,
	            "status"=>$r->status,
	            "endtime"=>date("Y-m-d H:i",$r->endtime),
	            "updateUrl"=> "#!/bedgeUpdate/".$r->id,
	            "poperUrl"=> "#!/activity/pop",
	            "activerUrl"=> "#!/activity/active"
			));
		}
		$this->status($info, 0);
		return $info;
	}
	function getBedgeData($id=0){
		$this->CI->load->module_library('bedge','bedge_lib');
		$res=$this->CI->bedge_model->fetch('B','*',null,array('id'=>$id));
		$info=array();
		if($res->num_rows()>0){
			$row=$res->row();
			$info['item']=$row;
			$info['item']->endtime=$info['item']->endtime?$info['item']->endtime:TIME;
			$info['item']->rules_data=json_decode($info['item']->rules_data);
		}
		$this->status($info, 0);
		return $info;
	}
	function setBedgeDate(){
		$this->CI->load->module_library('bedge','bedge_lib');
		$info=$this->CI->bedge_lib->setBedgeDate($this->user);
		return $info;
	}
	function getBedgeChat(){
		$this->CI->load->module_library('bedge','bedge_lib');
		$info['data']=$this->CI->bedge_model->getBedgeChat($this->user['appid'],$this->user['siteid']);
		$this->status($info, 0);
		return $info;
	}
}