<?

// Starter TuksiFTP 
$testFtp = new tuksiFtp();

// S�tte hostname, login og password
$testFtp->setHost("www.dudicom.dk","ds","sd");

// S�tter sti p� ftp-server
$testFtp->setHostPath("/web/dwarf");

//S�tte lokal sti
$testFtp->setLocalPath("/home/dwarfdk/tuksi.test/frontend/testpath");

// Upload hele mappen testpath
$testFtp->ftpPut("testpath");

// Vis debug info
$testFtp->showDebug();

?>