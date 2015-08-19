<?php

	/**
	 * Class for ease ajax responses
	 *
	 */
	class gJSON {
		
		/**
		 * Constructor
		 *
		 */
		private function __construct() {
		} // function __construct
		
		public static function respond($mixedData) {

			if (!is_array($mixedData) && !is_object($mixedData)) {
				$mixedData = (array)$mixedData;
			} // if

			self::encodeData($mixedData);

			print json_encode($mixedData);
		} // function respond

		private static function encodeData(&$mixedData) {
			if (is_array($mixedData) || is_object($mixedData)) {
				foreach ($mixedData as $key => &$value) {
					self::encodeData($value);
				} // foreach
			} else {
				$mixedData = tuksiTools::encode($mixedData);
			} // if
		} // function encodeData

	} // class gJSON
?>
