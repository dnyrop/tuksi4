<?

// Starter TuksiFTP 
$testFtp = new tuksiFtp();

// Sætte hostname, login og password
$testFtp->setHost("www.dudicom.dk","ds","sd");

// Sætter sti på ftp-server
$testFtp->setHostPath("/web/dwarf");

//Sætte lokal sti
$testFtp->setLocalPath("/home/dwarfdk/tuksi.test/frontend/testpath");

// Upload hele mappen testpath
$testFtp->ftpPut("testpath");

// Vis debug info
$testFtp->showDebug();

?>