<?php


/**
 * Enter description here...
 *
 * @Todo PHP DOC 
 * @uses tuksiDB
 * @package tuksiBackend
 */
class tuksiFormElements {
	
	function __construct(){
	}

	public function getCheckBox($options){
		
		$checked = $options['checked'] ? 'checked' : '';
		
		if(isset($options['id'])) {
			$options['name'] = $options['id'];
		}
		
		if (isset($options['disabled']) && $options['disabled']) {
			$disabled = " disabled='disabled' ";
		} else {
			$disabled = '';
		}
		if (!isset($options['id']))
			$options['id'] = '';
		
		$html = '<input type="checkbox" class="checkbox" id="'.$options['id'].'" name="'.$options['name'].'"';

		if(isset($options['value']) && $options['value'])
			$html.= 'value="'.$options['value'].'"';
		$html.= ' '.$checked.' '.$disabled.' />';
		
		if(isset($options['desc']) && $options['desc']) {
			$html.= "&nbsp;".$options['desc'];
		}
		$html.= self::setHelpOption($options);

		return $html;
	}
	
	public function getRadio($options){
		
		if (isset($options['checked']) && $options['checked']) {
						$checked = 'checked';
		} else {
						$checked = '';
		}

		
		if (isset($options['disabled']) && $options['disabled']) {
			$disabled = " disabled='disabled' ";
		} else {
			$disabled = '';
		}
		if(!$options['name']) {
			$options['name'] = $options['id'];
		}
		
		
		$html = '<input type="radio" class="radio" name="'.$options['name'].'" id="'.$options['id'].'" value = "'.$options['value'].'" '.$checked.' '.$disabled.' />';
		$html.= self::setHelpOption($options);
		
		return $html;
	}
	
	
	public function getSelect($options){
		
		$onchange = ($options['onchange']) ? 'onchange="'.$options['onchange'].'"' : '';
		
		if (isset($options['disabled']) && $options['disabled']) {
			$disabled = " disabled='disabled' ";
		} else {
			$disabled = '';
		}
		
		if(isset($options['id'])) {
			$options['name'] = $options['id'];
		}
		
		if(!isset($options['width'])) {
			$options['width'] = '130';
		}
		
		if(isset($options['error'])) {
			$style = "border:2px solid #7F1301;";
			$errorMsg = $options['errormsg'];
		} else {
			$style = "";
		}
		
		if($style) {
			$style = "style='".$style."'";
		}

		if (!isset($options['id']))
			$options['id'] = '';
		
		$html = '<select '.$style.' id="'.$options['id'].'" style="width:'.$options['width'].'px" name="'.$options['name'].'" '.$onchange.' '.$disabled.' />';
		
		if (is_array($options['options']))
			foreach($options['options'] as $opt) {
				$sel = $options['selected'] == $opt['value'] ? "selected" : "";
				$html.= '<option value="'.$opt['value'].'" '.$sel.'>'.$opt['name'].'</option>';	
			}
		
		$html.= "</select>";
		$html.= self::setHelpOption($options);
		
		return $html;
	}
	
	public function getInput($options){
		
		$style = "";
		
		if(!isset($options['class']) || $options['class'] == "")
			$options['class'] = "text";
		
		if(isset($options['error']) && $options['error']) {
			$style = "border:1px solid #7F1301;";
			$errorMsg = $options['errormsg'];
		} else {
			$style = "";
		}
		
		if(isset($options['id'])) {
			$options['name'] = $options['id'];
		}
		
		if(isset($options['maxlength']) && $options['maxlength'] > 0) {
			$maxlength = ' maxlength = "' . $options['maxlength'] .'"';
		} else {
			$maxlength = "";
		}
		
		if(isset($options['width']) && is_numeric($options['width'])) {
			$style.= "width:" . $options['width'] . "px;";
		}
		
		if($style) {
			$style = "style='".$style."'";
		}
		
		$type = 'text';
		
		if(isset($options['type'])) {
			$type = $options['type'];
		}
			
		if (isset($options['disabled']) && $options['disabled']) {
			$disabled = " disabled='disabled' ";
		} else {
			$disabled = '';
		}
		
		$html.= '<input '.$style.' '.$maxlength.' type="' .$type . '" value="'.$options['value'].'" name="'.$options['name'].'" id="'.$options['name'].'" class="'.$options['class'].'" size="20" '.$disabled.' />';
		
		$html.= self::setHelpOption($options);

		if (isset($errorMsg)) {
			$html.= "<br />" . $errorMsg;
		}
		return $html;
	}
	
	public function getDate($options){
		$tpl = new tuksiSmarty();
		
		if(isset($options['id'])) {
			$options['name'] = $options['id'];
		}
		if(!isset($options['usetime'])) {
			$options['usetime'] = 0;
		}
		
		if(!isset($options['usehour'])) {
			$options['usehour'] = 0;
		}
		
		if(!isset($options['htmltagname'])) {
			$options['htmltagname'] = $options['id'];
		}
		
		$tpl->assign('options',$options);
		
		$html = $tpl->fetch('fieldtypes/fieldDatepicker.tpl');
		$html.= self::setHelpOption($options);

		return $html;
	}
	
	public function getButton($options) {
		
		$action = !isset($options['action']) ? 'SAVE' : $options['action'];
		
		$colorClass = "buttonType3";
		
		if(isset($options['color'])) {
			switch ($options['color']) {
				case 'white':
					$colorClass = "buttonType3";
					break;
				case 'blue':
					$colorClass = "buttonType1";
					break;
				case 'black':
					$colorClass = "buttonType2";
					break;
				default:
					$colorClass = "buttonType3";
					break;
			}
		}
		
		$iconClass = "";
		
		if(isset($options['icon'])) {
			switch ($options['icon']) {
				case 'add':
					$iconClass = "itemAdd";
					break;
				case 'save':
					$iconClass = "itemSave";
					break;	
				case 'delete':
					$iconClass = 'itemDelete';
					break;
				case 'uploadImage':
					$iconClass =	'uploadImage';
					break;
			}
		}
		
		$classType = "";
		
		$hidden  = "";
		
		if($options['customaction']) {
			$onclick = $options['customaction'];
		} else {
			$onclick = $options['onclick'] . "doAction('".$action."');";
		}
		
		if($options['postvalue']) {
			$hidden = self::getInput(array('id' => $options['postvalue'],
											'value' => 0,
											'type' => 'hidden'));
												
			$onclick = "document.tuksiForm." . $options['postvalue'] . ".value = 1;". $onclick;		
		}		
			
		$html = $hidden . '<a class="'.$colorClass.' '.$iconClass.'" onclick="'.$onclick.' return false;" href="#"><span><span>'.$options['value'].'</span></span></a>';
		$html.= self::setHelpOption($options);
		return $html;
	}
	
	
	public function getCmstextInput($options){
		
		$html = "<div style='position:relative;'>"; 
		$html.= self::getInput($options);
		$html.= '<div id="tokens_'.$options['id'].'" class="autocomplete" style="z-index:999999;"></div></div>';
		$html.= "<script>";
		$html.= "document.observe('dom:loaded' , function() {";
		$html.= "new Ajax.Autocompleter('".$options['id']."' , 'tokens_".$options['id']."' , '/services/ajax/cmstext.php',{paramName:'token',select: 'testtest'} );";
		$html.= "});";
		$html.= "</script>";
		return $html;
	}

	function setHelpOption($options) {
		if (isset($options['help'])) {
			return '<br><i>' . $options['help'] . '</i>';
		}

		return '';
	}
}
?>
