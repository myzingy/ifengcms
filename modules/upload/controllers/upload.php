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
 * Welcome
 *
 * The default welcome controller
 *
 * @package  	BackendPro
 * @subpackage  Controllers
 */
class upload extends Public_Controller
{
	function upload()
	{
		parent::Public_Controller();
	}
	function fieldfile(){
		if($_POST){
			$day=date('Ymd',TIME);
			$path='uploads/'.$day;
			$dir=FCPATH.$path;
			@mkdir($dir,0777);
			$config['upload_path'] = $dir;
			$config['allowed_types'] = 'bmp|jpg|jpeg|png';
			$config['max_size'] = 1024*8;
			$config['max_width']  = 8000;
			$config['max_height']  = 8000;
			$config['encrypt_name'] = true;
			$this->load->library('upload', $config);
			if (!$this->upload->do_upload('pic1'))
			{
				$upload = $this->upload->display_errors();
			} 
			else
			{
				$upload = $this->upload->data();
			}
			if(is_array($upload)){
				$res=array(
					'errno'=>'0',
					'imgurl'=>base_url().$path.'/'.$upload['file_name']
				);
			}else{
				$res=array(
					'errno'=>1,
					'errmsg'=>'失败请更换其他设备或上传截图'
				);
			}
			die(json_encode($res));
		}else{
			parse_str($_SERVER['QUERY_STRING'],$_GET);
			$name=$_GET['name'];
			$type=$_GET['type'];
			if(strstr($type,"image/")===false){
				die('{"errno":1,"errmsg":"imgfile type error!"}');
			}
			$path='uploads/'.date('Ymd/',time());
			@mkdir($path,0777);
			$res=array();
			$filext=explode(".", $name);
			$filext=$filext[count($filext)-1];
			$fillpath=$path.md5(time()).'.'.$filext;
			file_put_contents($fillpath,file_get_contents("php://input"));
			
			list($width, $height, $type, $attr) = getimagesize($fillpath);
			
			$type = image_type_to_extension($type);
			$fun='imagecreatefrom'.substr($type,1);
			eval("\$old_img=@$fun(\$fillpath);");
			if($old_img){
				$height1=ceil(($width*360)/480);
			
				$new_img=imagecreatetruecolor(480,$height1);
				$bgColor = imagecolorallocate($new_img, 255,255,255);
				imagefill($new_img , 0,0 , $bgColor);
				
				imagecopyresized($new_img,$old_img,0,0,0,0,480,$height1,$width,$height);
				imagedestroy($old_img);
				$fun='image'.substr($type,1);
				eval("$fun(\$new_img,\$fillpath);");
			}
			
			
			$res=array(
				'errno'=>'0',
				'imgurl'=>base_url().$fillpath,
			);
			die(json_encode($res));
		}
		
		
	}
	function index()
	{
		//upload 
		$day=date('Ymd',TIME);
		$path='uploads/'.$day;
		$dir=FCPATH.$path;
		$filename=$day.rand(10000,99999);
		@mkdir($dir,0777);
		$res=array();
		if(isset($_SERVER['HTTP_CONTENT_DISPOSITION'])&&preg_match('/attachment;\s+name="(.+?)";\s+filename="(.+?)"/i',$_SERVER['HTTP_CONTENT_DISPOSITION'],$info)){//HTML5上传
			$filext=explode(".", $info[2]);
			$filext=$filext[count($filext)-1];
			$fillpath=$path.'/'.$filename.'.'.$filext;
			file_put_contents($fillpath,file_get_contents("php://input"));
			$localName=urldecode($info[2]);
			$res=array(
				'err'=>'',
				'msg'=>array(
					'url'=>'!'.base_url().$fillpath,
					'localname'=>$localName
				)
			);
		}else{
			$config['upload_path'] = $dir;
			$config['allowed_types'] = 'bmp|jpg|jpeg|png';
			$config['max_size'] = 1024*8;
			$config['max_width']  = 8000;
			$config['max_height']  = 8000;
			//$config['file_name'] = $filename;
			$config['encrypt_name'] = true;
			$this->load->library('upload', $config);
			if (!$this->upload->do_upload('filedata'))
			{
				$upload = $this->upload->display_errors();
			} 
			else
			{
				$upload = $this->upload->data();
			}
			
			if(is_array($upload)){
				$imgflag=true;
				$res=array(
					'err'=>'',
					'msg'=>array(
						'url'=>'!'.base_url().$path.'/'.$upload['file_name'],
						'localname'=>$upload['orig_name']
					)
				);
			}else{
				$res=array(
					'err'=>$upload,
					'msg'=>''
				);
			}
		}
		die(json_encode($res));
	}
}


/* End of file welcome.php */
/* Location: ./modules/welcome/controllers/welcome.php */