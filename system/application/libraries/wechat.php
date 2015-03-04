<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once BASEPATH."/application/libraries/wechat.class.php";
class CI_Wechat extends Wechat{
	public function __construct()
	{
		$this->CI=get_instance();
		$this->CI->load->config('wechat');
		$wechat_conf=$this->CI->config->item('wechat');
		parent::__construct($wechat_conf);
		
		$this->filedata=array();
		if(__APP__POS=='BAE'){
			console('\$wechat_conf',$wechat_conf);
			$this->CI->load->library('mongo');
		}else{
			$this->file=BASEPATH.'cache/mongo.cache.php';
			if(file_exists($this->file)){
				$this->filedata=include($this->file);
			}
		}
		
	}
	/*
	 * 用于释放mongo
	 */ 
	public function __destruct(){
		if(__APP__POS=='BAE'){
			/*
			$connections = $this->CI->mongo->mo->getConnections();
			foreach ( $connections as $con )
			{
			    if ( $con['connection']['connection_type_desc'] == "SECONDARY" )
			    {
			        $closed = $this->CI->mongo->mo->close( $con['hash'] );
			    }
			}*/
			$this->CI->mongo->mo->close();
		}
	}
	
	public function localData($cachename,$value='',$expired=''){
		if(__APP__POS=='BAE'){
			$this->filedata=$this->CI->mongo->coll->findOne(array('cachename' => $cachename));
			if($value){
				$this->filedata=array(
					'cachename'=>$cachename,
					'value'=>$value,
					'expired'=>$expired+TIME
				);
				if($expired==-1){
					$this->filedata['expired']=-1;//永久保存
				}
				$this->CI->mongo->coll->remove(array('cachename' => $cachename));
				$this->CI->mongo->coll->insert($this->filedata);
				return true;
			}
			if($this->filedata['expired']>TIME || $this->filedata['expired']==-1){
				console('return expired',$this->filedata['value']);
				return $this->filedata['value'];
			}
		}else{
			if($value){
				$this->filedata[$cachename]=array(
					'value'=>$value,
					'expired'=>$expired+TIME
				);
				if($expired==-1){
					$this->filedata[$cachename]['expired']=-1;//永久保存
				}
				file_put_contents($this->file,"<?php\n return ".var_export($this->filedata,true).";");
				return true;
			}
			if($this->filedata[$cachename]['expired']>TIME || $this->filedata[$cachename]['expired']==-1){
				return $this->filedata[$cachename]['value'];
			}
		}
		return false;
	}
	/**
	 * 设置缓存，按需重载
	 * @param string $cachename
	 * @param mixed $value
	 * @param int $expired
	 * @return boolean
	 */
	protected function setCache($cachename,$value,$expired){
		//TODO: set cache implementation
		return $this->localData($cachename,$value,$expired);
	}
	/**
	 * 获取缓存，按需重载
	 * @param string $cachename
	 * @return mixed
	 */
	protected function getCache($cachename){
		//TODO: get cache implementation
		return $this->localData($cachename);
	}
	/**
	 * 清除缓存，按需重载
	 * @param string $cachename
	 * @return boolean
	 */
	protected function removeCache($cachename){
		//TODO: remove cache implementation
		if(__APP__POS=='BAE'){
			$this->CI->mongo->coll->remove(array('cachename' => $cachename));
		}else{
			$this->localData($cachename,'remove',0);
		}
		return false;
	}
}