<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

?>
<!DOCTYPE html>
<html>

<body>

	<h1>Happy? Happy!</h1>

	<a href='grabber.php?t=<?php echo time(); ?>'>Try grab new images</a>
	<?php

	include 'helpers.php';

	$free = round((disk_free_space("/") / 1024) / 1024);
	$total = round((disk_total_space("/") / 1024) / 1024);
	print ('<p>Disk free space: ' . $free . 'MB (out of ' . $total . 'MB)</p>');

	$files = load_db_form_file();

	print ('<p>Images to render: ' . count($files) . '</p>');

	$files_grouped_by_date = (object) null;

	foreach ($files as $file) {
		$date_for_separator = url_param_to_date($file->{'unified_number'});
		$date_for_separator->setTimezone(new DateTimeZone('America/Winnipeg'));
		$date_for_separator = $date_for_separator->format('Y-m-d');

		if (property_exists($files_grouped_by_date, $date_for_separator)) {
			array_push($files_grouped_by_date->{$date_for_separator}, $file);
		} else {
			$files_grouped_by_date->{$date_for_separator} = [$file];
		}
		;
	}

	$size = 0;
	$not_null_count = 0;

	foreach ($files_grouped_by_date as $key => $value) {
		if (count($value) > 0) { //TODO remove?
			$output = [];
			foreach ($value as $file) {
				$filename = $file->{'path'};
				$file_size = $file->{'size'};
				if ($file_size <> 0) {
					$date = url_param_to_date($file->{'unified_number'});
					$date->setTimezone(new DateTimeZone('America/Winnipeg'));
					$date = $date->format('Y-m-d H:i');
					$size += $file_size;
					$size_readable = human_filesize($file_size);
					$not_null_count++;

					array_push($output, '<a href="' . $filename . '"><img loading="lazy" src="' . $filename . '" width="150" height="100" title="' . $date . ' ' . $size_readable . '"></a>');
				}
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
	print '<p>Non empty images: ' . $not_null_count . '</p>';
	?>
</body>

</html>