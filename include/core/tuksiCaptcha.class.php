<?php

	// Include reCAPTCHA lib
	include_once(dirname(__FILE__) . "/../../thirdparty/recaptcha/recaptchalib.php");

	class tuksiCaptcha {

		private $publicKey;
		private $privateKey;

		public function __construct() {

			$this->publicKey = tuksiIni::$arrIni['captcha']['public'];
			$this->privateKey = tuksiIni::$arrIni['captcha']['private'];
		} // function __construct

		public function checkAnswer($challenge, $response) {
			return recaptcha_check_answer($this->privateKey, $_SERVER['REMOTE_ADDR'], $challenge, $response);
		} // function checkAnswer

		public function getHtml($error = null) {
			return recaptcha_get_html($this->publicKey, $error);
		} // function getHtml
		
	} // class tuksiCaptcha

?>
