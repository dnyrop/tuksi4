<?php

	/**
	 * Extracts information from the user agent
	 *
	 */
	class gUserAgent {

		// Properties array
		private $arrData = array(

			// Mobile Platform
			'Android' => false,
			'BlackBerry' => false,
			'iOS' => false,
			'WindowsPhone' => false,

			// Mobile Device
			'Mobile' => false,
			'Tablet' => false,
			
			// Platform Version
			'Version' => 0

		);

		/**
		 * Constructor
		 *
		 */
		public function __construct($userAgent = '') {
			if (empty($userAgent)) {
				$userAgent = $_SERVER['HTTP_USER_AGENT'];
			} // if

			$this->parseUserAgent($userAgent);
		} // function __construct

		/**
		 * Public Getter for properties
		 * 
		 * @param string $property 
		 * @return mixed
		 * @throws Exception if property is not found
		 */
		public function __get($property) {
			// Sanity Check
			if (in_array($property, $this->arrData)) {
				return $this->arrData[$property];
			} // if

			throw new Exception('Property "' . $property . '" is not accessible.');
		} // function __get

		/**
		 * Public Setter for properties
		 * 
		 * @param string $property 
		 * @param mixed $value 
		 * @return void
		 */
		public function __set($property, $value) {
			// Sanity Check
			if (in_array($property, $this->arrData)) {
				$this->arrData[$property] = $value;
			} // if
		} // function __set

		/**
		 * Parses User Agent string
		 * 
		 * @param string $userAgent 
		 * @return void
		 */
		private function parseUserAgent($userAgent) {
			// Lower case string
			$userAgent = strtolower($userAgent);

			// Match iOS Devices
			if (preg_match('/iphone|ipod|ipad/i', $userAgent)) {
				$this->iOS = true;
				$this->Tablet = (bool)preg_match('/ipad/i', $userAgent);
				$this->Mobile = !$this->Tablet;
				$this->Version = floatval(str_replace('_', '.', substr($userAgent, strpos($userAgent, 'os') + 3, 5)));
			} // iOS

			// Match Android Devices
			else if (preg_match('/android/i', $userAgent)) {
				$this->Android = true;
				$this->Mobile = (bool)preg_match('/mobile/i', $userAgent);
				$this->Tablet = !$this->Mobile;
				$this->Version = floatval(substr($userAgent, strpos($userAgent, 'android') + 8, 5));
			} // Android

			// Match Windows Phone Devices
			else if (preg_match('/windows phone/i', $userAgent)) {
				$this->WindowsPhone = true;
				$this->Mobile = true;
				if (strpos($userAgent, 'os') !== false) {
					$this->Version = floatval(substr($userAgent, strpos($userAgent, 'os') + 3, 3));
				} else {
					$this->Version = floatval(substr($userAgent, strpos($userAgent, 'windows phone') + 14, 3));
				} // if
			} // Windows Phone

			// Match BlackBerry Devices
			else if (preg_match('/blackberry/i', $userAgent)) {
				$this->BlackBerry = true;
				$this->Mobile = true;
				if (strpos($userAgent, 'version/') !== false) {
					// User Agent in BlackBerry 6 and BlackBerry 7
					$this->Version = floatval(substr($userAgent, strpos($userAgent, 'version/') + 8, 3));
				} else {
					// User Agent in BlackBerry Device Software 4.2 to 5.0
					$this->Version = floatval(substr($userAgent, strpos($userAgent, '/') + 1, 3));
				} // if
			} // BlackBerry
		} // function parseUserAgent

	} // class gUserAgent
?>
