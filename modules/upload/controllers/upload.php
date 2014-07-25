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
class upload extends Admin_Controller
{
	function Welcome()
	{
		parent::Admin_Controller();
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
			$config['max_size'] = '2048';
			$config['max_width']  = '2000';
			$config['max_height']  = '1000';
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