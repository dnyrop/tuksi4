<?php

/**
 * Perform common file tasks.
 *
 * @todo Test class
 * @package tuksiBase
 */
class tuksiFile {
	
	/**
	 * Make safe url for filename
	 *
	 * @param unknown_type $filename
	 * @return unknown
	 */
	function makeUrlfile($filename) {
		$name = strtolower($filename);
		$name = str_replace("Ä", "a", $name);
		$name = str_replace("É", "ee", $name);
		$name = str_replace("Á", "a", $name);
		$name = str_replace("/", ",", $name);
		$name = str_replace("æ", "ae", $name);
		$name = str_replace("Æ", "ae", $name);
		$name = str_replace("ø", "oe", $name);
		$name = str_replace("Ø", "oe", $name);
		$name = str_replace("å", "aa", $name);
		$name = str_replace("Å", "aa", $name);
		$name = str_replace("&aelig;",  "ae", $name);
		$name = str_replace("&oslash;", "oe", $name);
		$name = str_replace("&aring;",  "aa", $name);
		$name = str_replace("&", "_", $name);
		
		//$name = trim($name);
		$name = str_replace("   ", "_", $name);
		$name = str_replace("  ", "_", $name);
		$name = str_replace(" ", "_", $name);
		
		$name = urlencode($name);
		
		return $name;
	}

	/**
	 * Return extension
	 *
	 * @param string $filename
	 * @return string
	 */
	function getExt($filename) {
		preg_match("/\.([a-zA-Z0-9]+)$/", $filename, $ext);
		
		return $ext[1];
	}
	
	/**
	 * Return filename without extension
	 *
	 * @param string $filename
	 * @return string
	 */
	function getBaseNoExt($filename) {
		preg_match("/^(.*)\.([a-zA-Z0-9]+)$/", $filename, $ext);
		
		return $ext[1];
	}
	
	function getMimetype($filename) {
		 
		if ($filename) {
			$f = escapeshellarg($filename); 
			$mimetype = trim(`file -bi $f` );

			list($mimetype, $foo) = explode("\t", $mimetype);
		} else {
			return false;
		}
		return $mimetype;
	}
	
	/**
	 * Return headers for viewing or downloading file.
	 *
	 * @param string $filename System filename
	 * @param string $filename_out File shown for user
	 * @param integer $force_download Force download
	 */
	function outfile($filename, $filename_out, $force_download=0) {
	
		$ext = $this->getExt($filename);
		$mimetype = $this->getMimetype($filename);

		if($force_download){
		  header("Content-Type: application/octet-stream\n");
		  header("Content-Disposition:  attachment; filename=$filename_out");
		}
		else{
		  if(preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT'])) { 
		    header("Content-Type: " . $mimetype . "\n");
		  } else { 
		    header("Content-Type: application/octet-stream\n");
		  }
		  header("Content-Disposition: filename=$filename_out"); 
		}
		
		header("Content-transfer-encoding: binary\n"); 
		header("Content-Length: ".filesize($filename) . "\n");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0\n"); 
		header("Pragma: no-cache\n"); 
		header("Expires: 0");
		
		
		// Output file to browser
		@$this->readfile_chunked($filename);

		exit(0);
	}

	function readfile_chunked ($filename) { 
		$chunksize = 1*(1024*1024); // how many bytes per chunk 
		$buffer = ''; 
		$handle = fopen($filename, 'rb'); 
		if ($handle === false) { 
			return false; 
		} 
		while (!feof($handle)) { 
			$buffer = fread($handle, $chunksize); 
			print $buffer; 
		} 
		return fclose($handle); 
	} 
	
