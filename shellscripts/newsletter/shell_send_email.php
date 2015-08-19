#!/pack/php/bin/php -q
<?
include_once(dirname(__FILE__)."/../../include/tuksi_init.php");

tuksiIni::setSystemType('backend');

$objShell = new tuksiShell(18, 0);

tuksiIni::loadNewsletterConf();
$arrConf = tuksiConf::getConf();

$dh = opendir($arrConf['newsletter']['path']['spool']);

if ($dh) {
  while (false !== ($dir = readdir($dh))) {
    $fulldir = $arrConf['newsletter']['path']['spool'] . "/" . $dir;

    if (is_dir($fulldir) && 
				!in_array(substr($dir,0,1), array('.', '..')) && 
				$fulldir != $arrConf['newsletter']['path']['spool_single']) {

			$objShell->log("Found folder : $fulldir");

      $handle = opendir($fulldir);

      if ($handle) {
				$objShell->log("Getting files");
				$emailsent = 0;
				while (false !== ($filename = readdir($handle))){

			   if ($emailsent < $arrConf['newsletter']['setup']['maxemails']) {
			   		$fullfilename= $fulldir . "/" . $filename;

				    if (is_file($fullfilename) && $fullfilename) {
				      //print "Filename: $fullfilename\n";
	  			    $emailsent += 1;
							$objShell->log($emailsent . ' ' . $filename, 1);

							if ($arrConf['newsletter']['setup']['errormail'])
								$errorEmail = $arrConf['newsletter']['setup']['errormail'];
							else 
								$errorEmail = 'admin@dwarf.dk';

	      			$shellCmd = "cat '$fullfilename' | /usr/sbin/sendmail -f $errorEmail {$filename}";
							$objShell->log($shellCmd, 1);
	      
	    			  system($shellCmd);
	     	 			unlink($fullfilename);
	    		}
	  	}
	}
	closedir($handle);
      }

			$objShell->log("E-mails sent: $emailsent");
      if (!$emailsent) {
							$objShell->log("Deleting empty folder $fulldir");
				rmdir($fulldir);
      }
    }
  }
  closedir($dh);
}

$objShell->end();
?>
