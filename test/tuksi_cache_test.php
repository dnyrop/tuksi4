#!/pack/php/bin/php
<?
include('../include/tuksi_init.php');

while ($i++ < 1000) {
	$data = tuksi_cache::get('test');

	if (!$data) {
		//print "Setting cache\n";

		tuksi_cache::set('test', file_get_contents('cache_data.txt'), rand(1, 4));
	} else {
		if (36252 != strlen($data)) 
			print "UPS: Wrong size cache: " . strlen($data) . "\n";
	}
	usleep(1000);
}
?>
