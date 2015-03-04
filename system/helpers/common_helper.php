<?php
require_once(BASEPATH.'application/libraries/baelog/BaeLog.class.php');
if ( ! function_exists('console'))
{
	function console()
	{
		$title=	"####".date('Y-m-d H:i:s',TIME)."####\n";
		$logdata='';
		$params=func_get_args();
		foreach ($params as $param) {
			if(is_array($param) || is_object($param)){
				$param=var_export($param,true);
			}
			$logdata.=$param."\n";
		}
		
		if(__APP__POS=='BAE'){
			
			$secret = array("user"=>"nkgKs3jiSUpl1chz0P0RR4QS","passwd"=>"qhlIfwXZYfwCroPcfOUOkCkf0wr6MW74" );
			$log = BaeLog::getInstance($secret);
			$log->setLogLevel(16);
			$log->Debug($logdata);
		}else{
			$file=BASEPATH.'logs/'.date('Ymd',TIME).'_debug.log';
			$fp=@fopen($file, 'a+');
			if($fp){
				fwrite($fp, $title);
				fwrite($fp, $logdata."\n");
			}
			@fclose($fp);
		}
	}	
}