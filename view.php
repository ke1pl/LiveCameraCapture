<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

?>
<!DOCTYPE html>
<html>

<body>

	<h1>Happy? Happy!</h1>

	<p><a href='grabber.php?t=<?php echo time(); ?>'>Try grab new images</a></p>
	<p><a href='cleanup.php?t=<?php echo time(); ?>'>Update Generated List </a></p>
	<?php

	include 'helpers.php';

	$free = round((disk_free_space("/") / 1024) / 1024);
	$total = round((disk_total_space("/") / 1024) / 1024);
	print ('<p>Disk free space: ' . $free . 'MB (out of ' . $total . 'MB)</p>');

	$files = get_u_files(load_db_form_file());
	//$files = array_slice(load_db_form_file(), 0, 500);
	//$files = load_db_form_file();
	
	print ('<p>Images to render: ' . count($files) . '</p>');

	$files_grouped_by_date = (object) null;

	foreach ($files as $file) {
		$date = new DateTime("@" . $file->{'timestamp'});
		$date->setTimezone(new DateTimeZone('America/Winnipeg'));
		$date = $date->format('Y-m-d');

		if (property_exists($files_grouped_by_date, $date)) {
			array_push($files_grouped_by_date->{$date}, $file);
		} else {
			$files_grouped_by_date->{$date} = [$file];
		}
	}

	function cmp($a, $b)
	{
		return $a->timestamp > $b->timestamp;
	}

	$size = 0;

	$keys = array_keys((array) $files_grouped_by_date);

	rsort($keys);

	foreach ($keys as $key) {
		$value = $files_grouped_by_date->{$key};

		if (count($value) > 0) { //TODO remove?
			$output = [];

			usort($value, "cmp");

			foreach ($value as $file) {
				$file_size = $file->{'size'};
				//if ($file_size <> 0) {
				$size += $file_size;

				array_push($output, render_file($file));
				//}
			}
			print '<p><b>' . $key . '</b> ' . count($output) . ' unique images of ' . count($value) . ' downloaded</p>';

			foreach ($output as $line) {
				print $line;
			}

			print '<br/>';
		}
	}

	$z = human_filesize($size);
	print '<p>Storage space taken: ' . $z . '</p>';
	?>
</body>

</html>