<?php

/**
 * Enter description here...
 *
 * @package tuksiBase
 */
class tuksiInputfilter implements ArrayAccess {
	
	protected $data;
	protected $isStrict;

	function __construct ($data, $isStrict = false) {
		
		if (!is_array ($data)) {
			throw new Exception ("Only arrays are allowed here");
		}
		
		$this->data = $data;
		$this->isStrict = $isStrict;
		
		if (get_magic_quotes_gpc()) {
			$this->removeMagicQuotes();
		}
	
	} // End __construct
	
	/**
	 * Fjerner slashes, hvis magic qoutes er slået til!
	 *
	 */
	protected function removeMagicQuotes() {
		if (count($this->data) > 0) {
			foreach ($this->data as $key => $value) {
				if(!is_array($value)) {
					$this->data[$key] = stripslashes($value);
				}
			}
		}
	}
	
	/**
	 * Henter værdi fra array (Bruges internt)
	 */
	protected function getValue ($offset) {
		if (isset($this->data[$offset]))
		return $this->data[$offset];
		
	}
	
	
	public function asArray($offset) {
		
		if(!is_array($this->data[$offset])) {
			throw new Exception ("You can only use asArray to extract arrays from a filter");
		}
		return new tuksiInputfilter(($this->data[$offset]));
	}
	
	public function getData(){
		return $this->data;
	}
	
	public function getArray($offset) {
		if (!isset($this->data[$offset])) {
			return array();
		} elseif(!is_array($this->data[$offset])) {
			return array();
		}
		return $this->data[$offset];
	}
	
	
	/**
	 * Henter værdien fra array direkte (Usikkert!)
	 *
	 * @param unknown_type $offset
	 * @return mixed
	 */
	function raw ($offset) {
    	return $this->getValue ($offset);
  	}
  	
  	function html ($offset) {
  		return htmlentities($this->getValue ($offset));
  	}
  	
  	function getInt($offset) {
  		if (isset($this->data[$offset]) && ctype_digit($this->getValue($offset))) {
  			return intval($this->getValue($offset));
  		} else {
  			return $this->returnEmpty();
  		}	 
  	}

	public function hasdata(){
		if(count($this->data) > 0)
			return true;
		else
			return false;
	}


  	/**
	 * Validering af string.
	 *
	 * @param string $value
	 * @return bool
	 */
	function getStr($offset, $len = 256000) {
		if (isset($this->data[$offset]) && is_string($this->data[$offset]) && strlen($this->data[$offset]) < $len) {
			return $this->data[$offset];
		} else {
			return $this->returnEmpty();
		}
	}
  	
  	
  	function getDecimal($offset,$nb = 2) {
  		if (isset($this->data[$offset]) && is_numeric($this->getValue($offset))) {
  			return $this->getValue($offset);
  		} else {
			return $this->returnEmpty();
  		}
  	}
  	
  	function getRegex($offset, $regex) {
  		
	    if (isset ($this->data[$offset])) {
	      if (preg_match ($regex, $this->data[$offset])) {
	      	return $this->getValue($offset);
	      } else {
	      	return $this->returnEmpty();
	      }
	    } else {
	      $this->untaintedData[$value] = 1;
	    }
 	}
  
  	function returnEmpty() {
		
  		if ($this->isStrict == true) {
  			throw new Exception ("Untainting violation [regex, $regex] for element $value");
   	} else {
   		return null;
   	}
  	}
  	/**
  	 * Henter værdi klar til db kald.
  	 *
  	 * @param mixed $offset
  	 * @param object $db
  	 * @return mixed
  	 */
  	function db($offset, $db = '') {
  		if (isset($db))
  			return mysql_real_escape_string($this->getValue ($offset), $db);
  		else
  			return mysql_real_escape_string($this->getValue ($offset));
  	}

	/**
	 * ArrayAccess implementation
	 * Returns whether the requested $index exists 
	 * 
	 */
	  
	function offsetExists ($offset) {
		return (isset ($this->data[$offset]));    
	}
	
	/**
	 * ArrayAccess implementation
	 * Sets the value at the specified $index to $newval
	 * This kan not be done here
	 * 
	 * @param int $offset
	 * @param mixed $value
	 */
										  
	function offsetSet ($offset, $value) {
		//$this->data = $offset;
		throw new Exception ("You cannot change information in an instance of InputFilter as if it were an array");
	}
																			  
	function offsetUnset ($offset) {
		//unset ($this->data[$offset]);
		throw new Exception ("You cannot delete information in an instance of InputFilter as if it were an array");
	}

	function offsetGet ($offset) {
		throw new Exception ("You cannot extract information from an instance of InputFilter as if it were an array");
	}

	
	function __get($offset) {
    	throw new Exception ("You cannot access members of "
      . "an array encapsulated by Filter directly");
  	}
  
  	function __set($offset, $value)  {
    	throw new Exception ("You cannot access members of "
      	. "an array encapsulated by Filter directly");
  	}
  	
  	function __tostring() {
  		return print_r($this->data, 1);
  	}
} // End tuksi_inputfilter
/*
$arrTest = array(	"int" => 1231231, 
					'ids' => array('name' => 'heino','age' => 12,'city' => 3),
					"deci" => '1.000231', 
					"int_withchars" => "sdf9293",
				 	"str" => "09sdfoisjdfo",
				 	"str_quote" => "0sdfj2\'sdfsdf",
				 	"str_html" => "<b>bold</b>");

				 	
$arrFilter = new tuksi_inputefilter($arrTest);

print "<br>";
print "int, getInt(): " . $arrFilter->getInt("int") . "<br>";
print "int_withchars, getInt(): " . $arrFilter->getInt("int_withchars") . "<br>";
print "str, raw(): " . $arrFilter->raw("str_html") . "<br>";
print "str, html(): " . $arrFilter->html("str_html") . "<br>";
print "int, decimal(): " . $arrFilter->getDecimal("ids") . "<br>";

//array ex.
$arrIdFilter = $arrFilter->asArray("ids");
print "sub array<br> ";
print "name, html(): " . $arrIdFilter->html("name") . "<br>";
print "age, decimal(): " . $arrIdFilter->getDecimal("age") . "<br>";*/



?>
