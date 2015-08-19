<?php
 
include_once(dirname(__FILE__) . "/../../thirdparty/smarty/Smarty.class.php");

/**
 * Tuksi smarty class
 * Wrapper class for smarty
 *
 * @package tuksiBase
 */
 
class tuksiSmarty extends Smarty {

	/**
	 * Setup
	 *
	 * @param string $template_path Ændring af standard template sti 
	 */
	public function __construct($template_path = "") {
		parent::__construct();
		// Prevent double escaping
		SmartyException::$escape = false;

		if ($template_path) { 
			$path = $template_path;
		} else {
			$path = dirname(__FILE__) . "/../../templates/";
		}
		$this->template_dir = $path;  
		$this->compile_dir = $path . "compiled/";
	} // End tuksi_smarty function
	
	/**
	 * Fetches a rendered Smarty template
	 *
	 * @param string $template          the resource handle of the template file or template object
	 * @param mixed  $cache_id          cache id to be used with this template
	 * @param mixed  $compile_id        compile id to be used with this template
	 * @param object $parent            next higher level of Smarty variables
	 * @param bool   $display           true: display, false: fetch
	 * @param bool   $merge_tpl_vars    if true parent template variables merged in to local scope
	 * @param bool   $no_output_filter  if true do not run output filter
	 * @return string rendered template output
	 */
	public function fetch($template = null, $cache_id = null, $compile_id = null, $parent = null, $display = false, $merge_tpl_vars = true, $no_output_filter = false) {
		
		$objDebug = tuksiDebug::getInstance();
		
		if ($objDebug->isActive()) {
			$startTime = $objDebug->getTime();
		}
                
		if (tuksiIni::$arrIni['setup']['system'] == 'frontend') {
			$objPage = tuksiFrontend::getInstance(true);
		} else if (tuksiIni::$arrIni['setup']['system'] == 'newsletter') {
			$objPage = tuksiNewsletter::getInstance(true);
		} else {
			$objPage = tuksiBackend::getInstance(true);
			$objUser = tuksiBackendUser::getInstance(true);
		}
		
		$arrConf = tuksiConf::getConf();

		$this->assign("conf", $arrConf);
		$this->assign("link",$arrConf['link']);
		$this->assign("path", $arrConf['path']);
		
		if (isset($objPage)) {
			$this->assign("page", $objPage->getPage());
		}

		if (isset($objUser)) {
			$this->assign("user", $objUser->getUserInfo());
		}
		
		$output = parent::fetch($template, $cache_id, $compile_id, $parent, $display, $merge_tpl_vars, $no_output_filter);
		
		if ($objDebug->isActive()) {
			$stopTime = $objDebug->getTime(); 
			$execTime = ($stopTime - $startTime);
			
			$objDebug->tpl($template, $startTime, $execTime);
		}
                
		return $output;
	}

	/**
	 * Displays a Smarty template
	 *
	 * @param string $template   the resource handle of the template file or template object
	 * @param mixed  $cache_id   cache id to be used with this template
	 * @param mixed  $compile_id compile id to be used with this template
	 * @param object $parent     next higher level of Smarty variables
	 * @param bool   $merge_tpl_vars    if true parent template variables merged in to local scope
	 * @param bool   $no_output_filter  if true do not run output filter
	 */
	public function display($template = null, $cache_id = null, $compile_id = null, $parent = null, $merge_tpl_vars = true, $no_output_filter = false) {
		$this->fetch($template, $cache_id, $compile_id, $parent, true, $merge_tpl_vars, $no_output_filter);
	}	
} // End tuksiSmarty klasse
?>
