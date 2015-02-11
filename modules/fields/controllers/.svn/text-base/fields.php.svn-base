<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class fields extends Public_Controller
{
	/**
	 * Constructor
	 */
	function fields(){
		parent::Public_Controller();
		// Load the Auth_form_processing class
		$this->load->library('fields_lib');
		$this->load->library('user_agent');
	}
	function index(){
echo<<<ENDHTMLAAA
<!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <meta charset="utf-8">
        <meta content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport" />
        <link href="http://cdn.bootcss.com/twitter-bootstrap/2.0.4/css/bootstrap.min.css" rel="stylesheet">
		<link href="http://cdn.bootcss.com/twitter-bootstrap/2.0.4/css/bootstrap-responsive.min.css" rel="stylesheet">
		
        <script src="http://apps.bdimg.com/libs/jquery/1.8.3/jquery.min.js"></script>
		<script src="http://cdn.bootcss.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
	</head>
    <body>
    	<div class="container">
      		<div class="row clearfix">
    			感谢你的支持，你的数据已经提交。
    		</div>
    	</div>
    </body>
</html>
ENDHTMLAAA;
	}
	function setIframeHeight($iframeid='fieldsForm',$notAct=''){
		$notAct=explode('&', $notAct);
$js['fromaddr']=<<<ENDHTML
	var {$iframeid}_dom=document.getElementById('{$iframeid}');
	var fromaddr=location.href.match(/fromaddr=[^&]+/);
	if(fromaddr){
		var src={$iframeid}_dom.src.replace(/fromaddr=[^&]+/,'');
		src+=(src.indexOf('?')>-1?'&':'?')+fromaddr;
		{$iframeid}_dom.src=src;
	}
ENDHTML;
$js['height']=<<<ENDHTML
	if (typeof window.addEventListener != 'undefined') {
	   window.addEventListener('message', function(e) {
	   	var ifname_dom=document.getElementById(e.data.ifname?e.data.ifname:'{$iframeid}');
	   	ifname_dom.style.height=e.data.height+'px';
	   }, false);
	} else if (typeof window.attachEvent != 'undefined') {
	   window.attachEvent('onmessage', function(e) {
	   	var ifname_dom=document.getElementById(e.data.ifname?e.data.ifname:'{$iframeid}');
	   	ifname_dom.style.height=e.data.height+'px';
	   });
	}
ENDHTML;
		foreach ($js as $act => $value) {
			if(in_array($act, $notAct))continue;
			echo $value;
		}
	}
	function display($queryid=0,$noAct=''){
		parse_str($_SERVER['QUERY_STRING'],$get);
		$id=$queryid+0;
		if($id>0):
			$res=$this->fields_model->fetch('F','*',null,array('id'=>$id));
			if($res->num_rows()>0):
				$data=$res->row();
				if($queryid==7 || $queryid=='7_part2'){
					//创业大赛
					$fields_htmls=file_get_contents('modules/fields/views/chuangyedasai'.(str_replace('7', '', $queryid)).'.php');
					$fields_htmls=strtr($fields_htmls,array(
						'{fieldid}'=>$id,
						'{base_url}'=>base_url(),
					));
				}elseif($queryid==8){
					$fields_htmls=file_get_contents('modules/fields/views/chuangyedasai_zhiyuanzhe.php');
					$fields_htmls=strtr($fields_htmls,array(
						'{fieldid}'=>$id,
						'{base_url}'=>base_url(),
					));
				}else{
					$fields_htmls=strtr($data->fields_htmls,array(
						'&lt;'=>'<',
						'&gt;'=>'>',
					));
					$fields_htmls='<div class="container"><div class="row clearfix">'.$fields_htmls.'</div></div>';
				}
				if($get['fromaddr']){
					//insert fromadd
					$fields_htmls=str_replace('<fieldset>', '<fieldset><input type="hidden" name="fromaddr" value="'.$get['fromaddr'].'">', $fields_htmls);
				}
				$fields_style=$data->fields_style;
				if($this->agent->is_mobile()){
					$fields_style=$data->fields_style_mobile;
				}
				$base_url=base_url();
				$autoHeight=($noAct=='height')?'':'crossFrame.init();';
echo<<<ENDHTML
<!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <meta charset="utf-8">
        <meta content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport" />
        <link href="http://cdn.bootcss.com/twitter-bootstrap/2.3.2/css/bootstrap.min.css" rel="stylesheet">
    	<link href="http://cdn.bootcss.com/twitter-bootstrap/2.3.2/css/bootstrap-responsive.min.css" rel="stylesheet">
		<link href="{$base_url}assets/css/fields.upload.css" rel="stylesheet">
		<script type="text/javascript">
			var base_url="{$base_url}";
			if(typeof window.console=='undefined'){window.console={log:function(){}};}
		</script>
		<title>{$data->name}</title>
        <script src="http://apps.bdimg.com/libs/jquery/1.9.1/jquery.min.js"></script>
		<script src="http://cdn.bootcss.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
		<script src="http://cms.wisheli.com/mnew/js/jquery.form.js"></script>
		<script src="{$base_url}assets/js/webuploader.js"></script>
		<script src="{$base_url}assets/js/swfupload/swfupload.js"></script>
		<script src="{$base_url}assets/js/fields.form.js"></script>
		<script type="text/javascript">
			$(function(){
				function Message() {
					this.send = function(a, b) {
						window.postMessage ? a.postMessage(b, "*") : a.name = b
					}
				}
				var msg = new Message, crossFrame = {};
				crossFrame.docHeight = function() {
					return document.body.offsetHeight
				};
				crossFrame.init = function(a) {
					crossFrame.setHeight();
				};
				crossFrame.setHeight = function() {
					msg.send(window.top, {
						ifname:'{$get['ifname']}'
						,height:crossFrame.docHeight()
					});
				};
				{$autoHeight}
				//totalcode
				(function(){
					var id={$id};
					if(id>0){
						$.ajax({
							url:'{$base_url}index.php/api/act/totalcodeActivity/'+id+'/1',
							dataType:'html',
							beforeSend:function(){},
							complete:function(XMLHttpRequest){
								$(XMLHttpRequest.responseText).appendTo('body');
							}
						});
					}
				})();
			});
		</script>
    </head>
    <body>
    	{$fields_htmls}
    	<div style="display:none;">
    		<a id="alertModalLink" href="#alertModal" role="button" class="btn" data-toggle="modal"></a> 
			<a id="loadModalLink" href="#loadModal" role="button" class="btn" data-toggle="modal"></a>
    	</div>
    	<div id="modalhelp">
    		<div id="alertModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			  <div class="modal-header">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			    <h3 id="myModalLabel">提示信息</h3>
			  </div>
			  <div class="modal-body">
			    <p>One fine body…</p>
			  </div>
			  <div class="modal-footer">
			    <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">确定</button>
			  </div>
			</div>
			<div id="loadModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			  <div class="modal-body">
			    <p>Loading...</p>
			  </div>
			  <div class="modal-footer" style="display:none;">
			    <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">确定</button>
			  </div>
			</div>
		</div>
    </body>
    {$fields_style}
</html>
ENDHTML;
			endif;
		endif;
		exit;		
	}
	function form($fid=0){
		$info=$this->fields_lib->form($fid);
		die(json_encode($info));
	}
	function getInfo(){
		$limit=array('offset'=>$page,'limit'=>1);
		$where=array();
		$res=$this->fields_model->getFieldsList($where,$limit,false);
		if($res->num_rows()>0){
			$row=$res->row();
			$info=array(
				'status'=>0,
				'data'=>array(
					'name'=>$row->name,
					'fields_json'=>json_decode($row->fields_json),
					'id'=>$row->id
				)
			);
		}else{
			$info=array(
				'status'=>10000,
			);
		}
		die(json_encode($info));
	}
	function fieldsDataList($id){
		$limit=array('offset'=>0,'limit'=>1);
		$where=array('id'=>$id);
		$res=$this->fields_model->getFieldsList($where,$limit,false);
		if($res->num_rows()>0){
			$row=$res->row();
			$table=$this->fields_model->fileds_table_prefix.$row->tab_name;
			$limit=array('offset'=>$page,'limit'=>30);
			$where=array('status'=>0);
			$info=$this->fields_model->getFieldsDataList($table,$where,$limit,true,'id asc');
			if($info['datarows']>0){
				$total['status']=0;
				$total['rows']=$info['datarows'];
				$total['data']=$info['data']->result();
				$total['limit']=$this->fields_model->page+$limit['limit'];
				$total['isnext']=$info['data']->num_rows<$limit['limit']?false:true;
				$info=array(
					'status'=>0,
					'data'=>$total
				);
			}else{
				$info=array(
					'status'=>1,
					'error'=>'数据源数据为空'
				);
			}
		}else{
			$info=array(
				'status'=>10000,
				'error'=>'没有找到数据源'
			);
		}
		die(json_encode($info));
	}
	function fieldsDataPage($type='html',$id){
		$res=$this->fields_model->fetch('F','*',null,array('id'=>$id));
		if($res->num_rows()>0){
			$field=$res->row();
			$table=$this->fields_model->fileds_table_prefix.$field->tab_name;
			$data['fid']=$fid;
			$data['dbkey']=json_decode($field->fields_json,true);
			$data['fields']=$this->fields_model->db->list_fields($table);
			$data['tab_index']=$fid;
			
			$limit=array('offset'=>$page,'limit'=>100);
			//$where=array('status'=>0);
			$info=$this->fields_model->getFieldsDataList($table,$where,$limit,true,'id asc');
			$data['members'] = $info['data'];
			$data['pagination']=$info['pagination'];
			
		}
		$data['module'] = 'fields';
		$data['title']=$data['header'] = $field->name;
		$data['page'] = $this->config->item('backendpro_template_dir') . "list_fields_data_public";
		$this->load->view($this->_container,$data);
	}
	///////////////
	function checkManCode(){
		$info=$this->fields_lib->checkManCode();
		die(json_encode($info));
	}
	function formChuangYe($fid=0){
		$info=$this->fields_lib->formChuangYe($fid);
		die(json_encode($info));
	}
	function formChuangYePart2($fid=0){
		$info=$this->fields_lib->formChuangYePart2($fid);
		die(json_encode($info));
	}
}