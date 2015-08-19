<?php

class tuksi404 {
	
	private static $alertLevel = 3;
	
	public function __construct(){
		
	}
	
	static function checkUrl(){
		
		$base = dirname(__FILE__) . "/../../services/404/";
		
		$host = $_SERVER['HTTP_HOST'];
		
		$completeUrl = $_SERVER['REQUEST_URI'];
		$url = strtok($completeUrl,"?");
		$urlMd5 = md5($url);
		
		if(!is_dir($base . $host)){
			mkdir($base . $host,0777);
		}
		
		$filepath = $base . $host . "/" . $urlMd5;
		
		if(file_exists($filepath.".alert")){
			return "";
		}
		
		$arrInfo = array(	'url' => $url,
											'times' => 0,
											'requests' => array()
		);
		
		if(file_exists($filepath)){
			$arrInfo = unserialize(file_get_contents($filepath));
		}
		
		$arrInfo['times']++;
		
		$arrInfo['requests'][] = array(	'referer' => $_SERVER['HTTP_REFERER'],
																		'url' => $completeUrl,
																		'date' => date('d-m-y H:i:s'));
		
		$strComplete = serialize($arrInfo);
		
		file_put_contents($filepath,$strComplete);
		
		if($arrInfo['times'] > self::$alertLevel) {
			
			rename($filepath,$filepath.".alert");
			
			$strText = "The following url has been tried to access over " .self::$alertLevel . " times and does not exsist:\n\n" . $host . $url;
			$strText.= "\n\nAccessed on the following urls:\n";
			foreach ($arrInfo['requests'] as $arrReq) {
				$strText.= $arrReq['date'] . " : " . $arrReq['url'] . " | " . $arrReq['referer'] . "\n";
			}
			$arrConf = tuksiConf::getConf();
			mail($arrConf['email']['admin_email'],"404 alert on " . $host,$strText);
			
			return "";
		}
	}
}
?>