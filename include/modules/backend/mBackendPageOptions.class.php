<?php

/**
 * mBackendPageOptions 
 * 
 * @uses mBackendBase
 * @package tuksiBackendModule 
 * @author Daniel Lynge <dly@dwarf.dk> 
 */
class mBackendPageOptions extends mBackendBase {
	
	function __construct(&$objMod){
		parent::__construct($objMod);
	}	

	function getHtml(){
		
		$objDB = tuksiDB::getInstance();
		$objPage = tuksiBackend::getInstance();

		$arrOptions = $this->getFields();
			
		$arrFieldUsed = array();
		foreach ($arrOptions as $arrField) {
			$arrFieldUsed[] = $arrField['colname'];
		}
					
		$html = '';
		if( $arrOptions ) {
			$objTuksiFielditem = new tuksiFielditemBase('option');
			
			// Save
			if( $_POST->getStr('userAction') == 'SAVE' && $objPage->arrPerms['SAVE'] ) {
				
				foreach ( $arrOptions AS $arrField ) {
					if ($_POST->getStr("showin_edit_" . $arrField['colname']))
						$showin_edit = 1;
					else
						$showin_edit = 0;
						
					if ($_POST->getStr("showin_settings_" . $arrField['colname']))
						$showin_settings = 1;
					else
						$showin_settings = 0;
					
					$arr = $_POST->getArray('option_template_'. $arrField['colname']);
					
					$this->saveTemplate($arrField['id'],$arr);
					
					$objTuksiFielditem->updateItem(
													$arrField['id'], 
													'option', 
													'cmstree', 
													$arrField['colname'], 
													0,
													$_POST->getStr('name_' . $arrField['colname']), 
													$_POST->getStr('cmsfieldtypeid_' . $arrField['colname']), 
													$_POST->getStr("fieldvalue1_" . $arrField['colname']), 
													$_POST->getStr("fieldvalue2_" . $arrField['colname']), 
													$_POST->getStr("fieldvalue3_" . $arrField['colname']), 
													$_POST->getStr("fieldvalue4_" . $arrField['colname']), 
													$_POST->getStr("fieldvalue5_" . $arrField['colname']), 
													0, 
													$_POST->getStr("cmsfieldgroupid_" . $arrField['colname']), 
													'', 
													'',
													$_POST->getStr("helptext_" . $arrField['colname'])
													);
					
					//save name
					$objField->htmltagname = 'name_' . $arrField['colname'];				
					$objField->value = $_POST->getStr('name_' . $arrField['colname']);														
					$objField->readonly = false;														
					$objField->id = $arrField['id'];																
					$objField->row = $arrField['id'];													
					$objFieldSuggest = new fieldTextSuggest($objField);
		
					$objFieldSuggest->saveData();	
					
					// Update pg_option settings:
					$sqlUpdOption = "UPDATE pg_option SET showin_edit='$showin_edit', showin_settings='$showin_settings' WHERE cmsfielditemid='{$arrField['id']}'";
					$objDB->write( $sqlUpdOption );
				}
				
				$objTuksiFielditem->saveData( $arrOptions );
				
				$arrFieldAdd = $_POST->getArray('field_add');
				
				if (count($arrFieldAdd) > 0) {
					
					foreach ($arrFieldAdd as $colname) {
							
						// Et colname må kun være der en gang
						if (!in_array($colname, $arrFieldUsed)) {
							$cmsfielditemid = $objTuksiFielditem->updateItem(0, 'option', 'cmstree', $colname, 0, $colname, 0, '', '', '', '', '', 1, 0, '', '', '');
							$objTuksiFielditem->addStandardPerms($cmsfielditemid);
							$objTuksiFielditem->findValues($colname, $cmsfielditemid);
							
							$sqlInsOption = "INSERT INTO pg_option (showin_edit, showin_settings, cmsfielditemid, isglobal) VALUES (0, 0, '$cmsfielditemid', 1)";
							$objDB->write( $sqlInsOption );
						}
					}
				}		
				
				$arrOptions = $this->getFields();
			}
			
			// Get column checkbox list:
			$fields = mysql_list_fields($objDB->arrSetup['dbname'], 'cmstree');
			$columns = mysql_num_fields($fields);
	
			$arrColnames = array();
			$arrFieldUsed = array();
			
			$arrDontShow = array('id', 'parentid', 'cmstreetypeid', 'value1', 'value2', 'value3', 'cmsfileid', 'backendscriptparam', 'name', 'tabwidth', 'pg_show_settings', 'pg_urlpart', 'pg_urlpart_full', 'datecreated', 'datechanged', 'datepublished', 'cmscontextid', 'cms_page_templateid', 'isdeleted', 'datedeleted','_DEL_cms_page_templateid');
			foreach ($arrOptions as $arrField) {
				$arrFieldUsed[] = $arrField['colname'];
			}
			
			$arrColnames = array();
					
			for ($i = 0; $i < $columns; $i++) {
				$colname = mysql_field_name($fields, $i);
				
				if (!in_array($colname, $arrDontShow)) {
					//$arrColnames[$colname] = $colname;				
					if (!in_array($colname, $arrFieldUsed))
						$arrColnames[$colname] = array('name' => $colname, 'value' => $colname);
					else
						$arrColnames[$colname] = array('name' => $colname, 'value' => $colname, 'used' => 1);
				} 
			} // END foreach field
					
			$arrTplData['fields'] = $arrColnames;
			$arrTplData['btnadd'] = $objPage->cmsText('btnadd');
			$arrTplData['nyt_element'] = $objPage->cmsText('nyt_element');
			
			$html = $objTuksiFielditem->getHTML( $arrOptions, $arrTplData );
		}
		
		$this->addButton("SAVE","","SAVE");	
					
		$objStdTpl = new tuksiStandardTemplateControl();
		$objStdTpl->addHeadline( "fonz" );
		$objStdTpl->addElement('', $html );
		
		return $objStdTpl->fetch();
	}
	
