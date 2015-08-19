<?php

define('CRLF', "\n");
	
/**
 * tuksiEmail 
 * Class for sending out multi-part emails(Plain text + HTML) with/without attachments 
 * 
 * @package tuksiBase 
 * @author Henrik Jochumsen <hjo@dwarf.dk> 
 */
class tuksiEmail {

	public $bound;
	public $message;
	public $email_text;
	public $to_email;
	public $setUTF8 = false;

	function __construct($fromname, $fromemail) {
		$this->from_name 	= $fromname;
		$this->from_email	= $fromemail;
		$this->files		= array();

		$this->bound = "000_mixed_" . uniqid("SO_PHP");
		$this->message = "This is a multi-part message in MIME format";
	}

	function make_headers() {

		$headers = '';
		//$headers .= "Content-Transfer-Encoding: 7bit\r\n";
		if ($this->returnemail) 
			$headers .= "Return-Path: {$this->returnemail}" . CRLF;
		if ($this->errors_to) 
			$headers .= "Errors-To: {$this->errors_to}" . CRLF;
		$headers .= "From: \"{$this->from_name}\" <{$this->from_email}>" . CRLF;
		$headers .= "MIME-Version: 1.0" . CRLF;
		$headers .= "Content-Type: multipart/mixed;" . CRLF;
		$headers .= "\tboundary=\"{$this->bound}\"" . CRLF; 
		//$headers .= "Content-Transfer-Encoding: 7bit" . CRLF; 
		$headers .= "{$this->message}" . CRLF;

		return $headers;
	}
	
	function add_uploaded_file($file) {
		
		$fileas = fopen($file['tmp_name'], "r");
		$filedata = "";
		while (!feof($fileas))
        {
             $filedata .= fread($fileas,4096);
        }
		
		$this->files[] = array("filename"=> $file['name'], "contenttype" => $file['type'], "charset" => "base64", "encoding" => "base64", "content" => $filedata);
	}
	
	//add a file as an attachtment to the email, name is optional
	function add_file($file,$filename = "") {
		
		if(file_exists($file)){
		   $filedata = file_get_contents($file);
         $filetype = filetype($file);
         if(!$filename){
            $filename = basename($file);
         }
         $this->files[] = array("filename"=> $filename, "contenttype" => $filetype, "charset" => "base64", "encoding" => "base64", "content" => $filedata);
		}
	}
	
	function add_file_stream($filedata,$filename,$filetype){
	   $this->files[] = array("filename"=> $filename, "contenttype" => $filetype, "charset" => "base64", "encoding" => "base64", "content" => $filedata);
	}
	
	function make_bodymail() {
		$message = "";

		$this->part = array();
		if ($this->email_text) {
			$this->part[] = array("contenttype" => "text/plain", "charset" => $this->setUTF8 ? "utf-8" : "iso-8859-1", "encoding" => "8bit", "content" => $this->email_text);
		}
		if ($this->email_html) {
			$this->part[] = array("contenttype" => "text/html", "charset" => $this->setUTF8 ? "utf-8" : "iso-8859-1", "encoding" => "8bit", "content" => $this->email_html);
		}
		
		$message .= "--{$this->bound}" . CRLF;
		$partcount = count($this->part);
		$currentpart = 0;
		if ($partcount > 1) {
			$this->bound1 = "000_alter_" . uniqid("SO_PHP");
			$message .= "Content-Type: multipart/alternative;" . CRLF;
			$message .= "\tboundary=\"{$this->bound1}\"" . CRLF. CRLF. CRLF; 
			$message .= "--{$this->bound1}" . CRLF; 
			
		}
		foreach ($this->part as $part) {
			$currentpart++;
			$message.= "Content-Type: {$part['contenttype']};" . CRLF;
			$message.= "\tcharset=\"{$part['charset']}\"" . CRLF;
			$message.= "Content-Transfer-Encoding: {$part['encoding']}" . CRLF;
			if (isset($part['attachment']))
				$message .= "Content-Disposition: attachment; filename=\"my.cnf.txt\"" . CRLF;
			$message.= CRLF;
			$message.= $part['content'] . CRLF;
			if ($partcount > 1) {
				if ($currentpart == $partcount) 
					$message.= "--{$this->bound1}--" . CRLF;
				else
					$message.= "--{$this->bound1}" . CRLF;
			}
		}

		// * ------------------------------------------------- *
		// Adding files
		// * ------------------------------------------------- *

		if (count($this->files)) {
			if ($partcount > 0) 
				$message .= "--{$this->bound}" . CRLF;
	
			$filecount = count($this->files);
			$currentfile = 0;
			foreach ($this->files as $file) {
				$currentfile++;
				// Adding file MIME header
				$message.= "Content-Type: {$file['contenttype']};" . CRLF;
				$message.= "\tcharset=\"{$file['charset']}\"" . CRLF;
				$message.= "Content-Transfer-Encoding: {$file['encoding']}" . CRLF;
				$message.= "Content-Disposition: attachment; filename=\"{$file['filename']}\"" . CRLF;
				$message.= CRLF;

				// Adding file data
				$message.= chunk_split(base64_encode($file['content'])) . CRLF.CRLF;

				// if last file. End mixed boundary
				if ($currentfile == $filecount) 
					$message.= "--{$this->bound}--" . CRLF . CRLF;
				else
					$message.= "--{$this->bound}" . CRLF;
			}

		} else { 
			// If no files. End mixed boundary
			$message .= "--{$this->bound}--" . CRLF; 
		}

		return $message;
	}

	function encode_iso88591($string){
 	 $text = '=?iso-8859-1?q?';
 	 for( $i = 0 ; $i < strlen($string) ; $i++ ){
		$encode = 1;
		$binno = ord($string[$i]);
		if ($binno >= 65 && $binno <= 90) $encode = 0;
		if ($binno >= 97 && $binno <= 122) $encode = 0;

		if ($encode) {
			//print $binno . " = " . $string[$i] . "<br>";;
 	  	$val = $binno;
 	  	$val = dechex($val);
 	  	$text .= '='.$val;
		} else
 	  	$text .= $string[$i];
 	 }
 	 $text .= '?=';
 	 return $text;
	}
  
	function utf8_entity_decode($matches) {
		$entity = $matches[0];
		$convmap = array(0x0, 0x10000, 0, 0xfffff);
		return mb_decode_numericentity($entity, $convmap, 'UTF-8');
	}
   
	function encode_utf8($string) {
		$text = '=?utf-8?b?';
		$text .= base64_encode(preg_replace_callback('/&#\d{2,5};/', array($this, 'utf8_entity_decode'), $string));
		$text .= '?=';
		return $text;
	}
	
	function make_email() {
		/* additional headers */

		$this->headers = $this->make_headers();
		$this->body = $this->make_bodymail();
		
		if ($this->setUTF8) {
			$this->subject = $this->encode_utf8($this->subject);
		} else {
			$this->subject = $this->encode_iso88591($this->subject);
		}	

		$this->emailsourceall = "To: " . $this->to_email . CRLF;
		$this->emailsourceall.= "Subject: " . $this->subject . CRLF;
		$this->emailsourceall.= $this->headers . $this->body;
		$this->emailsource = $this->headers . $this->body;
	}

	function send() {
		mail($this->to_email, $this->subject, $this->body, $this->headers);
	}
}


?>
