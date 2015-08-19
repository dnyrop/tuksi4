<?php

function smarty_function_treestructure($params, &$smarty) {
	
	$returnHtml = "";
	
	$arrNodes = $params['nodes'];
	$currentNodeId = $params['treeid'];
	
	if(is_array($arrNodes)) {
		$returnHtml = makeChildren($arrNodes,$currentNodeId,true);
	}
	
	return $returnHtml;
}

function makeNode($arrNode,$currentNodeId){

	$childrenHtml = "";
	
	if($arrNode['isactive'] || $arrNode['cmscontextid'] != 2) {
		$spanClass = "visible";
	} else {
		$spanClass = "invisible";
	}
	
	$addHtml = "";
	
	if ($arrNode['has_children']) {
		$spanClass.= "folder";
		if($arrNode['selected']) {
			$arrNode['name'] = $arrNode['name'];
			$addHtml = "<img src='/themes/default/images/menu/gx_tree_blank.png' onclick=\"closeMenuNode('".$currentNodeId."','".$arrNode['id']."');return false;\">";
			$liClass = ($arrNode['last']) ? "open openLast" : "open";
			$childrenHtml = makeChildren($arrNode['nodes'],$currentNodeId,false);
		}	else {
			$liClass = ($arrNode['last']) ? "closed closedLast" : "closed";
			$arrNode['name'] = $arrNode['name'];
			$addHtml = "<img src='/themes/default/images/menu/gx_tree_blank.png' onclick=\"openMenuNode('".$currentNodeId."','".$arrNode['id']."');return false;\">";
		}
	} else {
		$spanClass.= "page";
		$liClass = ($arrNode['last']) ? "noChild noChildLast" : "noChild";
	}
	
	if ($arrNode['unpublished'] && $arrNode['cmscontextid'] == 2) {
		$spanClass.= "Notpublic";
	}
	if ($currentNodeId == $arrNode['id']) {
		if($arrNode['rowid']) {
			if($currentNodeId == $arrNode['rowid']) {
				$aClass = "active";
			} else {
				$aClass = "";
			}
		} else {
			$aClass = "active";
		}
	} else {
		$aClass = "";
	}
	if ($arrNode['external_link']) {
		$link = $arrNode['external_link'];
		$target = "_blank";
		if($arrNode['popup']) {
			list($width,$height) = split("x",$arrNode['popup']);
			$onclick = "onclick = \"tuksi.util.setPopup('".$arrNode['external_link']."','$width','$height');return false;\"";
		}
	} else {
		
		$link = tuksiTools::getBackendUrl($arrNode['id']);
		$target = "_self";
		if($arrNode['rowid']) {
			$link.= "&rowid=".$arrNode['rowid'];
		}
	}
	
	$returnHtml = "<li class='$liClass'><a class='$aClass' href='".$link."' target='$target'><span class='$spanClass' $onclick>" . $arrNode['name'] . "</span></a>" . $addHtml . $childrenHtml . "</li>";
	return $returnHtml;

}

function makeChildren($arrNodes,$currentNodeId,$isTop = false){
	
	$returnHtml = "<ul>";
	
	if($isTop) {
		$returnHtml.= "<li class='home'>Home</li>";
	}
	
	foreach ($arrNodes as $arrNode) {
		$returnHtml.= makeNode($arrNode,$currentNodeId);
	}
	$returnHtml.= "</ul>";
	return $returnHtml;
}
?>