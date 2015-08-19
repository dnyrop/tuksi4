<?


	include("include/tuksi_eventlog.inc");

	$event = new tuksi_eventlog(1, "admin@dwarf.dk", 0);

	$event->log("Starting script");

	// Event that needs loggind.
	$event->log("Error in", 1);

	// Event that is need to be fixed.
	$event->log("Error in", 2);

	$event->log("End script");
	$event->end();
?>

<form method="POST">
<textarea name="text" cols=80 rows=20><?=$event->log?></textarea>
<input name="btnsend" type="submit">
</form>