	function getFields() {

		$objDB = tuksiDB::getInstance();
		
		unset($arrField);
		
		$sqlOptions = "SELECT fi.*, po.showin_edit, po.showin_settings, po.isglobal FROM cmsfielditem AS fi, pg_option AS po ";
		$sqlOptions.= "WHERE fi.itemtype='option' AND po.isglobal='1' AND fi.id=po.cmsfielditemid ORDER BY seq";
		$arrOptions = $objDB->fetch( $sqlOptions );
		
		$arrFields = array();
		if ($arrOptions['num_rows']) {

			foreach ($arrOptions['data'] as $arrField) {
				$arrField['option_template'] = $this->getTemplateRel($arrField['id']);
				$arrFields[] = $arrField;

			}
		}
		
		return $arrFields;
	}
	
	
	function getTemplateRel($cmsfielditemid){
		
		$objDB = tuksiDB::getInstance();
		
		$sqlTpl = "SELECT t.name,t.id,o.pg_page_templateid as checked FROM pg_page_template t ";
		$sqlTpl.= "LEFT JOIN pg_option_template o on (t.id = o.pg_page_templateid AND o.cmsfielditemid = '$cmsfielditemid') ";
		$sqlTpl.= "ORDER BY t.name ";
		
		$arrRsTpl = $objDB->fetch($sqlTpl);
		
		return $arrRsTpl['data'];
		
	}
	
	function saveTemplate($cmsfielditemid,$arr){
		
		$objDB = tuksiDB::getInstance();
		
		$sqlDel = "DELETE FROM pg_option_template WHERE cmsfielditemid = '$cmsfielditemid'";
		$objDB->write($sqlDel);
		
		if(count($arr) > 0){
			foreach($arr as $tplId){
				$sqlIns = "INSERT INTO pg_option_template (cmsfielditemid,pg_page_templateid) VALUES ";
				$sqlIns.= "('$cmsfielditemid','$tplId')";
				$objDB->write($sqlIns);
			}
		}
	}
	
	function saveData(){
	}
}
?>