	/**
	 * Return common extension pr mimetype.
	 *
	 * @param string $filetype
	 * @param string $filename
	 * @return string
	 */
	function extByFileType($filetype, $filename) {
			$extension = "";

			if ($filetype == "application/vnd.ms-word.document.macroEnabled.12") $extension = "docm";
			if ($filetype == "application/vnd.openxmlformats-officedocument.wordprocessingml.document") $extension = "docx";
			if ($filetype == "application/vnd.openxmlformats-officedocument.wordprocessingml.template") $extension = "dotx";
			if ($filetype == "application/vnd.ms-powerpoint.template.macroEnabled.12") $extension = "potm";
			if ($filetype == "application/vnd.openxmlformats-officedocument.presentationml.template") $extension = "potx";
			if ($filetype == "application/vnd.ms-powerpoint.addin.macroEnabled.12") $extension = "ppam";
			if ($filetype == "application/vnd.ms-powerpoint.slideshow.macroEnabled.12") $extension = "ppsm";
			if ($filetype == "application/vnd.openxmlformats-officedocument.presentationml.slideshow") $extension = "ppsx";
			if ($filetype == "application/vnd.ms-powerpoint.presentation.macroEnabled.12") $extension = "pptm";
			if ($filetype == "application/vnd.openxmlformats-officedocument.presentationml.presentation") $extension = "pptx";
			if ($filetype == "application/vnd.ms-excel.addin.macroEnabled.12") $extension = "xlam";
			if ($filetype == "application/vnd.ms-excel.sheet.binary.macroEnabled.12") $extension = "xlsb";
			if ($filetype == "application/vnd.ms-excel.sheet.macroEnabled.12") $extension = "xlsm";
			if ($filetype == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") $extension = "xlsx";
			if ($filetype == "application/vnd.ms-excel.template.macroEnabled.12") $extension = "xltm";
			if ($filetype == "application/vnd.openxmlformats-officedocument.spreadsheetml.template") $extension = "xltx";
			if ($filetype == "audio/mpeg") $extension = "mp3";
			if ($filetype == "image/pjpeg") $extension = "jpg";
			if ($filetype == "image/jpeg") $extension = "jpg"; 
			if ($filetype == "image/png") $extension = "png"; 
			if ($filetype == "image/gif") $extension = "gif"; 
			if ($filetype == "application/msword") $extension = "doc"; 
			if ($filetype == "application/pdf") $extension = "pdf"; 
			if ($filetype == "video/mpeg") $extension = "mpg"; 
			if ($filetype == "video/x-mpeg") $extension = "mpg"; 
			if ($filetype == "text/plain") $extension = "txt"; 
			if ($filetype == "video/avi") $extension = "avi"; 
			if ($filetype == "video/quicktime") $extension = "mov"; 
			if ($filetype == "video/x-msvideo") $extension = "avi"; 
			if ($filetype == "text/html") $extension = "html"; 
			if ($filetype == "application/x-zip-compressed") $extension = "zip"; 
			if ($filetype == "application/x-zip") $extension = "zip"; 
			if ($filetype == "application/zip") $extension = "zip"; 
			if ($filetype == "application/x-shockwave-flash") $extension = "swf"; 
			if ($filetype == "application/vnd.ms-excel") $extension = "xls"; 
			if ($filetype == "application/vnd.ms-powerpoint") $extension = "ppt"; 
			if ($filetype == "application/vnd.openxmlformats-officedocument.wordprocessingml.document") $extension = "docx"; 
			if ($filetype == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") $extension = "xlsx"; 
			if ($filetype == "application/vnd.openxmlformats-officedocument.presentationml.presentation") $extension = "pptx"; 
			if ($filetype == "application/octet-stream") {
				// could be any file, get file extension
				$extension = $this->getExt($filename); 			
			}
		return $extension;
	}
	
	function getFileCategory($ext){
		
		$ext = strtolower($ext);
		
		switch ($ext){
			case 'mp3' : $cat = "audio"; break;
			case 'jpg' : $cat = "picture"; break;
			case 'png' : $cat = "picture"; break;
			case 'gif' : $cat = "picture"; break;
			case 'bmp' : $cat = "picture"; break;
			case 'avi' : $cat = "movie"; break; 
			case 'mpg' : $cat = "movie"; break; 
			case 'mov' : $cat = "movie"; break; 
			case 'doc' : $cat = "document"; break; 
			case 'docx' : $cat = "document"; break; 
			case 'pdf' : $cat = "document"; break; 
			case 'xls' : $cat = "spreadsheet"; break; 
			case 'xlsx' : $cat = "spreadsheet"; break; 
			case 'ppt' : $cat = "presentation"; break; 
			case 'pptx' : $cat = "presentation"; break; 
			default 	 : $cat = "misc"; break; 
		}
		return $cat;
	}

}

?>
